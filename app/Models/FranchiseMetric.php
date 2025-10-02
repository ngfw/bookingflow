<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FranchiseMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'franchise_id',
        'metric_date',
        'metric_type',
        'metric_value',
        'metric_unit',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'metric_date' => 'date',
            'metric_value' => 'decimal:4',
        ];
    }

    public function franchise()
    {
        return $this->belongsTo(Franchise::class);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('metric_type', $type);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('metric_date', [$startDate, $endDate]);
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('metric_date', 'desc');
    }
}