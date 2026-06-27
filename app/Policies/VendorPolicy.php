<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vendor;

class VendorPolicy
{
    public function view(?User $user, Vendor $vendor): bool
    {
        return $vendor->is_active;
    }

    public function update(User $user, Vendor $vendor): bool
    {
        return $vendor->user_id === $user->id;
    }

    public function delete(User $user, Vendor $vendor): bool
    {
        return $vendor->user_id === $user->id;
    }
}
