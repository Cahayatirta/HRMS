<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceTaskSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('attendance_tasks')->insert([
            [
                'attendance_id' => 1,
                'task_id' => 1,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'attendance_id' => 1,
                'task_id' => 2,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'attendance_id' => 2,
                'task_id' => 3,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
