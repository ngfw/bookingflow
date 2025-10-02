<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_id',
        'position',
        'specializations',
        'hourly_rate',
        'commission_rate',
        'hire_date',
        'employment_type',
        'default_start_time',
        'default_end_time',
        'working_days',
        'can_book_online',
        'bio',
        'profile_image',
        'experience_years',
        'certifications',
        'education',
        'achievements',
        'social_media',
        'languages',
        'hobbies',
    ];

    protected function casts(): array
    {
        return [
            'specializations' => 'array',
            'hourly_rate' => 'decimal:2',
            'commission_rate' => 'decimal:2',
            'hire_date' => 'date',
            'default_start_time' => 'datetime:H:i',
            'default_end_time' => 'datetime:H:i',
            'working_days' => 'array',
            'can_book_online' => 'boolean',
            'social_media' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'staff_services')
                    ->withPivot('custom_price', 'is_primary')
                    ->withTimestamps();
    }

    public function productUsage()
    {
        return $this->hasMany(ProductUsage::class);
    }
}
