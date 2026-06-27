<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Support\Collection;

class DemoRepository
{
    public function countActiveProducts(): int
    {
        return Product::query()
            ->where('is_active', true)
            ->count();
    }

    public function latestActiveProductSummaries(int $limit = 10): Collection
    {
        return Product::query()
            ->where('is_active', true)
            ->orderByDesc('id')
            ->limit($limit)
            ->get(['id', 'name', 'slug', 'price']);
    }
}
