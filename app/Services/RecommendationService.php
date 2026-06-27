<?php

namespace App\Services;

class RecommendationService
{
    /**
     * Intentionally slow recommendation scoring for observability demos.
     *
     * @param  array<int, int>  $productIds
     * @return array<int, float>
     */
    public function scoreProducts(array $productIds): array
    {
        usleep(random_int(600_000, 1_000_000));

        $scores = [];

        foreach ($productIds as $productId) {
            $scores[$productId] = round(mt_rand(50, 100) / 100, 2);
        }

        return $scores;
    }
}
