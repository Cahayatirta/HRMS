<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('attendances')->insert([
            [
                'employee_id' => 1,
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'work_location' => 'office',
                'longitude' => 106.8456,
                'latitude' => -6.2088,
                'image_path' => null,
                'task_link' => null,
                'is_deleted' => false,
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
            [
                'employee_id' => 2,
                'start_time' => '09:00:00',
                'end_time' => '18:00:00',
                'work_location' => 'anywhere',
                'longitude' => null,
                'latitude' => null,
                'image_path' => null,
                'task_link' => null,
                'is_deleted' => false,
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
            [
                'employee_id' => 1,
                'start_time' => '08:30:00',
                'end_time' => null,
                'work_location' => 'office',
                'longitude' => 106.8456,
                'latitude' => -6.2088,
                'image_path' => null,
                'task_link' => null,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
