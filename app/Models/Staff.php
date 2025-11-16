<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'location_id',
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
        'is_active',
        'bio',
        'profile_image',
        'experience_years',
        'certifications',
        'skills',
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
            'is_active' => 'boolean',
            'experience_years' => 'integer',
            'certifications' => 'array',
            'skills' => 'array',
            'social_media' => 'array',
            'languages' => 'array',
        ];
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
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

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    // Accessor Attributes
    protected function name(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->user?->name,
        );
    }

    protected function email(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->user?->email,
        );
    }

    protected function phone(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->user?->phone,
        );
    }

    protected function totalAppointments(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->appointments()->count(),
        );
    }

    protected function completedAppointments(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->appointments()->where('status', 'completed')->count(),
        );
    }

    protected function cancelledAppointments(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->appointments()->where('status', 'cancelled')->count(),
        );
    }

    protected function totalRevenue(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->appointments()
                ->where('status', 'completed')
                ->sum('price'),
        );
    }

    protected function commissionEarned(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->totalRevenue * ($this->commission_rate / 100),
        );
    }

    protected function hoursWorked(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->appointments()
                ->where('status', 'completed')
                ->sum('duration') / 60,
        );
    }

    protected function averageRating(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->reviews()->avg('rating') ?? 0,
        );
    }

    protected function totalReviews(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->reviews()->count(),
        );
    }

    protected function employmentDuration(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->hire_date ? now()->diffInDays($this->hire_date) : 0,
        );
    }

    // Instance Methods
    public function hasSkill(string $skill): bool
    {
        $skills = $this->skills ?? [];
        return in_array($skill, $skills);
    }

    public function hasCertification(string $certification): bool
    {
        $certifications = $this->certifications ?? [];
        return in_array($certification, $certifications);
    }

    public function addSkill(string $skill): void
    {
        $skills = $this->skills ?? [];
        if (!in_array($skill, $skills)) {
            $skills[] = $skill;
            $this->update(['skills' => $skills]);
        }
    }

    public function addCertification(string $certification): void
    {
        $certifications = $this->certifications ?? [];
        if (!in_array($certification, $certifications)) {
            $certifications[] = $certification;
            $this->update(['certifications' => $certifications]);
        }
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAtLocation($query, $locationId)
    {
        return $query->where('location_id', $locationId);
    }

    public function scopeByPosition($query, $position)
    {
        return $query->where('position', $position);
    }

    public function scopeWithSkill($query, $skill)
    {
        return $query->whereJsonContains('skills', $skill);
    }

    public function scopeWithCertification($query, $certification)
    {
        return $query->whereJsonContains('certifications', $certification);
    }
}
