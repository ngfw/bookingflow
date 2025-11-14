<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PushNotification;
use App\Models\User;
use App\Models\Promotion;
use Carbon\Carbon;

class PushNotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('is_active', true)->get();
        $promotions = Promotion::where('is_active', true)->get();
        
        if ($users->isEmpty()) {
            $this->command->info('No users found. Please run the basic data seeder first.');
            return;
        }

        // Create sample notifications for each user
        foreach ($users as $user) {
            // Welcome notification
            PushNotification::createNotification(
                'Welcome to service business!',
                'Thank you for joining us. Book your first appointment and enjoy our services.',
                [
                    'user_id' => $user->id,
                    'type' => 'info',
                    'action_url' => '/appointments/create',
                    'action_text' => 'Book Now',
                    'status' => 'delivered',
                    'sent_at' => now()->subDays(rand(1, 30)),
                    'delivered_at' => now()->subDays(rand(1, 30)),
                ]
            );

            // Appointment reminder
            PushNotification::createNotification(
                'Appointment Reminder',
                'Your appointment is scheduled for tomorrow at 2:00 PM. Please arrive 10 minutes early.',
                [
                    'user_id' => $user->id,
                    'type' => 'appointment',
                    'data' => [
                        'appointment_id' => rand(1, 10),
                        'appointment_type' => 'reminder',
                    ],
                    'action_url' => '/appointments',
                    'action_text' => 'View Appointment',
                    'status' => 'sent',
                    'sent_at' => now()->subDays(rand(1, 7)),
                ]
            );

            // Promotion notification
            if ($promotions->count() > 0) {
                $promotion = $promotions->random();
                PushNotification::createNotification(
                    "Special Offer: {$promotion->name}",
                    $promotion->description,
                    [
                        'user_id' => $user->id,
                        'type' => 'promotion',
                        'data' => [
                            'promotion_id' => $promotion->id,
                        ],
                        'action_url' => '/promotions/' . $promotion->id,
                        'action_text' => 'View Offer',
                        'status' => 'delivered',
                        'sent_at' => now()->subDays(rand(1, 15)),
                        'delivered_at' => now()->subDays(rand(1, 15)),
                    ]
                );
            }

            // Service update notification
            PushNotification::createNotification(
                'New Service Available',
                'We have added a new facial treatment to our services. Book now to try it!',
                [
                    'user_id' => $user->id,
                    'type' => 'info',
                    'action_url' => '/services',
                    'action_text' => 'View Services',
                    'status' => 'read',
                    'sent_at' => now()->subDays(rand(1, 20)),
                    'delivered_at' => now()->subDays(rand(1, 20)),
                    'read_at' => now()->subDays(rand(1, 20)),
                ]
            );

            // Some pending notifications
            if (rand(0, 1)) {
                PushNotification::createNotification(
                    'Weekly Beauty Tips',
                    'Check out our latest beauty tips and tricks on our blog.',
                    [
                        'user_id' => $user->id,
                        'type' => 'info',
                        'action_url' => '/blog',
                        'action_text' => 'Read Blog',
                        'status' => 'pending',
                        'scheduled_at' => now()->addDays(rand(1, 7)),
                    ]
                );
            }

            // Some failed notifications
            if (rand(0, 1)) {
                PushNotification::createNotification(
                    'Failed Notification',
                    'This is a test notification that failed to send.',
                    [
                        'user_id' => $user->id,
                        'type' => 'error',
                        'status' => 'failed',
                        'error_message' => 'Device token invalid',
                        'retry_count' => rand(1, 3),
                    ]
                );
            }
        }

        // Create some system-wide notifications
        PushNotification::createNotification(
            'System Maintenance',
            'We will be performing system maintenance tonight from 2:00 AM to 4:00 AM. Some features may be temporarily unavailable.',
            [
                'user_id' => null, // System notification
                'type' => 'warning',
                'status' => 'sent',
                'sent_at' => now()->subDays(rand(1, 10)),
            ]
        );

        PushNotification::createNotification(
            'New Feature Release',
            'We have released a new feature that allows you to reschedule appointments directly from the app.',
            [
                'user_id' => null, // System notification
                'type' => 'success',
                'action_url' => '/features',
                'action_text' => 'Learn More',
                'status' => 'delivered',
                'sent_at' => now()->subDays(rand(1, 5)),
                'delivered_at' => now()->subDays(rand(1, 5)),
            ]
        );

        $this->command->info('Push notification data seeded successfully!');
    }
}