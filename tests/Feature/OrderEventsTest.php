<?php

namespace Tests\Feature;

use App\Enums\PaymentMethod;
use App\Events\OrderPaid;
use App\Events\OrderPlaced;
use App\Events\OrderStatusUpdated;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use App\Notifications\OrderInvoiceNotification;
use App\Notifications\OrderPaidNotification;
use App\Notifications\WarehouseOrderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class OrderEventsTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_dispatches_order_events(): void
    {
        Event::fake([OrderPlaced::class, OrderPaid::class, OrderStatusUpdated::class]);

        $user = User::factory()->create();
        $product = $this->createProduct();

        $this->actingAs($user)
            ->post(route('cart.store', $product), ['quantity' => 1]);

        $this->post(route('checkout.store'), [
            'payment_method' => PaymentMethod::Card->value,
        ]);

        Event::assertDispatched(OrderPlaced::class);
        Event::assertDispatched(OrderPaid::class);
        Event::assertDispatched(OrderStatusUpdated::class);
    }

    public function test_order_paid_sends_mail_and_database_notifications(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $product = $this->createProduct();

        $this->actingAs($user)
            ->post(route('cart.store', $product), ['quantity' => 1]);

        $this->post(route('checkout.store'), [
            'payment_method' => PaymentMethod::Card->value,
        ]);

        Notification::assertSentTo($user, OrderPaidNotification::class);
        Notification::assertSentTo($user, OrderInvoiceNotification::class);
    }

    public function test_order_placed_notifies_warehouse(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $product = $this->createProduct();

        $this->actingAs($user)
            ->post(route('cart.store', $product), ['quantity' => 1]);

        $this->post(route('checkout.store'), [
            'payment_method' => PaymentMethod::Card->value,
        ]);

        $warehouse = User::query()->where('email', 'warehouse@scout-poc.test')->first();

        $this->assertNotNull($warehouse);
        Notification::assertSentTo($warehouse, WarehouseOrderNotification::class);
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
            'reserved_quantity' => 5,
        ]);

        return $product;
    }
}
