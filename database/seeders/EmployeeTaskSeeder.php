<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;

class EmployeeTaskSeeder extends Seeder
{
    public function run(): void
    {
        $employee1 = Employee::first();
        $employee2 = Employee::skip(1)->first();

        DB::table('employee_tasks')->insert([
            [
                'employee_id' => 1,
                'task_id' => 1,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'employee_id' => 2,
                'task_id' => 2,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'employee_id' => 2,
                'task_id' => 3,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
