<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethod;
use App\Exceptions\CheckoutException;
use App\Http\Requests\CheckoutRequest;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
        private readonly CheckoutService $checkout,
    ) {}

    public function create(): View|RedirectResponse
    {
        if ($this->cart->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        return view('checkout.create', [
            'lines' => $this->cart->lines(),
            'paymentMethods' => PaymentMethod::cases(),
        ]);
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        try {
            $orders = $this->checkout->checkout(
                $request->user(),
                $request->validated('coupon_code'),
                PaymentMethod::from($request->validated('payment_method')),
            );
        } catch (CheckoutException $exception) {
            return redirect()
                ->route('checkout.create')
                ->withInput()
                ->with('error', $exception->getMessage());
        }

        $orderIds = $orders->pluck('id')->join(', ');

        return redirect()
            ->route('orders.index')
            ->with('status', "Order(s) #{$orderIds} placed successfully.");
    }
}
