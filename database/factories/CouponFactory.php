<?php

namespace Database\Factories;

use App\Enums\CouponType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Coupon>
 */
class CouponFactory extends Factory
{
    protected $model = \App\Models\Coupon::class;

    public function definition(): array
    {
        $type = fake()->randomElement(CouponType::cases());

        return [
            'code' => strtoupper(Str::random(8)),
            'type' => $type,
            'value' => $type === CouponType::Percentage
                ? fake()->numberBetween(5, 25)
                : fake()->randomFloat(2, 5, 50),
            'min_order_amount' => fake()->randomFloat(2, 0, 100),
            'max_uses' => fake()->optional()->numberBetween(50, 500),
            'used_count' => 0,
            'starts_at' => now()->subMonths(3),
            'expires_at' => now()->addMonths(6),
            'is_active' => true,
        ];
    }
}
