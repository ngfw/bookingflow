<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
{
    use HasFactory;

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
        return $this->hasMany(Service::class);
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

    public function getFullAddressAttribute()
    {
        return "{$this->address}, {$this->city}, {$this->state} {$this->postal_code}, {$this->country}";
    }

    public function getBusinessHoursFormattedAttribute()
    {
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

    public function getAmenitiesListAttribute()
    {
        if (!$this->amenities) {
            return [];
        }

        return array_values($this->amenities);
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

    public function getStaffCount()
    {
        return $this->staff()->count();
    }

    public function getActiveStaffCount()
    {
        return $this->staff()->whereHas('user', function ($query) {
            $query->where('is_active', true);
        })->count();
    }

    public function getTodayAppointmentsCount()
    {
        return $this->appointments()
            ->whereDate('appointment_date', today())
            ->where('status', '!=', 'cancelled')
            ->count();
    }

    public function getTodayRevenue()
    {
        return $this->payments()
            ->whereDate('payment_date', today())
            ->where('status', 'completed')
            ->sum('amount');
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