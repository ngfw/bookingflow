<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
class MobilePayment extends Component
{
    public $paymentMethod = 'card'; // card, digital_wallet, bank_transfer
    public $invoiceId = '';
    public $clientEmail = '';
    public $clientPhone = '';
    public $invoice = null;
    public $client = null;
    public $amount = 0;
    public $paymentSuccessful = false;
    public $paymentReference = '';
    public $transactionId = '';
    public $paymentDate = null;
    
    // Card payment fields
    public $cardNumber = '';
    public $cardExpiry = '';
    public $cardCvv = '';
    public $cardHolderName = '';
    
    // Digital wallet fields
    public $walletType = 'apple_pay'; // apple_pay, google_pay, paypal
    public $walletToken = '';
    
    // Bank transfer fields
    public $bankAccount = '';
    public $bankRouting = '';
    
    // Mobile-specific properties
    public $currentStep = 'search'; // search, payment, confirmation
    public $showPaymentModal = false;
    public $paymentProcessing = false;
    public $quickAmounts = [25, 50, 75, 100];
    public $customAmount = '';

    protected $rules = [
        'invoiceId' => 'required_if:currentStep,payment|exists:invoices,id',
        'clientEmail' => 'required_if:currentStep,search|email',
        'clientPhone' => 'required_if:currentStep,search|string|min:10',
        'paymentMethod' => 'required|in:card,digital_wallet,bank_transfer',
        'amount' => 'required|numeric|min:0.01',
    ];

    protected $cardRules = [
        'cardNumber' => 'required_if:paymentMethod,card|string|min:16|max:19',
        'cardExpiry' => 'required_if:paymentMethod,card|string|regex:/^\d{2}\/\d{2}$/',
        'cardCvv' => 'required_if:paymentMethod,card|string|min:3|max:4',
        'cardHolderName' => 'required_if:paymentMethod,card|string|max:255',
    ];

    protected $walletRules = [
        'walletType' => 'required_if:paymentMethod,digital_wallet|in:apple_pay,google_pay,paypal',
        'walletToken' => 'required_if:paymentMethod,digital_wallet|string',
    ];

    protected $bankRules = [
        'bankAccount' => 'required_if:paymentMethod,bank_transfer|string',
        'bankRouting' => 'required_if:paymentMethod,bank_transfer|string',
    ];

    public function mount()
    {
        $this->paymentDate = Carbon::now();
    }

    public function searchInvoice()
    {
        $this->validate([
            'clientEmail' => 'required|email',
            'clientPhone' => 'required|string|min:10',
        ]);

        // Find client by email or phone
        $client = Client::whereHas('user', function ($query) {
            $query->where('email', $this->clientEmail)
                  ->orWhere('phone', 'like', '%' . $this->clientPhone . '%');
        })->first();

        if (!$client) {
            session()->flash('error', 'Client not found. Please check your email and phone number.');
            return;
        }

        $this->client = $client;

        // Find unpaid invoices for this client
        $invoice = Invoice::where('client_id', $client->id)
            ->where('status', 'pending')
            ->where('total_amount', '>', 0)
            ->first();

        if (!$invoice) {
            session()->flash('error', 'No pending invoices found for this client.');
            return;
        }

        $this->invoice = $invoice;
        $this->invoiceId = $invoice->id;
        $this->amount = $invoice->total_amount;
        $this->currentStep = 'payment';
    }

    public function setPaymentMethod($method)
    {
        $this->paymentMethod = $method;
        $this->reset(['cardNumber', 'cardExpiry', 'cardCvv', 'cardHolderName', 'walletToken', 'bankAccount', 'bankRouting']);
    }

    public function setQuickAmount($amount)
    {
        $this->amount = $amount;
        $this->customAmount = '';
    }

    public function setCustomAmount()
    {
        if ($this->customAmount && is_numeric($this->customAmount)) {
            $this->amount = floatval($this->customAmount);
        }
    }

    public function updatedCustomAmount()
    {
        $this->setCustomAmount();
    }

    public function processPayment()
    {
        $this->paymentProcessing = true;

        try {
            // Validate payment details based on method
            if ($this->paymentMethod === 'card') {
                $this->validate($this->cardRules);
            } elseif ($this->paymentMethod === 'digital_wallet') {
                $this->validate($this->walletRules);
            } elseif ($this->paymentMethod === 'bank_transfer') {
                $this->validate($this->bankRules);
            }

            $this->validate([
                'invoiceId' => 'required|exists:invoices,id',
                'amount' => 'required|numeric|min:0.01',
            ]);

            DB::beginTransaction();

            // Generate payment reference
            $this->paymentReference = $this->generatePaymentReference();
            $this->transactionId = $this->generateTransactionId();

            // Create payment record
            $payment = Payment::create([
                'payment_number' => $this->paymentReference,
                'invoice_id' => $this->invoiceId,
                'client_id' => $this->client->id,
                'payment_method' => $this->paymentMethod,
                'amount' => $this->amount,
                'status' => 'completed',
                'payment_date' => $this->paymentDate,
                'reference_number' => $this->paymentReference,
                'transaction_id' => $this->transactionId,
                'payment_details' => $this->getPaymentDetails(),
            ]);

            // Update invoice status
            $this->invoice->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            // Update any associated appointment status
            if ($this->invoice->appointment) {
                $this->invoice->appointment->update([
                    'status' => 'completed',
                ]);
            }

            DB::commit();

            $this->paymentSuccessful = true;
            $this->currentStep = 'confirmation';
            $this->paymentProcessing = false;

            session()->flash('success', 'Payment processed successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            $this->paymentProcessing = false;
            session()->flash('error', 'Payment processing failed: ' . $e->getMessage());
        }
    }

    private function generatePaymentReference()
    {
        $prefix = 'PAY';
        $year = date('Y');
        $month = date('m');
        
        $lastPayment = Payment::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastPayment ? (intval(substr($lastPayment->payment_number, -4)) + 1) : 1;
        
        return $prefix . $year . $month . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    private function generateTransactionId()
    {
        return 'TXN' . time() . rand(1000, 9999);
    }

    private function getPaymentDetails()
    {
        $details = [
            'payment_method' => $this->paymentMethod,
            'processed_at' => now(),
            'mobile_payment' => true,
        ];

        if ($this->paymentMethod === 'card') {
            $details['card_last_four'] = substr(str_replace(' ', '', $this->cardNumber), -4);
            $details['card_expiry'] = $this->cardExpiry;
            $details['card_holder'] = $this->cardHolderName;
        } elseif ($this->paymentMethod === 'digital_wallet') {
            $details['wallet_type'] = $this->walletType;
        } elseif ($this->paymentMethod === 'bank_transfer') {
            $details['bank_account_last_four'] = substr($this->bankAccount, -4);
            $details['bank_routing'] = $this->bankRouting;
        }

        return $details;
    }

    public function newPayment()
    {
        $this->reset([
            'paymentMethod', 'invoiceId', 'clientEmail', 'clientPhone',
            'invoice', 'client', 'amount', 'paymentSuccessful', 'paymentReference',
            'transactionId', 'paymentDate', 'currentStep', 'showPaymentModal',
            'paymentProcessing', 'customAmount', 'cardNumber', 'cardExpiry',
            'cardCvv', 'cardHolderName', 'walletToken', 'bankAccount', 'bankRouting'
        ]);
        $this->paymentDate = Carbon::now();
    }

    public function render()
    {
        return view('livewire.public.mobile-payment');
    }
}

