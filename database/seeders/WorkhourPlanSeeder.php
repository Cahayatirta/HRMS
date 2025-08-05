<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;

class WorkhourPlanSeeder extends Seeder
{
    public function run(): void
    {

        $employee1 = Employee::first();
        $employee2 = Employee::skip(1)->first();
        
        DB::table('workhour_plans')->insert([
            [
                'employee_id' => $employee1?->id,
                'plan_date' => now()->addDays(1)->format('Y-m-d'),
                'planned_starttime' => '08:00:00',
                'planned_endtime' => '17:00:00',
                'work_location' => 'office',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'employee_id' => $employee2?->id,
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
