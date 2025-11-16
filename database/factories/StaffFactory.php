<?php

namespace Database\Factories;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StaffFactory extends Factory
{
    protected $model = Staff::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'employee_id' => 'EMP' . $this->faker->unique()->numerify('####'),
            'position' => $this->faker->randomElement(['Stylist', 'Senior Stylist', 'Colorist', 'Manager', 'Receptionist']),
            'specializations' => $this->faker->randomElements(['Hair Cutting', 'Coloring', 'Styling', 'Extensions', 'Treatments'], $this->faker->numberBetween(1, 3)),
            'hourly_rate' => $this->faker->randomFloat(2, 15, 50),
            'commission_rate' => $this->faker->randomFloat(2, 5, 30),
            'hire_date' => $this->faker->dateTimeBetween('-5 years', 'now'),
            'employment_type' => $this->faker->randomElement(['full_time', 'part_time', 'contract', 'freelance']),
            'default_start_time' => '09:00',
            'default_end_time' => '17:00',
            'working_days' => $this->faker->randomElements(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'], 5),
            'can_book_online' => $this->faker->boolean(80),
            'bio' => $this->faker->optional()->paragraph(),
            'profile_image' => $this->faker->optional()->imageUrl(400, 400, 'people'),
            'experience_years' => $this->faker->numberBetween(0, 20),
            'certifications' => $this->faker->optional()->sentence(),
            'education' => $this->faker->optional()->sentence(),
            'achievements' => $this->faker->optional()->sentence(),
            'social_media' => [
                'instagram' => $this->faker->optional()->userName(),
                'facebook' => $this->faker->optional()->userName(),
            ],
            'languages' => $this->faker->optional()->randomElements(['English', 'Spanish', 'French', 'German'], 2),
            'hobbies' => $this->faker->optional()->sentence(),
        ];
    }
}
