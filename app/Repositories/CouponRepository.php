<?php

namespace App\Repositories;

use App\Models\Coupon;
use App\Repositories\Contracts\CouponRepositoryInterface;

class CouponRepository implements CouponRepositoryInterface
{
    public function findValidByCode(string $code): ?Coupon
    {
        return Coupon::query()
            ->where('code', strtoupper($code))
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->where(function ($query) {
                $query->whereNull('max_uses')
                    ->orWhereColumn('used_count', '<', 'max_uses');
            })
            ->first();
    }

    public function incrementUsage(Coupon $coupon): void
    {
        $coupon->increment('used_count');
    }
}
