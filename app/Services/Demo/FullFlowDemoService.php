<?php

namespace App\Services\Demo;

use App\Events\OrderPaid;
use App\Events\OrderPlaced;
use App\Events\OrderStatusUpdated;
use App\Jobs\GenerateAnalyticsJob;
use App\Jobs\GenerateInvoiceJob;
use App\Jobs\SendEmailJob;
use App\Jobs\SyncERPJob;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redis;

class FullFlowDemoService
{
    public function __construct(
        private readonly ExternalApiDemoService $externalApis,
        private readonly CacheDemoService $cacheDemo,
        private readonly DemoOrderResolver $orderResolver,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function execute(User $user): array
    {
        return DB::transaction(function () use ($user) {
            $order = $this->orderResolver->resolveForUser($user);

            $product = $order->items->first()?->product
                ?? Product::query()->where('is_active', true)->with('vendor')->firstOrFail();

            Gate::authorize('view', $order);
            Gate::authorize('view', $product);
            Gate::authorize('view', $product->vendor);

            $queryCount = Product::query()
                ->where('vendor_id', $product->vendor_id)
                ->where('is_active', true)
                ->count();

            $redisStatus = $this->pingRedis();

            $this->cacheDemo->reset();
            $cacheResult = $this->cacheDemo->resolveCategoryCounts();

            $externalData = $this->externalApis->fetchSampleData();

            OrderPlaced::dispatch($order->loadMissing('items'));
            OrderPaid::dispatch($order);
            OrderStatusUpdated::dispatch($order);

            $orderId = $order->id;
            GenerateInvoiceJob::dispatch($orderId)->onQueue('invoices');
            SyncERPJob::dispatch($orderId)->onQueue('erp');
            GenerateAnalyticsJob::dispatch($orderId)->onQueue('analytics');
            SendEmailJob::dispatch($orderId)->onQueue('emails');

            return [
                'order_id' => $order->id,
                'product_id' => $product->id,
                'vendor_id' => $product->vendor_id,
                'vendor_product_count' => $queryCount,
                'redis' => $redisStatus,
                'cache' => $cacheResult['cache'],
                'external_apis' => array_keys($externalData),
                'events' => ['OrderPlaced', 'OrderPaid', 'OrderStatusUpdated'],
                'jobs_dispatched' => 4,
            ];
        });
    }

    private function pingRedis(): string
    {
        try {
            Redis::connection()->ping();

            return 'ok';
        } catch (\Throwable) {
            return 'unavailable';
        }
    }
}
