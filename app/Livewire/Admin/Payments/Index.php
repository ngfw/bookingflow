<?php

namespace App\Livewire\Admin\Payments;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Payment;
use App\Models\Invoice;
use Carbon\Carbon;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $methodFilter = '';
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

    public function updatingMethodFilter()
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

    public function updateStatus($paymentId, $status)
    {
        $payment = Payment::findOrFail($paymentId);
        $payment->update(['status' => $status]);
        
        // Update invoice if payment is completed
        if ($status === 'completed') {
            $invoice = $payment->invoice;
            $totalPaid = $invoice->payments()->where('status', 'completed')->sum('amount');
            $invoice->update([
                'amount_paid' => $totalPaid,
                'balance_due' => $invoice->total_amount - $totalPaid,
                'status' => $totalPaid >= $invoice->total_amount ? 'paid' : 'sent'
            ]);
        }
        
        session()->flash('success', 'Payment status updated successfully.');
    }

    public function deletePayment($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);
        
        if ($payment->status !== 'pending') {
            session()->flash('error', 'Only pending payments can be deleted.');
            return;
        }
        
        $payment->delete();
        session()->flash('success', 'Payment deleted successfully.');
    }

    public function processRefund($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);
        
        if ($payment->status !== 'completed') {
            session()->flash('error', 'Only completed payments can be refunded.');
            return;
        }
        
        $payment->update(['status' => 'refunded']);
        
        // Update invoice balance
        $invoice = $payment->invoice;
        $totalPaid = $invoice->payments()->where('status', 'completed')->sum('amount');
        $invoice->update([
            'amount_paid' => $totalPaid,
            'balance_due' => $invoice->total_amount - $totalPaid,
            'status' => $totalPaid >= $invoice->total_amount ? 'paid' : 'sent'
        ]);
        
        session()->flash('success', 'Payment refunded successfully.');
    }

    public function render()
    {
        $payments = Payment::with(['invoice.client.user', 'client.user', 'processedBy'])
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
            ->when($this->methodFilter, function ($query) {
                $query->where('payment_method', $this->methodFilter);
            })
            ->when($this->dateFilter, function ($query) {
                if ($this->dateFilter === 'today') {
                    $query->whereDate('payment_date', Carbon::today());
                } elseif ($this->dateFilter === 'this_week') {
                    $query->whereBetween('payment_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                } elseif ($this->dateFilter === 'this_month') {
                    $query->whereMonth('payment_date', Carbon::now()->month)
                          ->whereYear('payment_date', Carbon::now()->year);
                }
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.payments.index', [
            'payments' => $payments,
        ])->layout('layouts.admin');
    }
}
