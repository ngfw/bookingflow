<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'duration_minutes',
        'duration',
        'buffer_time_minutes',
        'requires_deposit',
        'deposit_amount',
        'required_products',
        'is_package',
        'package_services',
        'online_booking_enabled',
        'max_advance_booking_days',
        'preparation_instructions',
        'aftercare_instructions',
        'image',
        'is_active',
        'is_popular',
        'requires_consultation',
        'price_change_reason',
        'duration_change_reason',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'float',
            'duration_minutes' => 'integer',
            'duration' => 'integer',
            'buffer_time_minutes' => 'integer',
            'requires_deposit' => 'boolean',
            'deposit_amount' => 'decimal:2',
            'required_products' => 'array',
            'is_package' => 'boolean',
            'package_services' => 'array',
            'online_booking_enabled' => 'boolean',
            'max_advance_booking_days' => 'integer',
            'is_active' => 'boolean',
            'is_popular' => 'boolean',
            'requires_consultation' => 'boolean',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function staff()
    {
        return $this->belongsToMany(Staff::class, 'staff_services')
                    ->withPivot('custom_price', 'is_primary')
                    ->withTimestamps();
    }

    public function requiredProducts()
    {
        return $this->belongsToMany(Product::class, 'service_products');
    }

    public function locations()
    {
        return $this->belongsToMany(Location::class, 'location_service')
                    ->withTimestamps();
    }

    // Accessors and Mutators
    protected function duration(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ?? $this->duration_minutes,
            set: function ($value) {
                return [
                    'duration' => $value,
                    'duration_minutes' => $value,
                ];
            }
        );
    }

    protected function durationInHours(): Attribute
    {
        return Attribute::make(
            get: fn () => ($this->duration ?? $this->duration_minutes) / 60,
        );
    }

    protected function durationInMinutes(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->duration ?? $this->duration_minutes,
        );
    }

    protected function formattedPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => '$' . number_format($this->price, 2),
        );
    }

    protected function statusDisplay(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->is_active ? 'Active' : 'Inactive',
        );
    }

    protected function statusColor(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->is_active ? 'green' : 'red',
        );
    }

    protected function appointmentsCount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->appointments()->count(),
        );
    }

    protected function revenue(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->appointments()
                ->where('status', 'completed')
                ->count() * $this->price,
        );
    }

    protected function averageRating(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->appointments()
                ->whereNotNull('rating')
                ->avg('rating') ?? 0,
        );
    }

    protected function totalReviews(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->appointments()
                ->whereNotNull('rating')
                ->whereNotNull('review')
                ->count(),
        );
    }

    protected function isAvailable(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->is_active,
        );
    }

    // Instance Methods
    public function activate()
    {
        $this->is_active = true;
        $this->save();
    }

    public function deactivate()
    {
        $this->is_active = false;
        $this->save();
    }

    public function markAsPopular()
    {
        $this->is_popular = true;
        $this->save();
    }

    public function unmarkAsPopular()
    {
        $this->is_popular = false;
        $this->save();
    }

    public function updatePrice(float $newPrice, string $reason = null)
    {
        $this->price = $newPrice;
        if ($reason) {
            $this->price_change_reason = $reason;
        }
        $this->save();
    }

    public function updateDuration(int $newDuration, string $reason = null)
    {
        $this->duration = $newDuration;
        $this->duration_minutes = $newDuration;
        if ($reason) {
            $this->duration_change_reason = $reason;
        }
        $this->save();
    }

    public function getStatistics()
    {
        $totalAppointments = $this->appointments()->count();
        $completedAppointments = $this->appointments()->where('status', 'completed')->count();
        $cancelledAppointments = $this->appointments()->where('status', 'cancelled')->count();
        $averageRating = $this->appointments()->whereNotNull('rating')->avg('rating');

        return [
            'total_appointments' => $totalAppointments,
            'completed_appointments' => $completedAppointments,
            'cancelled_appointments' => $cancelledAppointments,
            'total_revenue' => $completedAppointments * $this->price,
            'average_rating' => $averageRating ? (float) $averageRating : 0,
            'total_reviews' => $this->appointments()->whereNotNull('rating')->count(),
        ];
    }

    public function getPerformanceMetrics()
    {
        $totalAppointments = $this->appointments()->count();
        $completedAppointments = $this->appointments()->where('status', 'completed')->count();
        $cancelledAppointments = $this->appointments()->where('status', 'cancelled')->count();

        $completionRate = $totalAppointments > 0
            ? ($completedAppointments / $totalAppointments) * 100
            : 0;

        $cancellationRate = $totalAppointments > 0
            ? ($cancelledAppointments / $totalAppointments) * 100
            : 0;

        return [
            'monthly_appointments' => $this->appointments()
                ->where('created_at', '>=', now()->subMonth())
                ->count(),
            'weekly_appointments' => $this->appointments()
                ->where('created_at', '>=', now()->subWeek())
                ->count(),
            'completion_rate' => round($completionRate, 2),
            'cancellation_rate' => round($cancellationRate, 2),
        ];
    }
}
