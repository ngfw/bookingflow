<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReminderSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'hours_before',
        'notification_types',
        'conditions',
        'is_active',
        'is_default',
        'priority',
    ];

    protected function casts(): array
    {
        return [
            'notification_types' => 'array',
            'conditions' => 'array',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ];
    }

    public function shouldSendReminder($appointment, $client)
    {
        if (!$this->is_active) {
            return false;
        }

        // Check if appointment is within the reminder window
        $reminderTime = $appointment->appointment_date->subHours($this->hours_before);
        $now = now();
        
        if ($now->lt($reminderTime) || $now->gt($appointment->appointment_date)) {
            return false;
        }

        // Check additional conditions
        if ($this->conditions) {
            foreach ($this->conditions as $condition => $value) {
                switch ($condition) {
                    case 'service_types':
                        if (!in_array($appointment->service->type, $value)) {
                            return false;
                        }
                        break;
                    case 'staff_members':
                        if (!in_array($appointment->staff_id, $value)) {
                            return false;
                        }
                        break;
                    case 'client_types':
                        if (!in_array($client->type ?? 'regular', $value)) {
                            return false;
                        }
                        break;
                    case 'min_appointment_value':
                        if ($appointment->service->price < $value) {
                            return false;
                        }
                        break;
                }
            }
        }

        return true;
    }

    public static function getActiveSchedules()
    {
        return self::where('is_active', true)
            ->orderBy('priority')
            ->orderBy('hours_before', 'desc')
            ->get();
    }

    public static function getDefaultSchedules()
    {
        return self::where('is_default', true)
            ->where('is_active', true)
            ->orderBy('priority')
            ->get();
    }

    public static function createDefaultSchedules()
    {
        $schedules = [
            [
                'name' => '24-Hour Reminder',
                'description' => 'Standard 24-hour appointment reminder',
                'hours_before' => 24,
                'notification_types' => ['email', 'sms'],
                'conditions' => null,
                'is_default' => true,
                'priority' => 1,
            ],
            [
                'name' => '2-Hour Reminder',
                'description' => 'Last-minute 2-hour reminder',
                'hours_before' => 2,
                'notification_types' => ['sms'],
                'conditions' => null,
                'is_default' => true,
                'priority' => 2,
            ],
            [
                'name' => '1-Week Reminder',
                'description' => 'Weekly advance reminder',
                'hours_before' => 168, // 7 days * 24 hours
                'notification_types' => ['email'],
                'conditions' => null,
                'is_default' => true,
                'priority' => 3,
            ],
            [
                'name' => 'VIP Client Reminder',
                'description' => 'Enhanced reminder for VIP clients',
                'hours_before' => 48,
                'notification_types' => ['email', 'sms'],
                'conditions' => [
                    'client_types' => ['vip'],
                ],
                'is_default' => true,
                'priority' => 4,
            ],
            [
                'name' => 'High-Value Service Reminder',
                'description' => 'Special reminder for high-value services',
                'hours_before' => 72,
                'notification_types' => ['email', 'sms'],
                'conditions' => [
                    'min_appointment_value' => 100,
                ],
                'is_default' => true,
                'priority' => 5,
            ],
        ];

        foreach ($schedules as $schedule) {
            self::firstOrCreate(
                ['name' => $schedule['name']],
                $schedule
            );
        }
    }

    public function getNotificationTypesText()
    {
        return implode(', ', array_map('ucfirst', $this->notification_types));
    }

    public function getConditionsText()
    {
        if (!$this->conditions) {
            return 'None';
        }

        $conditions = [];
        foreach ($this->conditions as $key => $value) {
            switch ($key) {
                case 'service_types':
                    $conditions[] = 'Service Types: ' . implode(', ', $value);
                    break;
                case 'staff_members':
                    $staffNames = \App\Models\User::whereIn('id', $value)->pluck('name')->toArray();
                    $conditions[] = 'Staff: ' . implode(', ', $staffNames);
                    break;
                case 'client_types':
                    $conditions[] = 'Client Types: ' . implode(', ', $value);
                    break;
                case 'min_appointment_value':
                    $conditions[] = 'Min Value: $' . $value;
                    break;
            }
        }

        return implode('; ', $conditions);
    }
}