<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Observability Dashboard — {{ config('app.name') }}</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
        <div class="container py-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h1 class="h3 mb-1">Observability Dashboard</h1>
                    <p class="text-muted mb-0">Live request metrics for Scout APM instrumentation demos.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('demo.index') }}" class="btn btn-outline-secondary btn-sm">All demos</a>
                    <a href="{{ route('demo.dashboard', ['format' => 'json']) }}" class="btn btn-outline-primary btn-sm">JSON</a>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header fw-semibold">Request Context</div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Request ID</span>
                                <code class="small">{{ $metrics['request_id'] }}</code>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Trace ID</span>
                                <code class="small">{{ $metrics['trace_id'] }}</code>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Correlation ID</span>
                                <code class="small">{{ $metrics['correlation_id'] }}</code>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header fw-semibold">Runtime</div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Memory Usage</span>
                                <span>{{ $metrics['memory_usage_human'] }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Peak Memory</span>
                                <span>{{ $metrics['peak_memory_human'] }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Execution Time</span>
                                <span>{{ $metrics['execution_time_ms'] }} ms</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header fw-semibold">Queue Jobs</div>
                        <div class="card-body">
                            <p class="display-6 mb-2">{{ $metrics['active_queue_jobs']['total'] }}</p>
                            <p class="text-muted small mb-2">Pending jobs across Horizon queues</p>
                            <ul class="list-unstyled small mb-0">
                                @foreach ($metrics['active_queue_jobs']['queues'] as $queue => $count)
                                    <li class="d-flex justify-content-between">
                                        <span>{{ $queue }}</span>
                                        <span>{{ $count }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header fw-semibold">Cache Statistics</div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Driver</span>
                                <span>{{ $metrics['cache_statistics']['driver'] }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Hits (this request)</span>
                                <span>{{ $metrics['cache_statistics']['hits'] }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Misses (this request)</span>
                                <span>{{ $metrics['cache_statistics']['misses'] }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Analytics summary cached</span>
                                <span>{{ $metrics['cache_statistics']['analytics_cached'] ? 'yes' : 'no' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header fw-semibold">Instrumentation Counters</div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Database Query Count</span>
                                <span>{{ $metrics['database_query_count'] }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Redis Operations</span>
                                <span>{{ $metrics['redis_operations'] }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Event Count</span>
                                <span>{{ $metrics['event_count'] }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Listener Count</span>
                                <span>{{ $metrics['listener_count'] }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
