<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GenerateInvoiceJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $orderId) {}

    public function handle(): void
    {
        $order = Order::query()
            ->with(['user', 'vendor', 'items.product'])
            ->findOrFail($this->orderId);

        Log::info('Invoice generated for order', [
            'order_id' => $order->id,
            'total' => $order->total,
            'line_items' => $order->items->count(),
        ]);
    }
}
