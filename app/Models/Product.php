<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'supplier_id',
        'name',
        'description',
        'sku',
        'barcode',
        'brand',
        'cost_price',
        'selling_price',
        'retail_price',
        'current_stock',
        'stock_quantity',
        'minimum_stock',
        'unit',
        'supplier',
        'expiry_date',
        'storage_location',
        'is_for_sale',
        'is_for_service',
        'type',
        'image',
        'is_active',
        'usage_notes',
    ];

    protected function casts(): array
    {
        return [
            'cost_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'retail_price' => 'decimal:2',
            'current_stock' => 'integer',
            'stock_quantity' => 'integer',
            'minimum_stock' => 'integer',
            'expiry_date' => 'date',
            'is_for_sale' => 'boolean',
            'is_for_service' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function usage()
    {
        return $this->hasMany(ProductUsage::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_products');
    }
}
