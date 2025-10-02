<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'discount_value',
        'minimum_quantity',
        'minimum_amount',
        'buy_quantity',
        'get_quantity',
        'get_discount_percentage',
        'applicable_products',
        'applicable_categories',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'is_active',
        'usage_limit',
        'used_count',
        'promo_code',
        'requires_promo_code',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'minimum_amount' => 'decimal:2',
            'get_discount_percentage' => 'decimal:2',
            'applicable_products' => 'array',
            'applicable_categories' => 'array',
            'start_date' => 'date',
            'end_date' => 'date',
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
            'is_active' => 'boolean',
            'requires_promo_code' => 'boolean',
        ];
    }

    public function isActive()
    {
        if (!$this->is_active) {
            return false;
        }

        $now = Carbon::now();
        $startDateTime = Carbon::parse($this->start_date . ' ' . ($this->start_time ?? '00:00:00'));
        $endDateTime = Carbon::parse($this->end_date . ' ' . ($this->end_time ?? '23:59:59'));

        return $now->between($startDateTime, $endDateTime);
    }

    public function isExpired()
    {
        $now = Carbon::now();
        $endDateTime = Carbon::parse($this->end_date . ' ' . ($this->end_time ?? '23:59:59'));

        return $now->isAfter($endDateTime);
    }

    public function hasUsageLimit()
    {
        return $this->usage_limit !== null;
    }

    public function canBeUsed()
    {
        if (!$this->isActive()) {
            return false;
        }

        if ($this->hasUsageLimit() && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    public function incrementUsage()
    {
        $this->increment('used_count');
    }

    public function calculateDiscount($cartItems, $subtotal)
    {
        if (!$this->canBeUsed()) {
            return 0;
        }

        // Check minimum amount requirement
        if ($this->minimum_amount && $subtotal < $this->minimum_amount) {
            return 0;
        }

        // Check if promotion applies to cart items
        if (!$this->appliesToCart($cartItems)) {
            return 0;
        }

        switch ($this->type) {
            case 'percentage':
                return $this->calculatePercentageDiscount($subtotal);
            
            case 'fixed_amount':
                return $this->calculateFixedDiscount($subtotal);
            
            case 'buy_x_get_y':
                return $this->calculateBuyXGetYDiscount($cartItems);
            
            case 'bulk_discount':
                return $this->calculateBulkDiscount($cartItems);
            
            default:
                return 0;
        }
    }

    private function appliesToCart($cartItems)
    {
        // If no specific products/categories, applies to all
        if (empty($this->applicable_products) && empty($this->applicable_categories)) {
            return true;
        }

        foreach ($cartItems as $item) {
            $productId = $item['product_id'];
            
            // Check if product is in applicable products
            if (!empty($this->applicable_products) && in_array($productId, $this->applicable_products)) {
                return true;
            }

            // Check if product category is in applicable categories
            if (!empty($this->applicable_categories)) {
                $product = Product::find($productId);
                if ($product && $product->category_id && in_array($product->category_id, $this->applicable_categories)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function calculatePercentageDiscount($subtotal)
    {
        return ($subtotal * $this->discount_value) / 100;
    }

    private function calculateFixedDiscount($subtotal)
    {
        return min($this->discount_value, $subtotal);
    }

    private function calculateBuyXGetYDiscount($cartItems)
    {
        $discount = 0;
        
        foreach ($cartItems as $item) {
            if ($this->isApplicableProduct($item['product_id'])) {
                $quantity = $item['quantity'];
                $price = $item['price'];
                
                // Calculate how many "buy X get Y" sets
                $sets = intval($quantity / $this->buy_quantity);
                $freeItems = $sets * $this->get_quantity;
                
                // Calculate discount for free items
                $discount += $freeItems * $price * ($this->get_discount_percentage / 100);
            }
        }

        return $discount;
    }

    private function calculateBulkDiscount($cartItems)
    {
        $totalQuantity = 0;
        $applicableItems = [];

        foreach ($cartItems as $item) {
            if ($this->isApplicableProduct($item['product_id'])) {
                $totalQuantity += $item['quantity'];
                $applicableItems[] = $item;
            }
        }

        if ($totalQuantity < $this->minimum_quantity) {
            return 0;
        }

        // Calculate discount based on total quantity
        $discount = 0;
        foreach ($applicableItems as $item) {
            $discount += ($item['price'] * $item['quantity']) * ($this->discount_value / 100);
        }

        return $discount;
    }

    private function isApplicableProduct($productId)
    {
        if (empty($this->applicable_products) && empty($this->applicable_categories)) {
            return true;
        }

        if (!empty($this->applicable_products) && in_array($productId, $this->applicable_products)) {
            return true;
        }

        if (!empty($this->applicable_categories)) {
            $product = Product::find($productId);
            if ($product && $product->category_id && in_array($product->category_id, $this->applicable_categories)) {
                return true;
            }
        }

        return false;
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'promotion_products');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'promotion_categories');
    }
}