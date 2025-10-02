<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccessLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_type',
        'permission',
        'resource',
        'result',
        'ip_address',
        'user_agent',
        'url',
        'method',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get event type display
     */
    public function getEventTypeDisplayAttribute()
    {
        return match($this->event_type) {
            'permission_granted' => 'Permission Granted',
            'permission_revoked' => 'Permission Revoked',
            'access_granted' => 'Access Granted',
            'access_denied' => 'Access Denied',
            'permission_escalation' => 'Permission Escalation',
            'resource_access' => 'Resource Access',
            'data_access' => 'Data Access',
            'location_access' => 'Location Access',
            'franchise_access' => 'Franchise Access',
            default => ucfirst(str_replace('_', ' ', $this->event_type)),
        };
    }

    /**
     * Get result display
     */
    public function getResultDisplayAttribute()
    {
        return match($this->result) {
            'granted' => 'Granted',
            'denied' => 'Denied',
            'attempted' => 'Attempted',
            default => ucfirst($this->result),
        };
    }

    /**
     * Get result color
     */
    public function getResultColorAttribute()
    {
        return match($this->result) {
            'granted' => 'green',
            'denied' => 'red',
            'attempted' => 'yellow',
            default => 'gray',
        };
    }

    /**
     * Get time ago display
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Scope for specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for specific event type
     */
    public function scopeEventType($query, $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    /**
     * Scope for specific permission
     */
    public function scopePermission($query, $permission)
    {
        return $query->where('permission', $permission);
    }

    /**
     * Scope for specific resource
     */
    public function scopeResource($query, $resource)
    {
        return $query->where('resource', $resource);
    }

    /**
     * Scope for specific result
     */
    public function scopeResult($query, $result)
    {
        return $query->where('result', $result);
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope for recent events
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for granted access
     */
    public function scopeGranted($query)
    {
        return $query->where('result', 'granted');
    }

    /**
     * Scope for denied access
     */
    public function scopeDenied($query)
    {
        return $query->where('result', 'denied');
    }

    /**
     * Scope for escalation attempts
     */
    public function scopeEscalation($query)
    {
        return $query->where('event_type', 'permission_escalation');
    }

    /**
     * Get access log statistics
     */
    public static function getStatistics($startDate = null, $endDate = null)
    {
        $query = static::query();

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return [
            'total' => $query->count(),
            'granted' => $query->where('result', 'granted')->count(),
            'denied' => $query->where('result', 'denied')->count(),
            'by_event_type' => $query->selectRaw('event_type, COUNT(*) as count')
                ->groupBy('event_type')
                ->pluck('count', 'event_type')
                ->toArray(),
            'by_permission' => $query->selectRaw('permission, COUNT(*) as count')
                ->whereNotNull('permission')
                ->groupBy('permission')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->pluck('count', 'permission')
                ->toArray(),
            'by_user' => $query->selectRaw('user_id, COUNT(*) as count')
                ->groupBy('user_id')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->pluck('count', 'user_id')
                ->toArray(),
        ];
    }

    /**
     * Get access dashboard data
     */
    public static function getDashboardData()
    {
        $today = now()->startOfDay();
        $weekAgo = now()->subWeek();

        return [
            'today_events' => static::where('created_at', '>=', $today)->count(),
            'week_events' => static::where('created_at', '>=', $weekAgo)->count(),
            'denied_access' => static::where('result', 'denied')
                ->where('created_at', '>=', $weekAgo)
                ->count(),
            'escalation_attempts' => static::where('event_type', 'permission_escalation')
                ->where('created_at', '>=', $weekAgo)
                ->count(),
            'top_permissions' => static::selectRaw('permission, COUNT(*) as count')
                ->whereNotNull('permission')
                ->where('created_at', '>=', $weekAgo)
                ->groupBy('permission')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->pluck('count', 'permission')
                ->toArray(),
            'recent_denials' => static::where('result', 'denied')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($log) {
                    return [
                        'user_id' => $log->user_id,
                        'permission' => $log->permission,
                        'resource' => $log->resource,
                        'ip_address' => $log->ip_address,
                        'created_at' => $log->created_at,
                    ];
                }),
        ];
    }
}
