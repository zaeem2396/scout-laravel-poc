<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Product;
use App\Models\Vendor;
use App\Repositories\Contracts\CatalogRepositoryInterface;
use Illuminate\Support\Collection;

class CatalogRepository implements CatalogRepositoryInterface
{
    public function allCategories(): Collection
    {
        return Category::query()
            ->orderBy('name')
            ->get();
    }

    public function allActiveVendors(): Collection
    {
        return Vendor::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }
}
