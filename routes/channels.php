<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('orders.{userId}', function ($user, int $userId) {
    return (int) $user->id === $userId;
});
