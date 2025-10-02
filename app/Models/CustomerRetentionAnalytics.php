<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class CustomerRetentionAnalytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'analysis_date',
        'period_type',
        'period_start',
        'period_end',
        'total_appointments',
        'completed_appointments',
        'cancelled_appointments',
        'no_show_appointments',
        'total_revenue',
        'average_appointment_value',
        'days_since_last_visit',
        'days_since_first_visit',
        'visit_frequency',
        'retention_score',
        'retention_status',
        'engagement_metrics',
        'loyalty_metrics',
        'satisfaction_metrics',
        'predictive_metrics',
        'recommendations',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'analysis_date' => 'date',
            'period_start' => 'date',
            'period_end' => 'date',
            'total_revenue' => 'decimal:2',
            'average_appointment_value' => 'decimal:2',
            'retention_score' => 'decimal:2',
            'engagement_metrics' => 'array',
            'loyalty_metrics' => 'array',
            'satisfaction_metrics' => 'array',
            'predictive_metrics' => 'array',
            'recommendations' => 'array',
            'metadata' => 'array',
        ];
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function retentionInsights()
    {
        return $this->hasMany(RetentionInsight::class, 'client_id', 'client_id');
    }

    // Helper methods
    public function getRetentionStatusDisplayAttribute()
    {
        return match($this->retention_status) {
            'active' => 'Active',
            'at_risk' => 'At Risk',
            'inactive' => 'Inactive',
            'churned' => 'Churned',
            default => 'Unknown'
        };
    }

    public function getRetentionStatusColorAttribute()
    {
        return match($this->retention_status) {
            'active' => 'green',
            'at_risk' => 'yellow',
            'inactive' => 'orange',
            'churned' => 'red',
            default => 'gray'
        };
    }

    public function getPeriodTypeDisplayAttribute()
    {
        return match($this->period_type) {
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'yearly' => 'Yearly',
            default => 'Unknown'
        };
    }

    public function getCompletionRateAttribute()
    {
        if ($this->total_appointments == 0) {
            return 0;
        }
        
        return round(($this->completed_appointments / $this->total_appointments) * 100, 2);
    }

    public function getCancellationRateAttribute()
    {
        if ($this->total_appointments == 0) {
            return 0;
        }
        
        return round(($this->cancelled_appointments / $this->total_appointments) * 100, 2);
    }

    public function getNoShowRateAttribute()
    {
        if ($this->total_appointments == 0) {
            return 0;
        }
        
        return round(($this->no_show_appointments / $this->total_appointments) * 100, 2);
    }

    public function getEngagementLevelAttribute()
    {
        $score = $this->retention_score;
        
        if ($score >= 80) return 'High';
        if ($score >= 60) return 'Medium';
        if ($score >= 40) return 'Low';
        return 'Very Low';
    }

    public function getRiskLevelAttribute()
    {
        $daysSinceLastVisit = $this->days_since_last_visit;
        
        if ($daysSinceLastVisit <= 30) return 'Low';
        if ($daysSinceLastVisit <= 60) return 'Medium';
        if ($daysSinceLastVisit <= 90) return 'High';
        return 'Critical';
    }

    // Static methods
    public static function calculateRetentionScore($clientId, $periodStart, $periodEnd)
    {
        $client = Client::find($clientId);
        if (!$client) return 0;

        $appointments = Appointment::where('client_id', $clientId)
                                  ->whereBetween('appointment_date', [$periodStart, $periodEnd])
                                  ->get();

        $totalAppointments = $appointments->count();
        $completedAppointments = $appointments->where('status', 'completed')->count();
        $cancelledAppointments = $appointments->where('status', 'cancelled')->count();
        $noShowAppointments = $appointments->where('status', 'no_show')->count();

        if ($totalAppointments == 0) return 0;

        // Calculate base score from completion rate
        $completionRate = ($completedAppointments / $totalAppointments) * 100;
        
        // Penalize cancellations and no-shows
        $cancellationPenalty = ($cancelledAppointments / $totalAppointments) * 20;
        $noShowPenalty = ($noShowAppointments / $totalAppointments) * 30;
        
        // Calculate frequency bonus
        $daysInPeriod = Carbon::parse($periodStart)->diffInDays(Carbon::parse($periodEnd));
        $frequencyBonus = min(($totalAppointments / max($daysInPeriod / 30, 1)) * 10, 20);
        
        $score = $completionRate - $cancellationPenalty - $noShowPenalty + $frequencyBonus;
        
        return max(0, min(100, round($score, 2)));
    }

    public static function determineRetentionStatus($retentionScore, $daysSinceLastVisit)
    {
        if ($retentionScore >= 80 && $daysSinceLastVisit <= 30) {
            return 'active';
        } elseif ($retentionScore >= 60 && $daysSinceLastVisit <= 60) {
            return 'at_risk';
        } elseif ($retentionScore >= 40 && $daysSinceLastVisit <= 90) {
            return 'inactive';
        } else {
            return 'churned';
        }
    }

    public static function getRetentionStatistics($startDate = null, $endDate = null)
    {
        $query = self::query();
        
        if ($startDate) {
            $query->where('analysis_date', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('analysis_date', '<=', $endDate);
        }

        return [
            'total_clients_analyzed' => $query->count(),
            'active_clients' => $query->where('retention_status', 'active')->count(),
            'at_risk_clients' => $query->where('retention_status', 'at_risk')->count(),
            'inactive_clients' => $query->where('retention_status', 'inactive')->count(),
            'churned_clients' => $query->where('retention_status', 'churned')->count(),
            'average_retention_score' => $query->avg('retention_score'),
            'average_visit_frequency' => $query->avg('visit_frequency'),
            'total_revenue' => $query->sum('total_revenue'),
            'average_appointment_value' => $query->avg('average_appointment_value'),
        ];
    }

    public static function getAtRiskClients($limit = 50)
    {
        return self::where('retention_status', 'at_risk')
                   ->orderBy('retention_score', 'asc')
                   ->limit($limit)
                   ->with('client')
                   ->get();
    }

    public static function getChurnedClients($limit = 50)
    {
        return self::where('retention_status', 'churned')
                   ->orderBy('days_since_last_visit', 'desc')
                   ->limit($limit)
                   ->with('client')
                   ->get();
    }

    public static function getTopPerformers($limit = 50)
    {
        return self::where('retention_status', 'active')
                   ->orderBy('retention_score', 'desc')
                   ->limit($limit)
                   ->with('client')
                   ->get();
    }
}