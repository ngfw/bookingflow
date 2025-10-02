<?php

namespace App\Services;

use App\Models\User;
use App\Models\SecurityEvent;
use App\Models\AccessLog;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SecurityMonitoringService
{
    /**
     * Monitor security events
     */
    public function monitorSecurityEvents()
    {
        try {
            $alerts = [];
            
            // Check for failed login attempts
            $failedLoginAlerts = $this->checkFailedLoginAttempts();
            $alerts = array_merge($alerts, $failedLoginAlerts);
            
            // Check for suspicious IP addresses
            $suspiciousIpAlerts = $this->checkSuspiciousIpAddresses();
            $alerts = array_merge($alerts, $suspiciousIpAlerts);
            
            // Check for permission escalation attempts
            $escalationAlerts = $this->checkPermissionEscalationAttempts();
            $alerts = array_merge($alerts, $escalationAlerts);
            
            // Check for unusual data access patterns
            $dataAccessAlerts = $this->checkUnusualDataAccess();
            $alerts = array_merge($alerts, $dataAccessAlerts);
            
            // Check for system anomalies
            $systemAlerts = $this->checkSystemAnomalies();
            $alerts = array_merge($alerts, $systemAlerts);
            
            // Process alerts
            $this->processAlerts($alerts);
            
            return [
                'success' => true,
                'alerts_found' => count($alerts),
                'alerts' => $alerts,
            ];

        } catch (\Exception $e) {
            Log::error("Security monitoring failed", [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Check for failed login attempts
     */
    protected function checkFailedLoginAttempts()
    {
        $alerts = [];
        $timeWindow = 15; // minutes
        $threshold = 5; // failed attempts
        
        $recentFailedLogins = SecurityEvent::where('event_type', 'login_failed')
            ->where('created_at', '>=', now()->subMinutes($timeWindow))
            ->get()
            ->groupBy('ip_address');
        
        foreach ($recentFailedLogins as $ipAddress => $events) {
            if ($events->count() >= $threshold) {
                $alerts[] = [
                    'type' => 'failed_login_attempts',
                    'severity' => 'high',
                    'title' => 'Multiple Failed Login Attempts',
                    'description' => "IP {$ipAddress} has {$events->count()} failed login attempts in the last {$timeWindow} minutes",
                    'ip_address' => $ipAddress,
                    'attempt_count' => $events->count(),
                    'time_window' => $timeWindow,
                    'recommended_action' => 'Consider blocking this IP address',
                ];
            }
        }
        
        return $alerts;
    }

    /**
     * Check for suspicious IP addresses
     */
    protected function checkSuspiciousIpAddresses()
    {
        $alerts = [];
        $timeWindow = 60; // minutes
        $threshold = 10; // events
        
        $recentEvents = SecurityEvent::where('created_at', '>=', now()->subMinutes($timeWindow))
            ->get()
            ->groupBy('ip_address');
        
        foreach ($recentEvents as $ipAddress => $events) {
            $deniedEvents = $events->where('event_type', 'access_denied')->count();
            $failedLogins = $events->where('event_type', 'login_failed')->count();
            
            if ($deniedEvents >= $threshold || $failedLogins >= 5) {
                $alerts[] = [
                    'type' => 'suspicious_ip',
                    'severity' => 'high',
                    'title' => 'Suspicious IP Address Activity',
                    'description' => "IP {$ipAddress} shows suspicious activity: {$deniedEvents} denied access attempts, {$failedLogins} failed logins",
                    'ip_address' => $ipAddress,
                    'denied_attempts' => $deniedEvents,
                    'failed_logins' => $failedLogins,
                    'time_window' => $timeWindow,
                    'recommended_action' => 'Investigate and consider blocking this IP address',
                ];
            }
        }
        
        return $alerts;
    }

    /**
     * Check for permission escalation attempts
     */
    protected function checkPermissionEscalationAttempts()
    {
        $alerts = [];
        $timeWindow = 60; // minutes
        
        $recentEscalations = SecurityEvent::where('event_type', 'permission_escalation')
            ->where('created_at', '>=', now()->subMinutes($timeWindow))
            ->get();
        
        foreach ($recentEscalations as $event) {
            $alerts[] = [
                'type' => 'permission_escalation',
                'severity' => 'critical',
                'title' => 'Permission Escalation Attempt',
                'description' => "User {$event->user_id} attempted to escalate permissions",
                'user_id' => $event->user_id,
                'ip_address' => $event->ip_address,
                'metadata' => $event->metadata,
                'recommended_action' => 'Review user permissions and investigate the attempt',
            ];
        }
        
        return $alerts;
    }

    /**
     * Check for unusual data access patterns
     */
    protected function checkUnusualDataAccess()
    {
        $alerts = [];
        $timeWindow = 24; // hours
        $exportThreshold = 5; // exports per day
        $bulkThreshold = 3; // bulk operations per day
        
        $recentDataEvents = SecurityEvent::whereIn('event_type', ['data_exported', 'bulk_operation'])
            ->where('created_at', '>=', now()->subHours($timeWindow))
            ->get()
            ->groupBy('user_id');
        
        foreach ($recentDataEvents as $userId => $events) {
            $exports = $events->where('event_type', 'data_exported')->count();
            $bulkOps = $events->where('event_type', 'bulk_operation')->count();
            
            if ($exports >= $exportThreshold || $bulkOps >= $bulkThreshold) {
                $alerts[] = [
                    'type' => 'unusual_data_access',
                    'severity' => 'medium',
                    'title' => 'Unusual Data Access Pattern',
                    'description' => "User {$userId} has {$exports} data exports and {$bulkOps} bulk operations in the last {$timeWindow} hours",
                    'user_id' => $userId,
                    'export_count' => $exports,
                    'bulk_operation_count' => $bulkOps,
                    'time_window' => $timeWindow,
                    'recommended_action' => 'Review user activity and verify legitimate business need',
                ];
            }
        }
        
        return $alerts;
    }

    /**
     * Check for system anomalies
     */
    protected function checkSystemAnomalies()
    {
        $alerts = [];
        
        // Check for high error rates
        $errorRate = $this->checkErrorRates();
        if ($errorRate > 0.1) { // 10% error rate
            $alerts[] = [
                'type' => 'high_error_rate',
                'severity' => 'high',
                'title' => 'High System Error Rate',
                'description' => "System error rate is {$errorRate}% which is above the threshold",
                'error_rate' => $errorRate,
                'recommended_action' => 'Investigate system errors and performance issues',
            ];
        }
        
        // Check for unusual system load
        $systemLoad = $this->checkSystemLoad();
        if ($systemLoad > 0.8) { // 80% load
            $alerts[] = [
                'type' => 'high_system_load',
                'severity' => 'medium',
                'title' => 'High System Load',
                'description' => "System load is {$systemLoad}% which is above the threshold",
                'system_load' => $systemLoad,
                'recommended_action' => 'Monitor system performance and consider scaling',
            ];
        }
        
        return $alerts;
    }

    /**
     * Check system error rates
     */
    protected function checkErrorRates()
    {
        $totalEvents = SecurityEvent::where('created_at', '>=', now()->subHour())->count();
        $errorEvents = SecurityEvent::where('event_type', 'system_error')
            ->where('created_at', '>=', now()->subHour())
            ->count();
        
        return $totalEvents > 0 ? ($errorEvents / $totalEvents) : 0;
    }

    /**
     * Check system load
     */
    protected function checkSystemLoad()
    {
        // This would typically integrate with system monitoring tools
        // For now, we'll use a simple heuristic based on recent events
        $recentEvents = SecurityEvent::where('created_at', '>=', now()->subMinutes(5))->count();
        $baselineEvents = SecurityEvent::where('created_at', '>=', now()->subMinutes(10))
            ->where('created_at', '<', now()->subMinutes(5))
            ->count();
        
        if ($baselineEvents == 0) {
            return 0;
        }
        
        return min(1.0, $recentEvents / $baselineEvents);
    }

    /**
     * Process security alerts
     */
    protected function processAlerts($alerts)
    {
        foreach ($alerts as $alert) {
            // Log the alert
            Log::warning("Security alert triggered", $alert);
            
            // Store in database
            SecurityEvent::create([
                'user_id' => null,
                'event_type' => 'security_alert',
                'severity' => $alert['severity'],
                'description' => $alert['description'],
                'ip_address' => $alert['ip_address'] ?? null,
                'metadata' => $alert,
                'created_at' => now(),
            ]);
            
            // Send notifications for high/critical alerts
            if (in_array($alert['severity'], ['high', 'critical'])) {
                $this->sendSecurityAlert($alert);
            }
        }
    }

    /**
     * Send security alert notification
     */
    protected function sendSecurityAlert($alert)
    {
        try {
            $recipients = config('security.alerts.recipients', []);
            
            if (empty($recipients)) {
                return;
            }

            $subject = "Security Alert: {$alert['title']}";
            $message = $this->formatSecurityAlertMessage($alert);

            foreach ($recipients as $recipient) {
                Mail::raw($message, function ($mail) use ($recipient, $subject) {
                    $mail->to($recipient)
                         ->subject($subject);
                });
            }

        } catch (\Exception $e) {
            Log::error("Failed to send security alert", [
                'alert' => $alert,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Format security alert message
     */
    protected function formatSecurityAlertMessage($alert)
    {
        return "Security Alert: {$alert['title']}\n\n" .
               "Description: {$alert['description']}\n" .
               "Severity: {$alert['severity']}\n" .
               "Type: {$alert['type']}\n" .
               "Time: " . now() . "\n\n" .
               "Recommended Action: {$alert['recommended_action']}\n\n" .
               "Please investigate this security event immediately.";
    }

    /**
     * Get security monitoring dashboard
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
            'critical_alerts' => SecurityEvent::where('severity', 'critical')
                ->where('created_at', '>=', $weekAgo)
                ->count(),
            'high_alerts' => SecurityEvent::where('severity', 'high')
                ->where('created_at', '>=', $weekAgo)
                ->count(),
            'failed_logins' => SecurityEvent::where('event_type', 'login_failed')
                ->where('created_at', '>=', $weekAgo)
                ->count(),
            'access_denials' => SecurityEvent::where('event_type', 'access_denied')
                ->where('created_at', '>=', $weekAgo)
                ->count(),
            'recent_alerts' => SecurityEvent::whereIn('severity', ['high', 'critical'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($event) {
                    return [
                        'id' => $event->id,
                        'event_type' => $event->event_type,
                        'severity' => $event->severity,
                        'description' => $event->description,
                        'created_at' => $event->created_at,
                    ];
                }),
            'top_ips' => SecurityEvent::selectRaw('ip_address, COUNT(*) as count')
                ->where('created_at', '>=', $weekAgo)
                ->whereNotNull('ip_address')
                ->groupBy('ip_address')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->pluck('count', 'ip_address')
                ->toArray(),
        ];
    }

    /**
     * Get security trends
     */
    public function getSecurityTrends($days = 30)
    {
        $startDate = now()->subDays($days);
        
        $trends = [];
        
        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i);
            $nextDate = $date->copy()->addDay();
            
            $trends[] = [
                'date' => $date->format('Y-m-d'),
                'total_events' => SecurityEvent::whereBetween('created_at', [$date, $nextDate])->count(),
                'critical_events' => SecurityEvent::where('severity', 'critical')
                    ->whereBetween('created_at', [$date, $nextDate])
                    ->count(),
                'high_events' => SecurityEvent::where('severity', 'high')
                    ->whereBetween('created_at', [$date, $nextDate])
                    ->count(),
                'failed_logins' => SecurityEvent::where('event_type', 'login_failed')
                    ->whereBetween('created_at', [$date, $nextDate])
                    ->count(),
                'access_denials' => SecurityEvent::where('event_type', 'access_denied')
                    ->whereBetween('created_at', [$date, $nextDate])
                    ->count(),
            ];
        }
        
        return $trends;
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
                ->whereNotNull('ip_address')
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
     * Run security health check
     */
    public function runSecurityHealthCheck()
    {
        $checks = [];
        
        // Check 2FA adoption
        $totalUsers = User::count();
        $usersWith2FA = User::where('two_factor_enabled', true)->count();
        $twoFactorRate = $totalUsers > 0 ? ($usersWith2FA / $totalUsers) : 0;
        
        $checks[] = [
            'name' => 'Two-Factor Authentication',
            'status' => $twoFactorRate > 0.8 ? 'good' : 'warning',
            'value' => round($twoFactorRate * 100, 2) . '%',
            'recommendation' => $twoFactorRate < 0.8 ? 'Encourage 2FA adoption' : 'Good 2FA adoption rate',
        ];
        
        // Check password strength
        $weakPasswords = User::where('password_strength', 'weak')->count();
        $passwordStrengthRate = $totalUsers > 0 ? (($totalUsers - $weakPasswords) / $totalUsers) : 1;
        
        $checks[] = [
            'name' => 'Password Strength',
            'status' => $passwordStrengthRate > 0.9 ? 'good' : 'warning',
            'value' => round($passwordStrengthRate * 100, 2) . '%',
            'recommendation' => $passwordStrengthRate < 0.9 ? 'Enforce stronger passwords' : 'Good password strength',
        ];
        
        // Check recent security events
        $recentCriticalEvents = SecurityEvent::where('severity', 'critical')
            ->where('created_at', '>=', now()->subWeek())
            ->count();
        
        $checks[] = [
            'name' => 'Critical Security Events',
            'status' => $recentCriticalEvents == 0 ? 'good' : 'warning',
            'value' => $recentCriticalEvents,
            'recommendation' => $recentCriticalEvents > 0 ? 'Investigate critical events' : 'No critical events',
        ];
        
        return [
            'overall_status' => $this->calculateOverallStatus($checks),
            'checks' => $checks,
            'checked_at' => now(),
        ];
    }

    /**
     * Calculate overall security status
     */
    protected function calculateOverallStatus($checks)
    {
        $goodCount = 0;
        $warningCount = 0;
        $criticalCount = 0;
        
        foreach ($checks as $check) {
            switch ($check['status']) {
                case 'good':
                    $goodCount++;
                    break;
                case 'warning':
                    $warningCount++;
                    break;
                case 'critical':
                    $criticalCount++;
                    break;
            }
        }
        
        if ($criticalCount > 0) {
            return 'critical';
        } elseif ($warningCount > 0) {
            return 'warning';
        } else {
            return 'good';
        }
    }
}
