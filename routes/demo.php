<?php

use App\Http\Controllers\DemoController;
use App\Http\Middleware\FeatureFlagMiddleware;
use App\Http\Middleware\RequestLogger;
use App\Http\Middleware\TenantResolver;
use Illuminate\Support\Facades\Route;

Route::prefix('demo')
    ->middleware([
        TenantResolver::class,
        RequestLogger::class,
        FeatureFlagMiddleware::class,
    ])
    ->name('demo.')
    ->group(function () {
        Route::get('/', [DemoController::class, 'index'])->name('index');
        Route::get('/request', [DemoController::class, 'request'])->name('request');
        Route::get('/n-plus-one', [DemoController::class, 'nPlusOne'])->name('n-plus-one');
        Route::get('/slow-query', [DemoController::class, 'slowQuery'])->name('slow-query');
        Route::get('/slow-method', [DemoController::class, 'slowMethod'])->name('slow-method');
        Route::get('/cache', [DemoController::class, 'cache'])->name('cache');
        Route::get('/events', [DemoController::class, 'events'])->name('events');
        Route::get('/jobs', [DemoController::class, 'jobs'])->name('jobs');
        Route::get('/memory', [DemoController::class, 'memory'])->name('memory');
        Route::get('/exception', [DemoController::class, 'exception'])->name('exception');
        Route::post('/full-flow', [DemoController::class, 'fullFlow'])
            ->middleware('auth')
            ->name('full-flow');
    });
