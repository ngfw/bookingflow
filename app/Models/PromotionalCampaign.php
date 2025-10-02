<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PromotionalCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'status',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'days_of_week',
        'target_audience',
        'discount_settings',
        'bonus_points_settings',
        'free_service_settings',
        'package_deal_settings',
        'seasonal_settings',
        'referral_bonus_settings',
        'min_purchase_amount',
        'max_discount_amount',
        'usage_limit',
        'usage_limit_per_client',
        'current_usage',
        'is_automatic',
        'requires_code',
        'promo_code',
        'channels',
        'creative_assets',
        'tracking_settings',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
            'days_of_week' => 'array',
            'target_audience' => 'array',
            'discount_settings' => 'array',
            'bonus_points_settings' => 'array',
            'free_service_settings' => 'array',
            'package_deal_settings' => 'array',
            'seasonal_settings' => 'array',
            'referral_bonus_settings' => 'array',
            'min_purchase_amount' => 'decimal:2',
            'max_discount_amount' => 'decimal:2',
            'current_usage' => 'integer',
            'is_automatic' => 'boolean',
            'requires_code' => 'boolean',
            'channels' => 'array',
            'creative_assets' => 'array',
            'tracking_settings' => 'array',
            'metadata' => 'array',
        ];
    }

    public function campaignUsage()
    {
        return $this->hasMany(CampaignUsage::class, 'campaign_id');
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'campaign_usage', 'campaign_id', 'client_id')
                    ->withPivot(['usage_type', 'original_amount', 'discount_amount', 'final_amount', 'bonus_points_earned', 'promo_code_used', 'channel', 'status', 'notes', 'metadata'])
                    ->withTimestamps();
    }

    // Helper methods
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isDraft()
    {
        return $this->status === 'draft';
    }

    public function isPaused()
    {
        return $this->status === 'paused';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function isCurrentlyRunning()
    {
        if (!$this->isActive()) {
            return false;
        }

        $now = now();
        
        // Check date range
        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }
        
        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }

        // Check time range
        if ($this->start_time && $this->end_time) {
            $currentTime = $now->format('H:i');
            if ($currentTime < $this->start_time || $currentTime > $this->end_time) {
                return false;
            }
        }

        // Check days of week
        if ($this->days_of_week && !in_array($now->dayOfWeek, $this->days_of_week)) {
            return false;
        }

        return true;
    }

    public function hasReachedUsageLimit()
    {
        if (!$this->usage_limit) {
            return false;
        }
        
        return $this->current_usage >= $this->usage_limit;
    }

    public function canBeUsedByClient($clientId)
    {
        // Check if client has reached per-client usage limit
        if ($this->usage_limit_per_client) {
            $clientUsage = $this->campaignUsage()
                               ->where('client_id', $clientId)
                               ->where('status', 'completed')
                               ->count();
            
            if ($clientUsage >= $this->usage_limit_per_client) {
                return false;
            }
        }

        return true;
    }

    public function calculateDiscount($amount)
    {
        if (!$this->discount_settings) {
            return 0;
        }

        $discount = 0;
        $settings = $this->discount_settings;

        // Percentage discount
        if (isset($settings['percentage']) && $settings['percentage'] > 0) {
            $discount = ($amount * $settings['percentage']) / 100;
        }

        // Fixed amount discount
        if (isset($settings['fixed_amount']) && $settings['fixed_amount'] > 0) {
            $discount = max($discount, $settings['fixed_amount']);
        }

        // Apply maximum discount limit
        if ($this->max_discount_amount && $discount > $this->max_discount_amount) {
            $discount = $this->max_discount_amount;
        }

        return min($discount, $amount); // Don't exceed the total amount
    }

    public function calculateBonusPoints($basePoints)
    {
        if (!$this->bonus_points_settings) {
            return 0;
        }

        $settings = $this->bonus_points_settings;
        
        if (isset($settings['multiplier']) && $settings['multiplier'] > 1) {
            return $basePoints * ($settings['multiplier'] - 1); // Bonus points only
        }
        
        if (isset($settings['fixed_points']) && $settings['fixed_points'] > 0) {
            return $settings['fixed_points'];
        }

        return 0;
    }

    public function getStatusDisplayAttribute()
    {
        return match($this->status) {
            'draft' => 'Draft',
            'active' => 'Active',
            'paused' => 'Paused',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default => 'Unknown'
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'draft' => 'gray',
            'active' => 'green',
            'paused' => 'yellow',
            'completed' => 'blue',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    public function getTypeDisplayAttribute()
    {
        return match($this->type) {
            'discount' => 'Discount',
            'bonus_points' => 'Bonus Points',
            'free_service' => 'Free Service',
            'package_deal' => 'Package Deal',
            'seasonal' => 'Seasonal',
            'referral_bonus' => 'Referral Bonus',
            default => 'Unknown'
        };
    }

    public function getDurationAttribute()
    {
        if (!$this->end_date) {
            return 'Ongoing';
        }
        
        return $this->start_date->diffInDays($this->end_date) . ' days';
    }

    public function getUsagePercentageAttribute()
    {
        if (!$this->usage_limit) {
            return 0;
        }
        
        return round(($this->current_usage / $this->usage_limit) * 100, 2);
    }

    public function getRemainingUsageAttribute()
    {
        if (!$this->usage_limit) {
            return null;
        }
        
        return max(0, $this->usage_limit - $this->current_usage);
    }

    // Static methods
    public static function getActiveCampaigns()
    {
        return self::where('status', 'active')
                   ->where(function ($query) {
                       $query->whereNull('start_date')
                             ->orWhere('start_date', '<=', now());
                   })
                   ->where(function ($query) {
                       $query->whereNull('end_date')
                             ->orWhere('end_date', '>=', now());
                   })
                   ->get();
    }

    public static function getCampaignTypes()
    {
        return [
            'discount' => 'Discount Campaign',
            'bonus_points' => 'Bonus Points Campaign',
            'free_service' => 'Free Service Campaign',
            'package_deal' => 'Package Deal Campaign',
            'seasonal' => 'Seasonal Campaign',
            'referral_bonus' => 'Referral Bonus Campaign',
        ];
    }

    public static function getStatuses()
    {
        return [
            'draft' => 'Draft',
            'active' => 'Active',
            'paused' => 'Paused',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
    }

    public static function generatePromoCode($length = 8)
    {
        do {
            $code = strtoupper(Str::random($length));
        } while (self::where('promo_code', $code)->exists());

        return $code;
    }

    public function activate()
    {
        $this->update(['status' => 'active']);
        return $this;
    }

    public function pause()
    {
        $this->update(['status' => 'paused']);
        return $this;
    }

    public function complete()
    {
        $this->update(['status' => 'completed']);
        return $this;
    }

    public function cancel()
    {
        $this->update(['status' => 'cancelled']);
        return $this;
    }

    public function incrementUsage()
    {
        $this->increment('current_usage');
        return $this;
    }
}