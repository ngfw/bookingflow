<?php

namespace Database\Factories;

use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationPreferenceFactory extends Factory
{
    protected $model = NotificationPreference::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'email_notifications' => $this->faker->boolean(80),
            'sms_notifications' => $this->faker->boolean(60),
            'push_notifications' => $this->faker->boolean(70),
            'appointment_reminders' => $this->faker->boolean(90),
            'promotional_emails' => $this->faker->boolean(50),
            'newsletter' => $this->faker->boolean(40),
        ];
    }
}
