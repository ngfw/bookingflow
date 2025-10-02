<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class BirthdayAnniversarySpecial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'description',
        'status',
        'start_date',
        'end_date',
        'days_before',
        'days_after',
        'discount_settings',
        'bonus_points_settings',
        'free_service_settings',
        'gift_settings',
        'min_purchase_amount',
        'max_discount_amount',
        'usage_limit_per_client',
        'requires_appointment',
        'auto_apply',
        'notification_settings',
        'target_criteria',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'discount_settings' => 'array',
            'bonus_points_settings' => 'array',
            'free_service_settings' => 'array',
            'gift_settings' => 'array',
            'min_purchase_amount' => 'decimal:2',
            'max_discount_amount' => 'decimal:2',
            'requires_appointment' => 'boolean',
            'auto_apply' => 'boolean',
            'notification_settings' => 'array',
            'target_criteria' => 'array',
            'metadata' => 'array',
        ];
    }

    public function clientSpecialUsage()
    {
        return $this->hasMany(ClientSpecialUsage::class, 'special_id');
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_special_usage', 'special_id', 'client_id')
                    ->withPivot(['event_type', 'event_date', 'special_date', 'original_amount', 'discount_amount', 'final_amount', 'bonus_points_earned', 'status', 'notes', 'metadata'])
                    ->withTimestamps();
    }

    // Helper methods
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isInactive()
    {
        return $this->status === 'inactive';
    }

    public function isExpired()
    {
        return $this->status === 'expired';
    }

    public function isCurrentlyValid()
    {
        if (!$this->isActive()) {
            return false;
        }

        $now = now();
        
        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }
        
        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }

        return true;
    }

    public function canBeUsedByClient($clientId, $eventDate)
    {
        // Check if client has already used this special for this event
        $existingUsage = $this->clientSpecialUsage()
                             ->where('client_id', $clientId)
                             ->where('event_date', $eventDate)
                             ->where('status', 'used')
                             ->exists();
        
        if ($existingUsage) {
            return false;
        }

        // Check usage limit per client per year
        $yearlyUsage = $this->clientSpecialUsage()
                            ->where('client_id', $clientId)
                            ->whereYear('event_date', Carbon::parse($eventDate)->year)
                            ->where('status', 'used')
                            ->count();
        
        if ($yearlyUsage >= $this->usage_limit_per_client) {
            return false;
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
            'active' => 'Active',
            'inactive' => 'Inactive',
            'expired' => 'Expired',
            default => 'Unknown'
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'active' => 'green',
            'inactive' => 'gray',
            'expired' => 'red',
            default => 'gray'
        };
    }

    public function getTypeDisplayAttribute()
    {
        return match($this->type) {
            'birthday' => 'Birthday',
            'anniversary' => 'Anniversary',
            'both' => 'Birthday & Anniversary',
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

    public function getValidityWindowAttribute()
    {
        return "{$this->days_before} days before to {$this->days_after} days after";
    }

    // Static methods
    public static function getActiveSpecials()
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

    public static function getApplicableSpecials($clientId, $eventType, $eventDate)
    {
        $specials = self::getActiveSpecials();
        $applicable = [];

        foreach ($specials as $special) {
            if (!$special->isCurrentlyValid()) {
                continue;
            }

            if ($special->type !== 'both' && $special->type !== $eventType) {
                continue;
            }

            if (!$special->canBeUsedByClient($clientId, $eventDate)) {
                continue;
            }

            $applicable[] = $special;
        }

        return collect($applicable);
    }

    public static function getSpecialTypes()
    {
        return [
            'birthday' => 'Birthday Special',
            'anniversary' => 'Anniversary Special',
            'both' => 'Birthday & Anniversary Special',
        ];
    }

    public static function getStatuses()
    {
        return [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'expired' => 'Expired',
        ];
    }

    public static function getUpcomingEvents($days = 30)
    {
        $startDate = now();
        $endDate = now()->addDays($days);

        return Client::whereHas('user', function ($query) use ($startDate, $endDate) {
            $query->whereRaw('DAYOFYEAR(date_of_birth) BETWEEN ? AND ?', [
                $startDate->dayOfYear,
                $endDate->dayOfYear
            ]);
        })->with('user')->get();
    }

    public static function getSpecialStatistics($startDate = null, $endDate = null)
    {
        $query = self::query();
        
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return [
            'total_specials' => $query->count(),
            'active' => $query->where('status', 'active')->count(),
            'inactive' => $query->where('status', 'inactive')->count(),
            'expired' => $query->where('status', 'expired')->count(),
            'birthday_specials' => $query->where('type', 'birthday')->count(),
            'anniversary_specials' => $query->where('type', 'anniversary')->count(),
            'both_specials' => $query->where('type', 'both')->count(),
            'total_usage' => ClientSpecialUsage::whereHas('special', function ($q) use ($startDate, $endDate) {
                if ($startDate) $q->where('created_at', '>=', $startDate);
                if ($endDate) $q->where('created_at', '<=', $endDate);
            })->where('status', 'used')->count(),
            'total_discount_given' => ClientSpecialUsage::whereHas('special', function ($q) use ($startDate, $endDate) {
                if ($startDate) $q->where('created_at', '>=', $startDate);
                if ($endDate) $q->where('created_at', '<=', $endDate);
            })->where('status', 'used')->sum('discount_amount'),
            'total_bonus_points' => ClientSpecialUsage::whereHas('special', function ($q) use ($startDate, $endDate) {
                if ($startDate) $q->where('created_at', '>=', $startDate);
                if ($endDate) $q->where('created_at', '<=', $endDate);
            })->where('status', 'used')->sum('bonus_points_earned'),
        ];
    }

    public function activate()
    {
        $this->update(['status' => 'active']);
        return $this;
    }

    public function deactivate()
    {
        $this->update(['status' => 'inactive']);
        return $this;
    }

    public function expire()
    {
        $this->update(['status' => 'expired']);
        return $this;
    }

    public static function processExpiredSpecials()
    {
        $expiredSpecials = self::where('end_date', '<', now())
                              ->where('status', 'active')
                              ->get();

        foreach ($expiredSpecials as $special) {
            $special->expire();
        }

        return $expiredSpecials->count();
    }

    public static function processUpcomingEvents()
    {
        $upcomingEvents = self::getUpcomingEvents(7); // Next 7 days
        $processed = 0;

        foreach ($upcomingEvents as $client) {
            $eventDate = Carbon::parse($client->user->date_of_birth)->setYear(now()->year);
            
            // Check if it's within the validity window
            $specials = self::getApplicableSpecials($client->id, 'birthday', $eventDate);
            
            foreach ($specials as $special) {
                if ($special->auto_apply) {
                    // Auto-apply the special
                    ClientSpecialUsage::create([
                        'special_id' => $special->id,
                        'client_id' => $client->id,
                        'event_type' => 'birthday',
                        'event_date' => $eventDate,
                        'special_date' => now(),
                        'status' => 'used',
                        'metadata' => ['auto_applied' => true],
                    ]);
                    $processed++;
                }
            }
        }

        return $processed;
    }
}