<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function view(?User $user, Product $product): bool
    {
        return $product->is_active;
    }

    public function update(User $user, Product $product): bool
    {
        return $product->vendor?->user_id === $user->id;
    }

    public function delete(User $user, Product $product): bool
    {
        return $product->vendor?->user_id === $user->id;
    }
}
