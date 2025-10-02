<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class AuditTrailService
{
    /**
     * Log an audit event
     */
    public function log($action, $model = null, $modelId = null, $oldData = null, $newData = null, $metadata = [])
    {
        try {
            $user = Auth::user();
            
            $auditLog = AuditLog::create([
                'user_id' => $user ? $user->id : null,
                'user_name' => $user ? $user->name : 'System',
                'user_email' => $user ? $user->email : null,
                'action' => $action,
                'model_type' => $model ? get_class($model) : null,
                'model_id' => $modelId ?: ($model ? $model->id : null),
                'old_data' => $oldData ? json_encode($oldData) : null,
                'new_data' => $newData ? json_encode($newData) : null,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'url' => Request::fullUrl(),
                'method' => Request::method(),
                'metadata' => json_encode($metadata),
                'created_at' => now(),
            ]);

            return $auditLog;

        } catch (\Exception $e) {
            Log::error("Failed to create audit log", [
                'action' => $action,
                'model' => $model ? get_class($model) : null,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Log user authentication events
     */
    public function logAuth($action, $user = null, $metadata = [])
    {
        return $this->log($action, null, null, null, null, array_merge($metadata, [
            'auth_action' => $action,
            'user_id' => $user ? $user->id : null,
        ]));
    }

    /**
     * Log model creation
     */
    public function logCreate($model, $metadata = [])
    {
        return $this->log('create', $model, null, null, $model->toArray(), $metadata);
    }

    /**
     * Log model update
     */
    public function logUpdate($model, $oldData, $newData, $metadata = [])
    {
        return $this->log('update', $model, null, $oldData, $newData, $metadata);
    }

    /**
     * Log model deletion
     */
    public function logDelete($model, $metadata = [])
    {
        return $this->log('delete', $model, null, $model->toArray(), null, $metadata);
    }

    /**
     * Log model restore
     */
    public function logRestore($model, $metadata = [])
    {
        return $this->log('restore', $model, null, null, $model->toArray(), $metadata);
    }

    /**
     * Log data export
     */
    public function logExport($exportType, $filters = [], $recordCount = 0, $metadata = [])
    {
        return $this->log('export', null, null, null, null, array_merge($metadata, [
            'export_type' => $exportType,
            'filters' => $filters,
            'record_count' => $recordCount,
        ]));
    }

    /**
     * Log data import
     */
    public function logImport($importType, $recordCount = 0, $successCount = 0, $errorCount = 0, $metadata = [])
    {
        return $this->log('import', null, null, null, null, array_merge($metadata, [
            'import_type' => $importType,
            'record_count' => $recordCount,
            'success_count' => $successCount,
            'error_count' => $errorCount,
        ]));
    }

    /**
     * Log system configuration changes
     */
    public function logConfigChange($configKey, $oldValue, $newValue, $metadata = [])
    {
        return $this->log('config_change', null, null, [$configKey => $oldValue], [$configKey => $newValue], $metadata);
    }

    /**
     * Log backup operations
     */
    public function logBackup($action, $backupType, $backupPath = null, $metadata = [])
    {
        return $this->log('backup', null, null, null, null, array_merge($metadata, [
            'backup_action' => $action,
            'backup_type' => $backupType,
            'backup_path' => $backupPath,
        ]));
    }

    /**
     * Log restore operations
     */
    public function logRestore($action, $backupPath = null, $metadata = [])
    {
        return $this->log('restore', null, null, null, null, array_merge($metadata, [
            'restore_action' => $action,
            'backup_path' => $backupPath,
        ]));
    }

    /**
     * Log security events
     */
    public function logSecurity($action, $severity = 'medium', $metadata = [])
    {
        return $this->log('security', null, null, null, null, array_merge($metadata, [
            'security_action' => $action,
            'severity' => $severity,
        ]));
    }

    /**
     * Log payment events
     */
    public function logPayment($action, $paymentId, $amount = null, $metadata = [])
    {
        return $this->log('payment', null, $paymentId, null, null, array_merge($metadata, [
            'payment_action' => $action,
            'amount' => $amount,
        ]));
    }

    /**
     * Log appointment events
     */
    public function logAppointment($action, $appointmentId, $metadata = [])
    {
        return $this->log('appointment', null, $appointmentId, null, null, array_merge($metadata, [
            'appointment_action' => $action,
        ]));
    }

    /**
     * Get audit logs with filters
     */
    public function getLogs($filters = [], $limit = 100, $offset = 0)
    {
        $query = AuditLog::query();

        // Apply filters
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (isset($filters['model_type'])) {
            $query->where('model_type', $filters['model_type']);
        }

        if (isset($filters['model_id'])) {
            $query->where('model_id', $filters['model_id']);
        }

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['ip_address'])) {
            $query->where('ip_address', $filters['ip_address']);
        }

        // Order by creation date (newest first)
        $query->orderBy('created_at', 'desc');

        // Apply pagination
        $query->limit($limit)->offset($offset);

        return $query->get();
    }

    /**
     * Get audit statistics
     */
    public function getStatistics($startDate = null, $endDate = null)
    {
        $query = AuditLog::query();

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return [
            'total_logs' => $query->count(),
            'by_action' => $query->selectRaw('action, COUNT(*) as count')
                ->groupBy('action')
                ->pluck('count', 'action')
                ->toArray(),
            'by_user' => $query->selectRaw('user_id, user_name, COUNT(*) as count')
                ->whereNotNull('user_id')
                ->groupBy('user_id', 'user_name')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($item) {
                    return [
                        'user_id' => $item->user_id,
                        'user_name' => $item->user_name,
                        'count' => $item->count,
                    ];
                })
                ->toArray(),
            'by_model' => $query->selectRaw('model_type, COUNT(*) as count')
                ->whereNotNull('model_type')
                ->groupBy('model_type')
                ->pluck('count', 'model_type')
                ->toArray(),
            'by_date' => $query->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->limit(30)
                ->pluck('count', 'date')
                ->toArray(),
        ];
    }

    /**
     * Get user activity summary
     */
    public function getUserActivity($userId, $days = 30)
    {
        $startDate = now()->subDays($days);

        $query = AuditLog::where('user_id', $userId)
            ->where('created_at', '>=', $startDate);

        return [
            'total_actions' => $query->count(),
            'actions_by_type' => $query->selectRaw('action, COUNT(*) as count')
                ->groupBy('action')
                ->pluck('count', 'action')
                ->toArray(),
            'recent_actions' => $query->orderBy('created_at', 'desc')
                ->limit(20)
                ->get()
                ->map(function ($log) {
                    return [
                        'action' => $log->action,
                        'model_type' => $log->model_type,
                        'model_id' => $log->model_id,
                        'created_at' => $log->created_at->toISOString(),
                        'ip_address' => $log->ip_address,
                    ];
                })
                ->toArray(),
        ];
    }

    /**
     * Get model change history
     */
    public function getModelHistory($modelType, $modelId)
    {
        return AuditLog::where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($log) {
                return [
                    'action' => $log->action,
                    'user_name' => $log->user_name,
                    'old_data' => $log->old_data ? json_decode($log->old_data, true) : null,
                    'new_data' => $log->new_data ? json_decode($log->new_data, true) : null,
                    'created_at' => $log->created_at->toISOString(),
                    'ip_address' => $log->ip_address,
                ];
            })
            ->toArray();
    }

    /**
     * Clean old audit logs
     */
    public function cleanOldLogs($days = 365)
    {
        $cutoffDate = now()->subDays($days);
        
        $deleted = AuditLog::where('created_at', '<', $cutoffDate)->delete();

        Log::info("Cleaned old audit logs", [
            'deleted_count' => $deleted,
            'cutoff_date' => $cutoffDate->toISOString(),
        ]);

        return $deleted;
    }

    /**
     * Export audit logs
     */
    public function exportLogs($filters = [], $format = 'csv')
    {
        $logs = $this->getLogs($filters, 10000); // Limit to 10k records for export

        if ($format === 'csv') {
            return $this->exportToCsv($logs);
        } elseif ($format === 'json') {
            return $this->exportToJson($logs);
        }

        throw new \Exception("Unsupported export format: {$format}");
    }

    /**
     * Export to CSV
     */
    protected function exportToCsv($logs)
    {
        $filename = 'audit_logs_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $filepath = storage_path('app/exports/' . $filename);

        // Create exports directory if it doesn't exist
        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        $file = fopen($filepath, 'w');

        // Write CSV header
        fputcsv($file, [
            'ID', 'User ID', 'User Name', 'User Email', 'Action', 'Model Type', 'Model ID',
            'Old Data', 'New Data', 'IP Address', 'User Agent', 'URL', 'Method',
            'Metadata', 'Created At'
        ]);

        // Write data rows
        foreach ($logs as $log) {
            fputcsv($file, [
                $log->id,
                $log->user_id,
                $log->user_name,
                $log->user_email,
                $log->action,
                $log->model_type,
                $log->model_id,
                $log->old_data,
                $log->new_data,
                $log->ip_address,
                $log->user_agent,
                $log->url,
                $log->method,
                $log->metadata,
                $log->created_at->toISOString(),
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
    protected function exportToJson($logs)
    {
        $filename = 'audit_logs_' . now()->format('Y-m-d_H-i-s') . '.json';
        $filepath = storage_path('app/exports/' . $filename);

        // Create exports directory if it doesn't exist
        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        $data = $logs->map(function ($log) {
            return [
                'id' => $log->id,
                'user_id' => $log->user_id,
                'user_name' => $log->user_name,
                'user_email' => $log->user_email,
                'action' => $log->action,
                'model_type' => $log->model_type,
                'model_id' => $log->model_id,
                'old_data' => $log->old_data ? json_decode($log->old_data, true) : null,
                'new_data' => $log->new_data ? json_decode($log->new_data, true) : null,
                'ip_address' => $log->ip_address,
                'user_agent' => $log->user_agent,
                'url' => $log->url,
                'method' => $log->method,
                'metadata' => $log->metadata ? json_decode($log->metadata, true) : null,
                'created_at' => $log->created_at->toISOString(),
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
