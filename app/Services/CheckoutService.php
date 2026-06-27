<?php

namespace App\Services;

use App\Enums\CouponType;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Events\OrderPaid;
use App\Events\OrderPlaced;
use App\Events\OrderStatusUpdated;
use App\Exceptions\CheckoutException;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Repositories\Contracts\CouponRepositoryInterface;
use App\Repositories\Contracts\InventoryRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    private const float TAX_RATE = 0.08;

    public function __construct(
        private readonly CartService $cart,
        private readonly OrderRepositoryInterface $orders,
        private readonly InventoryRepositoryInterface $inventory,
        private readonly CouponRepositoryInterface $coupons,
    ) {}

    /**
     * @return Collection<int, Order>
     */
    public function checkout(User $user, ?string $couponCode, PaymentMethod $paymentMethod): Collection
    {
        if ($this->cart->isEmpty()) {
            throw new CheckoutException('Your cart is empty.');
        }

        $linesByVendor = $this->cart->linesGroupedByVendor();
        $coupon = $couponCode ? $this->coupons->findValidByCode($couponCode) : null;

        if ($couponCode && $coupon === null) {
            throw new CheckoutException('The coupon code is invalid or expired.');
        }

        $orders = DB::transaction(function () use ($user, $linesByVendor, $coupon, $paymentMethod) {
            $createdOrders = collect();

            foreach ($linesByVendor as $vendorLines) {
                $this->assertStockAvailable($vendorLines);
            }

            foreach ($linesByVendor as $vendorLines) {
                $createdOrders->push(
                    $this->createVendorOrder($user, $vendorLines, $coupon, $paymentMethod)
                );
            }

            if ($coupon !== null) {
                $this->coupons->incrementUsage($coupon);
            }

            $this->cart->clear();

            return $createdOrders;
        });

        foreach ($orders as $order) {
            OrderPlaced::dispatch($order);
            OrderPaid::dispatch($order);
            OrderStatusUpdated::dispatch($order);
        }

        return $orders;
    }

    /**
     * @param  Collection<int, array{product: \App\Models\Product, quantity: int}>  $lines
     */
    private function assertStockAvailable(Collection $lines): void
    {
        foreach ($lines as $line) {
            if (! $this->inventory->hasAvailableStock($line['product']->id, $line['quantity'])) {
                throw new CheckoutException(
                    "Insufficient stock for {$line['product']->name}."
                );
            }
        }
    }

    /**
     * @param  Collection<int, array{product: \App\Models\Product, quantity: int}>  $lines
     */
    private function createVendorOrder(
        User $user,
        Collection $lines,
        ?Coupon $coupon,
        PaymentMethod $paymentMethod,
    ): Order {
        $subtotal = $lines->sum(
            fn (array $line) => $line['product']->price * $line['quantity']
        );

        $discount = $this->calculateDiscount($subtotal, $coupon);
        $taxable = max($subtotal - $discount, 0);
        $tax = round($taxable * self::TAX_RATE, 2);
        $total = round($taxable + $tax, 2);

        $order = $this->orders->create([
            'user_id' => $user->id,
            'vendor_id' => $lines->first()['product']->vendor_id,
            'coupon_id' => $discount > 0 ? $coupon?->id : null,
            'status' => OrderStatus::Paid,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'total' => $total,
            'placed_at' => now(),
        ]);

        foreach ($lines as $line) {
            $product = $line['product'];
            $quantity = $line['quantity'];
            $unitPrice = $product->price;

            $order->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total' => round($unitPrice * $quantity, 2),
            ]);

            $this->inventory->decrementStock($product->id, $quantity);
        }

        Payment::query()->create([
            'order_id' => $order->id,
            'amount' => $total,
            'currency' => 'USD',
            'method' => $paymentMethod,
            'status' => PaymentStatus::Completed,
            'transaction_id' => 'txn_'.strtoupper(uniqid()),
            'paid_at' => now(),
        ]);

        return $order->load(['items.product', 'vendor', 'payment']);
    }

    private function calculateDiscount(float $subtotal, ?Coupon $coupon): float
    {
        if ($coupon === null || $subtotal < (float) $coupon->min_order_amount) {
            return 0;
        }

        $discount = $coupon->type === CouponType::Percentage
            ? $subtotal * ((float) $coupon->value / 100)
            : (float) $coupon->value;

        return round(min($discount, $subtotal), 2);
    }
}
