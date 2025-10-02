<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'staff_id',
        'service_id',
        'appointment_number',
        'appointment_date',
        'end_time',
        'status',
        'booking_source',
        'service_price',
        'deposit_paid',
        'client_notes',
        'staff_notes',
        'cancellation_reason',
        'cancelled_at',
        'is_recurring',
        'recurring_pattern',
        'recurring_end_date',
        'reminder_sent',
        'reminder_sent_at',
        'follow_up_required',
    ];

    protected function casts(): array
    {
        return [
            'appointment_date' => 'datetime',
            'end_time' => 'datetime',
            'service_price' => 'decimal:2',
            'deposit_paid' => 'decimal:2',
            'cancelled_at' => 'datetime',
            'is_recurring' => 'boolean',
            'recurring_end_date' => 'date',
            'reminder_sent' => 'boolean',
            'reminder_sent_at' => 'datetime',
            'follow_up_required' => 'boolean',
        ];
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function productUsage()
    {
        return $this->hasMany(ProductUsage::class);
    }

    public function loyaltyTransactions()
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }
}
