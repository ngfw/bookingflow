<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'preferences',
        'allergies',
        'medical_conditions',
        'last_visit',
        'total_spent',
        'visit_count',
        'loyalty_points',
        'preferred_contact',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'preferences' => 'array',
            'last_visit' => 'date',
            'total_spent' => 'decimal:2',
            'visit_count' => 'integer',
            'loyalty_points' => 'integer',
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

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function loyaltyTransactions()
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }
}
