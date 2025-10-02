<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SecurityEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_type',
        'severity',
        'description',
        'ip_address',
        'user_agent',
        'url',
        'method',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get severity display
     */
    public function getSeverityDisplayAttribute()
    {
        return match($this->severity) {
            'critical' => 'Critical',
            'high' => 'High',
            'medium' => 'Medium',
            'low' => 'Low',
            default => 'Unknown',
        };
    }

    /**
     * Get severity color
     */
    public function getSeverityColorAttribute()
    {
        return match($this->severity) {
            'critical' => 'red',
            'high' => 'orange',
            'medium' => 'yellow',
            'low' => 'green',
            default => 'gray',
        };
    }

    /**
     * Get event type display
     */
    public function getEventTypeDisplayAttribute()
    {
        return match($this->event_type) {
            'login_success' => 'Login Success',
            'login_failed' => 'Login Failed',
            'logout' => 'Logout',
            'password_change' => 'Password Change',
            'password_reset' => 'Password Reset',
            'account_locked' => 'Account Locked',
            'account_unlocked' => 'Account Unlocked',
            'two_factor_enabled' => '2FA Enabled',
            'two_factor_disabled' => '2FA Disabled',
            'two_factor_failed' => '2FA Failed',
            'access_granted' => 'Access Granted',
            'access_denied' => 'Access Denied',
            'permission_escalation' => 'Permission Escalation',
            'unauthorized_access' => 'Unauthorized Access',
            'role_change' => 'Role Change',
            'permission_change' => 'Permission Change',
            'data_viewed' => 'Data Viewed',
            'data_exported' => 'Data Exported',
            'data_modified' => 'Data Modified',
            'data_deleted' => 'Data Deleted',
            'bulk_operation' => 'Bulk Operation',
            'sensitive_data_access' => 'Sensitive Data Access',
            'backup_created' => 'Backup Created',
            'backup_restored' => 'Backup Restored',
            'configuration_changed' => 'Configuration Changed',
            'maintenance_mode' => 'Maintenance Mode',
            'system_error' => 'System Error',
            'security_scan' => 'Security Scan',
            'virus_detected' => 'Virus Detected',
            'intrusion_detected' => 'Intrusion Detected',
            'suspicious_activity' => 'Suspicious Activity',
            default => ucfirst(str_replace('_', ' ', $this->event_type)),
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
     * Scope for specific severity
     */
    public function scopeSeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope for specific event type
     */
    public function scopeEventType($query, $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    /**
     * Scope for specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for specific IP
     */
    public function scopeForIp($query, $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
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
     * Scope for critical events
     */
    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    /**
     * Scope for high severity events
     */
    public function scopeHigh($query)
    {
        return $query->where('severity', 'high');
    }

    /**
     * Scope for authentication events
     */
    public function scopeAuth($query)
    {
        return $query->whereIn('event_type', [
            'login_success', 'login_failed', 'logout', 'password_change',
            'password_reset', 'account_locked', 'account_unlocked',
            'two_factor_enabled', 'two_factor_disabled', 'two_factor_failed'
        ]);
    }

    /**
     * Scope for access control events
     */
    public function scopeAccess($query)
    {
        return $query->whereIn('event_type', [
            'access_granted', 'access_denied', 'permission_escalation',
            'unauthorized_access', 'role_change', 'permission_change'
        ]);
    }

    /**
     * Scope for data events
     */
    public function scopeData($query)
    {
        return $query->whereIn('event_type', [
            'data_viewed', 'data_exported', 'data_modified', 'data_deleted',
            'bulk_operation', 'sensitive_data_access'
        ]);
    }

    /**
     * Scope for system events
     */
    public function scopeSystem($query)
    {
        return $query->whereIn('event_type', [
            'backup_created', 'backup_restored', 'configuration_changed',
            'maintenance_mode', 'system_error', 'security_scan',
            'virus_detected', 'intrusion_detected'
        ]);
    }

    /**
     * Get security event statistics
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
            'by_severity' => $query->selectRaw('severity, COUNT(*) as count')
                ->groupBy('severity')
                ->pluck('count', 'severity')
                ->toArray(),
            'by_event_type' => $query->selectRaw('event_type, COUNT(*) as count')
                ->groupBy('event_type')
                ->pluck('count', 'event_type')
                ->toArray(),
            'by_ip' => $query->selectRaw('ip_address, COUNT(*) as count')
                ->groupBy('ip_address')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->pluck('count', 'ip_address')
                ->toArray(),
            'by_user' => $query->selectRaw('user_id, COUNT(*) as count')
                ->whereNotNull('user_id')
                ->groupBy('user_id')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->pluck('count', 'user_id')
                ->toArray(),
        ];
    }

    /**
     * Get security alerts
     */
    public static function getAlerts($limit = 20)
    {
        return static::whereIn('severity', ['high', 'critical'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'event_type' => $event->event_type,
                    'severity' => $event->severity,
                    'description' => $event->description,
                    'ip_address' => $event->ip_address,
                    'user_id' => $event->user_id,
                    'created_at' => $event->created_at,
                ];
            });
    }

    /**
     * Get security dashboard data
     */
    public static function getDashboardData()
    {
        $today = now()->startOfDay();
        $weekAgo = now()->subWeek();
        $monthAgo = now()->subMonth();

        return [
            'today_events' => static::where('created_at', '>=', $today)->count(),
            'week_events' => static::where('created_at', '>=', $weekAgo)->count(),
            'month_events' => static::where('created_at', '>=', $monthAgo)->count(),
            'critical_events' => static::where('severity', 'critical')
                ->where('created_at', '>=', $weekAgo)
                ->count(),
            'high_events' => static::where('severity', 'high')
                ->where('created_at', '>=', $weekAgo)
                ->count(),
            'recent_alerts' => static::getAlerts(10),
            'top_ips' => static::selectRaw('ip_address, COUNT(*) as count')
                ->where('created_at', '>=', $weekAgo)
                ->groupBy('ip_address')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->pluck('count', 'ip_address')
                ->toArray(),
        ];
    }
}
