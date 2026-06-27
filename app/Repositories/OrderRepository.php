<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\User;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderRepository implements OrderRepositoryInterface
{
    public function create(array $attributes): Order
    {
        return Order::query()->create($attributes);
    }

    public function paginateForUser(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return Order::query()
            ->with(['vendor', 'items.product'])
            ->where('user_id', $user->id)
            ->orderByDesc('placed_at')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function findForUser(User $user, int $orderId): ?Order
    {
        return Order::query()
            ->with(['vendor', 'items.product', 'payment', 'coupon'])
            ->where('user_id', $user->id)
            ->whereKey($orderId)
            ->first();
    }
}
