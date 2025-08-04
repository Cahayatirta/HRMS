<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkhourPlanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('workhour_plans')->insert([
            [
                'user_id' => 2,
                'plan_date' => now()->addDays(1)->format('Y-m-d'),
                'planned_starttime' => '08:00:00',
                'planned_endtime' => '17:00:00',
                'work_location' => 'office',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'plan_date' => now()->addDays(1)->format('Y-m-d'),
                'planned_starttime' => '09:00:00',
                'planned_endtime' => '18:00:00',
                'work_location' => 'anywhere',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
