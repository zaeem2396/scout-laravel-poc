<?php

namespace App\Http\Middleware;

use App\Support\ObservabilityMetricsCollector;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CollectObservabilityMetrics
{
    public function __construct(
        private readonly ObservabilityMetricsCollector $metrics,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $this->metrics->beginRequest($request);

        return $next($request);
    }
}
