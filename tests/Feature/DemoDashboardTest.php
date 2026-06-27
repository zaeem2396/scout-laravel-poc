<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_dashboard_renders_html(): void
    {
        $response = $this->get(route('demo.dashboard'));

        $response->assertOk()
            ->assertSee('Observability Dashboard')
            ->assertSee('Request ID')
            ->assertSee('Trace ID')
            ->assertSee('Correlation ID')
            ->assertSee('Database Query Count');
    }

    public function test_demo_dashboard_returns_json(): void
    {
        $response = $this->getJson(route('demo.dashboard', ['format' => 'json']), [
            'X-Request-ID' => 'req-demo-123',
            'X-Trace-ID' => 'trace-demo-456',
            'X-Correlation-ID' => 'corr-demo-789',
        ]);

        $response->assertOk()
            ->assertJsonPath('request_id', 'req-demo-123')
            ->assertJsonPath('trace_id', 'trace-demo-456')
            ->assertJsonPath('correlation_id', 'corr-demo-789')
            ->assertJsonStructure([
                'memory_usage',
                'peak_memory',
                'execution_time_ms',
                'active_queue_jobs' => ['total', 'queues'],
                'cache_statistics' => ['driver', 'hits', 'misses'],
                'database_query_count',
                'redis_operations',
                'event_count',
                'listener_count',
            ]);
    }

    public function test_demo_dashboard_generates_ids_when_headers_missing(): void
    {
        $response = $this->getJson(route('demo.dashboard', ['format' => 'json']));

        $response->assertOk();

        $this->assertNotEmpty($response->json('request_id'));
        $this->assertNotEmpty($response->json('trace_id'));
        $this->assertNotEmpty($response->json('correlation_id'));
    }

    public function test_demo_dashboard_records_query_and_listener_activity(): void
    {
        $response = $this->getJson(route('demo.dashboard', ['format' => 'json']));

        $response->assertOk();

        $this->assertGreaterThan(0, $response->json('database_query_count'));
        $this->assertGreaterThanOrEqual(1, $response->json('listener_count'));
        $this->assertGreaterThanOrEqual(1, $response->json('event_count'));
    }

    public function test_demo_index_includes_dashboard_route(): void
    {
        $this->get(route('demo.index'))
            ->assertOk()
            ->assertJsonPath('demos.dashboard', route('demo.dashboard'));
    }
}
