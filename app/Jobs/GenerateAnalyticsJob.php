<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GenerateAnalyticsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $orderId) {}

    public function handle(): void
    {
        $order = Order::query()
            ->with(['vendor', 'items'])
            ->findOrFail($this->orderId);

        Log::info('Analytics generated for order', [
            'order_id' => $order->id,
            'vendor_id' => $order->vendor_id,
            'item_count' => $order->items->sum('quantity'),
            'revenue' => $order->total,
        ]);
    }
}
