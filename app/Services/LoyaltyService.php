<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class LoyaltyService
{
    public function rewardForOrder(User $user, Order $order): int
    {
        $points = (int) floor((float) $order->total);

        Log::info('Loyalty points awarded', [
            'user_id' => $user->id,
            'order_id' => $order->id,
            'points' => $points,
        ]);

        return $points;
    }
}
