<?php

namespace App\Services;

use App\Models\PushNotification;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class PushNotificationService
{
    protected $fcmServerKey;
    protected $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

    public function __construct()
    {
        $this->fcmServerKey = config('services.fcm.server_key');
    }

    /**
     * Send a push notification
     */
    public function sendNotification(PushNotification $notification)
    {
        try {
            if (!$notification->user_id) {
                throw new \Exception('No user ID provided for notification');
            }

            $user = User::find($notification->user_id);
            if (!$user) {
                throw new \Exception('User not found');
            }

            // Get device tokens for the user
            $deviceTokens = $this->getDeviceTokensForUser($user);
            if (empty($deviceTokens)) {
                throw new \Exception('No device tokens found for user');
            }

            // Send to each device
            $successCount = 0;
            foreach ($deviceTokens as $deviceToken) {
                if ($this->sendToDevice($notification, $deviceToken)) {
                    $successCount++;
                }
            }

            if ($successCount > 0) {
                $notification->markAsSent();
                Log::info("Push notification sent successfully", [
                    'notification_id' => $notification->id,
                    'user_id' => $notification->user_id,
                    'devices' => $successCount
                ]);
                return true;
            } else {
                throw new \Exception('Failed to send to any device');
            }

        } catch (\Exception $e) {
            $notification->markAsFailed($e->getMessage());
            Log::error("Push notification failed", [
                'notification_id' => $notification->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send notification to a specific device
     */
    protected function sendToDevice(PushNotification $notification, $deviceToken)
    {
        try {
            $payload = [
                'to' => $deviceToken,
                'notification' => [
                    'title' => $notification->title,
                    'body' => $notification->message,
                    'icon' => '/images/notification-icon.png',
                    'click_action' => $notification->action_url,
                ],
                'data' => [
                    'type' => $notification->type,
                    'data' => json_encode($notification->data),
                    'action_url' => $notification->action_url,
                    'action_text' => $notification->action_text,
                ],
                'priority' => 'high',
            ];

            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->fcmServerKey,
                'Content-Type' => 'application/json',
            ])->post($this->fcmUrl, $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                if (isset($responseData['success']) && $responseData['success'] == 1) {
                    return true;
                }
            }

            return false;

        } catch (\Exception $e) {
            Log::error("Failed to send to device", [
                'device_token' => $deviceToken,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get device tokens for a user
     */
    protected function getDeviceTokensForUser(User $user)
    {
        // In a real application, you would store device tokens in a separate table
        // For now, we'll simulate this with a simple array
        return [
            'device_token_1',
            'device_token_2',
        ];
    }

    /**
     * Send notification to multiple users
     */
    public function sendToUsers($userIds, $title, $message, $options = [])
    {
        $notifications = [];
        foreach ($userIds as $userId) {
            $notification = PushNotification::createNotification($title, $message, array_merge($options, [
                'user_id' => $userId
            ]));
            $notifications[] = $notification;
        }

        return $notifications;
    }

    /**
     * Send notification to all users
     */
    public function sendToAllUsers($title, $message, $options = [])
    {
        $userIds = User::where('is_active', true)->pluck('id')->toArray();
        return $this->sendToUsers($userIds, $title, $message, $options);
    }

    /**
     * Send appointment reminder
     */
    public function sendAppointmentReminder($appointment)
    {
        $notification = PushNotification::createAppointmentNotification($appointment, 'reminder');
        return $this->sendNotification($notification);
    }

    /**
     * Send appointment confirmation
     */
    public function sendAppointmentConfirmation($appointment)
    {
        $notification = PushNotification::createAppointmentNotification($appointment, 'confirmation');
        return $this->sendNotification($notification);
    }

    /**
     * Send appointment cancellation
     */
    public function sendAppointmentCancellation($appointment)
    {
        $notification = PushNotification::createAppointmentNotification($appointment, 'cancellation');
        return $this->sendNotification($notification);
    }

    /**
     * Send promotion notification
     */
    public function sendPromotionNotification($promotion, $userIds = [])
    {
        if (empty($userIds)) {
            $userIds = User::where('is_active', true)->pluck('id')->toArray();
        }

        $notifications = PushNotification::createPromotionNotification($promotion, $userIds);
        
        $successCount = 0;
        foreach ($notifications as $notification) {
            if ($this->sendNotification($notification)) {
                $successCount++;
            }
        }

        return $successCount;
    }

    /**
     * Process pending notifications
     */
    public function processPendingNotifications()
    {
        $pendingNotifications = PushNotification::getPendingNotifications();
        $processedCount = 0;

        foreach ($pendingNotifications as $notification) {
            if ($this->sendNotification($notification)) {
                $processedCount++;
            }
        }

        return $processedCount;
    }

    /**
     * Retry failed notifications
     */
    public function retryFailedNotifications()
    {
        $failedNotifications = PushNotification::getFailedNotifications();
        $retryCount = 0;

        foreach ($failedNotifications as $notification) {
            $notification->incrementRetryCount();
            if ($this->sendNotification($notification)) {
                $retryCount++;
            }
        }

        return $retryCount;
    }

    /**
     * Get notification statistics
     */
    public function getStatistics($startDate = null, $endDate = null)
    {
        $query = PushNotification::query();

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return [
            'total' => $query->count(),
            'pending' => $query->where('status', 'pending')->count(),
            'sent' => $query->where('status', 'sent')->count(),
            'delivered' => $query->where('status', 'delivered')->count(),
            'failed' => $query->where('status', 'failed')->count(),
            'read' => $query->where('status', 'read')->count(),
        ];
    }

    /**
     * Clean up old notifications
     */
    public function cleanupOldNotifications($days = 30)
    {
        $cutoffDate = now()->subDays($days);
        
        return PushNotification::where('created_at', '<', $cutoffDate)
            ->whereIn('status', ['read', 'failed'])
            ->delete();
    }
}

