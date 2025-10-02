<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyticsEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_type',
        'event_name',
        'event_data',
        'page_url',
        'page_title',
        'user_agent',
        'ip_address',
        'referrer',
        'session_id',
        'user_id',
        'device_info',
        'location_info',
    ];

    protected $casts = [
        'event_data' => 'array',
        'device_info' => 'array',
        'location_info' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Get events by type
     */
    public static function getByType($type, $days = 30)
    {
        return static::where('event_type', $type)
                    ->where('created_at', '>=', now()->subDays($days))
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    /**
     * Get page views
     */
    public static function getPageViews($days = 30)
    {
        return static::where('event_type', 'page_view')
                    ->where('created_at', '>=', now()->subDays($days))
                    ->get();
    }

    /**
     * Get top pages
     */
    public static function getTopPages($days = 30, $limit = 10)
    {
        return static::where('event_type', 'page_view')
                    ->where('created_at', '>=', now()->subDays($days))
                    ->selectRaw('page_url, page_title, COUNT(*) as views')
                    ->groupBy('page_url', 'page_title')
                    ->orderBy('views', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Get events by page
     */
    public static function getByPage($pageUrl, $days = 30)
    {
        return static::where('page_url', $pageUrl)
                    ->where('created_at', '>=', now()->subDays($days))
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    /**
     * Get conversion events
     */
    public static function getConversions($days = 30)
    {
        return static::whereIn('event_type', ['form_submit', 'booking', 'purchase'])
                    ->where('created_at', '>=', now()->subDays($days))
                    ->get();
    }

    /**
     * Get events by user
     */
    public static function getByUser($userId, $days = 30)
    {
        return static::where('user_id', $userId)
                    ->where('created_at', '>=', now()->subDays($days))
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    /**
     * Get events by session
     */
    public static function getBySession($sessionId)
    {
        return static::where('session_id', $sessionId)
                    ->orderBy('created_at', 'asc')
                    ->get();
    }

    /**
     * Get daily stats
     */
    public static function getDailyStats($days = 30)
    {
        return static::where('created_at', '>=', now()->subDays($days))
                    ->selectRaw('DATE(created_at) as date, event_type, COUNT(*) as count')
                    ->groupBy('date', 'event_type')
                    ->orderBy('date', 'desc')
                    ->get();
    }

    /**
     * Get hourly stats
     */
    public static function getHourlyStats($days = 7)
    {
        return static::where('created_at', '>=', now()->subDays($days))
                    ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
                    ->groupBy('hour')
                    ->orderBy('hour')
                    ->get();
    }

    /**
     * Get device stats
     */
    public static function getDeviceStats($days = 30)
    {
        return static::where('created_at', '>=', now()->subDays($days))
                    ->whereNotNull('device_info')
                    ->get()
                    ->groupBy(function ($event) {
                        $deviceInfo = $event->device_info ?? [];
                        return $deviceInfo['device_type'] ?? 'unknown';
                    })
                    ->map(function ($events) {
                        return $events->count();
                    });
    }

    /**
     * Get location stats
     */
    public static function getLocationStats($days = 30)
    {
        return static::where('created_at', '>=', now()->subDays($days))
                    ->whereNotNull('location_info')
                    ->get()
                    ->groupBy(function ($event) {
                        $locationInfo = $event->location_info ?? [];
                        return $locationInfo['country'] ?? 'unknown';
                    })
                    ->map(function ($events) {
                        return $events->count();
                    });
    }

    /**
     * Get referrer stats
     */
    public static function getReferrerStats($days = 30)
    {
        return static::where('created_at', '>=', now()->subDays($days))
                    ->whereNotNull('referrer')
                    ->selectRaw('referrer, COUNT(*) as count')
                    ->groupBy('referrer')
                    ->orderBy('count', 'desc')
                    ->limit(20)
                    ->get();
    }

    /**
     * Get bounce rate
     */
    public static function getBounceRate($days = 30)
    {
        $totalSessions = AnalyticsSession::where('started_at', '>=', now()->subDays($days))->count();
        $bouncedSessions = AnalyticsSession::where('started_at', '>=', now()->subDays($days))
                                          ->where('is_bounce', true)
                                          ->count();

        return $totalSessions > 0 ? ($bouncedSessions / $totalSessions) * 100 : 0;
    }

    /**
     * Get average session duration
     */
    public static function getAverageSessionDuration($days = 30)
    {
        return AnalyticsSession::where('started_at', '>=', now()->subDays($days))
                              ->where('is_bounce', false)
                              ->avg('duration');
    }

    /**
     * Get conversion rate
     */
    public static function getConversionRate($days = 30)
    {
        $totalSessions = AnalyticsSession::where('started_at', '>=', now()->subDays($days))->count();
        $conversions = static::getConversions($days)->count();

        return $totalSessions > 0 ? ($conversions / $totalSessions) * 100 : 0;
    }
}
