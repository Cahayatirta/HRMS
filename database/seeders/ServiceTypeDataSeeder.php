<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceTypeDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('service_type_data')->insert([
            [
                'field_id' => 1,
                'service_id' => 1,
                'value' => 'Laravel',
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'field_id' => 2,
                'service_id' => 1,
                'value' => 'MySQL',
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'field_id' => 3,
                'service_id' => 2,
                'value' => 'React Native',
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'field_id' => 4,
                'service_id' => 3,
                'value' => 'Cloud Architecture',
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
