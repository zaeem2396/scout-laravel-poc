<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Models\User;
use App\Notifications\WarehouseOrderNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class NotifyWarehouse
{
    public function handle(OrderPlaced $event): void
    {
        $order = $event->order->loadMissing(['vendor', 'items', 'user']);

        $warehouseContact = User::query()->firstOrCreate(
            ['email' => 'warehouse@scout-poc.test'],
            [
                'name' => 'Warehouse Team',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        Notification::send($warehouseContact, new WarehouseOrderNotification($order));
    }
}
