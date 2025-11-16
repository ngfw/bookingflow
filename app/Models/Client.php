<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'preferences',
        'allergies',
        'medical_conditions',
        'emergency_contact',
        'last_visit',
        'total_spent',
        'visit_count',
        'loyalty_points',
        'preferred_contact',
        'is_vip',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'preferences' => 'array',
            'allergies' => 'array',
            'medical_conditions' => 'array',
            'emergency_contact' => 'array',
            'last_visit' => 'date',
            'total_spent' => 'decimal:2',
            'visit_count' => 'integer',
            'loyalty_points' => 'integer',
            'is_vip' => 'boolean',
        ];
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'client_id', 'user_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'client_id', 'user_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'client_id', 'user_id');
    }

    public function loyaltyTransactions()
    {
        return $this->hasMany(LoyaltyTransaction::class);
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

    protected function totalSpent(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->payments()->where('status', 'completed')->sum('amount'),
        );
    }

    protected function averageSpending(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function () {
                $count = $this->payments()->where('status', 'completed')->count();
                return $count > 0 ? $this->payments()->where('status', 'completed')->sum('amount') / $count : 0;
            },
        );
    }

    protected function lastAppointment(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->appointments()->latest('appointment_date')->first(),
        );
    }

    protected function nextAppointment(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->appointments()
                ->where('appointment_date', '>', now())
                ->where('status', '!=', 'cancelled')
                ->orderBy('appointment_date', 'asc')
                ->first(),
        );
    }

    protected function loyaltyTier(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => match(true) {
                $this->loyalty_points >= 250 => 'gold',
                $this->loyalty_points >= 100 => 'silver',
                default => 'bronze',
            },
        );
    }

    protected function emergencyContactName(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->emergency_contact['name'] ?? null,
        );
    }

    protected function emergencyContactPhone(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->emergency_contact['phone'] ?? null,
        );
    }

    // Instance Methods
    public function addLoyaltyPoints(int $points, ?string $reason = null): void
    {
        $this->increment('loyalty_points', $points);

        if ($reason) {
            $this->loyaltyTransactions()->create([
                'points' => $points,
                'type' => 'earned',
                'reason' => $reason,
            ]);
        }
    }

    public function redeemLoyaltyPoints(int $points, ?string $reason = null): void
    {
        $this->decrement('loyalty_points', $points);

        if ($reason) {
            $this->loyaltyTransactions()->create([
                'points' => -$points,
                'type' => 'redeemed',
                'reason' => $reason,
            ]);
        }
    }

    public function hasAllergy(string $allergy): bool
    {
        $allergies = $this->allergies ?? [];
        return in_array($allergy, $allergies);
    }

    public function hasMedicalCondition(string $condition): bool
    {
        $conditions = $this->medical_conditions ?? [];
        return in_array($condition, $conditions);
    }

    public function getPreference(string $key, $default = null)
    {
        $preferences = $this->preferences ?? [];
        return $preferences[$key] ?? $default;
    }

    public function setPreference(string $key, $value): void
    {
        $preferences = $this->preferences ?? [];
        $preferences[$key] = $value;
        $this->update(['preferences' => $preferences]);
    }
}
