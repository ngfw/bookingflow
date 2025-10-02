<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyticsSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'ip_address',
        'user_agent',
        'device_info',
        'location_info',
        'referrer',
        'landing_page',
        'page_views',
        'duration',
        'is_bounce',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'device_info' => 'array',
        'location_info' => 'array',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'is_bounce' => 'boolean',
    ];

    /**
     * Get sessions by date range
     */
    public static function getByDateRange($startDate, $endDate)
    {
        return static::whereBetween('started_at', [$startDate, $endDate])
                    ->orderBy('started_at', 'desc')
                    ->get();
    }

    /**
     * Get recent sessions
     */
    public static function getRecent($days = 7)
    {
        return static::where('started_at', '>=', now()->subDays($days))
                    ->orderBy('started_at', 'desc')
                    ->get();
    }

    /**
     * Get sessions by user
     */
    public static function getByUser($userId, $days = 30)
    {
        return static::where('user_id', $userId)
                    ->where('started_at', '>=', now()->subDays($days))
                    ->orderBy('started_at', 'desc')
                    ->get();
    }

    /**
     * Get sessions by landing page
     */
    public static function getByLandingPage($landingPage, $days = 30)
    {
        return static::where('landing_page', $landingPage)
                    ->where('started_at', '>=', now()->subDays($days))
                    ->orderBy('started_at', 'desc')
                    ->get();
    }

    /**
     * Get sessions by referrer
     */
    public static function getByReferrer($referrer, $days = 30)
    {
        return static::where('referrer', $referrer)
                    ->where('started_at', '>=', now()->subDays($days))
                    ->orderBy('started_at', 'desc')
                    ->get();
    }

    /**
     * Get daily session counts
     */
    public static function getDailyCounts($days = 30)
    {
        return static::where('started_at', '>=', now()->subDays($days))
                    ->selectRaw('DATE(started_at) as date, COUNT(*) as count')
                    ->groupBy('date')
                    ->orderBy('date', 'desc')
                    ->get();
    }

    /**
     * Get hourly session counts
     */
    public static function getHourlyCounts($days = 7)
    {
        return static::where('started_at', '>=', now()->subDays($days))
                    ->selectRaw('HOUR(started_at) as hour, COUNT(*) as count')
                    ->groupBy('hour')
                    ->orderBy('hour')
                    ->get();
    }

    /**
     * Get device breakdown
     */
    public static function getDeviceBreakdown($days = 30)
    {
        return static::where('started_at', '>=', now()->subDays($days))
                    ->whereNotNull('device_info')
                    ->get()
                    ->groupBy(function ($session) {
                        $deviceInfo = $session->device_info ?? [];
                        return $deviceInfo['device_type'] ?? 'unknown';
                    })
                    ->map(function ($sessions) {
                        return $sessions->count();
                    });
    }

    /**
     * Get location breakdown
     */
    public static function getLocationBreakdown($days = 30)
    {
        return static::where('started_at', '>=', now()->subDays($days))
                    ->whereNotNull('location_info')
                    ->get()
                    ->groupBy(function ($session) {
                        $locationInfo = $session->location_info ?? [];
                        return $locationInfo['country'] ?? 'unknown';
                    })
                    ->map(function ($sessions) {
                        return $sessions->count();
                    });
    }

    /**
     * Get referrer breakdown
     */
    public static function getReferrerBreakdown($days = 30)
    {
        return static::where('started_at', '>=', now()->subDays($days))
                    ->whereNotNull('referrer')
                    ->selectRaw('referrer, COUNT(*) as count')
                    ->groupBy('referrer')
                    ->orderBy('count', 'desc')
                    ->limit(20)
                    ->get();
    }

    /**
     * Get top landing pages
     */
    public static function getTopLandingPages($days = 30, $limit = 10)
    {
        return static::where('started_at', '>=', now()->subDays($days))
                    ->whereNotNull('landing_page')
                    ->selectRaw('landing_page, COUNT(*) as count')
                    ->groupBy('landing_page')
                    ->orderBy('count', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Get average session duration
     */
    public static function getAverageDuration($days = 30)
    {
        return static::where('started_at', '>=', now()->subDays($days))
                    ->where('is_bounce', false)
                    ->avg('duration');
    }

    /**
     * Get bounce rate
     */
    public static function getBounceRate($days = 30)
    {
        $totalSessions = static::where('started_at', '>=', now()->subDays($days))->count();
        $bouncedSessions = static::where('started_at', '>=', now()->subDays($days))
                               ->where('is_bounce', true)
                               ->count();

        return $totalSessions > 0 ? ($bouncedSessions / $totalSessions) * 100 : 0;
    }

    /**
     * Get average page views per session
     */
    public static function getAveragePageViews($days = 30)
    {
        return static::where('started_at', '>=', now()->subDays($days))
                    ->where('is_bounce', false)
                    ->avg('page_views');
    }

    /**
     * Get returning vs new visitors
     */
    public static function getVisitorTypeBreakdown($days = 30)
    {
        $totalSessions = static::where('started_at', '>=', now()->subDays($days))->count();
        $newVisitors = static::where('started_at', '>=', now()->subDays($days))
                           ->whereNull('user_id')
                           ->count();
        $returningVisitors = $totalSessions - $newVisitors;

        return [
            'new' => $newVisitors,
            'returning' => $returningVisitors,
            'new_percentage' => $totalSessions > 0 ? ($newVisitors / $totalSessions) * 100 : 0,
            'returning_percentage' => $totalSessions > 0 ? ($returningVisitors / $totalSessions) * 100 : 0,
        ];
    }

    /**
     * Get session events
     */
    public function events()
    {
        return $this->hasMany(AnalyticsEvent::class, 'session_id', 'session_id');
    }

    /**
     * Get the user that owns the session
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if session is active
     */
    public function isActive()
    {
        return $this->ended_at === null;
    }

    /**
     * End the session
     */
    public function end()
    {
        $this->ended_at = now();
        $this->duration = $this->started_at->diffInSeconds($this->ended_at);
        $this->save();
    }

    /**
     * Increment page views
     */
    public function incrementPageViews()
    {
        $this->increment('page_views');
    }

    /**
     * Mark as bounce
     */
    public function markAsBounce()
    {
        $this->is_bounce = true;
        $this->save();
    }
}
