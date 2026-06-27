<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(MarketplaceSeeder::class);

        User::query()->firstOrCreate(
            ['email' => 'admin@scout-poc.test'],
            [
                'name' => 'Admin User',
                'password' => 'password',
                'email_verified_at' => now(),
            ],
        );
    }
}
