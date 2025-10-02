<?php

namespace App\Livewire\Admin\Invoices;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Invoice;
use App\Models\Client;
use Carbon\Carbon;

class Index extends Component
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

    public function updateStatus($invoiceId, $status)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $updateData = ['status' => $status];
        
        if ($status === 'paid') {
            $updateData['paid_date'] = Carbon::now();
            $updateData['amount_paid'] = $invoice->total_amount;
            $updateData['balance_due'] = 0;
        }
        
        $invoice->update($updateData);
        session()->flash('success', 'Invoice status updated successfully.');
    }

    public function deleteInvoice($invoiceId)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        
        if ($invoice->status !== 'draft') {
            session()->flash('error', 'Only draft invoices can be deleted.');
            return;
        }
        
        $invoice->delete();
        session()->flash('success', 'Invoice deleted successfully.');
    }

    public function markOverdue()
    {
        $overdueInvoices = Invoice::where('status', 'sent')
            ->where('due_date', '<', Carbon::now())
            ->update(['status' => 'overdue']);
            
        session()->flash('success', "Marked {$overdueInvoices} invoices as overdue.");
    }

    public function render()
    {
        $invoices = Invoice::with(['client.user', 'appointment.service', 'items'])
            ->when($this->search, function ($query) {
                $query->where('invoice_number', 'like', '%' . $this->search . '%')
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
                    $query->whereDate('invoice_date', Carbon::today());
                } elseif ($this->dateFilter === 'this_week') {
                    $query->whereBetween('invoice_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                } elseif ($this->dateFilter === 'this_month') {
                    $query->whereMonth('invoice_date', Carbon::now()->month)
                          ->whereYear('invoice_date', Carbon::now()->year);
                } elseif ($this->dateFilter === 'overdue') {
                    $query->where('status', '!=', 'paid')
                          ->where('status', '!=', 'cancelled')
                          ->where('due_date', '<', Carbon::now());
                }
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.invoices.index', [
            'invoices' => $invoices,
        ])->layout('layouts.admin');
    }
}
