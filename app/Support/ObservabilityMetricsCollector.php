<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ObservabilityMetricsCollector
{
    private float $startedAt;

    private string $requestId;

    private string $traceId;

    private string $correlationId;

    private int $queryCount = 0;

    private int $redisOperations = 0;

    private int $eventCount = 0;

    private int $listenerInvocations = 0;

    private int $cacheHits = 0;

    private int $cacheMisses = 0;

    private bool $active = false;

    public function beginRequest(Request $request): void
    {
        $this->active = true;
        $this->startedAt = microtime(true);
        $this->requestId = $request->headers->get('X-Request-ID', (string) Str::uuid());
        $this->traceId = $request->headers->get('X-Trace-ID', (string) Str::uuid());
        $this->correlationId = $request->headers->get('X-Correlation-ID', $this->traceId);

        $request->attributes->set('observability.request_id', $this->requestId);
        $request->attributes->set('observability.trace_id', $this->traceId);
        $request->attributes->set('observability.correlation_id', $this->correlationId);
    }

    public function incrementQueryCount(): void
    {
        if (! $this->active) {
            return;
        }

        $this->queryCount++;
    }

    public function incrementRedisOperations(int $count = 1): void
    {
        if (! $this->active) {
            return;
        }

        $this->redisOperations += $count;
    }

    public function incrementEventCount(): void
    {
        if (! $this->active) {
            return;
        }

        $this->eventCount++;
    }

    public function incrementListenerInvocations(int $count = 1): void
    {
        if (! $this->active) {
            return;
        }

        $this->listenerInvocations += $count;
    }

    public function recordCacheHit(): void
    {
        if (! $this->active) {
            return;
        }

        $this->cacheHits++;
    }

    public function recordCacheMiss(): void
    {
        if (! $this->active) {
            return;
        }

        $this->cacheMisses++;
    }

    public function requestId(): string
    {
        return $this->requestId;
    }

    public function traceId(): string
    {
        return $this->traceId;
    }

    public function correlationId(): string
    {
        return $this->correlationId;
    }

    public function queryCount(): int
    {
        return $this->queryCount;
    }

    public function redisOperations(): int
    {
        return $this->redisOperations;
    }

    public function eventCount(): int
    {
        return $this->eventCount;
    }

    public function listenerInvocations(): int
    {
        return $this->listenerInvocations;
    }

    public function cacheHits(): int
    {
        return $this->cacheHits;
    }

    public function cacheMisses(): int
    {
        return $this->cacheMisses;
    }

    public function executionTimeMs(): float
    {
        return round((microtime(true) - $this->startedAt) * 1000, 2);
    }

    public function memoryUsage(): int
    {
        return memory_get_usage(true);
    }

    public function peakMemory(): int
    {
        return memory_get_peak_usage(true);
    }
}
