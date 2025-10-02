<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClientSpecialUsage extends Model
{
    use HasFactory;

    protected $table = 'client_special_usage';

    protected $fillable = [
        'special_id',
        'client_id',
        'appointment_id',
        'invoice_id',
        'event_type',
        'event_date',
        'special_date',
        'original_amount',
        'discount_amount',
        'final_amount',
        'bonus_points_earned',
        'status',
        'notes',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'special_date' => 'date',
            'original_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'final_amount' => 'decimal:2',
            'metadata' => 'array',
        ];
    }

    public function special()
    {
        return $this->belongsTo(BirthdayAnniversarySpecial::class, 'special_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function getStatusDisplayAttribute()
    {
        return match($this->status) {
            'used' => 'Used',
            'expired' => 'Expired',
            'cancelled' => 'Cancelled',
            default => 'Unknown'
        };
    }

    public function getEventTypeDisplayAttribute()
    {
        return match($this->event_type) {
            'birthday' => 'Birthday',
            'anniversary' => 'Anniversary',
            default => 'Unknown'
        };
    }

    public function getSavingsAttribute()
    {
        return $this->discount_amount ?? 0;
    }

    public function getTotalValueAttribute()
    {
        return ($this->discount_amount ?? 0) + ($this->bonus_points_earned ?? 0);
    }
}