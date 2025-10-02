<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class NotificationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id',
        'type',
        'event',
        'recipient_type',
        'recipient_id',
        'recipient_email',
        'recipient_phone',
        'subject',
        'body',
        'status',
        'error_message',
        'sent_at',
        'delivered_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'sent_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function template()
    {
        return $this->belongsTo(NotificationTemplate::class, 'template_id');
    }

    public function recipient()
    {
        switch ($this->recipient_type) {
            case 'client':
                return $this->belongsTo(Client::class, 'recipient_id');
            case 'staff':
            case 'admin':
                return $this->belongsTo(User::class, 'recipient_id');
            default:
                return null;
        }
    }

    public function markAsSent()
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => Carbon::now(),
        ]);
    }

    public function markAsDelivered()
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => Carbon::now(),
        ]);
    }

    public function markAsFailed($errorMessage = null)
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
        ]);
    }

    public function markAsBounced()
    {
        $this->update([
            'status' => 'bounced',
        ]);
    }

    public static function getPendingNotifications($type = null)
    {
        $query = self::where('status', 'pending');
        
        if ($type) {
            $query->where('type', $type);
        }
        
        return $query->orderBy('created_at')->get();
    }

    public static function getFailedNotifications($type = null)
    {
        $query = self::where('status', 'failed');
        
        if ($type) {
            $query->where('type', $type);
        }
        
        return $query->orderBy('created_at')->get();
    }

    public static function getNotificationStats($startDate = null, $endDate = null)
    {
        $query = self::query();
        
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }
        
        return $query->selectRaw('
            type,
            status,
            COUNT(*) as count,
            COUNT(CASE WHEN status = "sent" THEN 1 END) as sent_count,
            COUNT(CASE WHEN status = "delivered" THEN 1 END) as delivered_count,
            COUNT(CASE WHEN status = "failed" THEN 1 END) as failed_count,
            COUNT(CASE WHEN status = "bounced" THEN 1 END) as bounced_count
        ')
        ->groupBy('type', 'status')
        ->get();
    }

    public static function getDeliveryRate($startDate = null, $endDate = null)
    {
        $query = self::query();
        
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }
        
        $stats = $query->selectRaw('
            COUNT(*) as total,
            COUNT(CASE WHEN status = "delivered" THEN 1 END) as delivered,
            COUNT(CASE WHEN status = "sent" THEN 1 END) as sent,
            COUNT(CASE WHEN status = "failed" THEN 1 END) as failed
        ')->first();
        
        if ($stats->total > 0) {
            return [
                'delivery_rate' => round(($stats->delivered / $stats->total) * 100, 2),
                'success_rate' => round((($stats->delivered + $stats->sent) / $stats->total) * 100, 2),
                'failure_rate' => round(($stats->failed / $stats->total) * 100, 2),
                'total' => $stats->total,
                'delivered' => $stats->delivered,
                'sent' => $stats->sent,
                'failed' => $stats->failed,
            ];
        }
        
        return [
            'delivery_rate' => 0,
            'success_rate' => 0,
            'failure_rate' => 0,
            'total' => 0,
            'delivered' => 0,
            'sent' => 0,
            'failed' => 0,
        ];
    }
}