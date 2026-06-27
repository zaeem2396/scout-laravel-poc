<?php

namespace Tests\Feature;

use App\Enums\PaymentMethod;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketplaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_browse_products(): void
    {
        $this->createProduct();

        $response = $this->get(route('products.index'));

        $response->assertOk();
        $response->assertSee('Products');
    }

    public function test_authenticated_user_can_checkout_and_view_order(): void
    {
        $user = User::factory()->create();
        $product = $this->createProduct();

        $this->actingAs($user)
            ->post(route('cart.store', $product), ['quantity' => 2])
            ->assertRedirect(route('cart.index'));

        $response = $this->post(route('checkout.store'), [
            'payment_method' => PaymentMethod::Card->value,
        ]);

        $response->assertRedirect(route('orders.index'));

        $order = $user->orders()->first();

        $this->assertNotNull($order);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
        ]);

        $this->get(route('orders.show', $order))->assertOk();
    }

    public function test_user_cannot_view_another_users_order(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $product = $this->createProduct();

        $this->actingAs($owner)
            ->post(route('cart.store', $product), ['quantity' => 1]);

        $this->actingAs($owner)
            ->post(route('checkout.store'), [
                'payment_method' => PaymentMethod::Card->value,
            ]);

        $order = $owner->orders()->first();

        $this->actingAs($other)
            ->get(route('orders.show', $order))
            ->assertNotFound();
    }

    private function createProduct(): Product
    {
        $vendor = Vendor::factory()->create();
        $category = Category::factory()->create();

        $product = Product::factory()->create([
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
        ]);

        Inventory::query()->create([
            'product_id' => $product->id,
            'quantity' => 100,
            'reserved_quantity' => 0,
        ]);

        return $product;
    }
}
