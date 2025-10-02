<?php

namespace App\Livewire\Admin\Payments;

use Livewire\Component;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Client;
use Carbon\Carbon;

class Process extends Component
{
    public $invoiceId = '';
    public $clientId = '';
    public $paymentMethod = 'cash';
    public $amount = '';
    public $referenceNumber = '';
    public $transactionId = '';
    public $notes = '';
    public $paymentDate = '';
    public $paymentDetails = [];

    protected $rules = [
        'invoiceId' => 'required|exists:invoices,id',
        'clientId' => 'required|exists:clients,id',
        'paymentMethod' => 'required|in:cash,card,bank_transfer,digital_wallet,check,other',
        'amount' => 'required|numeric|min:0.01',
        'referenceNumber' => 'nullable|string|max:255',
        'transactionId' => 'nullable|string|max:255',
        'notes' => 'nullable|string|max:1000',
        'paymentDate' => 'required|date',
    ];

    public function mount()
    {
        $this->paymentDate = Carbon::now()->format('Y-m-d');
    }

    public function updatedInvoiceId()
    {
        if ($this->invoiceId) {
            $invoice = Invoice::with('client.user')->find($this->invoiceId);
            if ($invoice) {
                $this->clientId = $invoice->client_id;
                $this->amount = $invoice->balance_due;
            }
        }
    }

    public function processPayment()
    {
        $this->validate();

        $invoice = Invoice::findOrFail($this->invoiceId);
        
        // Check if payment amount exceeds balance due
        if ($this->amount > $invoice->balance_due) {
            session()->flash('error', 'Payment amount cannot exceed the balance due.');
            return;
        }

        // Create payment
        $payment = Payment::create([
            'payment_number' => (new Payment())->generatePaymentNumber(),
            'invoice_id' => $this->invoiceId,
            'client_id' => $this->clientId,
            'processed_by' => auth()->id(),
            'payment_method' => $this->paymentMethod,
            'amount' => $this->amount,
            'status' => 'completed',
            'payment_date' => $this->paymentDate,
            'notes' => $this->notes,
            'reference_number' => $this->referenceNumber,
            'transaction_id' => $this->transactionId,
            'payment_details' => $this->paymentDetails,
        ]);

        // Update invoice
        $totalPaid = $invoice->payments()->where('status', 'completed')->sum('amount');
        $invoice->update([
            'amount_paid' => $totalPaid,
            'balance_due' => $invoice->total_amount - $totalPaid,
            'status' => $totalPaid >= $invoice->total_amount ? 'paid' : 'sent',
            'paid_date' => $totalPaid >= $invoice->total_amount ? Carbon::now() : null,
        ]);

        session()->flash('success', 'Payment processed successfully!');
        return redirect()->route('admin.payments.index');
    }

    public function render()
    {
        $invoices = Invoice::with(['client.user', 'items'])
            ->where('status', '!=', 'paid')
            ->where('status', '!=', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->get();

        $clients = Client::with('user')->orderBy('created_at', 'desc')->get();

        return view('livewire.admin.payments.process', [
            'invoices' => $invoices,
            'clients' => $clients,
        ])->layout('layouts.admin');
    }
}
