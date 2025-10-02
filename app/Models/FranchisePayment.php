<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FranchisePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'franchise_id',
        'payment_type',
        'amount',
        'due_date',
        'paid_date',
        'status',
        'payment_method',
        'transaction_reference',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'due_date' => 'date',
            'paid_date' => 'date',
        ];
    }

    public function franchise()
    {
        return $this->belongsTo(Franchise::class);
    }

    public function isOverdue()
    {
        return $this->status === 'overdue' || 
               ($this->status === 'pending' && $this->due_date < now());
    }

    public function markAsPaid($paymentMethod = null, $transactionReference = null)
    {
        $this->update([
            'status' => 'paid',
            'paid_date' => now(),
            'payment_method' => $paymentMethod ?? $this->payment_method,
            'transaction_reference' => $transactionReference ?? $this->transaction_reference,
        ]);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'overdue')
              ->orWhere(function ($q2) {
                  $q2->where('status', 'pending')
                     ->where('due_date', '<', now());
              });
        });
    }

    public function scopeByType($query, $type)
    {
        return $query->where('payment_type', $type);
    }
}