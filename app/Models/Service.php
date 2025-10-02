<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'duration_minutes',
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
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'duration_minutes' => 'integer',
            'buffer_time_minutes' => 'integer',
            'requires_deposit' => 'boolean',
            'deposit_amount' => 'decimal:2',
            'required_products' => 'array',
            'is_package' => 'boolean',
            'package_services' => 'array',
            'online_booking_enabled' => 'boolean',
            'max_advance_booking_days' => 'integer',
            'is_active' => 'boolean',
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
}
