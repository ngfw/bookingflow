<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class CashDrawer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'opened_at',
        'closed_at',
        'opening_amount',
        'closing_amount',
        'expected_amount',
        'difference',
        'status',
        'opening_notes',
        'closing_notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'opened_at' => 'datetime:H:i:s',
            'closed_at' => 'datetime:H:i:s',
            'opening_amount' => 'decimal:2',
            'closing_amount' => 'decimal:2',
            'expected_amount' => 'decimal:2',
            'difference' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isOpen()
    {
        return $this->status === 'open';
    }

    public function isClosed()
    {
        return $this->status === 'closed';
    }

    public function calculateExpectedAmount()
    {
        $startOfDay = Carbon::parse($this->date . ' ' . $this->opened_at);
        $endOfDay = $this->closed_at ? 
            Carbon::parse($this->date . ' ' . $this->closed_at) : 
            Carbon::now();

        // Calculate total cash sales for this drawer session
        $cashSales = Payment::where('payment_method', 'cash')
            ->where('payment_date', '>=', $startOfDay)
            ->where('payment_date', '<=', $endOfDay)
            ->whereHas('invoice', function($query) {
                $query->where('created_by', $this->user_id);
            })
            ->sum('amount');

        $this->expected_amount = $this->opening_amount + $cashSales;
        $this->save();

        return $this->expected_amount;
    }

    public function calculateDifference()
    {
        if ($this->closing_amount !== null && $this->expected_amount !== null) {
            $this->difference = $this->closing_amount - $this->expected_amount;
            $this->save();
        }

        return $this->difference;
    }

    public function close($closingAmount, $notes = null)
    {
        $this->closed_at = Carbon::now()->format('H:i:s');
        $this->closing_amount = $closingAmount;
        $this->closing_notes = $notes;
        $this->status = 'closed';
        
        $this->calculateExpectedAmount();
        $this->calculateDifference();
        
        $this->save();
    }

    public function getTotalCashSales()
    {
        $startOfDay = Carbon::parse($this->date . ' ' . $this->opened_at);
        $endOfDay = $this->closed_at ? 
            Carbon::parse($this->date . ' ' . $this->closed_at) : 
            Carbon::now();

        return Payment::where('payment_method', 'cash')
            ->where('payment_date', '>=', $startOfDay)
            ->where('payment_date', '<=', $endOfDay)
            ->whereHas('invoice', function($query) {
                $query->where('created_by', $this->user_id);
            })
            ->sum('amount');
    }

    public function getTotalTransactions()
    {
        $startOfDay = Carbon::parse($this->date . ' ' . $this->opened_at);
        $endOfDay = $this->closed_at ? 
            Carbon::parse($this->date . ' ' . $this->closed_at) : 
            Carbon::now();

        return Payment::where('payment_date', '>=', $startOfDay)
            ->where('payment_date', '<=', $endOfDay)
            ->whereHas('invoice', function($query) {
                $query->where('created_by', $this->user_id);
            })
            ->count();
    }

    public function getTotalRevenue()
    {
        $startOfDay = Carbon::parse($this->date . ' ' . $this->opened_at);
        $endOfDay = $this->closed_at ? 
            Carbon::parse($this->date . ' ' . $this->closed_at) : 
            Carbon::now();

        return Payment::where('payment_date', '>=', $startOfDay)
            ->where('payment_date', '<=', $endOfDay)
            ->whereHas('invoice', function($query) {
                $query->where('created_by', $this->user_id);
            })
            ->sum('amount');
    }

    public function getPaymentMethodBreakdown()
    {
        $startOfDay = Carbon::parse($this->date . ' ' . $this->opened_at);
        $endOfDay = $this->closed_at ? 
            Carbon::parse($this->date . ' ' . $this->closed_at) : 
            Carbon::now();

        return Payment::where('payment_date', '>=', $startOfDay)
            ->where('payment_date', '<=', $endOfDay)
            ->whereHas('invoice', function($query) {
                $query->where('created_by', $this->user_id);
            })
            ->selectRaw('payment_method, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('payment_method')
            ->get();
    }

    public static function getCurrentDrawer($userId = null)
    {
        $userId = $userId ?? auth()->id();
        
        return self::where('user_id', $userId)
            ->where('date', Carbon::today())
            ->where('status', 'open')
            ->first();
    }

    public static function openDrawer($userId, $openingAmount, $notes = null)
    {
        // Check if drawer is already open for today
        $existingDrawer = self::getCurrentDrawer($userId);
        if ($existingDrawer) {
            throw new \Exception('Cash drawer is already open for today.');
        }

        return self::create([
            'user_id' => $userId,
            'date' => Carbon::today(),
            'opened_at' => Carbon::now()->format('H:i:s'),
            'opening_amount' => $openingAmount,
            'status' => 'open',
            'opening_notes' => $notes,
        ]);
    }
}