<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Coupon;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = \App\Models\Order::class;

    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 20, 500);
        $discount = fake()->randomFloat(2, 0, $subtotal * 0.2);
        $tax = round(($subtotal - $discount) * 0.08, 2);

        return [
            'user_id' => User::factory(),
            'vendor_id' => Vendor::factory(),
            'coupon_id' => null,
            'status' => fake()->randomElement(OrderStatus::cases()),
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'total' => round($subtotal - $discount + $tax, 2),
            'placed_at' => fake()->dateTimeBetween('-2 years', 'now'),
        ];
    }

    public function withCoupon(): static
    {
        return $this->state(fn () => ['coupon_id' => Coupon::factory()]);
    }
}
