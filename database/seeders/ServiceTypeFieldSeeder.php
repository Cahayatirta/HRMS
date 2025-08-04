<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceTypeFieldSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('service_type_fields')->insert([
            [
                'service_type_id' => 1,
                'field_name' => 'Framework',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'service_type_id' => 1,
                'field_name' => 'Database Type',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'service_type_id' => 2,
                'field_name' => 'Platform',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'service_type_id' => 3,
                'field_name' => 'Expertise Area',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
