<?php

namespace App\Services;

use App\Models\AnalyticsEvent;
use App\Models\AnalyticsSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AnalyticsService
{
    /**
     * Track a page view
     */
    public function trackPageView(Request $request, $pageTitle = null)
    {
        $sessionId = $this->getOrCreateSession($request);
        $deviceInfo = $this->getDeviceInfo($request);
        $locationInfo = $this->getLocationInfo($request);

        AnalyticsEvent::create([
            'event_type' => 'page_view',
            'event_name' => 'page_view',
            'page_url' => $request->fullUrl(),
            'page_title' => $pageTitle ?: $request->route()?->getName(),
            'user_agent' => $request->userAgent(),
            'ip_address' => $request->ip(),
            'referrer' => $request->header('referer'),
            'session_id' => $sessionId,
            'user_id' => auth()->id(),
            'device_info' => $deviceInfo,
            'location_info' => $locationInfo,
        ]);

        // Update session
        $session = AnalyticsSession::where('session_id', $sessionId)->first();
        if ($session) {
            $session->incrementPageViews();
        }
    }

    /**
     * Track a custom event
     */
    public function trackEvent(Request $request, $eventType, $eventName, $eventData = [])
    {
        $sessionId = $this->getOrCreateSession($request);
        $deviceInfo = $this->getDeviceInfo($request);
        $locationInfo = $this->getLocationInfo($request);

        AnalyticsEvent::create([
            'event_type' => $eventType,
            'event_name' => $eventName,
            'event_data' => $eventData,
            'page_url' => $request->fullUrl(),
            'page_title' => $request->route()?->getName(),
            'user_agent' => $request->userAgent(),
            'ip_address' => $request->ip(),
            'referrer' => $request->header('referer'),
            'session_id' => $sessionId,
            'user_id' => auth()->id(),
            'device_info' => $deviceInfo,
            'location_info' => $locationInfo,
        ]);
    }

    /**
     * Track a conversion event
     */
    public function trackConversion(Request $request, $conversionType, $conversionData = [])
    {
        $this->trackEvent($request, 'conversion', $conversionType, $conversionData);
    }

    /**
     * Track a form submission
     */
    public function trackFormSubmission(Request $request, $formName, $formData = [])
    {
        $this->trackEvent($request, 'form_submit', $formName, $formData);
    }

    /**
     * Track a booking
     */
    public function trackBooking(Request $request, $bookingData = [])
    {
        $this->trackEvent($request, 'booking', 'appointment_booking', $bookingData);
    }

    /**
     * Get or create a session
     */
    private function getOrCreateSession(Request $request)
    {
        $sessionId = $request->session()->getId();
        
        $session = AnalyticsSession::where('session_id', $sessionId)->first();
        
        if (!$session) {
            $deviceInfo = $this->getDeviceInfo($request);
            $locationInfo = $this->getLocationInfo($request);
            
            $session = AnalyticsSession::create([
                'session_id' => $sessionId,
                'user_id' => auth()->id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'device_info' => $deviceInfo,
                'location_info' => $locationInfo,
                'referrer' => $request->header('referer'),
                'landing_page' => $request->fullUrl(),
                'started_at' => now(),
            ]);
        }
        
        return $sessionId;
    }

    /**
     * Get device information
     */
    private function getDeviceInfo(Request $request)
    {
        $userAgent = $request->userAgent();
        
        // Simple device detection (you might want to use a more sophisticated library)
        $deviceType = 'desktop';
        if (preg_match('/Mobile|Android|iPhone|iPad/', $userAgent)) {
            $deviceType = 'mobile';
        } elseif (preg_match('/Tablet|iPad/', $userAgent)) {
            $deviceType = 'tablet';
        }
        
        $browser = 'unknown';
        if (preg_match('/Chrome/', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Firefox/', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/Safari/', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/Edge/', $userAgent)) {
            $browser = 'Edge';
        }
        
        $os = 'unknown';
        if (preg_match('/Windows/', $userAgent)) {
            $os = 'Windows';
        } elseif (preg_match('/Mac/', $userAgent)) {
            $os = 'macOS';
        } elseif (preg_match('/Linux/', $userAgent)) {
            $os = 'Linux';
        } elseif (preg_match('/Android/', $userAgent)) {
            $os = 'Android';
        } elseif (preg_match('/iOS/', $userAgent)) {
            $os = 'iOS';
        }
        
        return [
            'device_type' => $deviceType,
            'browser' => $browser,
            'os' => $os,
            'user_agent' => $userAgent,
        ];
    }

    /**
     * Get location information (mock implementation)
     */
    private function getLocationInfo(Request $request)
    {
        // In a real implementation, you would use a geolocation service
        // like MaxMind GeoIP2 or similar
        $ip = $request->ip();
        
        // Mock location data
        $mockLocations = [
            '127.0.0.1' => ['country' => 'Local', 'city' => 'Local'],
            '::1' => ['country' => 'Local', 'city' => 'Local'],
        ];
        
        return $mockLocations[$ip] ?? [
            'country' => 'Unknown',
            'city' => 'Unknown',
            'ip' => $ip,
        ];
    }

    /**
     * Get analytics summary
     */
    public function getAnalyticsSummary($days = 30)
    {
        $startDate = now()->subDays($days);
        
        $pageViews = AnalyticsEvent::where('event_type', 'page_view')
                                 ->where('created_at', '>=', $startDate)
                                 ->count();
        
        $sessions = AnalyticsSession::where('started_at', '>=', $startDate)->count();
        
        $uniqueVisitors = AnalyticsSession::where('started_at', '>=', $startDate)
                                        ->distinct('ip_address')
                                        ->count();
        
        $bounceRate = AnalyticsSession::getBounceRate($days);
        $avgSessionDuration = AnalyticsSession::getAverageDuration($days);
        $conversionRate = AnalyticsEvent::getConversionRate($days);
        
        return [
            'page_views' => $pageViews,
            'sessions' => $sessions,
            'unique_visitors' => $uniqueVisitors,
            'bounce_rate' => round($bounceRate, 2),
            'avg_session_duration' => round($avgSessionDuration, 2),
            'conversion_rate' => round($conversionRate, 2),
        ];
    }

    /**
     * Get top pages
     */
    public function getTopPages($days = 30, $limit = 10)
    {
        return AnalyticsEvent::getTopPages($days, $limit);
    }

    /**
     * Get traffic sources
     */
    public function getTrafficSources($days = 30)
    {
        return AnalyticsSession::getReferrerBreakdown($days);
    }

    /**
     * Get device breakdown
     */
    public function getDeviceBreakdown($days = 30)
    {
        return AnalyticsSession::getDeviceBreakdown($days);
    }

    /**
     * Get location breakdown
     */
    public function getLocationBreakdown($days = 30)
    {
        return AnalyticsSession::getLocationBreakdown($days);
    }

    /**
     * Get daily stats
     */
    public function getDailyStats($days = 30)
    {
        return AnalyticsEvent::getDailyStats($days);
    }

    /**
     * Get hourly stats
     */
    public function getHourlyStats($days = 7)
    {
        return AnalyticsEvent::getHourlyStats($days);
    }

    /**
     * Get conversion events
     */
    public function getConversions($days = 30)
    {
        return AnalyticsEvent::getConversions($days);
    }

    /**
     * Get real-time visitors
     */
    public function getRealTimeVisitors($minutes = 5)
    {
        $cutoffTime = now()->subMinutes($minutes);
        
        return AnalyticsSession::where('started_at', '>=', $cutoffTime)
                              ->whereNull('ended_at')
                              ->get();
    }

    /**
     * Get page performance
     */
    public function getPagePerformance($pageUrl, $days = 30)
    {
        $events = AnalyticsEvent::getByPage($pageUrl, $days);
        
        $pageViews = $events->where('event_type', 'page_view')->count();
        $conversions = $events->where('event_type', 'conversion')->count();
        $formSubmissions = $events->where('event_type', 'form_submit')->count();
        
        return [
            'page_views' => $pageViews,
            'conversions' => $conversions,
            'form_submissions' => $formSubmissions,
            'conversion_rate' => $pageViews > 0 ? ($conversions / $pageViews) * 100 : 0,
        ];
    }

    /**
     * Get user journey
     */
    public function getUserJourney($userId, $days = 30)
    {
        $sessions = AnalyticsSession::getByUser($userId, $days);
        $journey = [];
        
        foreach ($sessions as $session) {
            $events = $session->events()->orderBy('created_at')->get();
            $journey[] = [
                'session' => $session,
                'events' => $events,
            ];
        }
        
        return $journey;
    }

    /**
     * Export analytics data
     */
    public function exportData($startDate, $endDate, $format = 'csv')
    {
        $events = AnalyticsEvent::whereBetween('created_at', [$startDate, $endDate])
                               ->orderBy('created_at')
                               ->get();
        
        if ($format === 'json') {
            return $events->toJson();
        }
        
        // CSV format
        $csv = "Date,Event Type,Event Name,Page URL,User ID,Session ID\n";
        
        foreach ($events as $event) {
            $csv .= implode(',', [
                $event->created_at->format('Y-m-d H:i:s'),
                $event->event_type,
                $event->event_name,
                $event->page_url,
                $event->user_id ?: '',
                $event->session_id,
            ]) . "\n";
        }
        
        return $csv;
    }
}
