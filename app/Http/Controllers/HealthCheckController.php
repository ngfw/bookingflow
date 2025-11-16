<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Exception;

/**
 * Health Check Controller
 *
 * Provides comprehensive health check endpoints for monitoring
 * application services and infrastructure status.
 */
class HealthCheckController extends Controller
{
    /**
     * Perform a comprehensive health check of all critical services
     *
     * This endpoint checks:
     * - Database connectivity
     * - Cache system (Redis)
     * - Queue system
     * - Storage/Filesystem
     * - Application status
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $status = 'healthy';
        $checks = [];
        $overallHealthy = true;

        // Check Database
        $checks['database'] = $this->checkDatabase();
        if (!$checks['database']['healthy']) {
            $overallHealthy = false;
        }

        // Check Cache (Redis)
        $checks['cache'] = $this->checkCache();
        if (!$checks['cache']['healthy']) {
            $overallHealthy = false;
        }

        // Check Queue
        $checks['queue'] = $this->checkQueue();
        if (!$checks['queue']['healthy']) {
            $overallHealthy = false;
        }

        // Check Storage
        $checks['storage'] = $this->checkStorage();
        if (!$checks['storage']['healthy']) {
            $overallHealthy = false;
        }

        // Check Redis connection
        $checks['redis'] = $this->checkRedis();
        if (!$checks['redis']['healthy']) {
            $overallHealthy = false;
        }

        // Application info
        $checks['application'] = [
            'healthy' => true,
            'environment' => app()->environment(),
            'debug_mode' => config('app.debug'),
            'version' => config('app.version', '1.0.0'),
            'timezone' => config('app.timezone'),
        ];

        // System resources
        $checks['system'] = $this->checkSystemResources();

        $status = $overallHealthy ? 'healthy' : 'unhealthy';
        $httpCode = $overallHealthy ? 200 : 503;

        return response()->json([
            'status' => $status,
            'timestamp' => now()->toIso8601String(),
            'checks' => $checks,
        ], $httpCode);
    }

    /**
     * Simple ping endpoint for basic availability check
     *
     * @return JsonResponse
     */
    public function ping(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
            'message' => 'BookingFlow is running',
        ]);
    }

    /**
     * Check database connectivity and performance
     *
     * @return array
     */
    private function checkDatabase(): array
    {
        try {
            $start = microtime(true);

            // Test basic connection
            DB::connection()->getPdo();

            // Test a simple query
            $result = DB::select('SELECT 1 as test');

            $responseTime = round((microtime(true) - $start) * 1000, 2);

            return [
                'healthy' => true,
                'connection' => config('database.default'),
                'response_time_ms' => $responseTime,
                'message' => 'Database connection successful',
            ];
        } catch (Exception $e) {
            return [
                'healthy' => false,
                'connection' => config('database.default'),
                'message' => 'Database connection failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check cache system (Redis) connectivity
     *
     * @return array
     */
    private function checkCache(): array
    {
        try {
            $start = microtime(true);

            // Test cache write
            $testKey = 'health_check_' . time();
            $testValue = 'test_value';
            Cache::put($testKey, $testValue, 10);

            // Test cache read
            $retrieved = Cache::get($testKey);

            // Clean up
            Cache::forget($testKey);

            $responseTime = round((microtime(true) - $start) * 1000, 2);

            if ($retrieved === $testValue) {
                return [
                    'healthy' => true,
                    'driver' => config('cache.default'),
                    'response_time_ms' => $responseTime,
                    'message' => 'Cache system operational',
                ];
            }

            return [
                'healthy' => false,
                'driver' => config('cache.default'),
                'message' => 'Cache read/write verification failed',
            ];
        } catch (Exception $e) {
            return [
                'healthy' => false,
                'driver' => config('cache.default'),
                'message' => 'Cache system check failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check queue system status
     *
     * @return array
     */
    private function checkQueue(): array
    {
        try {
            $connection = config('queue.default');

            // For Redis queue, check connection
            if ($connection === 'redis') {
                $size = Redis::connection('default')->llen('queues:default');

                return [
                    'healthy' => true,
                    'driver' => $connection,
                    'pending_jobs' => $size,
                    'message' => 'Queue system operational',
                ];
            }

            return [
                'healthy' => true,
                'driver' => $connection,
                'message' => 'Queue system configured',
            ];
        } catch (Exception $e) {
            return [
                'healthy' => false,
                'driver' => config('queue.default'),
                'message' => 'Queue system check failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check storage/filesystem availability
     *
     * @return array
     */
    private function checkStorage(): array
    {
        try {
            $disk = config('filesystems.default');

            // Test write
            $testFile = 'health_check_' . time() . '.txt';
            Storage::disk($disk)->put($testFile, 'health check');

            // Test read
            $content = Storage::disk($disk)->get($testFile);

            // Test delete
            Storage::disk($disk)->delete($testFile);

            return [
                'healthy' => true,
                'disk' => $disk,
                'message' => 'Storage system operational',
            ];
        } catch (Exception $e) {
            return [
                'healthy' => false,
                'disk' => config('filesystems.default'),
                'message' => 'Storage system check failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check Redis connectivity
     *
     * @return array
     */
    private function checkRedis(): array
    {
        try {
            $start = microtime(true);

            // Test Redis ping
            $pong = Redis::ping();

            $responseTime = round((microtime(true) - $start) * 1000, 2);

            return [
                'healthy' => true,
                'response_time_ms' => $responseTime,
                'message' => 'Redis connection successful',
            ];
        } catch (Exception $e) {
            return [
                'healthy' => false,
                'message' => 'Redis connection failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check system resources
     *
     * @return array
     */
    private function checkSystemResources(): array
    {
        return [
            'memory_usage' => [
                'current_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
                'peak_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
            ],
            'disk_space' => [
                'free_gb' => round(disk_free_space('/') / 1024 / 1024 / 1024, 2),
                'total_gb' => round(disk_total_space('/') / 1024 / 1024 / 1024, 2),
            ],
        ];
    }
}
