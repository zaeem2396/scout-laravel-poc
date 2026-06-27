<?php

namespace App\Services\Demo;

use Illuminate\Support\Facades\DB;

class SlowQueryDemoService
{
    /**
     * Execute an intentionally inefficient query for observability demos.
     *
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            $result = DB::selectOne('
                SELECT COUNT(*) AS total
                FROM products p
                INNER JOIN vendors v ON v.id = p.vendor_id
                INNER JOIN categories c ON c.id = p.category_id
                LEFT JOIN reviews r ON r.product_id = p.id
                WHERE p.is_active = 1
                  AND SLEEP(1.5) = 0
            ');

            return [
                'driver' => $driver,
                'matched_products' => (int) $result->total,
            ];
        }

        $result = DB::selectOne('
            SELECT COUNT(*) AS total
            FROM products p
            CROSS JOIN categories c
            WHERE p.is_active = 1
        ');

        return [
            'driver' => $driver,
            'matched_products' => (int) $result->total,
        ];
    }
}
