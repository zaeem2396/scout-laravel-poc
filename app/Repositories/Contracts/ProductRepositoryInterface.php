<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ProductRepositoryInterface
{
    /**
     * @param  array{search?: string, category_id?: int, vendor_id?: int}  $filters
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function findBySlug(string $slug): ?Product;

    /**
     * @param  list<int>  $ids
     */
    public function findActiveByIds(array $ids): Collection;
}
