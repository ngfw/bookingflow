<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Illuminate\Support\Str;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
        'referrer_id',
        'referred_id',
        'referred_email',
        'referred_name',
        'referred_phone',
        'referral_code',
        'status',
        'referral_method',
        'notes',
        'expiry_date',
        'completed_date',
        'completed_appointment_id',
        'completed_invoice_id',
        'referrer_reward_amount',
        'referred_reward_amount',
        'referrer_points',
        'referred_points',
        'referrer_reward_claimed',
        'referred_reward_claimed',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'expiry_date' => 'date',
            'completed_date' => 'date',
            'referrer_reward_amount' => 'decimal:2',
            'referred_reward_amount' => 'decimal:2',
            'referrer_reward_claimed' => 'boolean',
            'referred_reward_claimed' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function referrer()
    {
        return $this->belongsTo(Client::class, 'referrer_id');
    }

    public function referred()
    {
        return $this->belongsTo(Client::class, 'referred_id');
    }

    public function completedAppointment()
    {
        return $this->belongsTo(Appointment::class, 'completed_appointment_id');
    }

    public function completedInvoice()
    {
        return $this->belongsTo(Invoice::class, 'completed_invoice_id');
    }

    // Helper methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
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
            'completed' => 'Completed',
            'expired' => 'Expired',
            'cancelled' => 'Cancelled',
            default => 'Unknown'
        };
    }

    public function getReferralMethodDisplayAttribute()
    {
        return match($this->referral_method) {
            'code' => 'Referral Code',
            'link' => 'Referral Link',
            'manual' => 'Manual Entry',
            'social_media' => 'Social Media',
            'email' => 'Email',
            'sms' => 'SMS',
            default => 'Unknown'
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'completed' => 'green',
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

    public function getReferralUrlAttribute()
    {
        return url('/referral/' . $this->referral_code);
    }

    // Static methods for referral management
    public static function createReferral($referrerId, $referredData, $options = [])
    {
        $referralCode = self::generateReferralCode();
        $expiryDate = $options['expiry_date'] ?? now()->addMonths(3); // Default 3 months

        return self::create([
            'referrer_id' => $referrerId,
            'referred_email' => $referredData['email'] ?? null,
            'referred_name' => $referredData['name'] ?? null,
            'referred_phone' => $referredData['phone'] ?? null,
            'referral_code' => $referralCode,
            'status' => 'pending',
            'referral_method' => $options['method'] ?? 'code',
            'notes' => $options['notes'] ?? null,
            'expiry_date' => $expiryDate,
            'referrer_reward_amount' => $options['referrer_reward'] ?? 0,
            'referred_reward_amount' => $options['referred_reward'] ?? 0,
            'referrer_points' => $options['referrer_points'] ?? 0,
            'referred_points' => $options['referred_points'] ?? 0,
            'metadata' => $options['metadata'] ?? null,
        ]);
    }

    public static function generateReferralCode()
    {
        do {
            $code = 'REF-' . strtoupper(Str::random(8));
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }

    public function complete($referredClientId, $appointmentId = null, $invoiceId = null)
    {
        if ($this->status !== 'pending') {
            throw new \Exception('Referral is not in pending status.');
        }

        if ($this->isExpiredByDate()) {
            throw new \Exception('Referral has expired.');
        }

        $this->update([
            'status' => 'completed',
            'referred_id' => $referredClientId,
            'completed_date' => now(),
            'completed_appointment_id' => $appointmentId,
            'completed_invoice_id' => $invoiceId,
        ]);

        // Award points to both referrer and referred
        if ($this->referrer_points > 0) {
            LoyaltyPoint::earnPoints(
                $this->referrer_id,
                $this->referrer_points,
                'referral',
                [
                    'description' => 'Referral bonus - new client signed up',
                    'metadata' => [
                        'referral_id' => $this->id,
                        'referred_client' => $referredClientId,
                    ],
                ]
            );
        }

        if ($this->referred_points > 0) {
            LoyaltyPoint::earnPoints(
                $referredClientId,
                $this->referred_points,
                'referral',
                [
                    'description' => 'Welcome bonus - referred by existing client',
                    'metadata' => [
                        'referral_id' => $this->id,
                        'referrer_client' => $this->referrer_id,
                    ],
                ]
            );
        }

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

    public function claimReferrerReward()
    {
        if ($this->referrer_reward_claimed) {
            throw new \Exception('Referrer reward already claimed.');
        }

        if ($this->status !== 'completed') {
            throw new \Exception('Referral must be completed to claim reward.');
        }

        $this->update(['referrer_reward_claimed' => true]);
        return $this;
    }

    public function claimReferredReward()
    {
        if ($this->referred_reward_claimed) {
            throw new \Exception('Referred reward already claimed.');
        }

        if ($this->status !== 'completed') {
            throw new \Exception('Referral must be completed to claim reward.');
        }

        $this->update(['referred_reward_claimed' => true]);
        return $this;
    }

    public static function processExpiredReferrals()
    {
        $expiredReferrals = self::where('expiry_date', '<', now())
            ->where('status', 'pending')
            ->get();

        foreach ($expiredReferrals as $referral) {
            $referral->expire();
        }

        return $expiredReferrals->count();
    }

    public static function getReferralStatistics($startDate = null, $endDate = null)
    {
        $query = self::query();

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return [
            'total_referrals' => $query->count(),
            'pending' => $query->where('status', 'pending')->count(),
            'completed' => $query->where('status', 'completed')->count(),
            'expired' => $query->where('status', 'expired')->count(),
            'cancelled' => $query->where('status', 'cancelled')->count(),
            'completion_rate' => $query->count() > 0 ? round(($query->where('status', 'completed')->count() / $query->count()) * 100, 2) : 0,
            'total_referrer_points' => $query->where('status', 'completed')->sum('referrer_points'),
            'total_referred_points' => $query->where('status', 'completed')->sum('referred_points'),
            'total_referrer_rewards' => $query->where('status', 'completed')->sum('referrer_reward_amount'),
            'total_referred_rewards' => $query->where('status', 'completed')->sum('referred_reward_amount'),
        ];
    }

    public static function getTopReferrers($limit = 10)
    {
        return self::select('referrer_id')
            ->selectRaw('COUNT(*) as total_referrals')
            ->selectRaw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_referrals')
            ->selectRaw('SUM(CASE WHEN status = "completed" THEN referrer_points ELSE 0 END) as total_points_earned')
            ->selectRaw('SUM(CASE WHEN status = "completed" THEN referrer_reward_amount ELSE 0 END) as total_rewards_earned')
            ->groupBy('referrer_id')
            ->orderByDesc('completed_referrals')
            ->limit($limit)
            ->with('referrer.user')
            ->get();
    }

    public static function getReferralMethods()
    {
        return [
            'code' => 'Referral Code',
            'link' => 'Referral Link',
            'manual' => 'Manual Entry',
            'social_media' => 'Social Media',
            'email' => 'Email',
            'sms' => 'SMS',
        ];
    }

    public static function getDefaultReferralSettings()
    {
        return [
            'referrer_points' => 100,
            'referred_points' => 50,
            'referrer_reward_amount' => 10.00,
            'referred_reward_amount' => 5.00,
            'expiry_days' => 90,
            'min_appointment_value' => 50.00,
        ];
    }
}