<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RequestLogger
{
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('Demo request received', [
            'path' => $request->path(),
            'method' => $request->method(),
            'tenant_id' => $request->attributes->get('tenant_id'),
        ]);

        $response = $next($request);

        Log::info('Demo request completed', [
            'path' => $request->path(),
            'status' => $response->getStatusCode(),
        ]);

        return $response;
    }
}
