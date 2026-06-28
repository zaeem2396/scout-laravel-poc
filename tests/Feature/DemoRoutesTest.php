<?php

namespace Tests\Feature;

use App\Exceptions\HumanErrorDemoException;
use App\Exceptions\ObservabilityDemoException;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class DemoRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_index_lists_routes(): void
    {
        $response = $this->get(route('demo.index'));

        $response->assertOk()
            ->assertJsonStructure(['demos' => ['request', 'n_plus_one', 'slow_query', 'cache', 'dashboard', 'full_flow']]);
    }

    public function test_demo_request_runs_lifecycle(): void
    {
        $response = $this->getJson(route('demo.request'), [
            'X-Tenant-ID' => 'tenant-demo',
        ]);

        $response->assertOk()
            ->assertJsonPath('tenant_id', 'tenant-demo')
            ->assertJsonStructure(['result' => ['active_products', 'sample_products']]);
    }

    public function test_demo_n_plus_one_returns_products(): void
    {
        $response = $this->getJson(route('demo.n-plus-one'));

        $response->assertOk()
            ->assertJsonStructure(['products']);
    }

    public function test_demo_slow_query_executes(): void
    {
        $response = $this->getJson(route('demo.slow-query'));

        $response->assertOk()
            ->assertJsonStructure(['driver', 'matched_products']);
    }

    public function test_demo_slow_method_returns_scores(): void
    {
        $response = $this->getJson(route('demo.slow-method'));

        $response->assertOk()
            ->assertJsonStructure(['product_ids', 'scores']);
    }

    public function test_demo_cache_miss_then_hit(): void
    {
        $this->getJson(route('demo.cache', ['reset' => 1]))
            ->assertOk()
            ->assertJsonPath('cache', 'miss');

        $this->getJson(route('demo.cache'))
            ->assertOk()
            ->assertJsonPath('cache', 'hit');
    }

    public function test_demo_events_dispatches_order_placed(): void
    {
        $response = $this->getJson(route('demo.events'));

        $response->assertOk()
            ->assertJsonPath('listeners', fn ($listeners) => in_array('UpdateInventory', $listeners, true));
    }

    public function test_demo_jobs_dispatches_fulfillment_jobs(): void
    {
        Queue::fake();

        $response = $this->getJson(route('demo.jobs'));

        $response->assertOk()
            ->assertJsonStructure(['order_id', 'jobs', 'queues']);

        Queue::assertPushed(\App\Jobs\GenerateInvoiceJob::class);
        Queue::assertPushed(\App\Jobs\SyncERPJob::class);
        Queue::assertPushed(\App\Jobs\GenerateAnalyticsJob::class);
        Queue::assertPushed(\App\Jobs\SendEmailJob::class);
    }

    public function test_demo_memory_returns_usage_metrics(): void
    {
        $response = $this->getJson(route('demo.memory'));

        $response->assertOk()
            ->assertJsonStructure(['items', 'memory_usage', 'peak_memory']);
    }

    public function test_demo_exception_is_captured(): void
    {
        $this->withoutExceptionHandling();

        $this->expectException(ObservabilityDemoException::class);

        $this->get(route('demo.exception'));
    }

    public function test_demo_sql_error_raises_query_exception(): void
    {
        $this->withoutExceptionHandling();

        $this->expectException(\Illuminate\Database\QueryException::class);

        $this->get(route('demo.sql-error'));
    }

    public function test_demo_human_error_raises_human_error_exception(): void
    {
        $this->withoutExceptionHandling();

        $this->expectException(HumanErrorDemoException::class);

        $this->get(route('demo.human-error'));
    }

    public function test_demo_full_flow_requires_authentication(): void
    {
        $this->post(route('demo.full-flow'))
            ->assertRedirect(route('login'));
    }

    public function test_demo_full_flow_executes_for_authenticated_user(): void
    {
        Http::fake([
            'jsonplaceholder.typicode.com/*' => Http::response(['id' => 1, 'title' => 'Demo'], 200),
            'dummyjson.com/*' => Http::response(['id' => 1, 'title' => 'Demo'], 200),
            'api.github.com/*' => Http::response(['full_name' => 'laravel/framework'], 200),
        ]);

        Queue::fake();

        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('demo.full-flow'), [
            'note' => 'observability smoke test',
        ]);

        $response->assertOk()
            ->assertJsonPath('note', 'observability smoke test')
            ->assertJsonStructure(['result' => ['order_id', 'events', 'jobs_dispatched']]);

        Queue::assertPushed(\App\Jobs\GenerateInvoiceJob::class);
    }
}
