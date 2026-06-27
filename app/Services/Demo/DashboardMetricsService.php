<?php

namespace App\Services\Demo;

use App\Events\DashboardProbeEvent;
use App\Models\Order;
use App\Models\Product;
use App\Support\ObservabilityMetricsCollector;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class DashboardMetricsService
{
    /** @var list<string> */
    private const QUEUE_NAMES = ['default', 'invoices', 'erp', 'analytics', 'emails'];

    public function __construct(
        private readonly ObservabilityMetricsCollector $metrics,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function gather(): array
    {
        $this->probeSubsystems();

        return [
            'request_id' => $this->metrics->requestId(),
            'trace_id' => $this->metrics->traceId(),
            'correlation_id' => $this->metrics->correlationId(),
            'memory_usage' => $this->metrics->memoryUsage(),
            'memory_usage_human' => $this->formatBytes($this->metrics->memoryUsage()),
            'peak_memory' => $this->metrics->peakMemory(),
            'peak_memory_human' => $this->formatBytes($this->metrics->peakMemory()),
            'execution_time_ms' => $this->metrics->executionTimeMs(),
            'active_queue_jobs' => $this->activeQueueJobs(),
            'cache_statistics' => $this->cacheStatistics(),
            'database_query_count' => $this->metrics->queryCount(),
            'redis_operations' => $this->metrics->redisOperations(),
            'event_count' => $this->metrics->eventCount(),
            'listener_count' => $this->metrics->listenerInvocations(),
        ];
    }

    private function probeSubsystems(): void
    {
        Product::query()->where('is_active', true)->count();
        Order::query()->count();

        $cacheKey = 'demo:dashboard-probe';

        if (Cache::has($cacheKey)) {
            $this->metrics->recordCacheHit();
            Cache::get($cacheKey);
        } else {
            $this->metrics->recordCacheMiss();
            Cache::put($cacheKey, now()->toIso8601String(), now()->addMinutes(5));
        }

        $this->pingRedisQueues();

        DashboardProbeEvent::dispatch();
    }

    /**
     * @return array<string, int|string>
     */
    private function activeQueueJobs(): array
    {
        $pending = 0;
        $queueLengths = [];

        foreach (self::QUEUE_NAMES as $queue) {
            $length = $this->queueLength($queue);
            $queueLengths[$queue] = $length;
            $pending += $length;
        }

        return [
            'total' => $pending,
            'queues' => $queueLengths,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function cacheStatistics(): array
    {
        return [
            'driver' => config('cache.default'),
            'hits' => $this->metrics->cacheHits(),
            'misses' => $this->metrics->cacheMisses(),
            'analytics_cached' => Cache::has('analytics:marketplace_summary'),
        ];
    }

    private function queueLength(string $queue): int
    {
        try {
            $this->metrics->incrementRedisOperations();
            $length = (int) Redis::connection()->llen("queues:{$queue}");

            return max($length, 0);
        } catch (\Throwable) {
            return 0;
        }
    }

    private function pingRedisQueues(): void
    {
        try {
            $this->metrics->incrementRedisOperations();
            Redis::connection()->ping();
        } catch (\Throwable) {
            //
        }
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $index = 0;
        $size = (float) $bytes;

        while ($size >= 1024 && $index < count($units) - 1) {
            $size /= 1024;
            $index++;
        }

        return round($size, 2).' '.$units[$index];
    }
}
