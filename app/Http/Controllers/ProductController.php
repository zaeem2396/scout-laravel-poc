<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\CatalogRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\ProductBrowsingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductBrowsingService $browsing,
        private readonly ProductRepositoryInterface $products,
        private readonly CatalogRepositoryInterface $catalog,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'category_id', 'vendor_id']);

        return view('products.index', [
            'products' => $this->browsing->browse($filters),
            'categories' => $this->catalog->allCategories(),
            'vendors' => $this->catalog->allActiveVendors(),
            'filters' => $filters,
        ]);
    }

    public function show(string $slug): View
    {
        $product = $this->products->findBySlug($slug);

        abort_if($product === null, 404);

        return view('products.show', [
            'product' => $product,
        ]);
    }
}
