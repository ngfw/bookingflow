<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'appointment_id',
        'staff_id',
        'quantity_used',
        'cost_per_unit',
        'total_cost',
        'notes',
        'usage_date',
    ];

    protected function casts(): array
    {
        return [
            'quantity_used' => 'integer',
            'cost_per_unit' => 'decimal:2',
            'total_cost' => 'decimal:2',
            'usage_date' => 'date',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
