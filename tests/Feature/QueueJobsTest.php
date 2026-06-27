<?php

namespace Tests\Feature;

use App\Enums\PaymentMethod;
use App\Jobs\GenerateAnalyticsJob;
use App\Jobs\GenerateInvoiceJob;
use App\Jobs\SendEmailJob;
use App\Jobs\SyncERPJob;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schedule;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class QueueJobsTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_dispatches_fulfillment_jobs(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $product = $this->createProduct();

        $this->actingAs($user)
            ->post(route('cart.store', $product), ['quantity' => 1]);

        $this->post(route('checkout.store'), [
            'payment_method' => PaymentMethod::Card->value,
        ]);

        Queue::assertPushed(GenerateInvoiceJob::class, fn (GenerateInvoiceJob $job) => $job->queue === 'invoices');
        Queue::assertPushed(SyncERPJob::class, fn (SyncERPJob $job) => $job->queue === 'erp');
        Queue::assertPushed(GenerateAnalyticsJob::class, fn (GenerateAnalyticsJob $job) => $job->queue === 'analytics');
        Queue::assertPushed(SendEmailJob::class, fn (SendEmailJob $job) => $job->queue === 'emails');
    }

    public function test_sync_erp_job_fails_before_final_attempt(): void
    {
        config(['queue.default' => 'redis']);

        $order = $this->createOrder();

        $job = Mockery::mock(SyncERPJob::class, [$order->id])->makePartial();
        $job->shouldReceive('attempts')->andReturn(1);

        $this->expectException(RuntimeException::class);

        $job->handle();
    }

    public function test_sync_erp_job_succeeds_on_final_attempt(): void
    {
        $order = $this->createOrder();

        $job = Mockery::mock(SyncERPJob::class, [$order->id])->makePartial();
        $job->shouldReceive('attempts')->andReturn(3);

        $job->handle();

        $this->assertTrue(true);
    }

    public function test_scheduled_commands_are_registered(): void
    {
        $this->assertTrue(
            collect(Schedule::events())->contains(
                fn ($event) => str_contains($event->command ?? '', 'report:daily')
            )
        );

        $this->assertTrue(
            collect(Schedule::events())->contains(
                fn ($event) => str_contains($event->command ?? '', 'logs:cleanup')
            )
        );

        $this->assertTrue(
            collect(Schedule::events())->contains(
                fn ($event) => str_contains($event->command ?? '', 'analytics:sync')
            )
        );
    }

    public function test_analytics_sync_command_stores_summary_in_cache(): void
    {
        Artisan::call('analytics:sync');

        $this->assertNotNull(cache('analytics:marketplace_summary'));
        $this->assertArrayHasKey('orders_total', cache('analytics:marketplace_summary'));
    }

    private function createProduct(): Product
    {
        $vendor = Vendor::factory()->create();
        $category = Category::factory()->create();

        $product = Product::factory()->create([
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
        ]);

        Inventory::query()->create([
            'product_id' => $product->id,
            'quantity' => 100,
            'reserved_quantity' => 0,
        ]);

        return $product;
    }

    private function createOrder(): Order
    {
        $user = User::factory()->create();
        $vendor = Vendor::factory()->create();

        return Order::factory()->create([
            'user_id' => $user->id,
            'vendor_id' => $vendor->id,
        ]);
    }
}
