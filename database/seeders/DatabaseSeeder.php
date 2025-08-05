<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            AccessSeeder::class,
            DivisionSeeder::class,
            EmployeeSeeder::class,
            TaskSeeder::class,
            AttendanceSeeder::class,
            WorkhourPlanSeeder::class,
            ClientSeeder::class,
            ServiceTypeSeeder::class,
            ServiceTypeFieldSeeder::class,
            ServiceSeeder::class,
            MeetingSeeder::class,
            DivisionAccessSeeder::class,
            MeetingUserSeeder::class,
            MeetingClientSeeder::class,
            EmployeeTaskSeeder::class,
            AttendanceTaskSeeder::class,
            ServiceTypeDataSeeder::class,
        ]);
    }
}
