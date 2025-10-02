<?php

namespace App\Livewire\Admin\Payments;

use Livewire\Component;
use App\Models\Payment;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class Receipt extends Component
{
    public $paymentId = '';
    public $payment = null;
    public $invoice = null;
    public $client = null;

    public function mount($paymentId = null)
    {
        if ($paymentId) {
            $this->paymentId = $paymentId;
            $this->loadPayment();
        }
    }

    public function loadPayment()
    {
        if ($this->paymentId) {
            $this->payment = Payment::with(['invoice.client.user', 'invoice.items', 'client.user', 'processedBy'])
                ->find($this->paymentId);
            
            if ($this->payment) {
                $this->invoice = $this->payment->invoice;
                $this->client = $this->payment->client;
            }
        }
    }

    public function updatedPaymentId()
    {
        $this->loadPayment();
    }

    public function generatePDF()
    {
        if (!$this->payment) {
            session()->flash('error', 'Please select a payment first.');
            return;
        }

        $data = [
            'payment' => $this->payment,
            'invoice' => $this->invoice,
            'client' => $this->client,
            'salon' => [
                'name' => 'Beauty Salon Management',
                'address' => '123 Beauty Street, City, State 12345',
                'phone' => '(555) 123-4567',
                'email' => 'info@beautysalon.com',
                'website' => 'www.beautysalon.com',
            ]
        ];

        $pdf = Pdf::loadView('livewire.admin.payments.receipt-pdf', $data);
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'receipt-' . $this->payment->payment_number . '.pdf');
    }

    public function printReceipt()
    {
        if (!$this->payment) {
            session()->flash('error', 'Please select a payment first.');
            return;
        }

        $this->dispatch('print-receipt');
    }

    public function render()
    {
        $payments = Payment::with(['invoice.client.user', 'client.user'])
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.admin.payments.receipt', [
            'payments' => $payments,
        ])->layout('layouts.admin');
    }
}
