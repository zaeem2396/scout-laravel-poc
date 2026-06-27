<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Services\LoyaltyService;

class RewardCustomer
{
    public function __construct(
        private readonly LoyaltyService $loyalty,
    ) {}

    public function handle(OrderPlaced $event): void
    {
        $order = $event->order->loadMissing('user');

        $this->loyalty->rewardForOrder($order->user, $order);
    }
}
