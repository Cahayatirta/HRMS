<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('employees')->insert([
            [
                'user_id' => 2,
                'division_id' => 2,
                'full_name' => 'John Doe',
                'gender' => 'male',
                'birth_date' => '1990-05-15',
                'phone_number' => '081234567890',
                'address' => 'Jl. Sudirman No. 123, Jakarta',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'division_id' => 1,
                'full_name' => 'Jane Smith',
                'gender' => 'female',
                'birth_date' => '1992-08-20',
                'phone_number' => '081234567891',
                'address' => 'Jl. Thamrin No. 456, Jakarta',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
