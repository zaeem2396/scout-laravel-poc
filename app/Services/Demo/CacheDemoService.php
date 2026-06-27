<?php

namespace App\Services\Demo;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class CacheDemoService
{
    private const string CACHE_KEY = 'demo:product-category-counts';

    /**
     * @return array<string, mixed>
     */
    public function resolveCategoryCounts(): array
    {
        if (Cache::has(self::CACHE_KEY)) {
            return [
                'cache' => 'hit',
                'data' => Cache::get(self::CACHE_KEY),
            ];
        }

        $data = Product::query()
            ->where('is_active', true)
            ->selectRaw('category_id, COUNT(*) as product_count')
            ->groupBy('category_id')
            ->orderBy('category_id')
            ->get()
            ->toArray();

        Cache::put(self::CACHE_KEY, $data, now()->addMinutes(10));

        return [
            'cache' => 'miss',
            'data' => $data,
        ];
    }

    public function reset(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
