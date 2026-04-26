<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DivisionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('divisions')->insert([
            [
                'division_name' => 'Developer',
                'required_workhours' => 40,
                'is_deleted' => false,
                'created_at' => '2024-05-01 09:00:00',
                'updated_at' => '2024-05-01 09:00:00',
            ],
            [
                'division_name' => 'Project Manager',
                'required_workhours' => 40,
                'is_deleted' => false,
                'created_at' => '2024-05-01 09:00:00',
                'updated_at' => '2024-05-01 09:00:00',
            ],
            [
                'division_name' => 'Designer',
                'required_workhours' => 40,
                'is_deleted' => false,
                'created_at' => '2024-05-01 09:00:00',
                'updated_at' => '2024-05-01 09:00:00',
            ],
            [
                'division_name' => 'QA Tester',
                'required_workhours' => 35,
                'is_deleted' => false,
                'created_at' => '2024-05-01 09:00:00',
                'updated_at' => '2024-05-01 09:00:00',
            ],
        ]);

        $this->command->info('Divisions seeded successfully!');
    }
}
