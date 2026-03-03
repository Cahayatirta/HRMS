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
                'user_id' => 1,
                'division_id' => 2,
                'full_name' => 'Admin User',
                'gender' => 'male',
                'birth_date' => '1990-01-01',
                'phone_number' => '08111111111',
                'address' => '789 Admin Road, Jakarta',
                'status' => 'active',
                'image_path' => null,
                'is_deleted' => false,
                'created_at' => '2024-05-01 08:30:00',
                'updated_at' => '2024-05-01 08:30:00',
            ],
            [
                'user_id' => 2,
                'division_id' => 2,
                'full_name' => 'John Doe',
                'gender' => 'male',
                'birth_date' => '1995-03-15',
                'phone_number' => '08123456789',
                'address' => '123 Main St, Jakarta',
                'status' => 'active',
                'image_path' => null,
                'is_deleted' => false,
                'created_at' => '2024-05-15 09:30:00',
                'updated_at' => '2024-06-01 12:00:00',
            ],
            [
                'user_id' => 3,
                'division_id' => 1,
                'full_name' => 'Jane Smith',
                'gender' => 'female',
                'birth_date' => '1998-07-07',
                'phone_number' => '08198765432',
                'address' => '456 Developer Ave, Bandung',
                'status' => 'active',
                'image_path' => null,
                'is_deleted' => false,
                'created_at' => '2024-05-20 10:30:00',
                'updated_at' => '2024-06-05 14:00:00',
            ],
            [
                'user_id' => 4,
                'division_id' => 3,
                'full_name' => 'Bob Designer',
                'gender' => 'male',
                'birth_date' => '1996-05-20',
                'phone_number' => '08133333333',
                'address' => '321 Design St, Surabaya',
                'status' => 'active',
                'image_path' => null,
                'is_deleted' => false,
                'created_at' => '2024-05-25 10:30:00',
                'updated_at' => '2024-05-25 10:30:00',
            ],
            [
                'user_id' => 5,
                'division_id' => 4,
                'full_name' => 'Alice Tester',
                'gender' => 'female',
                'birth_date' => '1997-09-12',
                'phone_number' => '08144444444',
                'address' => '654 QA Lane, Yogyakarta',
                'status' => 'active',
                'image_path' => null,
                'is_deleted' => false,
                'created_at' => '2024-05-28 10:30:00',
                'updated_at' => '2024-05-28 10:30:00',
            ],
        ]);

        $this->command->info('Employees seeded successfully!');
    }
}
