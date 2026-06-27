<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProductRepository implements ProductRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Product::query()
            ->with(['vendor', 'category', 'inventory'])
            ->where('is_active', true)
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $query->where('name', 'like', '%'.$search.'%');
            })
            ->when($filters['category_id'] ?? null, function ($query, int $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($filters['vendor_id'] ?? null, function ($query, int $vendorId) {
                $query->where('vendor_id', $vendorId);
            })
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findBySlug(string $slug): ?Product
    {
        return Product::query()
            ->with(['vendor', 'category', 'images', 'inventory', 'reviews'])
            ->where('is_active', true)
            ->where('slug', $slug)
            ->first();
    }

    public function findActiveByIds(array $ids): Collection
    {
        if ($ids === []) {
            return collect();
        }

        return Product::query()
            ->with(['vendor', 'inventory'])
            ->where('is_active', true)
            ->whereIn('id', $ids)
            ->get();
    }
}
