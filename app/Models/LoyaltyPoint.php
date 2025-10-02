<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class LoyaltyPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'appointment_id',
        'invoice_id',
        'transaction_type',
        'points',
        'source',
        'description',
        'transaction_value',
        'points_per_dollar',
        'expiry_date',
        'is_expired',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'transaction_value' => 'decimal:2',
            'points_per_dollar' => 'decimal:2',
            'expiry_date' => 'date',
            'is_expired' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
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
    public function isEarned()
    {
        return $this->transaction_type === 'earned';
    }

    public function isRedeemed()
    {
        return $this->transaction_type === 'redeemed';
    }

    public function isExpired()
    {
        return $this->transaction_type === 'expired' || $this->is_expired;
    }

    public function isAdjusted()
    {
        return $this->transaction_type === 'adjusted';
    }

    public function getTransactionTypeDisplayAttribute()
    {
        return match($this->transaction_type) {
            'earned' => 'Earned',
            'redeemed' => 'Redeemed',
            'expired' => 'Expired',
            'adjusted' => 'Adjusted',
            default => 'Unknown'
        };
    }

    public function getSourceDisplayAttribute()
    {
        return match($this->source) {
            'appointment' => 'Appointment',
            'purchase' => 'Purchase',
            'referral' => 'Referral',
            'bonus' => 'Bonus',
            'manual_adjustment' => 'Manual Adjustment',
            'birthday' => 'Birthday Bonus',
            'anniversary' => 'Anniversary Bonus',
            'review' => 'Review Bonus',
            default => 'Unknown'
        };
    }

    public function getPointsDisplayAttribute()
    {
        $prefix = $this->points > 0 ? '+' : '';
        return $prefix . number_format($this->points);
    }

    public function getExpiryStatusAttribute()
    {
        if (!$this->expiry_date) {
            return 'Never';
        }

        if ($this->is_expired) {
            return 'Expired';
        }

        $daysUntilExpiry = now()->diffInDays($this->expiry_date, false);
        
        if ($daysUntilExpiry < 0) {
            return 'Expired';
        } elseif ($daysUntilExpiry === 0) {
            return 'Expires Today';
        } elseif ($daysUntilExpiry <= 7) {
            return "Expires in {$daysUntilExpiry} days";
        } else {
            return $this->expiry_date->format('M j, Y');
        }
    }

    // Static methods for loyalty point management
    public static function getClientBalance($clientId)
    {
        return self::where('client_id', $clientId)
            ->where('is_expired', false)
            ->sum('points');
    }

    public static function getClientExpiredPoints($clientId)
    {
        return self::where('client_id', $clientId)
            ->where('is_expired', true)
            ->sum('points');
    }

    public static function getClientTotalEarned($clientId)
    {
        return self::where('client_id', $clientId)
            ->where('transaction_type', 'earned')
            ->sum('points');
    }

    public static function getClientTotalRedeemed($clientId)
    {
        return abs(self::where('client_id', $clientId)
            ->where('transaction_type', 'redeemed')
            ->sum('points'));
    }

    public static function earnPoints($clientId, $points, $source, $options = [])
    {
        $expiryDate = $options['expiry_date'] ?? now()->addYear(); // Default 1 year expiry
        
        return self::create([
            'client_id' => $clientId,
            'appointment_id' => $options['appointment_id'] ?? null,
            'invoice_id' => $options['invoice_id'] ?? null,
            'transaction_type' => 'earned',
            'points' => abs($points), // Ensure positive
            'source' => $source,
            'description' => $options['description'] ?? null,
            'transaction_value' => $options['transaction_value'] ?? null,
            'points_per_dollar' => $options['points_per_dollar'] ?? 1.00,
            'expiry_date' => $expiryDate,
            'is_expired' => false,
            'metadata' => $options['metadata'] ?? null,
        ]);
    }

    public static function redeemPoints($clientId, $points, $description = null, $options = [])
    {
        $balance = self::getClientBalance($clientId);
        
        if ($balance < $points) {
            throw new \Exception("Insufficient points balance. Available: {$balance}, Requested: {$points}");
        }

        return self::create([
            'client_id' => $clientId,
            'appointment_id' => $options['appointment_id'] ?? null,
            'invoice_id' => $options['invoice_id'] ?? null,
            'transaction_type' => 'redeemed',
            'points' => -abs($points), // Ensure negative
            'source' => 'redemption',
            'description' => $description ?? "Points redeemed",
            'transaction_value' => $options['transaction_value'] ?? null,
            'points_per_dollar' => $options['points_per_dollar'] ?? 1.00,
            'expiry_date' => null,
            'is_expired' => false,
            'metadata' => $options['metadata'] ?? null,
        ]);
    }

    public static function adjustPoints($clientId, $points, $description, $options = [])
    {
        return self::create([
            'client_id' => $clientId,
            'appointment_id' => $options['appointment_id'] ?? null,
            'invoice_id' => $options['invoice_id'] ?? null,
            'transaction_type' => 'adjusted',
            'points' => $points, // Can be positive or negative
            'source' => 'manual_adjustment',
            'description' => $description,
            'transaction_value' => $options['transaction_value'] ?? null,
            'points_per_dollar' => $options['points_per_dollar'] ?? 1.00,
            'expiry_date' => $options['expiry_date'] ?? now()->addYear(),
            'is_expired' => false,
            'metadata' => $options['metadata'] ?? null,
        ]);
    }

    public static function expirePoints($clientId, $points, $description = null)
    {
        return self::create([
            'client_id' => $clientId,
            'transaction_type' => 'expired',
            'points' => -abs($points), // Ensure negative
            'source' => 'expiration',
            'description' => $description ?? "Points expired",
            'expiry_date' => now(),
            'is_expired' => true,
        ]);
    }

    public static function processExpiredPoints()
    {
        $expiredPoints = self::where('expiry_date', '<', now())
            ->where('is_expired', false)
            ->where('transaction_type', 'earned')
            ->get();

        foreach ($expiredPoints as $point) {
            $point->update(['is_expired' => true]);
            
            // Create an expiration record
            self::expirePoints(
                $point->client_id,
                $point->points,
                "Points expired on {$point->expiry_date->format('M j, Y')}"
            );
        }

        return $expiredPoints->count();
    }

    public static function getClientTransactionHistory($clientId, $limit = 50)
    {
        return self::where('client_id', $clientId)
            ->with(['appointment', 'invoice'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public static function getTopClientsByPoints($limit = 10)
    {
        return self::select('client_id')
            ->selectRaw('SUM(CASE WHEN transaction_type = "earned" AND is_expired = 0 THEN points ELSE 0 END) as total_earned')
            ->selectRaw('SUM(CASE WHEN transaction_type = "redeemed" THEN ABS(points) ELSE 0 END) as total_redeemed')
            ->selectRaw('SUM(CASE WHEN transaction_type = "earned" AND is_expired = 0 THEN points ELSE 0 END) - SUM(CASE WHEN transaction_type = "redeemed" THEN ABS(points) ELSE 0 END) as current_balance')
            ->groupBy('client_id')
            ->orderByDesc('current_balance')
            ->limit($limit)
            ->with('client.user')
            ->get();
    }

    public static function getLoyaltyStatistics($startDate = null, $endDate = null)
    {
        $query = self::query();

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return [
            'total_earned' => $query->where('transaction_type', 'earned')->sum('points'),
            'total_redeemed' => abs($query->where('transaction_type', 'redeemed')->sum('points')),
            'total_expired' => abs($query->where('transaction_type', 'expired')->sum('points')),
            'total_adjusted' => $query->where('transaction_type', 'adjusted')->sum('points'),
            'active_balance' => $query->where('transaction_type', 'earned')
                ->where('is_expired', false)
                ->sum('points') - abs($query->where('transaction_type', 'redeemed')->sum('points')),
            'expiring_soon' => $query->where('expiry_date', '<=', now()->addDays(30))
                ->where('is_expired', false)
                ->where('transaction_type', 'earned')
                ->sum('points'),
        ];
    }
}