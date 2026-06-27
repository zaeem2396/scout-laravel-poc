<?php

namespace App\Providers;

use App\Repositories\CatalogRepository;
use App\Repositories\Contracts\CatalogRepositoryInterface;
use App\Repositories\Contracts\CouponRepositoryInterface;
use App\Repositories\Contracts\InventoryRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\CouponRepository;
use App\Repositories\InventoryRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Support\ObservabilityMetricsCollector;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(\App\Support\ObservabilityMetricsCollector::class);

        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(CatalogRepositoryInterface::class, CatalogRepository::class);
        $this->app->bind(CouponRepositoryInterface::class, CouponRepository::class);
        $this->app->bind(InventoryRepositoryInterface::class, InventoryRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        DB::listen(function () {
            if (! app()->bound(ObservabilityMetricsCollector::class)) {
                return;
            }

            app(ObservabilityMetricsCollector::class)->incrementQueryCount();
        });

        Event::listen('*', function () {
            if (! app()->bound(ObservabilityMetricsCollector::class)) {
                return;
            }

            app(ObservabilityMetricsCollector::class)->incrementEventCount();
        });
    }
}
