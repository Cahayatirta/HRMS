<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccessSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('accesses')->insert([
            [
                'access_name' => 'View Dashboard',
                'access_description' => 'Access to view main dashboard',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'access_name' => 'Manage Users',
                'access_description' => 'Create, edit, delete users',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'access_name' => 'View Reports',
                'access_description' => 'Access to view various reports',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
