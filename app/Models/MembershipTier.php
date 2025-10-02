<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class MembershipTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'icon',
        'min_points',
        'max_points',
        'min_spent',
        'max_spent',
        'min_visits',
        'max_visits',
        'discount_percentage',
        'discount_amount',
        'bonus_points_multiplier',
        'free_shipping',
        'priority_booking',
        'exclusive_services',
        'birthday_bonus',
        'anniversary_bonus',
        'benefits',
        'restrictions',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'min_spent' => 'decimal:2',
            'max_spent' => 'decimal:2',
            'discount_percentage' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'bonus_points_multiplier' => 'decimal:2',
            'free_shipping' => 'boolean',
            'priority_booking' => 'boolean',
            'exclusive_services' => 'boolean',
            'birthday_bonus' => 'boolean',
            'anniversary_bonus' => 'boolean',
            'benefits' => 'array',
            'restrictions' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function clientMemberships()
    {
        return $this->hasMany(ClientMembership::class);
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_memberships')
                    ->withPivot(['start_date', 'end_date', 'status', 'total_spent', 'total_visits', 'total_points_earned', 'last_visit_date', 'next_review_date', 'metadata'])
                    ->withTimestamps();
    }

    // Helper methods
    public function isActive()
    {
        return $this->is_active;
    }

    public function getDisplayNameAttribute()
    {
        return $this->name;
    }

    public function getTierLevelAttribute()
    {
        return $this->sort_order;
    }

    public function getNextTier()
    {
        return self::where('sort_order', '>', $this->sort_order)
                   ->where('is_active', true)
                   ->orderBy('sort_order')
                   ->first();
    }

    public function getPreviousTier()
    {
        return self::where('sort_order', '<', $this->sort_order)
                   ->where('is_active', true)
                   ->orderByDesc('sort_order')
                   ->first();
    }

    public function calculateDiscount($amount)
    {
        $discount = 0;
        
        if ($this->discount_percentage > 0) {
            $discount = ($amount * $this->discount_percentage) / 100;
        }
        
        if ($this->discount_amount > 0) {
            $discount = max($discount, $this->discount_amount);
        }
        
        return min($discount, $amount); // Don't exceed the total amount
    }

    public function calculateBonusPoints($basePoints)
    {
        return $basePoints * $this->bonus_points_multiplier;
    }

    public function meetsRequirements($points, $spent, $visits)
    {
        if ($this->min_points > 0 && $points < $this->min_points) {
            return false;
        }
        
        if ($this->max_points && $points > $this->max_points) {
            return false;
        }
        
        if ($this->min_spent > 0 && $spent < $this->min_spent) {
            return false;
        }
        
        if ($this->max_spent && $spent > $this->max_spent) {
            return false;
        }
        
        if ($this->min_visits > 0 && $visits < $this->min_visits) {
            return false;
        }
        
        if ($this->max_visits && $visits > $this->max_visits) {
            return false;
        }
        
        return true;
    }

    public function getProgressToNextTier($points, $spent, $visits)
    {
        $nextTier = $this->getNextTier();
        
        if (!$nextTier) {
            return null; // Already at highest tier
        }
        
        $progress = [];
        
        if ($nextTier->min_points > 0) {
            $progress['points'] = [
                'current' => $points,
                'required' => $nextTier->min_points,
                'percentage' => min(100, ($points / $nextTier->min_points) * 100),
            ];
        }
        
        if ($nextTier->min_spent > 0) {
            $progress['spent'] = [
                'current' => $spent,
                'required' => $nextTier->min_spent,
                'percentage' => min(100, ($spent / $nextTier->min_spent) * 100),
            ];
        }
        
        if ($nextTier->min_visits > 0) {
            $progress['visits'] = [
                'current' => $visits,
                'required' => $nextTier->min_visits,
                'percentage' => min(100, ($visits / $nextTier->min_visits) * 100),
            ];
        }
        
        return $progress;
    }

    // Static methods
    public static function getTierForClient($clientId)
    {
        $client = Client::find($clientId);
        
        if (!$client) {
            return null;
        }
        
        $points = LoyaltyPoint::getClientBalance($clientId);
        $spent = $client->total_spent ?? 0;
        $visits = $client->total_visits ?? 0;
        
        return self::getTierForMetrics($points, $spent, $visits);
    }

    public static function getTierForMetrics($points, $spent, $visits)
    {
        return self::where('is_active', true)
                   ->orderByDesc('sort_order')
                   ->get()
                   ->first(function ($tier) use ($points, $spent, $visits) {
                       return $tier->meetsRequirements($points, $spent, $visits);
                   });
    }

    public static function getAllTiers()
    {
        return self::where('is_active', true)
                   ->orderBy('sort_order')
                   ->get();
    }

    public static function getDefaultTiers()
    {
        return [
            [
                'name' => 'Bronze',
                'slug' => 'bronze',
                'description' => 'Our entry-level membership with basic benefits',
                'color' => '#CD7F32',
                'icon' => 'fas fa-medal',
                'min_points' => 0,
                'max_points' => 499,
                'min_spent' => 0,
                'max_spent' => 999.99,
                'min_visits' => 0,
                'max_visits' => 9,
                'discount_percentage' => 0,
                'discount_amount' => 0,
                'bonus_points_multiplier' => 1,
                'free_shipping' => false,
                'priority_booking' => false,
                'exclusive_services' => false,
                'birthday_bonus' => false,
                'anniversary_bonus' => false,
                'benefits' => [
                    'Basic customer support',
                    'Standard booking times',
                ],
                'restrictions' => [],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Silver',
                'slug' => 'silver',
                'description' => 'Enhanced membership with additional perks',
                'color' => '#C0C0C0',
                'icon' => 'fas fa-medal',
                'min_points' => 500,
                'max_points' => 1499,
                'min_spent' => 1000,
                'max_spent' => 2999.99,
                'min_visits' => 10,
                'max_visits' => 24,
                'discount_percentage' => 5,
                'discount_amount' => 0,
                'bonus_points_multiplier' => 1.25,
                'free_shipping' => false,
                'priority_booking' => true,
                'exclusive_services' => false,
                'birthday_bonus' => true,
                'anniversary_bonus' => false,
                'benefits' => [
                    '5% discount on services',
                    'Priority booking',
                    'Birthday bonus',
                    'Enhanced customer support',
                ],
                'restrictions' => [],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Gold',
                'slug' => 'gold',
                'description' => 'Premium membership with exclusive benefits',
                'color' => '#FFD700',
                'icon' => 'fas fa-medal',
                'min_points' => 1500,
                'max_points' => 4999,
                'min_spent' => 3000,
                'max_spent' => 9999.99,
                'min_visits' => 25,
                'max_visits' => 49,
                'discount_percentage' => 10,
                'discount_amount' => 0,
                'bonus_points_multiplier' => 1.5,
                'free_shipping' => true,
                'priority_booking' => true,
                'exclusive_services' => true,
                'birthday_bonus' => true,
                'anniversary_bonus' => true,
                'benefits' => [
                    '10% discount on services',
                    'Free shipping on products',
                    'Priority booking',
                    'Exclusive services access',
                    'Birthday and anniversary bonuses',
                    'VIP customer support',
                ],
                'restrictions' => [],
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Platinum',
                'slug' => 'platinum',
                'description' => 'Ultimate membership with maximum benefits',
                'color' => '#E5E4E2',
                'icon' => 'fas fa-crown',
                'min_points' => 5000,
                'max_points' => null,
                'min_spent' => 10000,
                'max_spent' => null,
                'min_visits' => 50,
                'max_visits' => null,
                'discount_percentage' => 15,
                'discount_amount' => 0,
                'bonus_points_multiplier' => 2,
                'free_shipping' => true,
                'priority_booking' => true,
                'exclusive_services' => true,
                'birthday_bonus' => true,
                'anniversary_bonus' => true,
                'benefits' => [
                    '15% discount on services',
                    'Free shipping on products',
                    'Priority booking',
                    'Exclusive services access',
                    'Birthday and anniversary bonuses',
                    'Personal concierge service',
                    'Exclusive events access',
                ],
                'restrictions' => [],
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];
    }

    public static function createDefaultTiers()
    {
        $defaultTiers = self::getDefaultTiers();
        
        foreach ($defaultTiers as $tierData) {
            self::updateOrCreate(
                ['slug' => $tierData['slug']],
                $tierData
            );
        }
        
        return self::getAllTiers();
    }

    public static function getTierStatistics()
    {
        $tiers = self::getAllTiers();
        $stats = [];
        
        foreach ($tiers as $tier) {
            $stats[$tier->slug] = [
                'name' => $tier->name,
                'color' => $tier->color,
                'total_clients' => $tier->clientMemberships()->where('status', 'active')->count(),
                'total_spent' => $tier->clientMemberships()->where('status', 'active')->sum('total_spent'),
                'total_visits' => $tier->clientMemberships()->where('status', 'active')->sum('total_visits'),
                'total_points' => $tier->clientMemberships()->where('status', 'active')->sum('total_points_earned'),
            ];
        }
        
        return $stats;
    }
}