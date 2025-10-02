<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class ClientMembership extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'membership_tier_id',
        'start_date',
        'end_date',
        'status',
        'total_spent',
        'total_visits',
        'total_points_earned',
        'last_visit_date',
        'next_review_date',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'total_spent' => 'decimal:2',
            'last_visit_date' => 'date',
            'next_review_date' => 'date',
            'metadata' => 'array',
        ];
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function membershipTier()
    {
        return $this->belongsTo(MembershipTier::class);
    }

    // Helper methods
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isExpired()
    {
        return $this->status === 'expired';
    }

    public function isSuspended()
    {
        return $this->status === 'suspended';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function isExpiredByDate()
    {
        return $this->end_date && $this->end_date->isPast();
    }

    public function getStatusDisplayAttribute()
    {
        return match($this->status) {
            'active' => 'Active',
            'expired' => 'Expired',
            'suspended' => 'Suspended',
            'cancelled' => 'Cancelled',
            default => 'Unknown'
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'active' => 'green',
            'expired' => 'red',
            'suspended' => 'yellow',
            'cancelled' => 'gray',
            default => 'gray'
        };
    }

    public function getDurationAttribute()
    {
        if (!$this->end_date) {
            return 'Lifetime';
        }
        
        return $this->start_date->diffInDays($this->end_date) . ' days';
    }

    public function getDaysRemainingAttribute()
    {
        if (!$this->end_date) {
            return null;
        }
        
        $days = now()->diffInDays($this->end_date, false);
        return $days > 0 ? $days : 0;
    }

    public function getProgressToNextTierAttribute()
    {
        $nextTier = $this->membershipTier->getNextTier();
        
        if (!$nextTier) {
            return null;
        }
        
        return $this->membershipTier->getProgressToNextTier(
            $this->total_points_earned,
            $this->total_spent,
            $this->total_visits
        );
    }

    public function activate()
    {
        $this->update(['status' => 'active']);
        return $this;
    }

    public function suspend($reason = null)
    {
        $this->update([
            'status' => 'suspended',
            'metadata' => array_merge($this->metadata ?? [], ['suspension_reason' => $reason]),
        ]);
        return $this;
    }

    public function cancel($reason = null)
    {
        $this->update([
            'status' => 'cancelled',
            'metadata' => array_merge($this->metadata ?? [], ['cancellation_reason' => $reason]),
        ]);
        return $this;
    }

    public function expire()
    {
        $this->update(['status' => 'expired']);
        return $this;
    }

    public function updateSpent($amount)
    {
        $this->increment('total_spent', $amount);
        return $this;
    }

    public function updateVisits($count = 1)
    {
        $this->increment('total_visits', $count);
        $this->update(['last_visit_date' => now()]);
        return $this;
    }

    public function updatePoints($points)
    {
        $this->increment('total_points_earned', $points);
        return $this;
    }

    public function setNextReviewDate($days = 30)
    {
        $this->update(['next_review_date' => now()->addDays($days)]);
        return $this;
    }

    public function needsReview()
    {
        return $this->next_review_date && $this->next_review_date->isPast();
    }

    public function canUpgrade()
    {
        $nextTier = $this->membershipTier->getNextTier();
        
        if (!$nextTier) {
            return false;
        }
        
        return $nextTier->meetsRequirements(
            $this->total_points_earned,
            $this->total_spent,
            $this->total_visits
        );
    }

    public function canDowngrade()
    {
        $previousTier = $this->membershipTier->getPreviousTier();
        
        if (!$previousTier) {
            return false;
        }
        
        return !$this->membershipTier->meetsRequirements(
            $this->total_points_earned,
            $this->total_spent,
            $this->total_visits
        );
    }

    public function upgradeToTier($tierId)
    {
        $newTier = MembershipTier::findOrFail($tierId);
        
        if (!$newTier->meetsRequirements($this->total_points_earned, $this->total_spent, $this->total_visits)) {
            throw new \Exception('Client does not meet requirements for this tier.');
        }
        
        // End current membership
        $this->update(['end_date' => now()]);
        
        // Create new membership
        return self::create([
            'client_id' => $this->client_id,
            'membership_tier_id' => $tierId,
            'start_date' => now(),
            'end_date' => null, // Lifetime membership
            'status' => 'active',
            'total_spent' => 0, // Reset counters for new tier
            'total_visits' => 0,
            'total_points_earned' => 0,
            'last_visit_date' => null,
            'next_review_date' => now()->addDays(30),
            'metadata' => [
                'upgraded_from' => $this->membership_tier_id,
                'upgrade_date' => now()->toDateString(),
            ],
        ]);
    }

    // Static methods
    public static function assignTierToClient($clientId, $tierId = null, $startDate = null)
    {
        $client = Client::findOrFail($clientId);
        
        if (!$tierId) {
            $tierId = MembershipTier::getTierForClient($clientId)?->id;
        }
        
        if (!$tierId) {
            throw new \Exception('No suitable tier found for client.');
        }
        
        $tier = MembershipTier::findOrFail($tierId);
        $startDate = $startDate ? Carbon::parse($startDate) : now();
        
        // Check if client already has an active membership
        $existingMembership = self::where('client_id', $clientId)
                                 ->where('status', 'active')
                                 ->first();
        
        if ($existingMembership) {
            // End existing membership
            $existingMembership->update(['end_date' => $startDate->subDay()]);
        }
        
        return self::create([
            'client_id' => $clientId,
            'membership_tier_id' => $tierId,
            'start_date' => $startDate,
            'end_date' => null, // Lifetime membership
            'status' => 'active',
            'total_spent' => 0,
            'total_visits' => 0,
            'total_points_earned' => 0,
            'last_visit_date' => null,
            'next_review_date' => $startDate->addDays(30),
            'metadata' => [
                'assigned_date' => now()->toDateString(),
                'assigned_by' => auth()->id(),
            ],
        ]);
    }

    public static function processExpiredMemberships()
    {
        $expiredMemberships = self::where('end_date', '<', now())
                                 ->where('status', 'active')
                                 ->get();
        
        foreach ($expiredMemberships as $membership) {
            $membership->expire();
        }
        
        return $expiredMemberships->count();
    }

    public static function processTierReviews()
    {
        $membershipsToReview = self::where('next_review_date', '<', now())
                                  ->where('status', 'active')
                                  ->get();
        
        $upgrades = 0;
        $downgrades = 0;
        
        foreach ($membershipsToReview as $membership) {
            if ($membership->canUpgrade()) {
                $nextTier = $membership->membershipTier->getNextTier();
                $membership->upgradeToTier($nextTier->id);
                $upgrades++;
            } elseif ($membership->canDowngrade()) {
                $previousTier = $membership->membershipTier->getPreviousTier();
                $membership->upgradeToTier($previousTier->id);
                $downgrades++;
            }
            
            // Set next review date
            $membership->setNextReviewDate(30);
        }
        
        return [
            'upgrades' => $upgrades,
            'downgrades' => $downgrades,
            'total_reviewed' => $membershipsToReview->count(),
        ];
    }

    public static function getMembershipStatistics($startDate = null, $endDate = null)
    {
        $query = self::query();
        
        if ($startDate) {
            $query->where('start_date', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('start_date', '<=', $endDate);
        }
        
        return [
            'total_memberships' => $query->count(),
            'active' => $query->where('status', 'active')->count(),
            'expired' => $query->where('status', 'expired')->count(),
            'suspended' => $query->where('status', 'suspended')->count(),
            'cancelled' => $query->where('status', 'cancelled')->count(),
            'total_spent' => $query->where('status', 'active')->sum('total_spent'),
            'total_visits' => $query->where('status', 'active')->sum('total_visits'),
            'total_points' => $query->where('status', 'active')->sum('total_points_earned'),
        ];
    }

    public static function getTopTiers($limit = 5)
    {
        return self::select('membership_tier_id')
                   ->selectRaw('COUNT(*) as total_memberships')
                   ->selectRaw('SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) as active_memberships')
                   ->selectRaw('SUM(CASE WHEN status = "active" THEN total_spent ELSE 0 END) as total_spent')
                   ->selectRaw('SUM(CASE WHEN status = "active" THEN total_visits ELSE 0 END) as total_visits')
                   ->groupBy('membership_tier_id')
                   ->orderByDesc('active_memberships')
                   ->limit($limit)
                   ->with('membershipTier')
                   ->get();
    }
}