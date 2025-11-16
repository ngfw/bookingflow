<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Performance Monitoring Middleware
 *
 * Monitors application performance by tracking:
 * - Request response times
 * - Memory usage
 * - Slow queries
 * - Resource-intensive requests
 */
class MonitorPerformance
{
    /**
     * Threshold for slow requests in milliseconds
     * Requests taking longer than this will be logged
     */
    private const SLOW_REQUEST_THRESHOLD_MS = 1000;

    /**
     * Memory usage warning threshold in MB
     * Requests consuming more than this will be logged
     */
    private const HIGH_MEMORY_THRESHOLD_MB = 128;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Record start time and memory
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);

        // Process the request
        $response = $next($request);

        // Calculate metrics
        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);

        $duration = ($endTime - $startTime) * 1000; // Convert to milliseconds
        $memoryUsed = ($endMemory - $startMemory) / 1024 / 1024; // Convert to MB
        $peakMemory = memory_get_peak_usage(true) / 1024 / 1024; // Convert to MB

        // Log slow requests
        if ($duration > self::SLOW_REQUEST_THRESHOLD_MS) {
            $this->logSlowRequest($request, $duration, $memoryUsed, $peakMemory);
        }

        // Log high memory usage
        if ($memoryUsed > self::HIGH_MEMORY_THRESHOLD_MB) {
            $this->logHighMemoryUsage($request, $duration, $memoryUsed, $peakMemory);
        }

        // Add performance headers in non-production environments
        if (!app()->environment('production')) {
            $response->headers->set('X-Response-Time', round($duration, 2) . 'ms');
            $response->headers->set('X-Memory-Usage', round($memoryUsed, 2) . 'MB');
            $response->headers->set('X-Peak-Memory', round($peakMemory, 2) . 'MB');
        }

        // In production, add a simplified header
        if (app()->environment('production')) {
            $response->headers->set('X-Response-Time', round($duration, 2) . 'ms');
        }

        return $response;
    }

    /**
     * Log slow request details
     *
     * @param Request $request
     * @param float $duration
     * @param float $memoryUsed
     * @param float $peakMemory
     * @return void
     */
    private function logSlowRequest(Request $request, float $duration, float $memoryUsed, float $peakMemory): void
    {
        Log::warning('Slow request detected', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'duration_ms' => round($duration, 2),
            'memory_used_mb' => round($memoryUsed, 2),
            'peak_memory_mb' => round($peakMemory, 2),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => auth()->id(),
            'threshold_ms' => self::SLOW_REQUEST_THRESHOLD_MS,
        ]);
    }

    /**
     * Log high memory usage
     *
     * @param Request $request
     * @param float $duration
     * @param float $memoryUsed
     * @param float $peakMemory
     * @return void
     */
    private function logHighMemoryUsage(Request $request, float $duration, float $memoryUsed, float $peakMemory): void
    {
        Log::warning('High memory usage detected', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'duration_ms' => round($duration, 2),
            'memory_used_mb' => round($memoryUsed, 2),
            'peak_memory_mb' => round($peakMemory, 2),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => auth()->id(),
            'threshold_mb' => self::HIGH_MEMORY_THRESHOLD_MB,
        ]);
    }

    /**
     * Terminate the request
     *
     * This method is called after the response has been sent to the browser
     * Perfect for logging without affecting response time
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function terminate(Request $request, Response $response): void
    {
        // Log all requests in a structured format for analytics
        // This runs after the response is sent, so it doesn't affect performance

        if (app()->environment('production')) {
            // In production, you might want to send this to a monitoring service
            // like New Relic, DataDog, or a custom analytics endpoint

            $this->logRequestMetrics($request, $response);
        }
    }

    /**
     * Log request metrics for analytics
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    private function logRequestMetrics(Request $request, Response $response): void
    {
        // Only log if we have a valid response
        if (!$response) {
            return;
        }

        $metrics = [
            'timestamp' => now()->toIso8601String(),
            'method' => $request->method(),
            'path' => $request->path(),
            'status_code' => $response->getStatusCode(),
            'user_id' => auth()->id(),
            'ip' => $request->ip(),
        ];

        // Log to a dedicated channel or send to monitoring service
        Log::channel('daily')->info('Request metrics', $metrics);
    }
}
