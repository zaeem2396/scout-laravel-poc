<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Models\Inventory;

class UpdateInventory
{
    public function handle(OrderPlaced $event): void
    {
        $order = $event->order->loadMissing('items.product');

        foreach ($order->items as $item) {
            Inventory::query()
                ->where('product_id', $item->product_id)
                ->update([
                    'reserved_quantity' => 0,
                ]);
        }
    }
}
