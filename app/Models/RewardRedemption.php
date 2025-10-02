<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Illuminate\Support\Str;

class RewardRedemption extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'loyalty_point_id',
        'reward_type',
        'reward_name',
        'description',
        'points_required',
        'discount_amount',
        'discount_percentage',
        'cash_value',
        'status',
        'redemption_code',
        'expiry_date',
        'redeemed_date',
        'redeemed_by_staff_id',
        'appointment_id',
        'invoice_id',
        'notes',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'discount_amount' => 'decimal:2',
            'discount_percentage' => 'decimal:2',
            'cash_value' => 'decimal:2',
            'expiry_date' => 'date',
            'redeemed_date' => 'date',
            'metadata' => 'array',
        ];
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function loyaltyPoint()
    {
        return $this->belongsTo(LoyaltyPoint::class);
    }

    public function redeemedByStaff()
    {
        return $this->belongsTo(Staff::class, 'redeemed_by_staff_id');
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // Helper methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRedeemed()
    {
        return $this->status === 'redeemed';
    }

    public function isExpired()
    {
        return $this->status === 'expired';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function isExpiredByDate()
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function getStatusDisplayAttribute()
    {
        return match($this->status) {
            'pending' => 'Pending',
            'approved' => 'Approved',
            'redeemed' => 'Redeemed',
            'expired' => 'Expired',
            'cancelled' => 'Cancelled',
            default => 'Unknown'
        };
    }

    public function getRewardTypeDisplayAttribute()
    {
        return match($this->reward_type) {
            'discount' => 'Discount',
            'product' => 'Product',
            'service' => 'Service',
            'cash_back' => 'Cash Back',
            'gift_card' => 'Gift Card',
            default => 'Unknown'
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'approved' => 'blue',
            'redeemed' => 'green',
            'expired' => 'red',
            'cancelled' => 'gray',
            default => 'gray'
        };
    }

    public function getExpiryStatusAttribute()
    {
        if (!$this->expiry_date) {
            return 'Never';
        }

        if ($this->isExpiredByDate()) {
            return 'Expired';
        }

        $daysUntilExpiry = now()->diffInDays($this->expiry_date, false);
        
        if ($daysUntilExpiry === 0) {
            return 'Expires Today';
        } elseif ($daysUntilExpiry <= 7) {
            return "Expires in {$daysUntilExpiry} days";
        } else {
            return $this->expiry_date->format('M j, Y');
        }
    }

    public function getValueDisplayAttribute()
    {
        if ($this->discount_amount) {
            return '$' . number_format($this->discount_amount, 2);
        } elseif ($this->discount_percentage) {
            return $this->discount_percentage . '%';
        } elseif ($this->cash_value) {
            return '$' . number_format($this->cash_value, 2);
        } else {
            return 'N/A';
        }
    }

    // Static methods for reward redemption management
    public static function createReward($clientId, $rewardType, $rewardName, $pointsRequired, $options = [])
    {
        $redemptionCode = $options['redemption_code'] ?? self::generateRedemptionCode();
        $expiryDate = $options['expiry_date'] ?? now()->addMonths(6); // Default 6 months

        return self::create([
            'client_id' => $clientId,
            'loyalty_point_id' => $options['loyalty_point_id'] ?? null,
            'reward_type' => $rewardType,
            'reward_name' => $rewardName,
            'description' => $options['description'] ?? null,
            'points_required' => $pointsRequired,
            'discount_amount' => $options['discount_amount'] ?? null,
            'discount_percentage' => $options['discount_percentage'] ?? null,
            'cash_value' => $options['cash_value'] ?? null,
            'status' => 'pending',
            'redemption_code' => $redemptionCode,
            'expiry_date' => $expiryDate,
            'notes' => $options['notes'] ?? null,
            'metadata' => $options['metadata'] ?? null,
        ]);
    }

    public static function generateRedemptionCode()
    {
        do {
            $code = 'REWARD-' . strtoupper(Str::random(8));
        } while (self::where('redemption_code', $code)->exists());

        return $code;
    }

    public function approve()
    {
        $this->update(['status' => 'approved']);
        return $this;
    }

    public function redeem($staffId, $appointmentId = null, $invoiceId = null, $notes = null)
    {
        if ($this->status !== 'approved') {
            throw new \Exception('Reward must be approved before redemption.');
        }

        if ($this->isExpiredByDate()) {
            throw new \Exception('Reward has expired.');
        }

        $this->update([
            'status' => 'redeemed',
            'redeemed_date' => now(),
            'redeemed_by_staff_id' => $staffId,
            'appointment_id' => $appointmentId,
            'invoice_id' => $invoiceId,
            'notes' => $notes,
        ]);

        return $this;
    }

    public function cancel($reason = null)
    {
        $this->update([
            'status' => 'cancelled',
            'notes' => $reason ? ($this->notes . "\nCancelled: " . $reason) : $this->notes,
        ]);

        return $this;
    }

    public function expire()
    {
        $this->update(['status' => 'expired']);
        return $this;
    }

    public static function processExpiredRewards()
    {
        $expiredRewards = self::where('expiry_date', '<', now())
            ->whereIn('status', ['pending', 'approved'])
            ->get();

        foreach ($expiredRewards as $reward) {
            $reward->expire();
        }

        return $expiredRewards->count();
    }

    public static function getClientRewards($clientId, $status = null)
    {
        $query = self::where('client_id', $clientId);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public static function getRewardStatistics($startDate = null, $endDate = null)
    {
        $query = self::query();

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return [
            'total_created' => $query->count(),
            'pending' => $query->where('status', 'pending')->count(),
            'approved' => $query->where('status', 'approved')->count(),
            'redeemed' => $query->where('status', 'redeemed')->count(),
            'expired' => $query->where('status', 'expired')->count(),
            'cancelled' => $query->where('status', 'cancelled')->count(),
            'total_points_used' => $query->where('status', 'redeemed')->sum('points_required'),
            'total_value_redeemed' => $query->where('status', 'redeemed')->sum('cash_value'),
        ];
    }

    public static function getRewardTypes()
    {
        return [
            'discount' => 'Discount',
            'product' => 'Product',
            'service' => 'Service',
            'cash_back' => 'Cash Back',
            'gift_card' => 'Gift Card',
        ];
    }

    public static function getDefaultRewards()
    {
        return [
            [
                'reward_type' => 'discount',
                'reward_name' => '10% Off Next Service',
                'points_required' => 100,
                'discount_percentage' => 10.00,
                'description' => 'Get 10% off your next service',
            ],
            [
                'reward_type' => 'discount',
                'reward_name' => '$5 Off Next Purchase',
                'points_required' => 50,
                'discount_amount' => 5.00,
                'description' => 'Get $5 off your next product purchase',
            ],
            [
                'reward_type' => 'service',
                'reward_name' => 'Free Consultation',
                'points_required' => 200,
                'cash_value' => 25.00,
                'description' => 'Free 30-minute consultation with our stylist',
            ],
            [
                'reward_type' => 'product',
                'reward_name' => 'Free Shampoo',
                'points_required' => 150,
                'cash_value' => 15.00,
                'description' => 'Free professional shampoo (250ml)',
            ],
            [
                'reward_type' => 'cash_back',
                'reward_name' => '$10 Cash Back',
                'points_required' => 1000,
                'cash_value' => 10.00,
                'description' => 'Get $10 cash back on your account',
            ],
        ];
    }
}