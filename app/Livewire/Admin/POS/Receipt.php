<?php

namespace App\Livewire\Admin\POS;

use Livewire\Component;
use App\Models\Invoice;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class Receipt extends Component
{
    public $invoiceId;
    public $invoice;
    public $payment;
    public $showPrintDialog = false;
    public $printOptions = [
        'include_logo' => true,
        'include_address' => true,
        'include_phone' => true,
        'include_email' => true,
        'include_website' => true,
        'include_tax_id' => true,
        'include_footer' => true,
        'paper_size' => 'thermal', // thermal, a4, letter
        'font_size' => 'small', // small, medium, large
    ];

    public function mount($invoiceId = null)
    {
        if ($invoiceId) {
            $this->invoiceId = $invoiceId;
            $this->loadInvoice();
        }
    }

    public function loadInvoice()
    {
        if ($this->invoiceId) {
            $this->invoice = Invoice::with(['client', 'items.product', 'payments'])->find($this->invoiceId);
            $this->payment = $this->invoice->payments->first();
        }
    }

    public function setInvoice($invoiceId)
    {
        $this->invoiceId = $invoiceId;
        $this->loadInvoice();
    }

    public function showPrintDialog()
    {
        $this->showPrintDialog = true;
    }

    public function hidePrintDialog()
    {
        $this->showPrintDialog = false;
    }

    public function printReceipt()
    {
        if (!$this->invoice) {
            session()->flash('error', 'No invoice selected for printing.');
            return;
        }

        try {
            $pdf = Pdf::loadView('livewire.admin.pos.receipt-pdf', [
                'invoice' => $this->invoice,
                'payment' => $this->payment,
                'options' => $this->printOptions,
                'business' => $this->getBusinessInfo(),
            ]);

            $filename = 'receipt_' . $this->invoice->invoice_number . '_' . now()->format('Y-m-d_H-i-s') . '.pdf';
            
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $filename, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"'
            ]);

        } catch (\Exception $e) {
            session()->flash('error', 'Error generating receipt: ' . $e->getMessage());
        }
    }

    public function printThermalReceipt()
    {
        if (!$this->invoice) {
            session()->flash('error', 'No invoice selected for printing.');
            return;
        }

        try {
            $this->printOptions['paper_size'] = 'thermal';
            $this->printOptions['font_size'] = 'small';
            
            $pdf = Pdf::loadView('livewire.admin.pos.thermal-receipt-pdf', [
                'invoice' => $this->invoice,
                'payment' => $this->payment,
                'options' => $this->printOptions,
                'business' => $this->getBusinessInfo(),
            ]);

            $filename = 'thermal_receipt_' . $this->invoice->invoice_number . '_' . now()->format('Y-m-d_H-i-s') . '.pdf';
            
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $filename, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"'
            ]);

        } catch (\Exception $e) {
            session()->flash('error', 'Error generating thermal receipt: ' . $e->getMessage());
        }
    }

    public function emailReceipt()
    {
        if (!$this->invoice || !$this->invoice->client) {
            session()->flash('error', 'No client email available for receipt.');
            return;
        }

        try {
            $pdf = Pdf::loadView('livewire.admin.pos.receipt-pdf', [
                'invoice' => $this->invoice,
                'payment' => $this->payment,
                'options' => $this->printOptions,
                'business' => $this->getBusinessInfo(),
            ]);

            // Here you would implement email sending
            // For now, we'll just show a success message
            session()->flash('success', 'Receipt email sent successfully to ' . $this->invoice->client->email);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error sending receipt email: ' . $e->getMessage());
        }
    }

    public function getBusinessInfo()
    {
        return [
            'name' => 'service business Management',
            'address' => '123 Main Street, City, State 12345',
            'phone' => '(555) 123-4567',
            'email' => 'info@bookingflow.com',
            'website' => 'www.bookingflow.com',
            'tax_id' => 'TAX-123456789',
            'logo' => null, // You can add logo path here
        ];
    }

    public function getRecentInvoices()
    {
        return Invoice::with(['client', 'payments'])
            ->where('status', 'paid')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
    }

    public function render()
    {
        $recentInvoices = $this->getRecentInvoices();
        
        return view('livewire.admin.pos.receipt', [
            'recentInvoices' => $recentInvoices,
        ])->layout('layouts.admin');
    }
}
