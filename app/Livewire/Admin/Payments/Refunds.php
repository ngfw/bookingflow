<?php

namespace App\Livewire\Admin\Payments;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Payment;
use App\Models\Invoice;
use Carbon\Carbon;

class Refunds extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $dateFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingDateFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function processRefund($paymentId, $refundAmount = null, $reason = '')
    {
        $payment = Payment::findOrFail($paymentId);
        
        if ($payment->status !== 'completed') {
            session()->flash('error', 'Only completed payments can be refunded.');
            return;
        }

        $refundAmount = $refundAmount ?: $payment->amount;
        
        if ($refundAmount > $payment->amount) {
            session()->flash('error', 'Refund amount cannot exceed the original payment amount.');
            return;
        }

        // Create refund record (we'll use the same payment record but mark as refunded)
        $payment->update([
            'status' => 'refunded',
            'notes' => $payment->notes . "\n\nRefunded: " . $refundAmount . " - " . $reason,
        ]);

        // Update invoice balance
        $invoice = $payment->invoice;
        $totalPaid = $invoice->payments()->where('status', 'completed')->sum('amount');
        $invoice->update([
            'amount_paid' => $totalPaid,
            'balance_due' => $invoice->total_amount - $totalPaid,
            'status' => $totalPaid >= $invoice->total_amount ? 'paid' : 'sent',
        ]);

        session()->flash('success', 'Refund processed successfully.');
    }

    public function reverseRefund($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);
        
        if ($payment->status !== 'refunded') {
            session()->flash('error', 'Only refunded payments can be reversed.');
            return;
        }

        $payment->update(['status' => 'completed']);

        // Update invoice balance
        $invoice = $payment->invoice;
        $totalPaid = $invoice->payments()->where('status', 'completed')->sum('amount');
        $invoice->update([
            'amount_paid' => $totalPaid,
            'balance_due' => $invoice->total_amount - $totalPaid,
            'status' => $totalPaid >= $invoice->total_amount ? 'paid' : 'sent',
        ]);

        session()->flash('success', 'Refund reversed successfully.');
    }

    public function render()
    {
        $payments = Payment::with(['invoice.client.user', 'client.user', 'processedBy'])
            ->whereIn('status', ['refunded', 'completed'])
            ->when($this->search, function ($query) {
                $query->where('payment_number', 'like', '%' . $this->search . '%')
                      ->orWhere('reference_number', 'like', '%' . $this->search . '%')
                      ->orWhere('transaction_id', 'like', '%' . $this->search . '%')
                      ->orWhereHas('client.user', function ($q) {
                          $q->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%');
                      });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->dateFilter, function ($query) {
                if ($this->dateFilter === 'today') {
                    $query->whereDate('payment_date', Carbon::today());
                } elseif ($this->dateFilter === 'this_week') {
                    $query->whereBetween('payment_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                } elseif ($this->dateFilter === 'this_month') {
                    $query->whereMonth('payment_date', Carbon::now()->month)
                          ->whereYear('payment_date', Carbon::now()->year);
                } elseif ($this->dateFilter === 'last_30_days') {
                    $query->where('payment_date', '>=', Carbon::now()->subDays(30));
                }
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        // Calculate refund statistics
        $refundStats = [
            'total_refunded' => Payment::where('status', 'refunded')->sum('amount'),
            'refunds_count' => Payment::where('status', 'refunded')->count(),
            'completed_payments' => Payment::where('status', 'completed')->count(),
            'refund_rate' => Payment::where('status', 'completed')->count() > 0 
                ? (Payment::where('status', 'refunded')->count() / Payment::where('status', 'completed')->count()) * 100 
                : 0,
        ];

        return view('livewire.admin.payments.refunds', [
            'payments' => $payments,
            'refundStats' => $refundStats,
        ])->layout('layouts.admin');
    }
}
