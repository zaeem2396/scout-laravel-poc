<?php

namespace App\Services\Demo;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;

class DemoOrderResolver
{
    public function resolveForUser(User $user): Order
    {
        $existing = Order::query()
            ->with(['items.product', 'user', 'vendor'])
            ->where('user_id', $user->id)
            ->whereHas('items')
            ->latest('id')
            ->first();

        if ($existing !== null) {
            return $existing;
        }

        return $this->createOrderWithItems($user);
    }

    public function resolveAny(): Order
    {
        $existing = Order::query()
            ->with(['items.product', 'user', 'vendor'])
            ->whereHas('items')
            ->latest('id')
            ->first();

        if ($existing !== null) {
            return $existing;
        }

        $user = User::factory()->create();

        return $this->createOrderWithItems($user);
    }

    private function createOrderWithItems(User $user): Order
    {
        $vendor = Vendor::factory()->create();
        $category = Category::factory()->create();

        $product = Product::factory()->create([
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
        ]);

        Inventory::query()->create([
            'product_id' => $product->id,
            'quantity' => 100,
            'reserved_quantity' => 0,
        ]);

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'vendor_id' => $vendor->id,
            'subtotal' => $product->price,
            'total' => $product->price,
        ]);

        OrderItem::query()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => $product->price,
            'total' => $product->price,
        ]);

        return $order->load(['items.product', 'user', 'vendor']);
    }
}
