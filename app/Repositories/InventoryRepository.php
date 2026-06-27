<?php

namespace App\Repositories;

use App\Models\Inventory;
use App\Repositories\Contracts\InventoryRepositoryInterface;

class InventoryRepository implements InventoryRepositoryInterface
{
    public function findForProduct(int $productId): ?Inventory
    {
        return Inventory::query()
            ->where('product_id', $productId)
            ->first();
    }

    public function hasAvailableStock(int $productId, int $quantity): bool
    {
        $inventory = Inventory::query()
            ->where('product_id', $productId)
            ->lockForUpdate()
            ->first();

        if ($inventory === null) {
            return false;
        }

        return $inventory->quantity >= $quantity;
    }

    public function decrementStock(int $productId, int $quantity): void
    {
        $inventory = Inventory::query()
            ->where('product_id', $productId)
            ->lockForUpdate()
            ->firstOrFail();

        $inventory->decrement('quantity', $quantity);
    }

    public function reserveStock(int $productId, int $quantity): void
    {
        Inventory::query()
            ->where('product_id', $productId)
            ->increment('reserved_quantity', $quantity);
    }
}
