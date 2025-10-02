<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Get all payments with filtering and pagination
     */
    public function index(Request $request)
    {
        $query = Payment::with(['client.user', 'invoice', 'location']);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->has('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->has('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        if ($request->has('min_amount')) {
            $query->where('amount', '>=', $request->min_amount);
        }

        if ($request->has('max_amount')) {
            $query->where('amount', '<=', $request->max_amount);
        }

        $perPage = $request->get('per_page', 15);
        $payments = $query->orderBy('payment_date', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $payments
        ]);
    }

    /**
     * Get specific payment
     */
    public function show($id)
    {
        $payment = Payment::with(['client.user', 'invoice.appointment.service', 'location'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $payment
        ]);
    }

    /**
     * Process new payment
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoice_id' => 'required|exists:invoices,id',
            'client_id' => 'required|exists:clients,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,card,digital_wallet,bank_transfer,check',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
            'location_id' => 'nullable|exists:locations,id',
            'payment_details' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $invoice = Invoice::findOrFail($request->invoice_id);

        // Check if payment amount exceeds invoice balance
        if ($request->amount > $invoice->balance_due) {
            return response()->json([
                'success' => false,
                'message' => 'Payment amount cannot exceed invoice balance'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Create payment record
            $payment = Payment::create([
                'invoice_id' => $request->invoice_id,
                'client_id' => $request->client_id,
                'processed_by' => auth()->id(),
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'status' => 'completed',
                'payment_date' => $request->payment_date,
                'notes' => $request->notes,
                'location_id' => $request->location_id,
                'payment_details' => $request->payment_details,
            ]);

            // Update invoice balance
            $newBalance = $invoice->balance_due - $request->amount;
            $invoice->balance_due = max(0, $newBalance);
            $invoice->status = ($invoice->balance_due <= 0) ? 'paid' : 'partially_paid';
            $invoice->save();

            // Update appointment status if fully paid
            if ($invoice->appointment && $invoice->status === 'paid') {
                $invoice->appointment->update(['status' => 'completed']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully',
                'data' => $payment->load(['client.user', 'invoice', 'location'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update payment
     */
    public function update(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|in:pending,completed,failed,refunded',
            'notes' => 'nullable|string|max:1000',
            'payment_details' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $payment->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Payment updated successfully',
            'data' => $payment->load(['client.user', 'invoice', 'location'])
        ]);
    }

    /**
     * Refund payment
     */
    public function refund(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'refund_amount' => 'required|numeric|min:0.01|max:' . $payment->amount,
            'refund_reason' => 'required|string|max:500',
            'refund_method' => 'required|in:original_method,cash,check',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($payment->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Only completed payments can be refunded'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Create refund payment record
            $refundPayment = Payment::create([
                'invoice_id' => $payment->invoice_id,
                'client_id' => $payment->client_id,
                'processed_by' => auth()->id(),
                'amount' => -$request->refund_amount, // Negative amount for refund
                'payment_method' => $request->refund_method,
                'status' => 'completed',
                'payment_date' => now(),
                'notes' => 'Refund: ' . $request->refund_reason,
                'location_id' => $payment->location_id,
                'payment_details' => [
                    'refund_reason' => $request->refund_reason,
                    'original_payment_id' => $payment->id,
                ],
            ]);

            // Update original invoice balance
            $invoice = $payment->invoice;
            $invoice->balance_due += $request->refund_amount;
            $invoice->status = 'partially_paid';
            $invoice->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Refund processed successfully',
                'data' => $refundPayment->load(['client.user', 'invoice', 'location'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Refund processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment statistics
     */
    public function statistics(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth());
        $dateTo = $request->get('date_to', now()->endOfMonth());

        $stats = [
            'total_payments' => Payment::whereBetween('payment_date', [$dateFrom, $dateTo])->count(),
            'total_amount' => Payment::whereBetween('payment_date', [$dateFrom, $dateTo])
                ->where('status', 'completed')
                ->sum('amount'),
            'refunded_amount' => Payment::whereBetween('payment_date', [$dateFrom, $dateTo])
                ->where('status', 'completed')
                ->where('amount', '<', 0)
                ->sum('amount'),
            'payment_methods' => Payment::whereBetween('payment_date', [$dateFrom, $dateTo])
                ->where('status', 'completed')
                ->selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total')
                ->groupBy('payment_method')
                ->get(),
            'daily_revenue' => Payment::whereBetween('payment_date', [$dateFrom, $dateTo])
                ->where('status', 'completed')
                ->where('amount', '>', 0)
                ->selectRaw('DATE(payment_date) as date, SUM(amount) as total')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get payment methods
     */
    public function methods()
    {
        $methods = [
            'cash' => 'Cash',
            'card' => 'Credit/Debit Card',
            'digital_wallet' => 'Digital Wallet',
            'bank_transfer' => 'Bank Transfer',
            'check' => 'Check',
        ];

        return response()->json([
            'success' => true,
            'data' => $methods
        ]);
    }
}