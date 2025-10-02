<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StaffPerformance extends Model
{
    use HasFactory;

    protected $table = 'staff_performance';

    protected $fillable = [
        'staff_id',
        'performance_date',
        'appointments_completed',
        'appointments_cancelled',
        'appointments_no_show',
        'total_revenue',
        'commission_earned',
        'hours_worked',
        'overtime_hours',
        'client_satisfaction_rating',
        'products_used',
        'product_cost',
        'notes',
        'performance_metrics',
    ];

    protected function casts(): array
    {
        return [
            'performance_date' => 'date',
            'total_revenue' => 'decimal:2',
            'commission_earned' => 'decimal:2',
            'client_satisfaction_rating' => 'decimal:2',
            'product_cost' => 'decimal:2',
            'performance_metrics' => 'array',
        ];
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    // Helper methods for performance calculations
    public function getTotalAppointmentsAttribute()
    {
        return $this->appointments_completed + $this->appointments_cancelled + $this->appointments_no_show;
    }

    public function getCompletionRateAttribute()
    {
        $total = $this->total_appointments;
        return $total > 0 ? ($this->appointments_completed / $total) * 100 : 0;
    }

    public function getCancellationRateAttribute()
    {
        $total = $this->total_appointments;
        return $total > 0 ? ($this->appointments_cancelled / $total) * 100 : 0;
    }

    public function getNoShowRateAttribute()
    {
        $total = $this->total_appointments;
        return $total > 0 ? ($this->appointments_no_show / $total) * 100 : 0;
    }

    public function getRevenuePerHourAttribute()
    {
        $hours = $this->hours_worked / 60; // Convert minutes to hours
        return $hours > 0 ? $this->total_revenue / $hours : 0;
    }

    public function getRevenuePerAppointmentAttribute()
    {
        return $this->appointments_completed > 0 ? $this->total_revenue / $this->appointments_completed : 0;
    }

    public function getEfficiencyScoreAttribute()
    {
        // Calculate efficiency based on multiple factors
        $completionRate = $this->completion_rate;
        $satisfactionScore = $this->client_satisfaction_rating ? ($this->client_satisfaction_rating / 5) * 100 : 0;
        $revenueScore = min(100, ($this->revenue_per_hour / 50) * 100); // Assuming $50/hour is good
        
        return ($completionRate + $satisfactionScore + $revenueScore) / 3;
    }

    // Static methods for analytics
    public static function getStaffPerformanceSummary($staffId, $startDate, $endDate)
    {
        return self::where('staff_id', $staffId)
            ->whereBetween('performance_date', [$startDate, $endDate])
            ->selectRaw('
                SUM(appointments_completed) as total_completed,
                SUM(appointments_cancelled) as total_cancelled,
                SUM(appointments_no_show) as total_no_show,
                SUM(total_revenue) as total_revenue,
                SUM(commission_earned) as total_commission,
                SUM(hours_worked) as total_hours,
                AVG(client_satisfaction_rating) as avg_satisfaction,
                COUNT(*) as days_worked
            ')
            ->first();
    }

    public static function getTopPerformers($startDate, $endDate, $limit = 10)
    {
        return self::whereBetween('performance_date', [$startDate, $endDate])
            ->with('staff.user')
            ->selectRaw('
                staff_id,
                SUM(appointments_completed) as total_completed,
                SUM(total_revenue) as total_revenue,
                AVG(client_satisfaction_rating) as avg_satisfaction,
                SUM(hours_worked) as total_hours
            ')
            ->groupBy('staff_id')
            ->orderBy('total_revenue', 'desc')
            ->limit($limit)
            ->get();
    }
}