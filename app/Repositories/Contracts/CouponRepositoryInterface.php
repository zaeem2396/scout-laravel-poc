<?php

namespace App\Repositories\Contracts;

use App\Models\Coupon;

interface CouponRepositoryInterface
{
    public function findValidByCode(string $code): ?Coupon;

    public function incrementUsage(Coupon $coupon): void;
}
