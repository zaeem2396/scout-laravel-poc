<?php

namespace App\Repositories\Contracts;

use App\Models\Inventory;

interface InventoryRepositoryInterface
{
    public function findForProduct(int $productId): ?Inventory;

    public function hasAvailableStock(int $productId, int $quantity): bool;

    public function decrementStock(int $productId, int $quantity): void;
}
