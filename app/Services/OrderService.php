<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderService
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
    ) {}

    public function listForUser(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return $this->orders->paginateForUser($user, $perPage);
    }

    public function showForUser(User $user, int $orderId): ?Order
    {
        return $this->orders->findForUser($user, $orderId);
    }
}
