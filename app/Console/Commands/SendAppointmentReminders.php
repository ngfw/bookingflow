<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Models\ReminderSchedule;
use App\Models\NotificationLog;
use App\Services\NotificationService;
use Carbon\Carbon;

class SendAppointmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:send-reminders 
                            {--dry-run : Show what would be sent without actually sending}
                            {--schedule= : Specific schedule ID to process}
                            {--hours= : Custom hours before appointment}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send appointment reminders based on configured schedules';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting appointment reminder process...');
        
        $dryRun = $this->option('dry-run');
        $scheduleId = $this->option('schedule');
        $customHours = $this->option('hours');

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No actual notifications will be sent');
        }

        $remindersSent = 0;
        $errors = 0;

        try {
            if ($scheduleId) {
                $schedule = ReminderSchedule::find($scheduleId);
                if (!$schedule) {
                    $this->error("Schedule with ID {$scheduleId} not found.");
                    return 1;
                }
                $schedules = collect([$schedule]);
            } elseif ($customHours) {
                $schedules = collect([(object)[
                    'id' => 'custom',
                    'name' => 'Custom Schedule',
                    'hours_before' => (int)$customHours,
                    'notification_types' => ['email', 'sms'],
                    'conditions' => null,
                ]]);
            } else {
                $schedules = ReminderSchedule::getActiveSchedules();
            }

            foreach ($schedules as $schedule) {
                $this->info("Processing schedule: {$schedule->name} ({$schedule->hours_before} hours before)");
                
                $appointments = $this->getAppointmentsForReminder($schedule);
                
                $this->info("Found {$appointments->count()} appointments for this schedule");
                
                foreach ($appointments as $appointment) {
                    try {
                        if ($this->shouldSendReminder($appointment, $schedule)) {
                            $this->sendReminder($appointment, $schedule, $dryRun);
                            $remindersSent++;
                            
                            if (!$dryRun) {
                                $this->logReminderSent($appointment, $schedule);
                            }
                        }
                    } catch (\Exception $e) {
                        $this->error("Error processing appointment {$appointment->id}: " . $e->getMessage());
                        $errors++;
                    }
                }
            }

            $this->info("Reminder process completed!");
            $this->info("Reminders sent: {$remindersSent}");
            if ($errors > 0) {
                $this->warn("Errors encountered: {$errors}");
            }

            return 0;

        } catch (\Exception $e) {
            $this->error("Fatal error: " . $e->getMessage());
            return 1;
        }
    }

    protected function getAppointmentsForReminder($schedule)
    {
        $now = Carbon::now();
        $reminderTime = $now->copy()->addHours($schedule->hours_before);
        
        // Get appointments that are due for reminders
        $query = Appointment::with(['client', 'service', 'staff'])
            ->where('status', 'confirmed')
            ->whereBetween('appointment_date', [
                $now->copy()->addHours($schedule->hours_before - 1),
                $now->copy()->addHours($schedule->hours_before + 1)
            ]);

        // Apply additional conditions if specified
        if ($schedule->conditions) {
            foreach ($schedule->conditions as $condition => $value) {
                switch ($condition) {
                    case 'service_types':
                        $query->whereHas('service', function($q) use ($value) {
                            $q->whereIn('type', $value);
                        });
                        break;
                    case 'staff_members':
                        $query->whereIn('staff_id', $value);
                        break;
                    case 'min_appointment_value':
                        $query->whereHas('service', function($q) use ($value) {
                            $q->where('price', '>=', $value);
                        });
                        break;
                }
            }
        }

        return $query->get();
    }

    protected function shouldSendReminder($appointment, $schedule)
    {
        // Check if reminder was already sent for this schedule
        $existingReminder = NotificationLog::where('type', 'email')
            ->where('event', 'appointment_reminder')
            ->where('metadata->appointment_id', $appointment->id)
            ->where('metadata->schedule_id', $schedule->id)
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->exists();

        if ($existingReminder) {
            return false;
        }

        // Check client preferences
        if ($appointment->client->notification_preferences) {
            $preferences = $appointment->client->notification_preferences;
            
            foreach ($schedule->notification_types as $type) {
                if (isset($preferences[$type]) && !$preferences[$type]) {
                    return false;
                }
            }
        }

        return true;
    }

    protected function sendReminder($appointment, $schedule, $dryRun = false)
    {
        $notificationService = new NotificationService();
        
        $this->line("  - Sending reminder for appointment #{$appointment->id} to {$appointment->client->name}");
        
        if ($dryRun) {
            $this->line("    [DRY RUN] Would send: " . implode(', ', $schedule->notification_types));
            return;
        }

        foreach ($schedule->notification_types as $type) {
            try {
                $notificationService->sendCustomNotification(
                    $type,
                    'appointment_reminder',
                    'client',
                    $appointment->client_id,
                    $this->getReminderData($appointment),
                    [
                        'appointment_id' => $appointment->id,
                        'schedule_id' => $schedule->id,
                        'hours_before' => $schedule->hours_before,
                    ]
                );
                
                $this->line("    ✓ {$type} reminder sent");
            } catch (\Exception $e) {
                $this->error("    ✗ Failed to send {$type} reminder: " . $e->getMessage());
            }
        }
    }

    protected function getReminderData($appointment)
    {
        return [
            'client_name' => $appointment->client->name,
            'appointment_date' => $appointment->appointment_date->format('M d, Y'),
            'appointment_time' => $appointment->appointment_time->format('g:i A'),
            'service_name' => $appointment->service->name,
            'staff_name' => $appointment->staff->name,
            'salon_name' => config('app.name', 'BookingFlow'),
            'appointment_id' => $appointment->id,
        ];
    }

    protected function logReminderSent($appointment, $schedule)
    {
        // Log that reminder was sent for this appointment and schedule
        \Log::info("Appointment reminder sent", [
            'appointment_id' => $appointment->id,
            'client_id' => $appointment->client_id,
            'schedule_id' => $schedule->id,
            'schedule_name' => $schedule->name,
            'hours_before' => $schedule->hours_before,
            'notification_types' => $schedule->notification_types,
        ]);
    }
}