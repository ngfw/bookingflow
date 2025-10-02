<?php

namespace App\Services;

use App\Models\User;
use App\Models\AuditLog;
use App\Models\SecurityEvent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Carbon\Carbon;

class SecurityAuditService
{
    /**
     * Log security event
     */
    public function logSecurityEvent($eventType, $severity = 'medium', $description = '', $metadata = [])
    {
        try {
            $user = auth()->user();
            
            $securityEvent = SecurityEvent::create([
                'user_id' => $user ? $user->id : null,
                'event_type' => $eventType,
                'severity' => $severity,
                'description' => $description,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'url' => Request::fullUrl(),
                'method' => Request::method(),
                'metadata' => $metadata,
                'created_at' => now(),
            ]);

            // Log to application log
            Log::warning("Security event logged", [
                'event_id' => $securityEvent->id,
                'event_type' => $eventType,
                'severity' => $severity,
                'user_id' => $user ? $user->id : null,
                'ip_address' => Request::ip(),
            ]);

            // Check for suspicious patterns
            $this->checkSuspiciousPatterns($securityEvent);

            return $securityEvent;

        } catch (\Exception $e) {
            Log::error("Failed to log security event", [
                'event_type' => $eventType,
                'severity' => $severity,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Log authentication events
     */
    public function logAuthEvent($eventType, $user = null, $metadata = [])
    {
        $descriptions = [
            'login_success' => 'User logged in successfully',
            'login_failed' => 'Failed login attempt',
            'logout' => 'User logged out',
            'password_change' => 'Password changed',
            'password_reset' => 'Password reset requested',
            'account_locked' => 'Account locked due to failed attempts',
            'account_unlocked' => 'Account unlocked',
            'two_factor_enabled' => 'Two-factor authentication enabled',
            'two_factor_disabled' => 'Two-factor authentication disabled',
            'two_factor_failed' => 'Two-factor authentication failed',
        ];

        $severity = match($eventType) {
            'login_failed', 'account_locked', 'two_factor_failed' => 'high',
            'password_change', 'two_factor_enabled', 'two_factor_disabled' => 'medium',
            default => 'low',
        };

        return $this->logSecurityEvent(
            $eventType,
            $severity,
            $descriptions[$eventType] ?? $eventType,
            array_merge($metadata, [
                'user_email' => $user ? $user->email : null,
                'user_name' => $user ? $user->name : null,
            ])
        );
    }

    /**
     * Log access control events
     */
    public function logAccessEvent($eventType, $resource = null, $metadata = [])
    {
        $descriptions = [
            'access_granted' => 'Access granted to resource',
            'access_denied' => 'Access denied to resource',
            'permission_escalation' => 'Permission escalation detected',
            'unauthorized_access' => 'Unauthorized access attempt',
            'role_change' => 'User role changed',
            'permission_change' => 'User permissions changed',
        ];

        $severity = match($eventType) {
            'access_denied', 'unauthorized_access', 'permission_escalation' => 'high',
            'role_change', 'permission_change' => 'medium',
            default => 'low',
        };

        return $this->logSecurityEvent(
            $eventType,
            $severity,
            $descriptions[$eventType] ?? $eventType,
            array_merge($metadata, [
                'resource' => $resource,
            ])
        );
    }

    /**
     * Log data access events
     */
    public function logDataAccessEvent($eventType, $dataType = null, $recordId = null, $metadata = [])
    {
        $descriptions = [
            'data_viewed' => 'Data viewed',
            'data_exported' => 'Data exported',
            'data_modified' => 'Data modified',
            'data_deleted' => 'Data deleted',
            'bulk_operation' => 'Bulk operation performed',
            'sensitive_data_access' => 'Sensitive data accessed',
        ];

        $severity = match($eventType) {
            'data_deleted', 'sensitive_data_access' => 'high',
            'data_exported', 'bulk_operation' => 'medium',
            default => 'low',
        };

        return $this->logSecurityEvent(
            $eventType,
            $severity,
            $descriptions[$eventType] ?? $eventType,
            array_merge($metadata, [
                'data_type' => $dataType,
                'record_id' => $recordId,
            ])
        );
    }

    /**
     * Log system events
     */
    public function logSystemEvent($eventType, $metadata = [])
    {
        $descriptions = [
            'backup_created' => 'System backup created',
            'backup_restored' => 'System backup restored',
            'configuration_changed' => 'System configuration changed',
            'maintenance_mode' => 'Maintenance mode activated',
            'system_error' => 'System error occurred',
            'security_scan' => 'Security scan performed',
            'virus_detected' => 'Virus detected',
            'intrusion_detected' => 'Intrusion detected',
        ];

        $severity = match($eventType) {
            'virus_detected', 'intrusion_detected', 'system_error' => 'critical',
            'backup_restored', 'configuration_changed' => 'high',
            'backup_created', 'security_scan' => 'medium',
            default => 'low',
        };

        return $this->logSecurityEvent(
            $eventType,
            $severity,
            $descriptions[$eventType] ?? $eventType,
            $metadata
        );
    }

    /**
     * Check for suspicious patterns
     */
    protected function checkSuspiciousPatterns($securityEvent)
    {
        $ipAddress = $securityEvent->ip_address;
        $userId = $securityEvent->user_id;
        $eventType = $securityEvent->event_type;

        // Check for multiple failed login attempts
        if ($eventType === 'login_failed') {
            $this->checkFailedLoginAttempts($ipAddress, $userId);
        }

        // Check for unusual access patterns
        if (in_array($eventType, ['access_denied', 'unauthorized_access'])) {
            $this->checkUnusualAccessPatterns($ipAddress, $userId);
        }

        // Check for data exfiltration attempts
        if (in_array($eventType, ['data_exported', 'bulk_operation'])) {
            $this->checkDataExfiltrationAttempts($userId);
        }
    }

    /**
     * Check failed login attempts
     */
    protected function checkFailedLoginAttempts($ipAddress, $userId)
    {
        $recentAttempts = SecurityEvent::where('event_type', 'login_failed')
            ->where('ip_address', $ipAddress)
            ->where('created_at', '>=', now()->subMinutes(15))
            ->count();

        if ($recentAttempts >= 5) {
            $this->logSecurityEvent(
                'suspicious_activity',
                'high',
                'Multiple failed login attempts detected',
                [
                    'ip_address' => $ipAddress,
                    'attempt_count' => $recentAttempts,
                    'time_window' => '15 minutes',
                ]
            );

            // Lock IP address temporarily
            $this->lockIpAddress($ipAddress, 30); // 30 minutes
        }
    }

    /**
     * Check unusual access patterns
     */
    protected function checkUnusualAccessPatterns($ipAddress, $userId)
    {
        $recentDenials = SecurityEvent::where('event_type', 'access_denied')
            ->where('ip_address', $ipAddress)
            ->where('created_at', '>=', now()->subHour())
            ->count();

        if ($recentDenials >= 10) {
            $this->logSecurityEvent(
                'suspicious_activity',
                'high',
                'Unusual access pattern detected',
                [
                    'ip_address' => $ipAddress,
                    'denial_count' => $recentDenials,
                    'time_window' => '1 hour',
                ]
            );
        }
    }

    /**
     * Check data exfiltration attempts
     */
    protected function checkDataExfiltrationAttempts($userId)
    {
        $recentExports = SecurityEvent::where('event_type', 'data_exported')
            ->where('user_id', $userId)
            ->where('created_at', '>=', now()->subDay())
            ->count();

        if ($recentExports >= 5) {
            $this->logSecurityEvent(
                'suspicious_activity',
                'medium',
                'Multiple data exports detected',
                [
                    'user_id' => $userId,
                    'export_count' => $recentExports,
                    'time_window' => '1 day',
                ]
            );
        }
    }

    /**
     * Lock IP address
     */
    protected function lockIpAddress($ipAddress, $minutes)
    {
        $key = "locked_ip_{$ipAddress}";
        Cache::put($key, true, $minutes * 60);
        
        Log::warning("IP address locked", [
            'ip_address' => $ipAddress,
            'lock_duration' => $minutes,
        ]);
    }

    /**
     * Check if IP is locked
     */
    public function isIpLocked($ipAddress)
    {
        $key = "locked_ip_{$ipAddress}";
        return Cache::has($key);
    }

    /**
     * Get security events
     */
    public function getSecurityEvents($filters = [], $limit = 100, $offset = 0)
    {
        $query = SecurityEvent::query();

        // Apply filters
        if (isset($filters['event_type'])) {
            $query->where('event_type', $filters['event_type']);
        }

        if (isset($filters['severity'])) {
            $query->where('severity', $filters['severity']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['ip_address'])) {
            $query->where('ip_address', $filters['ip_address']);
        }

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        // Order by creation date (newest first)
        $query->orderBy('created_at', 'desc');

        // Apply pagination
        $query->limit($limit)->offset($offset);

        return $query->get();
    }

    /**
     * Get security statistics
     */
    public function getSecurityStatistics($startDate = null, $endDate = null)
    {
        $query = SecurityEvent::query();

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return [
            'total_events' => $query->count(),
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
    public function getSecurityAlerts($limit = 20)
    {
        return SecurityEvent::whereIn('severity', ['high', 'critical'])
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
    public function getSecurityDashboard()
    {
        $today = now()->startOfDay();
        $weekAgo = now()->subWeek();
        $monthAgo = now()->subMonth();

        return [
            'today_events' => SecurityEvent::where('created_at', '>=', $today)->count(),
            'week_events' => SecurityEvent::where('created_at', '>=', $weekAgo)->count(),
            'month_events' => SecurityEvent::where('created_at', '>=', $monthAgo)->count(),
            'critical_events' => SecurityEvent::where('severity', 'critical')
                ->where('created_at', '>=', $weekAgo)
                ->count(),
            'high_events' => SecurityEvent::where('severity', 'high')
                ->where('created_at', '>=', $weekAgo)
                ->count(),
            'recent_alerts' => $this->getSecurityAlerts(10),
            'top_ips' => SecurityEvent::selectRaw('ip_address, COUNT(*) as count')
                ->where('created_at', '>=', $weekAgo)
                ->groupBy('ip_address')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->pluck('count', 'ip_address')
                ->toArray(),
        ];
    }

    /**
     * Export security events
     */
    public function exportSecurityEvents($filters = [], $format = 'csv')
    {
        $events = $this->getSecurityEvents($filters, 10000);

        if ($format === 'csv') {
            return $this->exportToCsv($events);
        } elseif ($format === 'json') {
            return $this->exportToJson($events);
        }

        throw new \Exception("Unsupported export format: {$format}");
    }

    /**
     * Export to CSV
     */
    protected function exportToCsv($events)
    {
        $filename = 'security_events_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $filepath = storage_path('app/exports/' . $filename);

        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        $file = fopen($filepath, 'w');

        // Write CSV header
        fputcsv($file, [
            'ID', 'User ID', 'Event Type', 'Severity', 'Description',
            'IP Address', 'User Agent', 'URL', 'Method', 'Metadata', 'Created At'
        ]);

        // Write data rows
        foreach ($events as $event) {
            fputcsv($file, [
                $event->id,
                $event->user_id,
                $event->event_type,
                $event->severity,
                $event->description,
                $event->ip_address,
                $event->user_agent,
                $event->url,
                $event->method,
                json_encode($event->metadata),
                $event->created_at->toISOString(),
            ]);
        }

        fclose($file);

        return [
            'filename' => $filename,
            'filepath' => $filepath,
            'size' => filesize($filepath),
        ];
    }

    /**
     * Export to JSON
     */
    protected function exportToJson($events)
    {
        $filename = 'security_events_' . now()->format('Y-m-d_H-i-s') . '.json';
        $filepath = storage_path('app/exports/' . $filename);

        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        $data = $events->map(function ($event) {
            return [
                'id' => $event->id,
                'user_id' => $event->user_id,
                'event_type' => $event->event_type,
                'severity' => $event->severity,
                'description' => $event->description,
                'ip_address' => $event->ip_address,
                'user_agent' => $event->user_agent,
                'url' => $event->url,
                'method' => $event->method,
                'metadata' => $event->metadata,
                'created_at' => $event->created_at->toISOString(),
            ];
        })->toArray();

        file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT));

        return [
            'filename' => $filename,
            'filepath' => $filepath,
            'size' => filesize($filepath),
        ];
    }
}
