<?php

namespace App\Jobs;

use App\Models\Order;
use App\Notifications\OrderInvoiceNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendEmailJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $orderId) {}

    public function handle(): void
    {
        $order = Order::query()
            ->with(['user', 'items.product'])
            ->findOrFail($this->orderId);

        $order->user->notify(new OrderInvoiceNotification($order));
    }
}
