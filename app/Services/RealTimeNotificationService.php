<?php

namespace App\Services;

use App\Models\User;
use App\Models\Appointment;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Redis;

class RealTimeNotificationService
{
    protected $redis;
    protected $broadcastChannel = 'beauty-salon';

    public function __construct()
    {
        $this->redis = Redis::connection();
    }

    /**
     * Send real-time notification to specific user
     */
    public function sendToUser($userId, $type, $title, $message, $data = [])
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                throw new \Exception("User not found: {$userId}");
            }

            // Create notification record
            $notification = Notification::create([
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => $data,
                'channel' => 'realtime',
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            // Broadcast to user's private channel
            $this->broadcastToUser($user, [
                'id' => $notification->id,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => $data,
                'timestamp' => now()->toISOString(),
            ]);

            // Store in Redis for offline users
            $this->storeInRedis($user, $notification);

            Log::info("Real-time notification sent to user {$userId}", [
                'notification_id' => $notification->id,
                'type' => $type,
            ]);

            return $notification;

        } catch (\Exception $e) {
            Log::error("Failed to send real-time notification to user {$userId}", [
                'error' => $e->getMessage(),
                'type' => $type,
            ]);
            return false;
        }
    }

    /**
     * Send real-time notification to multiple users
     */
    public function sendToUsers($userIds, $type, $title, $message, $data = [])
    {
        $notifications = [];
        
        foreach ($userIds as $userId) {
            $notification = $this->sendToUser($userId, $type, $title, $message, $data);
            if ($notification) {
                $notifications[] = $notification;
            }
        }

        return $notifications;
    }

    /**
     * Send real-time notification to all staff
     */
    public function sendToAllStaff($type, $title, $message, $data = [])
    {
        $staffIds = User::where('role', 'staff')
            ->where('is_active', true)
            ->pluck('id')
            ->toArray();

        return $this->sendToUsers($staffIds, $type, $title, $message, $data);
    }

    /**
     * Send real-time notification to all admins
     */
    public function sendToAllAdmins($type, $title, $message, $data = [])
    {
        $adminIds = User::where('role', 'admin')
            ->where('is_active', true)
            ->pluck('id')
            ->toArray();

        return $this->sendToUsers($adminIds, $type, $title, $message, $data);
    }

    /**
     * Send appointment-related real-time notifications
     */
    public function sendAppointmentNotification($appointment, $event, $additionalData = [])
    {
        $data = array_merge([
            'appointment_id' => $appointment->id,
            'client_name' => $appointment->client->user->name ?? 'Unknown',
            'service_name' => $appointment->service->name ?? 'Unknown Service',
            'appointment_date' => $appointment->appointment_date->format('Y-m-d H:i'),
            'staff_name' => $appointment->staff->user->name ?? 'Unknown Staff',
        ], $additionalData);

        $notifications = [];

        // Notify client
        if ($appointment->client && $appointment->client->user) {
            $notifications[] = $this->sendToUser(
                $appointment->client->user->id,
                "appointment_{$event}",
                $this->getAppointmentTitle($event),
                $this->getAppointmentMessage($appointment, $event),
                $data
            );
        }

        // Notify staff
        if ($appointment->staff && $appointment->staff->user) {
            $notifications[] = $this->sendToUser(
                $appointment->staff->user->id,
                "appointment_{$event}",
                $this->getAppointmentTitle($event),
                $this->getAppointmentMessage($appointment, $event),
                $data
            );
        }

        // Notify admins for important events
        if (in_array($event, ['cancelled', 'no_show', 'emergency'])) {
            $clientName = $appointment->client->user->name ?? 'Unknown';
            $this->sendToAllAdmins(
                "appointment_{$event}",
                "Appointment {$event}: {$clientName}",
                $this->getAppointmentMessage($appointment, $event),
                $data
            );
        }

        return $notifications;
    }

    /**
     * Send system-wide announcements
     */
    public function sendSystemAnnouncement($title, $message, $data = [], $targetRoles = ['admin', 'staff'])
    {
        $userIds = User::whereIn('role', $targetRoles)
            ->where('is_active', true)
            ->pluck('id')
            ->toArray();

        return $this->sendToUsers($userIds, 'system_announcement', $title, $message, $data);
    }

    /**
     * Send emergency notifications
     */
    public function sendEmergencyNotification($title, $message, $data = [])
    {
        // Send to all active users
        $userIds = User::where('is_active', true)->pluck('id')->toArray();
        
        return $this->sendToUsers($userIds, 'emergency', $title, $message, $data);
    }

    /**
     * Broadcast notification to user's private channel
     */
    protected function broadcastToUser($user, $notification)
    {
        try {
            Broadcast::channel("user.{$user->id}", function () use ($user) {
                return $user;
            });

            broadcast(new \App\Events\RealTimeNotification($user, $notification))
                ->toOthers();

        } catch (\Exception $e) {
            Log::error("Failed to broadcast notification to user {$user->id}", [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Store notification in Redis for offline users
     */
    protected function storeInRedis($user, $notification)
    {
        try {
            $key = "notifications:user:{$user->id}";
            $this->redis->lpush($key, json_encode([
                'id' => $notification->id,
                'type' => $notification->type,
                'title' => $notification->title,
                'message' => $notification->message,
                'data' => $notification->data,
                'created_at' => $notification->created_at->toISOString(),
            ]));

            // Keep only last 50 notifications per user
            $this->redis->ltrim($key, 0, 49);
            $this->redis->expire($key, 86400 * 7); // Expire after 7 days

        } catch (\Exception $e) {
            Log::error("Failed to store notification in Redis for user {$user->id}", [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get pending notifications for user from Redis
     */
    public function getPendingNotifications($userId)
    {
        try {
            $key = "notifications:user:{$userId}";
            $notifications = $this->redis->lrange($key, 0, -1);
            
            return array_map('json_decode', $notifications);

        } catch (\Exception $e) {
            Log::error("Failed to get pending notifications for user {$userId}", [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Mark notification as read and remove from Redis
     */
    public function markAsRead($userId, $notificationId)
    {
        try {
            // Update database
            $notification = Notification::where('user_id', $userId)
                ->where('id', $notificationId)
                ->first();

            if ($notification) {
                $notification->update([
                    'status' => 'read',
                    'read_at' => now(),
                ]);
            }

            // Remove from Redis
            $key = "notifications:user:{$userId}";
            $notifications = $this->redis->lrange($key, 0, -1);
            
            foreach ($notifications as $index => $notificationData) {
                $data = json_decode($notificationData, true);
                if ($data['id'] == $notificationId) {
                    $this->redis->lrem($key, 1, $notificationData);
                    break;
                }
            }

            return true;

        } catch (\Exception $e) {
            Log::error("Failed to mark notification as read", [
                'user_id' => $userId,
                'notification_id' => $notificationId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get appointment notification title
     */
    protected function getAppointmentTitle($event)
    {
        return match($event) {
            'created' => 'New Appointment Booked',
            'confirmed' => 'Appointment Confirmed',
            'cancelled' => 'Appointment Cancelled',
            'rescheduled' => 'Appointment Rescheduled',
            'completed' => 'Appointment Completed',
            'no_show' => 'Appointment No-Show',
            'reminder' => 'Appointment Reminder',
            default => 'Appointment Update',
        };
    }

    /**
     * Get appointment notification message
     */
    protected function getAppointmentMessage($appointment, $event)
    {
        $clientName = $appointment->client->user->name ?? 'Unknown Client';
        $serviceName = $appointment->service->name ?? 'Unknown Service';
        $date = $appointment->appointment_date->format('M j, Y \a\t g:i A');

        return match($event) {
            'created' => "New appointment booked: {$serviceName} for {$clientName} on {$date}",
            'confirmed' => "Appointment confirmed: {$serviceName} for {$clientName} on {$date}",
            'cancelled' => "Appointment cancelled: {$serviceName} for {$clientName} on {$date}",
            'rescheduled' => "Appointment rescheduled: {$serviceName} for {$clientName} on {$date}",
            'completed' => "Appointment completed: {$serviceName} for {$clientName} on {$date}",
            'no_show' => "No-show: {$serviceName} for {$clientName} on {$date}",
            'reminder' => "Reminder: {$serviceName} appointment for {$clientName} on {$date}",
            default => "Appointment update: {$serviceName} for {$clientName} on {$date}",
        };
    }

    /**
     * Get real-time notification statistics
     */
    public function getStatistics($startDate = null, $endDate = null)
    {
        $query = Notification::where('channel', 'realtime');

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return [
            'total_sent' => $query->count(),
            'total_read' => $query->where('status', 'read')->count(),
            'total_unread' => $query->where('status', 'sent')->count(),
            'by_type' => $query->selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray(),
        ];
    }

    /**
     * Clean up old real-time notifications
     */
    public function cleanupOldNotifications($days = 30)
    {
        $cutoffDate = now()->subDays($days);
        
        $deleted = Notification::where('channel', 'realtime')
            ->where('created_at', '<', $cutoffDate)
            ->where('status', 'read')
            ->delete();

        return $deleted;
    }
}
