<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Location extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'phone',
        'email',
        'website',
        'business_hours',
        'amenities',
        'timezone',
        'tax_rate',
        'currency',
        'is_active',
        'is_headquarters',
        'max_staff',
        'max_clients_per_day',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'tax_rate' => 'decimal:4',
            'business_hours' => 'array',
            'amenities' => 'array',
            'settings' => 'array',
            'is_active' => 'boolean',
            'is_headquarters' => 'boolean',
            'max_staff' => 'integer',
            'max_clients_per_day' => 'integer',
        ];
    }

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'location_service')
                    ->withTimestamps();
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'primary_location_id');
    }

    // Accessors
    protected function fullAddress(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->address}, {$this->city}, {$this->state} {$this->postal_code}, {$this->country}",
        );
    }

    protected function businessHoursFormatted(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->business_hours) {
                    return null;
                }

                $formatted = [];
                $days = [
                    'monday' => 'Monday',
                    'tuesday' => 'Tuesday',
                    'wednesday' => 'Wednesday',
                    'thursday' => 'Thursday',
                    'friday' => 'Friday',
                    'saturday' => 'Saturday',
                    'sunday' => 'Sunday',
                ];

                foreach ($this->business_hours as $day => $hours) {
                    if (isset($days[$day])) {
                        $formatted[$days[$day]] = $hours;
                    }
                }

                return $formatted;
            }
        );
    }

    protected function amenitiesList(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->amenities ? array_values($this->amenities) : [],
        );
    }

    protected function staffCount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->staff()->count(),
        );
    }

    protected function activeStaffCount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->staff()->whereHas('user', function ($query) {
                $query->where('is_active', true);
            })->count(),
        );
    }

    protected function todayAppointmentsCount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->appointments()
                ->whereDate('appointment_date', today())
                ->where('status', '!=', 'cancelled')
                ->count(),
        );
    }

    protected function todayRevenue(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->payments()
                ->whereDate('payment_date', today())
                ->where('status', 'completed')
                ->sum('amount'),
        );
    }

    public function isOpen()
    {
        if (!$this->business_hours) {
            return false;
        }

        $now = now()->setTimezone($this->timezone);
        $currentDay = strtolower($now->format('l'));
        $currentTime = $now->format('H:i');

        if (!isset($this->business_hours[$currentDay])) {
            return false;
        }

        $hours = $this->business_hours[$currentDay];
        if (!isset($hours['open']) || !isset($hours['close'])) {
            return false;
        }

        return $currentTime >= $hours['open'] && $currentTime <= $hours['close'];
    }

    public function getNextOpenTime()
    {
        if (!$this->business_hours) {
            return null;
        }

        $now = now()->setTimezone($this->timezone);
        $currentDay = strtolower($now->format('l'));
        $currentTime = $now->format('H:i');

        // Check if open today
        if (isset($this->business_hours[$currentDay])) {
            $hours = $this->business_hours[$currentDay];
            if (isset($hours['open']) && $currentTime < $hours['open']) {
                return $now->setTimeFromTimeString($hours['open']);
            }
        }

        // Find next open day
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $currentDayIndex = array_search($currentDay, $days);

        for ($i = 1; $i <= 7; $i++) {
            $nextDayIndex = ($currentDayIndex + $i) % 7;
            $nextDay = $days[$nextDayIndex];
            
            if (isset($this->business_hours[$nextDay])) {
                $hours = $this->business_hours[$nextDay];
                if (isset($hours['open'])) {
                    return $now->addDays($i)->setTimeFromTimeString($hours['open']);
                }
            }
        }

        return null;
    }

    public function getDistanceFrom($latitude, $longitude)
    {
        if (!$this->latitude || !$this->longitude) {
            return null;
        }

        $earthRadius = 6371; // Earth's radius in kilometers
        
        $lat1 = deg2rad($latitude);
        $lon1 = deg2rad($longitude);
        $lat2 = deg2rad($this->latitude);
        $lon2 = deg2rad($this->longitude);
        
        $deltaLat = $lat2 - $lat1;
        $deltaLon = $lon2 - $lon1;
        
        $a = sin($deltaLat / 2) * sin($deltaLat / 2) + cos($lat1) * cos($lat2) * sin($deltaLon / 2) * sin($deltaLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return round($earthRadius * $c, 2);
    }

    // Instance Methods
    public function getStatistics()
    {
        $totalAppointments = $this->appointments()->count();
        $completedAppointments = $this->appointments()->where('status', 'completed')->count();
        $cancelledAppointments = $this->appointments()->where('status', 'cancelled')->count();
        $totalRevenue = $this->payments()->where('status', 'completed')->sum('amount');
        $averageRating = $this->appointments()->whereNotNull('rating')->avg('rating');

        return [
            'total_appointments' => $totalAppointments,
            'completed_appointments' => $completedAppointments,
            'cancelled_appointments' => $cancelledAppointments,
            'total_staff' => $this->staff()->count(),
            'active_staff' => $this->staff()->whereHas('user', function ($query) {
                $query->where('is_active', true);
            })->count(),
            'total_revenue' => $totalRevenue,
            'average_rating' => $averageRating ? (float) $averageRating : 0,
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
            'monthly_revenue' => $this->payments()
                ->where('payment_date', '>=', now()->subMonth())
                ->where('status', 'completed')
                ->sum('amount'),
            'weekly_revenue' => $this->payments()
                ->where('payment_date', '>=', now()->subWeek())
                ->where('status', 'completed')
                ->sum('amount'),
            'completion_rate' => round($completionRate, 2),
            'cancellation_rate' => round($cancellationRate, 2),
        ];
    }

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

    public function setAsHeadquarters()
    {
        $this->is_headquarters = true;
        $this->save();
    }

    public function unsetAsHeadquarters()
    {
        $this->is_headquarters = false;
        $this->save();
    }

    public function updateBusinessHours(array $hours)
    {
        $this->business_hours = $hours;
        $this->save();
    }

    public function addAmenity(string $amenity)
    {
        $amenities = $this->amenities ?? [];
        if (!in_array($amenity, $amenities)) {
            $amenities[] = $amenity;
            $this->amenities = $amenities;
            $this->save();
        }
    }

    public function removeAmenity(string $amenity)
    {
        $amenities = $this->amenities ?? [];
        $key = array_search($amenity, $amenities);
        if ($key !== false) {
            unset($amenities[$key]);
            $this->amenities = array_values($amenities);
            $this->save();
        }
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeHeadquarters($query)
    {
        return $query->where('is_headquarters', true);
    }

    public function scopeInCity($query, $city)
    {
        return $query->where('city', 'like', "%{$city}%");
    }

    public function scopeInState($query, $state)
    {
        return $query->where('state', 'like', "%{$state}%");
    }
}