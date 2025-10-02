<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Waitlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'service_id',
        'staff_id',
        'preferred_date',
        'preferred_time_start',
        'preferred_time_end',
        'status',
        'notes',
        'admin_notes',
        'contacted_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'preferred_date' => 'date',
            'preferred_time_start' => 'datetime:H:i',
            'preferred_time_end' => 'datetime:H:i',
            'contacted_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
