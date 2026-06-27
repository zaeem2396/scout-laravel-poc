<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Notifications\OrderInvoiceNotification;

class SendInvoice
{
    public function handle(OrderPlaced $event): void
    {
        $order = $event->order->loadMissing(['user', 'items.product']);

        $order->user->notify(new OrderInvoiceNotification($order));
    }
}
