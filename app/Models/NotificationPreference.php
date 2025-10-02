<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotificationPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'notification_type',
        'email_enabled',
        'sms_enabled',
        'push_enabled',
        'phone_enabled',
        'timing_preferences',
        'frequency_preferences',
        'content_preferences',
        'preferred_language',
        'timezone',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'email_enabled' => 'boolean',
            'sms_enabled' => 'boolean',
            'push_enabled' => 'boolean',
            'phone_enabled' => 'boolean',
            'timing_preferences' => 'array',
            'frequency_preferences' => 'array',
            'content_preferences' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper methods
    public function isEmailEnabled()
    {
        return $this->email_enabled && $this->is_active;
    }

    public function isSmsEnabled()
    {
        return $this->sms_enabled && $this->is_active;
    }

    public function isPushEnabled()
    {
        return $this->push_enabled && $this->is_active;
    }

    public function isPhoneEnabled()
    {
        return $this->phone_enabled && $this->is_active;
    }

    public function getEnabledChannels()
    {
        $channels = [];
        
        if ($this->isEmailEnabled()) {
            $channels[] = 'email';
        }
        
        if ($this->isSmsEnabled()) {
            $channels[] = 'sms';
        }
        
        if ($this->isPushEnabled()) {
            $channels[] = 'push';
        }
        
        if ($this->isPhoneEnabled()) {
            $channels[] = 'phone';
        }
        
        return $channels;
    }

    public function getNotificationTypeDisplayAttribute()
    {
        return match($this->notification_type) {
            'appointment_reminder' => 'Appointment Reminders',
            'appointment_confirmation' => 'Appointment Confirmations',
            'appointment_cancellation' => 'Appointment Cancellations',
            'appointment_reschedule' => 'Appointment Reschedules',
            'promotion' => 'Promotions & Offers',
            'system_update' => 'System Updates',
            'newsletter' => 'Newsletters',
            'birthday' => 'Birthday Wishes',
            'anniversary' => 'Anniversary Reminders',
            'feedback_request' => 'Feedback Requests',
            'payment_reminder' => 'Payment Reminders',
            'service_update' => 'Service Updates',
            'staff_update' => 'Staff Updates',
            'emergency' => 'Emergency Notifications',
            default => 'Unknown'
        };
    }

    public function getNotificationTypeDescriptionAttribute()
    {
        return match($this->notification_type) {
            'appointment_reminder' => 'Receive reminders before your appointments',
            'appointment_confirmation' => 'Get confirmation when appointments are booked',
            'appointment_cancellation' => 'Be notified when appointments are cancelled',
            'appointment_reschedule' => 'Get updates when appointments are rescheduled',
            'promotion' => 'Receive special offers and promotions',
            'system_update' => 'Get notified about system maintenance and updates',
            'newsletter' => 'Receive our monthly newsletter with beauty tips',
            'birthday' => 'Get birthday wishes and special offers',
            'anniversary' => 'Receive anniversary reminders and offers',
            'feedback_request' => 'Get requests to provide feedback after services',
            'payment_reminder' => 'Receive reminders for outstanding payments',
            'service_update' => 'Get updates about new services and changes',
            'staff_update' => 'Be notified about staff changes and availability',
            'emergency' => 'Receive emergency notifications and important updates',
            default => 'Unknown notification type'
        };
    }

    // Static methods for preference management
    public static function getDefaultPreferences()
    {
        return [
            'appointment_reminder' => [
                'email_enabled' => true,
                'sms_enabled' => false,
                'push_enabled' => true,
                'phone_enabled' => false,
                'timing_preferences' => ['24h', '2h', '1h'],
                'frequency_preferences' => ['before_appointment'],
                'content_preferences' => ['appointment_details', 'staff_info', 'location'],
            ],
            'appointment_confirmation' => [
                'email_enabled' => true,
                'sms_enabled' => true,
                'push_enabled' => true,
                'phone_enabled' => false,
                'timing_preferences' => ['immediately'],
                'frequency_preferences' => ['on_booking'],
                'content_preferences' => ['appointment_details', 'staff_info', 'location', 'cancellation_policy'],
            ],
            'promotion' => [
                'email_enabled' => true,
                'sms_enabled' => false,
                'push_enabled' => true,
                'phone_enabled' => false,
                'timing_preferences' => ['business_hours'],
                'frequency_preferences' => ['weekly'],
                'content_preferences' => ['offer_details', 'expiry_date', 'terms'],
            ],
        ];
    }

    public static function createDefaultPreferencesForUser($userId)
    {
        $defaults = self::getDefaultPreferences();
        $preferences = [];

        foreach ($defaults as $type => $settings) {
            $preferences[] = self::updateOrCreate(
                [
                    'user_id' => $userId,
                    'notification_type' => $type,
                ],
                [
                'email_enabled' => $settings['email_enabled'],
                'sms_enabled' => $settings['sms_enabled'],
                'push_enabled' => $settings['push_enabled'],
                'phone_enabled' => $settings['phone_enabled'],
                'timing_preferences' => $settings['timing_preferences'],
                'frequency_preferences' => $settings['frequency_preferences'],
                'content_preferences' => $settings['content_preferences'],
                'preferred_language' => 'en',
                'timezone' => 'UTC',
                'is_active' => true,
                ]
            );
        }

        return $preferences;
    }

    public static function getUserPreferences($userId)
    {
        return self::where('user_id', $userId)
            ->where('is_active', true)
            ->get()
            ->keyBy('notification_type');
    }

    public static function updateUserPreference($userId, $notificationType, $data)
    {
        return self::updateOrCreate(
            [
                'user_id' => $userId,
                'notification_type' => $notificationType,
            ],
            array_merge($data, ['is_active' => true])
        );
    }

    public static function getNotificationTypes()
    {
        return [
            'appointment_reminder' => 'Appointment Reminders',
            'appointment_confirmation' => 'Appointment Confirmations',
            'appointment_cancellation' => 'Appointment Cancellations',
            'appointment_reschedule' => 'Appointment Reschedules',
            'promotion' => 'Promotions & Offers',
            'system_update' => 'System Updates',
            'newsletter' => 'Newsletters',
            'birthday' => 'Birthday Wishes',
            'anniversary' => 'Anniversary Reminders',
            'feedback_request' => 'Feedback Requests',
            'payment_reminder' => 'Payment Reminders',
            'service_update' => 'Service Updates',
            'staff_update' => 'Staff Updates',
            'emergency' => 'Emergency Notifications',
        ];
    }
}