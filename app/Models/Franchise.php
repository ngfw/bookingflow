<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Franchise extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'franchise_code',
        'description',
        'franchise_type',
        'status',
        'owner_name',
        'owner_email',
        'owner_phone',
        'owner_address',
        'owner_city',
        'owner_state',
        'owner_postal_code',
        'owner_country',
        'agreement_start_date',
        'agreement_end_date',
        'initial_franchise_fee',
        'royalty_rate',
        'marketing_fee_rate',
        'technology_fee_rate',
        'payment_frequency',
        'next_payment_due',
        'territory_boundaries',
        'territory_description',
        'max_locations_allowed',
        'current_locations_count',
        'monthly_sales_target',
        'yearly_sales_target',
        'current_month_sales',
        'current_year_sales',
        'current_month_appointments',
        'current_year_appointments',
        'required_training',
        'completed_training',
        'last_compliance_check',
        'next_compliance_check',
        'compliance_notes',
        'assigned_manager',
        'support_level',
        'communication_preferences',
        'support_notes',
        'credit_limit',
        'outstanding_balance',
        'payment_method',
        'banking_info',
        'tax_id',
        'approved_marketing_materials',
        'brand_guidelines_compliance',
        'local_marketing_approved',
        'local_marketing_budget',
        'operational_standards',
        'quality_metrics',
        'customer_satisfaction_target',
        'current_satisfaction_score',
        'audit_history',
        'performance_history',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'agreement_start_date' => 'date',
            'agreement_end_date' => 'date',
            'initial_franchise_fee' => 'decimal:2',
            'royalty_rate' => 'decimal:4',
            'marketing_fee_rate' => 'decimal:4',
            'technology_fee_rate' => 'decimal:4',
            'next_payment_due' => 'date',
            'territory_boundaries' => 'array',
            'monthly_sales_target' => 'decimal:2',
            'yearly_sales_target' => 'decimal:2',
            'current_month_sales' => 'decimal:2',
            'current_year_sales' => 'decimal:2',
            'required_training' => 'array',
            'completed_training' => 'array',
            'last_compliance_check' => 'date',
            'next_compliance_check' => 'date',
            'communication_preferences' => 'array',
            'credit_limit' => 'decimal:2',
            'outstanding_balance' => 'decimal:2',
            'banking_info' => 'array',
            'approved_marketing_materials' => 'array',
            'brand_guidelines_compliance' => 'array',
            'local_marketing_budget' => 'decimal:2',
            'operational_standards' => 'array',
            'quality_metrics' => 'array',
            'customer_satisfaction_target' => 'decimal:2',
            'current_satisfaction_score' => 'decimal:2',
            'audit_history' => 'array',
            'performance_history' => 'array',
        ];
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function payments()
    {
        return $this->hasMany(FranchisePayment::class);
    }

    public function metrics()
    {
        return $this->hasMany(FranchiseMetric::class);
    }

    public function communications()
    {
        return $this->hasMany(FranchiseCommunication::class);
    }

    public function getOwnerFullAddressAttribute()
    {
        return "{$this->owner_address}, {$this->owner_city}, {$this->owner_state} {$this->owner_postal_code}, {$this->owner_country}";
    }

    public function getAgreementDurationAttribute()
    {
        if (!$this->agreement_start_date || !$this->agreement_end_date) {
            return null;
        }

        return $this->agreement_start_date->diffInYears($this->agreement_end_date);
    }

    public function getAgreementDaysRemainingAttribute()
    {
        if (!$this->agreement_end_date) {
            return null;
        }

        $days = now()->diffInDays($this->agreement_end_date, false);
        return $days > 0 ? $days : 0;
    }

    public function isAgreementExpiringSoon($days = 90)
    {
        if (!$this->agreement_end_date) {
            return false;
        }

        return now()->addDays($days)->greaterThanOrEqualTo($this->agreement_end_date);
    }

    public function getTotalFeesDue()
    {
        return $this->payments()
            ->where('status', 'pending')
            ->where('due_date', '<=', now())
            ->sum('amount');
    }

    public function getOverdueAmount()
    {
        return $this->payments()
            ->where('status', 'overdue')
            ->sum('amount');
    }

    public function getMonthlyPerformance($month = null, $year = null)
    {
        $date = $month && $year ? Carbon::create($year, $month) : now();
        
        return [
            'sales' => $this->current_month_sales,
            'appointments' => $this->current_month_appointments,
            'target_sales' => $this->monthly_sales_target,
            'performance_percentage' => $this->monthly_sales_target > 0 
                ? ($this->current_month_sales / $this->monthly_sales_target) * 100 
                : 0,
        ];
    }

    public function getYearlyPerformance($year = null)
    {
        $year = $year ?? now()->year;
        
        return [
            'sales' => $this->current_year_sales,
            'appointments' => $this->current_year_appointments,
            'target_sales' => $this->yearly_sales_target,
            'performance_percentage' => $this->yearly_sales_target > 0 
                ? ($this->current_year_sales / $this->yearly_sales_target) * 100 
                : 0,
        ];
    }

    public function getComplianceStatus()
    {
        if (!$this->next_compliance_check) {
            return 'unknown';
        }

        $daysUntilCheck = now()->diffInDays($this->next_compliance_check, false);
        
        if ($daysUntilCheck < 0) {
            return 'overdue';
        } elseif ($daysUntilCheck <= 30) {
            return 'due_soon';
        } else {
            return 'compliant';
        }
    }

    public function getTrainingCompletionPercentage()
    {
        if (!$this->required_training || !$this->completed_training) {
            return 0;
        }

        $required = count($this->required_training);
        $completed = count($this->completed_training);
        
        return $required > 0 ? ($completed / $required) * 100 : 0;
    }

    public function getActiveLocationsCount()
    {
        return $this->locations()->where('is_active', true)->count();
    }

    public function getActiveStaffCount()
    {
        return $this->users()
            ->whereHas('staff')
            ->where('is_active', true)
            ->count();
    }

    public function getTotalRevenue($period = 'month')
    {
        $query = $this->payments()->where('status', 'paid');
        
        if ($period === 'month') {
            $query->whereMonth('paid_date', now()->month)
                  ->whereYear('paid_date', now()->year);
        } elseif ($period === 'year') {
            $query->whereYear('paid_date', now()->year);
        }
        
        return $query->sum('amount');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    public function scopeOwned($query)
    {
        return $query->where('franchise_type', 'owned');
    }

    public function scopeFranchisee($query)
    {
        return $query->where('franchise_type', 'franchisee');
    }

    public function scopeCorporate($query)
    {
        return $query->where('franchise_type', 'corporate');
    }

    public function scopeOverdue($query)
    {
        return $query->whereHas('payments', function ($q) {
            $q->where('status', 'overdue');
        });
    }

    public function scopeExpiringSoon($query, $days = 90)
    {
        return $query->where('agreement_end_date', '<=', now()->addDays($days))
                     ->where('agreement_end_date', '>', now());
    }
}