<?php

namespace Database\Seeders;

use App\Enums\CouponType;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MarketplaceSeeder extends Seeder
{
    private const int USER_COUNT = 500;

    private const int VENDOR_COUNT = 50;

    private const int CATEGORY_COUNT = 100;

    private const int PRODUCT_COUNT = 5000;

    private const int REVIEW_COUNT = 50000;

    private const int ORDER_COUNT = 100000;

    private const int ORDER_ITEM_COUNT = 500000;

    private const int COUPON_COUNT = 50;

    private const int IMAGES_PER_PRODUCT = 2;

    private const int ITEMS_PER_ORDER = 5;

    private const int CHUNK_SIZE = 1000;

    private string $now;

    /** @var array<int, array{start: int, end: int}> */
    private array $vendorProductRanges = [];

    public function run(): void
    {
        $this->now = now()->toDateTimeString();

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $this->truncateMarketplaceTables();

        $this->seedUsers();
        $this->seedVendors();
        $this->seedCategories();
        $this->seedProducts();
        $this->seedProductImages();
        $this->seedInventory();
        $this->seedCoupons();
        $this->seedOrders();
        $this->seedOrderItems();
        $this->seedReviews();
        $this->seedPayments();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    private function truncateMarketplaceTables(): void
    {
        $tables = [
            'payments',
            'order_items',
            'orders',
            'reviews',
            'inventory',
            'product_images',
            'products',
            'coupons',
            'categories',
            'vendors',
            'notifications',
            'users',
        ];

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }
    }

    private function seedUsers(): void
    {
        $password = Hash::make('password');
        $rows = [];

        for ($i = 1; $i <= self::USER_COUNT; $i++) {
            $rows[] = [
                'name' => "User {$i}",
                'email' => "user{$i}@scout-poc.test",
                'email_verified_at' => $this->now,
                'password' => $password,
                'remember_token' => Str::random(10),
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ];
        }

        foreach (array_chunk($rows, self::CHUNK_SIZE) as $chunk) {
            DB::table('users')->insert($chunk);
        }
    }

    private function seedVendors(): void
    {
        $rows = [];

        for ($i = 1; $i <= self::VENDOR_COUNT; $i++) {
            $name = "Vendor {$i}";
            $rows[] = [
                'user_id' => $i,
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => "Marketplace vendor {$i} offering curated products.",
                'is_active' => true,
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ];
        }

        DB::table('vendors')->insert($rows);
    }

    private function seedCategories(): void
    {
        $rows = [];

        for ($i = 1; $i <= self::CATEGORY_COUNT; $i++) {
            $name = "Category {$i}";
            $rows[] = [
                'parent_id' => $i > 20 ? (($i - 1) % 20) + 1 : null,
                'name' => $name,
                'slug' => Str::slug($name)."-{$i}",
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ];
        }

        DB::table('categories')->insert($rows);
    }

    private function seedProducts(): void
    {
        $productsPerVendor = intdiv(self::PRODUCT_COUNT, self::VENDOR_COUNT);
        $productId = 1;
        $rows = [];

        for ($vendorId = 1; $vendorId <= self::VENDOR_COUNT; $vendorId++) {
            $startId = $productId;

            for ($n = 0; $n < $productsPerVendor; $n++) {
                $name = "Product {$productId}";
                $rows[] = [
                    'vendor_id' => $vendorId,
                    'category_id' => (($productId - 1) % self::CATEGORY_COUNT) + 1,
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'description' => "Description for {$name}.",
                    'sku' => 'SKU-'.str_pad((string) $productId, 8, '0', STR_PAD_LEFT),
                    'price' => round(5 + ($productId % 495) + ($productId % 100) / 100, 2),
                    'is_active' => true,
                    'created_at' => $this->now,
                    'updated_at' => $this->now,
                ];

                if (count($rows) >= self::CHUNK_SIZE) {
                    DB::table('products')->insert($rows);
                    $rows = [];
                }

                $productId++;
            }

            $this->vendorProductRanges[$vendorId] = [
                'start' => $startId,
                'end' => $productId - 1,
            ];
        }

        if ($rows !== []) {
            DB::table('products')->insert($rows);
        }
    }

    private function seedProductImages(): void
    {
        $rows = [];

        for ($productId = 1; $productId <= self::PRODUCT_COUNT; $productId++) {
            for ($sort = 0; $sort < self::IMAGES_PER_PRODUCT; $sort++) {
                $rows[] = [
                    'product_id' => $productId,
                    'path' => "products/{$productId}/image-{$sort}.jpg",
                    'sort_order' => $sort,
                    'created_at' => $this->now,
                    'updated_at' => $this->now,
                ];
            }

            if (count($rows) >= self::CHUNK_SIZE) {
                DB::table('product_images')->insert($rows);
                $rows = [];
            }
        }

        if ($rows !== []) {
            DB::table('product_images')->insert($rows);
        }
    }

    private function seedInventory(): void
    {
        $rows = [];

        for ($productId = 1; $productId <= self::PRODUCT_COUNT; $productId++) {
            $rows[] = [
                'product_id' => $productId,
                'quantity' => 50 + ($productId % 200),
                'reserved_quantity' => $productId % 10,
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ];

            if (count($rows) >= self::CHUNK_SIZE) {
                DB::table('inventory')->insert($rows);
                $rows = [];
            }
        }

        if ($rows !== []) {
            DB::table('inventory')->insert($rows);
        }
    }

    private function seedCoupons(): void
    {
        $rows = [];

        for ($i = 1; $i <= self::COUPON_COUNT; $i++) {
            $isPercentage = $i % 2 === 0;
            $rows[] = [
                'code' => 'SAVE'.str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                'type' => $isPercentage ? CouponType::Percentage->value : CouponType::Fixed->value,
                'value' => $isPercentage ? 10 + ($i % 15) : round(5 + ($i % 20), 2),
                'min_order_amount' => $i % 5 === 0 ? 50 : 0,
                'max_uses' => 1000,
                'used_count' => $i * 10,
                'starts_at' => Carbon::now()->subMonths(6),
                'expires_at' => Carbon::now()->addMonths(6),
                'is_active' => true,
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ];
        }

        DB::table('coupons')->insert($rows);
    }

    private function seedOrders(): void
    {
        $ordersPerVendor = intdiv(self::ORDER_COUNT, self::VENDOR_COUNT);
        $orderId = 1;
        $rows = [];
        $statuses = OrderStatus::cases();

        for ($vendorId = 1; $vendorId <= self::VENDOR_COUNT; $vendorId++) {
            for ($n = 0; $n < $ordersPerVendor; $n++) {
                $subtotal = round(25 + ($orderId % 400) + ($orderId % 97) / 100, 2);
                $discount = $orderId % 7 === 0 ? round($subtotal * 0.1, 2) : 0;
                $tax = round(($subtotal - $discount) * 0.08, 2);
                $status = $statuses[$orderId % count($statuses)];

                $rows[] = [
                    'user_id' => ($orderId % self::USER_COUNT) + 1,
                    'vendor_id' => $vendorId,
                    'coupon_id' => $orderId % 11 === 0 ? (($orderId % self::COUPON_COUNT) + 1) : null,
                    'status' => $status->value,
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'tax' => $tax,
                    'total' => round($subtotal - $discount + $tax, 2),
                    'placed_at' => Carbon::now()->subDays($orderId % 730),
                    'created_at' => $this->now,
                    'updated_at' => $this->now,
                ];

                if (count($rows) >= self::CHUNK_SIZE) {
                    DB::table('orders')->insert($rows);
                    $rows = [];
                }

                $orderId++;
            }
        }

        if ($rows !== []) {
            DB::table('orders')->insert($rows);
        }
    }

    private function seedOrderItems(): void
    {
        $rows = [];
        $itemsCreated = 0;
        $orderId = 1;

        while ($itemsCreated < self::ORDER_ITEM_COUNT) {
            $vendorId = (($orderId - 1) % self::VENDOR_COUNT) + 1;
            $range = $this->vendorProductRanges[$vendorId];

            for ($line = 0; $line < self::ITEMS_PER_ORDER && $itemsCreated < self::ORDER_ITEM_COUNT; $line++) {
                $productId = $range['start'] + (($orderId + $line) % ($range['end'] - $range['start'] + 1));
                $quantity = 1 + (($orderId + $line) % 4);
                $unitPrice = round(5 + ($productId % 120) + 0.99, 2);

                $rows[] = [
                    'order_id' => $orderId,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total' => round($unitPrice * $quantity, 2),
                    'created_at' => $this->now,
                    'updated_at' => $this->now,
                ];

                $itemsCreated++;
            }

            if (count($rows) >= self::CHUNK_SIZE) {
                DB::table('order_items')->insert($rows);
                $rows = [];
            }

            $orderId++;
        }

        if ($rows !== []) {
            DB::table('order_items')->insert($rows);
        }
    }

    private function seedReviews(): void
    {
        $pairs = [];
        $rows = [];
        $attempts = 0;
        $maxAttempts = self::REVIEW_COUNT * 3;

        while (count($pairs) < self::REVIEW_COUNT && $attempts < $maxAttempts) {
            $attempts++;
            $userId = random_int(1, self::USER_COUNT);
            $productId = random_int(1, self::PRODUCT_COUNT);
            $key = "{$userId}:{$productId}";

            if (isset($pairs[$key])) {
                continue;
            }

            $pairs[$key] = true;
            $rating = 1 + (count($pairs) % 5);

            $rows[] = [
                'user_id' => $userId,
                'product_id' => $productId,
                'rating' => $rating,
                'title' => "Review for product {$productId}",
                'body' => "Detailed feedback from user {$userId} about product {$productId}.",
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ];

            if (count($rows) >= self::CHUNK_SIZE) {
                DB::table('reviews')->insert($rows);
                $rows = [];
            }
        }

        if ($rows !== []) {
            DB::table('reviews')->insert($rows);
        }
    }

    private function seedPayments(): void
    {
        $paidStatuses = [
            OrderStatus::Paid->value,
            OrderStatus::Processing->value,
            OrderStatus::Shipped->value,
            OrderStatus::Delivered->value,
        ];

        $rows = [];
        $paymentId = 1;

        DB::table('orders')
            ->select(['id', 'total', 'status'])
            ->orderBy('id')
            ->chunk(self::CHUNK_SIZE, function ($orders) use (&$rows, &$paymentId, $paidStatuses) {
                foreach ($orders as $order) {
                    if (! in_array($order->status, $paidStatuses, true)) {
                        continue;
                    }

                    $rows[] = [
                        'order_id' => $order->id,
                        'amount' => $order->total,
                        'currency' => 'USD',
                        'method' => PaymentMethod::cases()[$paymentId % 3]->value,
                        'status' => PaymentStatus::Completed->value,
                        'transaction_id' => 'txn_'.str_pad((string) $paymentId, 10, '0', STR_PAD_LEFT),
                        'paid_at' => $this->now,
                        'created_at' => $this->now,
                        'updated_at' => $this->now,
                    ];

                    $paymentId++;

                    if (count($rows) >= self::CHUNK_SIZE) {
                        DB::table('payments')->insert($rows);
                        $rows = [];
                    }
                }
            });

        if ($rows !== []) {
            DB::table('payments')->insert($rows);
        }
    }
}
