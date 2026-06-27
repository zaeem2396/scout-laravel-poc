<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Collection;

class CartService
{
    private const string SESSION_KEY = 'cart';

    public function __construct(
        private readonly ProductRepositoryInterface $products,
    ) {}

    /** @return array<int, int> */
    public function items(): array
    {
        return session(self::SESSION_KEY, []);
    }

    public function count(): int
    {
        return array_sum($this->items());
    }

    public function add(Product $product, int $quantity = 1): void
    {
        $items = $this->items();
        $items[$product->id] = ($items[$product->id] ?? 0) + $quantity;
        session([self::SESSION_KEY => $items]);
    }

    public function update(Product $product, int $quantity): void
    {
        $items = $this->items();

        if ($quantity <= 0) {
            unset($items[$product->id]);
        } else {
            $items[$product->id] = $quantity;
        }

        session([self::SESSION_KEY => $items]);
    }

    public function remove(Product $product): void
    {
        $items = $this->items();
        unset($items[$product->id]);
        session([self::SESSION_KEY => $items]);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function isEmpty(): bool
    {
        return $this->items() === [];
    }

    /** @return Collection<int, array{product: Product, quantity: int}> */
    public function lines(): Collection
    {
        $items = $this->items();

        if ($items === []) {
            return collect();
        }

        $products = $this->products->findActiveByIds(array_keys($items))->keyBy('id');

        return collect($items)
            ->map(function (int $quantity, int $productId) use ($products) {
                $product = $products->get($productId);

                if ($product === null) {
                    return null;
                }

                return [
                    'product' => $product,
                    'quantity' => $quantity,
                ];
            })
            ->filter()
            ->values();
    }

    /** @return Collection<int, Collection<int, array{product: Product, quantity: int}>> */
    public function linesGroupedByVendor(): Collection
    {
        return $this->lines()
            ->groupBy(fn (array $line) => $line['product']->vendor_id);
    }
}
