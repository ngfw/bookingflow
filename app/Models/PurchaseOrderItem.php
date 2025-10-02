<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'quantity_ordered',
        'quantity_received',
        'unit_cost',
        'total_cost',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity_ordered' => 'integer',
            'quantity_received' => 'integer',
            'unit_cost' => 'decimal:2',
            'total_cost' => 'decimal:2',
        ];
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function calculateTotalCost()
    {
        $this->total_cost = $this->quantity_ordered * $this->unit_cost;
        $this->save();
    }

    public function isFullyReceived()
    {
        return $this->quantity_received >= $this->quantity_ordered;
    }

    public function getRemainingQuantity()
    {
        return max(0, $this->quantity_ordered - $this->quantity_received);
    }
}
