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
                'division_name' => 'Human Resources',
                'required_workhours' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'division_name' => 'Information Technology',
                'required_workhours' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'division_name' => 'Marketing',
                'required_workhours' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
