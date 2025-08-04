<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('services')->insert([
            [
                'client_id' => 1,
                'service_type_id' => 1,
                'status' => 'ongoing',
                'price' => 50000000,
                'start_time' => now()->subDays(30),
                'expired_time' => now()->addDays(60),
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => 2,
                'service_type_id' => 2,
                'status' => 'pending',
                'price' => 75000000,
                'start_time' => null,
                'expired_time' => null,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => 1,
                'service_type_id' => 3,
                'status' => 'ongoing',
                'price' => 25000000,
                'start_time' => now()->subDays(15),
                'expired_time' => now()->addDays(45),
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
