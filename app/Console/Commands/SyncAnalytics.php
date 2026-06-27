<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SyncAnalytics extends Command
{
    protected $signature = 'analytics:sync';

    protected $description = 'Sync marketplace analytics aggregates to cache';

    public function handle(): int
    {
        $summary = [
            'orders_total' => Order::query()->count(),
            'products_total' => Product::query()->count(),
            'revenue_total' => (float) Order::query()->sum('total'),
            'synced_at' => now()->toIso8601String(),
        ];

        Cache::put('analytics:marketplace_summary', $summary, now()->addHour());

        Log::info('Analytics synced', $summary);

        $this->info('Marketplace analytics synced to cache.');

        return self::SUCCESS;
    }
}
