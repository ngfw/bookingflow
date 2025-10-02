<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RetentionInsight extends Model
{
    use HasFactory;

    protected $fillable = [
        'insight_type',
        'title',
        'description',
        'category',
        'priority',
        'status',
        'client_id',
        'data',
        'recommendations',
        'targets',
        'insight_date',
        'expiry_date',
        'is_automated',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'insight_date' => 'date',
            'expiry_date' => 'date',
            'data' => 'array',
            'recommendations' => 'array',
            'targets' => 'array',
            'is_automated' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function getPriorityDisplayAttribute()
    {
        return match($this->priority) {
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'critical' => 'Critical',
            default => 'Unknown'
        };
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'low' => 'gray',
            'medium' => 'blue',
            'high' => 'yellow',
            'critical' => 'red',
            default => 'gray'
        };
    }

    public function getStatusDisplayAttribute()
    {
        return match($this->status) {
            'active' => 'Active',
            'dismissed' => 'Dismissed',
            'implemented' => 'Implemented',
            'expired' => 'Expired',
            default => 'Unknown'
        };
    }

    public function getCategoryDisplayAttribute()
    {
        return match($this->category) {
            'retention' => 'Retention',
            'engagement' => 'Engagement',
            'loyalty' => 'Loyalty',
            'satisfaction' => 'Satisfaction',
            'revenue' => 'Revenue',
            default => 'Unknown'
        };
    }

    public function getInsightTypeDisplayAttribute()
    {
        return match($this->insight_type) {
            'client_insight' => 'Client Insight',
            'cohort_analysis' => 'Cohort Analysis',
            'trend_analysis' => 'Trend Analysis',
            'prediction' => 'Prediction',
            'recommendation' => 'Recommendation',
            default => 'Unknown'
        };
    }

    public function isExpired()
    {
        return $this->expiry_date && now()->gt($this->expiry_date);
    }

    public function isActive()
    {
        return $this->status === 'active' && !$this->isExpired();
    }

    public function dismiss()
    {
        $this->update(['status' => 'dismissed']);
        return $this;
    }

    public function implement()
    {
        $this->update(['status' => 'implemented']);
        return $this;
    }

    public function expire()
    {
        $this->update(['status' => 'expired']);
        return $this;
    }

    // Static methods
    public static function getInsightTypes()
    {
        return [
            'client_insight' => 'Client Insight',
            'cohort_analysis' => 'Cohort Analysis',
            'trend_analysis' => 'Trend Analysis',
            'prediction' => 'Prediction',
            'recommendation' => 'Recommendation',
        ];
    }

    public static function getCategories()
    {
        return [
            'retention' => 'Retention',
            'engagement' => 'Engagement',
            'loyalty' => 'Loyalty',
            'satisfaction' => 'Satisfaction',
            'revenue' => 'Revenue',
        ];
    }

    public static function getPriorities()
    {
        return [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'critical' => 'Critical',
        ];
    }

    public static function getStatuses()
    {
        return [
            'active' => 'Active',
            'dismissed' => 'Dismissed',
            'implemented' => 'Implemented',
            'expired' => 'Expired',
        ];
    }

    public static function getActiveInsights($limit = 50)
    {
        return self::where('status', 'active')
                   ->where(function ($query) {
                       $query->whereNull('expiry_date')
                             ->orWhere('expiry_date', '>', now());
                   })
                   ->orderBy('priority', 'desc')
                   ->orderBy('created_at', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getInsightsByCategory($category, $limit = 50)
    {
        return self::where('category', $category)
                   ->where('status', 'active')
                   ->orderBy('priority', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function getCriticalInsights($limit = 20)
    {
        return self::where('priority', 'critical')
                   ->where('status', 'active')
                   ->orderBy('created_at', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public static function processExpiredInsights()
    {
        $expiredInsights = self::where('expiry_date', '<', now())
                              ->where('status', 'active')
                              ->get();

        foreach ($expiredInsights as $insight) {
            $insight->expire();
        }

        return $expiredInsights->count();
    }
}