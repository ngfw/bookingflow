<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\NotificationPreference;

class NotificationPreferencesSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('is_active', true)->get();
        
        if ($users->isEmpty()) {
            $this->command->info('No users found. Please run the admin user seeder first.');
            return;
        }

        // Create default preferences for each user
        foreach ($users as $user) {
            NotificationPreference::createDefaultPreferencesForUser($user->id);
        }

        // Create some additional custom preferences
        foreach ($users->take(3) as $user) {
            // Newsletter preference
            NotificationPreference::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'notification_type' => 'newsletter',
                ],
                [
                'email_enabled' => true,
                'sms_enabled' => false,
                'push_enabled' => false,
                'phone_enabled' => false,
                'timing_preferences' => ['business_hours'],
                'frequency_preferences' => ['monthly'],
                'content_preferences' => ['beauty_tips', 'new_services', 'staff_spotlight'],
                'preferred_language' => 'en',
                'timezone' => 'UTC',
                'is_active' => true,
            ]);

            // Birthday preference
            NotificationPreference::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'notification_type' => 'birthday',
                ],
                [
                'email_enabled' => true,
                'sms_enabled' => true,
                'push_enabled' => true,
                'phone_enabled' => false,
                'timing_preferences' => ['morning'],
                'frequency_preferences' => ['annually'],
                'content_preferences' => ['birthday_wish', 'special_offer'],
                'preferred_language' => 'en',
                'timezone' => 'UTC',
                'is_active' => true,
            ]);

            // Feedback request preference
            NotificationPreference::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'notification_type' => 'feedback_request',
                ],
                [
                'email_enabled' => true,
                'sms_enabled' => false,
                'push_enabled' => true,
                'phone_enabled' => false,
                'timing_preferences' => ['evening'],
                'frequency_preferences' => ['after_service'],
                'content_preferences' => ['service_details', 'rating_request'],
                'preferred_language' => 'en',
                'timezone' => 'UTC',
                'is_active' => true,
            ]);
        }

        // Create some inactive preferences
        foreach ($users->skip(3)->take(2) as $user) {
            NotificationPreference::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'notification_type' => 'promotion',
                ],
                [
                'email_enabled' => false,
                'sms_enabled' => false,
                'push_enabled' => false,
                'phone_enabled' => false,
                'timing_preferences' => ['business_hours'],
                'frequency_preferences' => ['weekly'],
                'content_preferences' => ['offer_details', 'expiry_date'],
                'preferred_language' => 'en',
                'timezone' => 'UTC',
                'is_active' => false, // User opted out of promotions
            ]);
        }

        $this->command->info('Notification preferences data seeded successfully!');
    }
}