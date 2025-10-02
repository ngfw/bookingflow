<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CampaignUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'client_id',
        'appointment_id',
        'invoice_id',
        'usage_type',
        'original_amount',
        'discount_amount',
        'final_amount',
        'bonus_points_earned',
        'promo_code_used',
        'channel',
        'status',
        'notes',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'original_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'final_amount' => 'decimal:2',
            'metadata' => 'array',
        ];
    }

    public function campaign()
    {
        return $this->belongsTo(PromotionalCampaign::class, 'campaign_id');
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
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'refunded' => 'Refunded',
            default => 'Unknown'
        };
    }

    public function getUsageTypeDisplayAttribute()
    {
        return match($this->usage_type) {
            'appointment' => 'Appointment',
            'purchase' => 'Purchase',
            'referral' => 'Referral',
            'manual' => 'Manual',
            default => 'Unknown'
        };
    }
}