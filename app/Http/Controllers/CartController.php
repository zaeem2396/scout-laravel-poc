<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
    ) {}

    public function index(): View
    {
        return view('cart.index', [
            'lines' => $this->cart->lines(),
        ]);
    }

    public function store(AddToCartRequest $request, Product $product): RedirectResponse
    {
        abort_if(! $product->is_active, 404);

        $this->cart->add($product, (int) $request->validated('quantity'));

        return redirect()
            ->route('cart.index')
            ->with('status', "{$product->name} added to cart.");
    }

    public function update(UpdateCartRequest $request, Product $product): RedirectResponse
    {
        $this->cart->update($product, (int) $request->validated('quantity'));

        return redirect()
            ->route('cart.index')
            ->with('status', 'Cart updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->cart->remove($product);

        return redirect()
            ->route('cart.index')
            ->with('status', 'Item removed from cart.');
    }
}
