<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateDailyReport extends Command
{
    protected $signature = 'report:daily';

    protected $description = 'Generate a daily marketplace sales report';

    public function handle(): int
    {
        $ordersToday = Order::query()
            ->whereDate('created_at', today())
            ->count();

        $revenueToday = (float) Order::query()
            ->whereDate('created_at', today())
            ->sum('total');

        Log::info('Daily report generated', [
            'date' => today()->toDateString(),
            'orders' => $ordersToday,
            'revenue' => $revenueToday,
        ]);

        $this->info("Daily report: {$ordersToday} orders, {$revenueToday} revenue.");

        return self::SUCCESS;
    }
}
