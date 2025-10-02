<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StaffCommissionSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'service_id',
        'commission_type',
        'commission_rate',
        'fixed_amount',
        'tiered_rates',
        'minimum_threshold',
        'maximum_cap',
        'calculation_basis',
        'payment_frequency',
        'is_active',
        'effective_date',
        'expiry_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'commission_rate' => 'decimal:2',
            'fixed_amount' => 'decimal:2',
            'tiered_rates' => 'array',
            'minimum_threshold' => 'decimal:2',
            'maximum_cap' => 'decimal:2',
            'is_active' => 'boolean',
            'effective_date' => 'date',
            'expiry_date' => 'date',
        ];
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    // Helper methods for commission calculations
    public function calculateCommission($amount, $appointments = 1)
    {
        if (!$this->is_active) {
            return 0;
        }

        // Check if setting is within effective date range
        $now = now();
        if ($now->lt($this->effective_date) || 
            ($this->expiry_date && $now->gt($this->expiry_date))) {
            return 0;
        }

        // Check minimum threshold
        if ($amount < $this->minimum_threshold) {
            return 0;
        }

        $commission = 0;

        switch ($this->commission_type) {
            case 'percentage':
                $commission = $amount * ($this->commission_rate / 100);
                break;
                
            case 'fixed':
                $commission = $this->fixed_amount * $appointments;
                break;
                
            case 'tiered':
                $commission = $this->calculateTieredCommission($amount);
                break;
        }

        // Apply maximum cap if set
        if ($this->maximum_cap && $commission > $this->maximum_cap) {
            $commission = $this->maximum_cap;
        }

        return round($commission, 2);
    }

    private function calculateTieredCommission($amount)
    {
        if (!$this->tiered_rates) {
            return 0;
        }

        $commission = 0;
        $remainingAmount = $amount;

        foreach ($this->tiered_rates as $tier) {
            $tierMin = $tier['min'] ?? 0;
            $tierMax = $tier['max'] ?? PHP_FLOAT_MAX;
            $tierRate = $tier['rate'] ?? 0;

            if ($remainingAmount <= 0) {
                break;
            }

            $tierAmount = min($remainingAmount, $tierMax - $tierMin);
            if ($tierAmount > 0) {
                $commission += $tierAmount * ($tierRate / 100);
                $remainingAmount -= $tierAmount;
            }
        }

        return $commission;
    }

    public function getCommissionTypeDisplayAttribute()
    {
        return match($this->commission_type) {
            'percentage' => 'Percentage',
            'fixed' => 'Fixed Amount',
            'tiered' => 'Tiered Structure',
            default => 'Unknown'
        };
    }

    public function getPaymentFrequencyDisplayAttribute()
    {
        return match($this->payment_frequency) {
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'bi_weekly' => 'Bi-Weekly',
            'monthly' => 'Monthly',
            default => 'Unknown'
        };
    }

    public function getCalculationBasisDisplayAttribute()
    {
        return match($this->calculation_basis) {
            'revenue' => 'Revenue',
            'profit' => 'Profit',
            'appointments' => 'Appointments',
            default => 'Unknown'
        };
    }

    // Static methods for commission management
    public static function getActiveSettingsForStaff($staffId)
    {
        return self::where('staff_id', $staffId)
            ->where('is_active', true)
            ->where('effective_date', '<=', now())
            ->where(function($query) {
                $query->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>=', now());
            })
            ->get();
    }

    public static function getDefaultCommissionRate($staffId, $serviceId = null)
    {
        $setting = self::where('staff_id', $staffId)
            ->where(function($query) use ($serviceId) {
                if ($serviceId) {
                    $query->where('service_id', $serviceId)
                          ->orWhereNull('service_id');
                } else {
                    $query->whereNull('service_id');
                }
            })
            ->where('is_active', true)
            ->where('effective_date', '<=', now())
            ->where(function($query) {
                $query->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>=', now());
            })
            ->orderBy('service_id', 'desc') // Prefer service-specific over general
            ->first();

        return $setting ? $setting->commission_rate : 0;
    }

    public function isCurrentlyActive()
    {
        $now = now();
        return $this->is_active && 
               $now->gte($this->effective_date) && 
               (!$this->expiry_date || $now->lte($this->expiry_date));
    }
}