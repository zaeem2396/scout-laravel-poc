<?php

namespace App\Repositories\Contracts;

use App\Models\Category;
use App\Models\Vendor;
use Illuminate\Support\Collection;

interface CatalogRepositoryInterface
{
    /** @return Collection<int, Category> */
    public function allCategories(): Collection;

    /** @return Collection<int, Vendor> */
    public function allActiveVendors(): Collection;
}
