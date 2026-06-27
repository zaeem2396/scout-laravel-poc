<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Notifications\OrderPaidNotification;

class SendOrderPaidNotification
{
    public function handle(OrderPaid $event): void
    {
        $order = $event->order->loadMissing(['user', 'vendor']);

        $order->user->notify(new OrderPaidNotification($order));
    }
}
