<?php

namespace App\Repositories\Contracts;

use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Order;

    public function paginateForUser(User $user, int $perPage = 15): LengthAwarePaginator;

    public function findForUser(User $user, int $orderId): ?Order;
}
