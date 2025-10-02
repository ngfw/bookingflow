<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_email',
        'action',
        'model_type',
        'model_id',
        'old_data',
        'new_data',
        'ip_address',
        'user_agent',
        'url',
        'method',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'old_data' => 'array',
            'new_data' => 'array',
            'metadata' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the model instance
     */
    public function getModel()
    {
        if (!$this->model_type || !$this->model_id) {
            return null;
        }

        try {
            return $this->model_type::find($this->model_id);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get formatted action display
     */
    public function getActionDisplayAttribute()
    {
        return match($this->action) {
            'create' => 'Created',
            'update' => 'Updated',
            'delete' => 'Deleted',
            'restore' => 'Restored',
            'export' => 'Exported',
            'import' => 'Imported',
            'login' => 'Logged In',
            'logout' => 'Logged Out',
            'password_change' => 'Password Changed',
            'backup' => 'Backup',
            'restore' => 'Restore',
            'security' => 'Security Event',
            'payment' => 'Payment',
            'appointment' => 'Appointment',
            'config_change' => 'Configuration Changed',
            default => ucfirst($this->action),
        };
    }

    /**
     * Get formatted model type display
     */
    public function getModelTypeDisplayAttribute()
    {
        if (!$this->model_type) {
            return 'System';
        }

        $modelName = class_basename($this->model_type);
        return match($modelName) {
            'User' => 'User',
            'Client' => 'Client',
            'Staff' => 'Staff',
            'Appointment' => 'Appointment',
            'Service' => 'Service',
            'Product' => 'Product',
            'Invoice' => 'Invoice',
            'Payment' => 'Payment',
            'Location' => 'Location',
            'Franchise' => 'Franchise',
            default => $modelName,
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
     * Get changes summary
     */
    public function getChangesSummaryAttribute()
    {
        if ($this->action === 'create') {
            return 'New record created';
        }

        if ($this->action === 'delete') {
            return 'Record deleted';
        }

        if ($this->action === 'update' && $this->old_data && $this->new_data) {
            $changes = [];
            foreach ($this->new_data as $key => $newValue) {
                $oldValue = $this->old_data[$key] ?? null;
                if ($oldValue !== $newValue) {
                    $changes[] = "{$key}: {$oldValue} → {$newValue}";
                }
            }
            return implode(', ', $changes);
        }

        return $this->action_display;
    }

    /**
     * Scope for specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for specific action
     */
    public function scopeForAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope for specific model
     */
    public function scopeForModel($query, $modelType, $modelId = null)
    {
        $query->where('model_type', $modelType);
        
        if ($modelId) {
            $query->where('model_id', $modelId);
        }
        
        return $query;
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope for recent logs
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for security events
     */
    public function scopeSecurity($query)
    {
        return $query->where('action', 'security')
            ->orWhere('action', 'login')
            ->orWhere('action', 'logout')
            ->orWhere('action', 'password_change');
    }

    /**
     * Scope for data changes
     */
    public function scopeDataChanges($query)
    {
        return $query->whereIn('action', ['create', 'update', 'delete', 'restore']);
    }

    /**
     * Scope for system events
     */
    public function scopeSystemEvents($query)
    {
        return $query->whereIn('action', ['backup', 'restore', 'config_change', 'export', 'import']);
    }

    /**
     * Get audit log statistics
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
    public static function getUserActivity($userId, $days = 30)
    {
        $startDate = now()->subDays($days);

        $query = static::where('user_id', $userId)
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
    public static function getModelHistory($modelType, $modelId)
    {
        return static::where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($log) {
                return [
                    'action' => $log->action,
                    'user_name' => $log->user_name,
                    'old_data' => $log->old_data,
                    'new_data' => $log->new_data,
                    'created_at' => $log->created_at->toISOString(),
                    'ip_address' => $log->ip_address,
                ];
            })
            ->toArray();
    }
}
