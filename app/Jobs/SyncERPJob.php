<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class SyncERPJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /** @var array<int, int> */
    public array $backoff = [1, 5, 10];

    public function __construct(public int $orderId) {}

    public function handle(): void
    {
        if ($this->shouldSimulateTransientFailure()) {
            throw new RuntimeException('ERP sync temporarily unavailable.');
        }

        $order = Order::query()->findOrFail($this->orderId);

        Log::info('Order synced to ERP', [
            'order_id' => $order->id,
            'attempt' => $this->attempts(),
        ]);
    }

    private function shouldSimulateTransientFailure(): bool
    {
        if (config('queue.default') === 'sync') {
            return false;
        }

        return $this->attempts() < 3;
    }
}
