<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FeatureFlagMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->attributes->set('feature_flags', [
            'observability_demos' => true,
            'slow_queries_enabled' => $request->boolean('slow', true),
        ]);

        return $next($request);
    }
}
