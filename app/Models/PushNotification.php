<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class PushNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'data',
        'action_url',
        'action_text',
        'status',
        'scheduled_at',
        'sent_at',
        'delivered_at',
        'read_at',
        'device_token',
        'platform',
        'error_message',
        'retry_count',
        'retry_at',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'scheduled_at' => 'datetime',
            'sent_at' => 'datetime',
            'delivered_at' => 'datetime',
            'read_at' => 'datetime',
            'retry_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper methods
    public function markAsSent()
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function markAsDelivered()
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    public function markAsRead()
    {
        $this->update([
            'status' => 'read',
            'read_at' => now(),
        ]);
    }

    public function markAsFailed($errorMessage = null)
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
        ]);
    }

    public function incrementRetryCount()
    {
        $this->increment('retry_count');
        $this->update(['retry_at' => now()->addMinutes(5)]); // Retry in 5 minutes
    }

    public function isScheduled()
    {
        return $this->scheduled_at && $this->scheduled_at->isFuture();
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isSent()
    {
        return in_array($this->status, ['sent', 'delivered', 'read']);
    }

    public function isFailed()
    {
        return $this->status === 'failed';
    }

    public function getStatusDisplayAttribute()
    {
        return match($this->status) {
            'pending' => 'Pending',
            'sent' => 'Sent',
            'delivered' => 'Delivered',
            'failed' => 'Failed',
            'read' => 'Read',
            default => 'Unknown'
        };
    }

    public function getTypeDisplayAttribute()
    {
        return match($this->type) {
            'info' => 'Information',
            'success' => 'Success',
            'warning' => 'Warning',
            'error' => 'Error',
            'appointment' => 'Appointment',
            'promotion' => 'Promotion',
            default => 'Unknown'
        };
    }

    public function getPlatformDisplayAttribute()
    {
        return match($this->platform) {
            'ios' => 'iOS',
            'android' => 'Android',
            'web' => 'Web',
            default => 'Unknown'
        };
    }

    // Static methods for notification management
    public static function createNotification($title, $message, $options = [])
    {
        return self::create([
            'user_id' => $options['user_id'] ?? null,
            'title' => $title,
            'message' => $message,
            'type' => $options['type'] ?? 'info',
            'data' => $options['data'] ?? null,
            'action_url' => $options['action_url'] ?? null,
            'action_text' => $options['action_text'] ?? null,
            'scheduled_at' => $options['scheduled_at'] ?? null,
            'device_token' => $options['device_token'] ?? null,
            'platform' => $options['platform'] ?? null,
        ]);
    }

    public static function createAppointmentNotification($appointment, $type = 'reminder')
    {
        $title = match($type) {
            'reminder' => 'Appointment Reminder',
            'confirmation' => 'Appointment Confirmed',
            'cancellation' => 'Appointment Cancelled',
            'reschedule' => 'Appointment Rescheduled',
            default => 'Appointment Update'
        };

        $message = match($type) {
            'reminder' => "Your appointment with {$appointment->staff->user->name} is scheduled for {$appointment->appointment_date->format('M j, Y')} at {$appointment->appointment_time->format('g:i A')}.",
            'confirmation' => "Your appointment with {$appointment->staff->user->name} has been confirmed for {$appointment->appointment_date->format('M j, Y')} at {$appointment->appointment_time->format('g:i A')}.",
            'cancellation' => "Your appointment with {$appointment->staff->user->name} scheduled for {$appointment->appointment_date->format('M j, Y')} at {$appointment->appointment_time->format('g:i A')} has been cancelled.",
            'reschedule' => "Your appointment with {$appointment->staff->user->name} has been rescheduled to {$appointment->appointment_date->format('M j, Y')} at {$appointment->appointment_time->format('g:i A')}.",
            default => "Your appointment with {$appointment->staff->user->name} has been updated."
        };

        return self::createNotification($title, $message, [
            'user_id' => $appointment->client->user_id,
            'type' => 'appointment',
            'data' => [
                'appointment_id' => $appointment->id,
                'appointment_type' => $type,
            ],
            'action_url' => route('appointments.show', $appointment->id),
            'action_text' => 'View Appointment',
        ]);
    }

    public static function createPromotionNotification($promotion, $userIds = [])
    {
        $title = "Special Offer: {$promotion->name}";
        $message = $promotion->description;

        $notifications = [];
        foreach ($userIds as $userId) {
            $notifications[] = self::createNotification($title, $message, [
                'user_id' => $userId,
                'type' => 'promotion',
                'data' => [
                    'promotion_id' => $promotion->id,
                ],
                'action_url' => route('promotions.show', $promotion->id),
                'action_text' => 'View Offer',
            ]);
        }

        return $notifications;
    }

    public static function getPendingNotifications()
    {
        return self::where('status', 'pending')
            ->where(function($query) {
                $query->whereNull('scheduled_at')
                      ->orWhere('scheduled_at', '<=', now());
            })
            ->orderBy('created_at')
            ->get();
    }

    public static function getFailedNotifications()
    {
        return self::where('status', 'failed')
            ->where('retry_count', '<', 3)
            ->where(function($query) {
                $query->whereNull('retry_at')
                      ->orWhere('retry_at', '<=', now());
            })
            ->orderBy('retry_at')
            ->get();
    }

    public static function getUnreadNotificationsForUser($userId)
    {
        return self::where('user_id', $userId)
            ->where('status', '!=', 'read')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public static function markAllAsReadForUser($userId)
    {
        return self::where('user_id', $userId)
            ->where('status', '!=', 'read')
            ->update([
                'status' => 'read',
                'read_at' => now(),
            ]);
    }
}