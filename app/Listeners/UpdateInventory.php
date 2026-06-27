<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Repositories\Contracts\InventoryRepositoryInterface;

class UpdateInventory
{
    public function __construct(
        private readonly InventoryRepositoryInterface $inventory,
    ) {}

    public function handle(OrderPlaced $event): void
    {
        $order = $event->order->loadMissing('items');

        foreach ($order->items as $item) {
            $this->inventory->reserveStock($item->product_id, $item->quantity);
        }
    }
}
