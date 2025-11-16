<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'staff_id',
        'service_id',
        'location_id',
        'appointment_number',
        'appointment_date',
        'end_time',
        'duration',
        'status',
        'booking_source',
        'price',
        'service_price',
        'deposit_paid',
        'tax_amount',
        'discount_amount',
        'notes',
        'client_notes',
        'staff_notes',
        'cancellation_reason',
        'cancelled_at',
        'completion_notes',
        'completed_at',
        'reschedule_reason',
        'is_recurring',
        'recurring_pattern',
        'recurring_end_date',
        'reminder_hours',
        'reminder_sent',
        'reminder_sent_at',
        'follow_up_required',
        'rating',
        'review',
    ];

    protected function casts(): array
    {
        return [
            'appointment_date' => 'datetime',
            'end_time' => 'datetime',
            'duration' => 'integer',
            'price' => 'decimal:2',
            'service_price' => 'decimal:2',
            'deposit_paid' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'cancelled_at' => 'datetime',
            'completed_at' => 'datetime',
            'is_recurring' => 'boolean',
            'recurring_end_date' => 'date',
            'reminder_hours' => 'integer',
            'reminder_sent' => 'boolean',
            'reminder_sent_at' => 'datetime',
            'follow_up_required' => 'boolean',
            'rating' => 'integer',
        ];
    }

    // Relationships
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function productUsage()
    {
        return $this->hasMany(ProductUsage::class);
    }

    public function loyaltyTransactions()
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }

    // Accessor Attributes
    protected function durationInHours(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->duration ? $this->duration / 60 : 0,
        );
    }

    protected function durationInMinutes(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->duration ?? 0,
        );
    }

    protected function endTime(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn ($value) => $value ? Carbon::parse($value) : ($this->appointment_date && $this->duration ? $this->appointment_date->copy()->addMinutes($this->duration) : null),
        );
    }

    protected function isUpcoming(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->appointment_date && $this->appointment_date->isFuture(),
        );
    }

    protected function isPast(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->appointment_date && $this->appointment_date->isPast(),
        );
    }

    protected function isToday(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->appointment_date && $this->appointment_date->isToday(),
        );
    }

    protected function isOverdue(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->appointment_date && $this->appointment_date->isPast() && $this->status === 'scheduled',
        );
    }

    protected function statusDisplay(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => ucfirst($this->status),
        );
    }

    protected function statusColor(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => match($this->status) {
                'scheduled', 'pending', 'confirmed' => 'blue',
                'completed' => 'green',
                'cancelled' => 'red',
                'in_progress' => 'yellow',
                'no_show' => 'gray',
                default => 'blue',
            },
        );
    }

    protected function totalPrice(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => ($this->price ?? 0) + ($this->tax_amount ?? 0) - ($this->discount_amount ?? 0),
        );
    }

    protected function discountPercentage(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->price && $this->price > 0 ? ($this->discount_amount / $this->price) * 100 : 0,
        );
    }

    protected function taxPercentage(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->price && $this->price > 0 ? ($this->tax_amount / $this->price) * 100 : 0,
        );
    }

    protected function reminderTime(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->appointment_date && $this->reminder_hours ? $this->appointment_date->copy()->subHours($this->reminder_hours) : null,
        );
    }

    protected function isReminderDue(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->reminder_time && now()->greaterThanOrEqualTo($this->reminder_time) && !$this->reminder_sent,
        );
    }

    protected function confirmationCode(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => strtoupper(substr(md5($this->id . $this->appointment_date), 0, 8)),
        );
    }

    // Instance Methods
    public function cancel(?string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
            'cancelled_at' => now(),
        ]);
    }

    public function complete(?string $notes = null): void
    {
        $this->update([
            'status' => 'completed',
            'completion_notes' => $notes,
            'completed_at' => now(),
        ]);
    }

    public function reschedule(Carbon $newDate, ?string $reason = null): void
    {
        $this->update([
            'appointment_date' => $newDate,
            'reschedule_reason' => $reason,
        ]);
    }
}
