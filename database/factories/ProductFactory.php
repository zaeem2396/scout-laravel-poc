<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = \App\Models\Product::class;

    public function definition(): array
    {
        $name = fake()->words(3, true);

        return [
            'vendor_id' => Vendor::factory(),
            'category_id' => Category::factory(),
            'name' => ucfirst($name),
            'slug' => Str::slug($name).'-'.fake()->unique()->numerify('#####'),
            'description' => fake()->paragraphs(2, true),
            'sku' => strtoupper(fake()->unique()->bothify('SKU-########')),
            'price' => fake()->randomFloat(2, 5, 500),
            'is_active' => true,
        ];
    }
}
