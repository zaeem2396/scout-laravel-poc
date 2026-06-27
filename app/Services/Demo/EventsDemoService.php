<?php

namespace App\Services\Demo;

use App\Events\OrderPlaced;
use App\Models\Order;

class EventsDemoService
{
    /**
     * @return array<string, mixed>
     */
    public function dispatchOrderPlacedListeners(Order $order): array
    {
        $order->loadMissing(['items.product', 'user', 'vendor']);

        OrderPlaced::dispatch($order);

        return [
            'order_id' => $order->id,
            'listeners' => [
                'UpdateInventory',
                'DispatchOrderFulfillmentJobs',
                'RewardCustomer',
                'NotifyWarehouse',
            ],
        ];
    }
}
