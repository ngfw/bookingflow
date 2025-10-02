<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'date',
        'start_time',
        'end_time',
        'break_start',
        'break_end',
        'status',
        'notes',
        'is_recurring',
        'recurring_type',
        'recurring_end_date',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
            'break_start' => 'datetime:H:i',
            'break_end' => 'datetime:H:i',
            'is_recurring' => 'boolean',
            'recurring_end_date' => 'date',
        ];
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function isAvailable()
    {
        return $this->status === 'available';
    }

    public function isScheduled()
    {
        return $this->status === 'scheduled';
    }

    public function isUnavailable()
    {
        return in_array($this->status, ['unavailable', 'sick', 'vacation']);
    }

    public function getDurationAttribute()
    {
        $start = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);
        return $start->diffInMinutes($end);
    }

    public function getWorkingHoursAttribute()
    {
        $start = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);
        $breakStart = $this->break_start ? \Carbon\Carbon::parse($this->break_start) : null;
        $breakEnd = $this->break_end ? \Carbon\Carbon::parse($this->break_end) : null;

        $totalMinutes = $start->diffInMinutes($end);
        
        if ($breakStart && $breakEnd) {
            $breakMinutes = $breakStart->diffInMinutes($breakEnd);
            $totalMinutes -= $breakMinutes;
        }

        return $totalMinutes;
    }
}
