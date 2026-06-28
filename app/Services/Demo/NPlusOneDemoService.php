<?php

namespace App\Services\Demo;

use App\Models\Product;

class NPlusOneDemoService
{
    /**
     * Intentionally trigger N+1 queries across related models.
     *
     * Default limit (100) is sized so local Docker MySQL usually exceeds Scout's
     * ~150ms N+1 insight threshold. Use ?limit= on /demo/n-plus-one to tune.
     *
     * @return array<int, array<string, mixed>>
     */
    public function loadProductsWithRelations(int $limit = 100): array
    {
        $products = Product::query()
            ->where('is_active', true)
            ->limit($limit)
            ->get();

        $payload = [];

        foreach ($products as $product) {
            $payload[] = [
                'id' => $product->id,
                'name' => $product->name,
                'vendor' => $product->vendor->name,
                'category' => $product->category->name,
                'review_count' => $product->reviews->count(),
                'image_count' => $product->images->count(),
            ];
        }

        return $payload;
    }
}
