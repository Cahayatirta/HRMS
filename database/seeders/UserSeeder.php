<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin User',
                'email' => 'admin@company.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_deleted' => false,
                'created_at' => '2024-05-01 08:00:00',
                'updated_at' => '2024-05-01 08:00:00',
            ],
            [
                'name' => 'John Doe',
                'email' => 'john.doe@company.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'is_deleted' => false,
                'created_at' => '2024-05-15 09:00:00',
                'updated_at' => '2024-06-01 12:00:00',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@company.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'is_deleted' => false,
                'created_at' => '2024-05-20 10:00:00',
                'updated_at' => '2024-06-05 14:00:00',
            ],
            [
                'name' => 'Bob Designer',
                'email' => 'bob@company.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'is_deleted' => false,
                'created_at' => '2024-05-25 10:00:00',
                'updated_at' => '2024-05-25 10:00:00',
            ],
            [
                'name' => 'Alice Tester',
                'email' => 'alice@company.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'is_deleted' => false,
                'created_at' => '2024-05-28 10:00:00',
                'updated_at' => '2024-05-28 10:00:00',
            ],
        ]);

        $this->command->info('Users seeded successfully!');
        $this->command->info('Test credentials:');
        $this->command->info('- admin@company.com (password: admin123) - Admin');
        $this->command->info('- john.doe@company.com (password: password123) - Project Manager');
        $this->command->info('- jane.smith@company.com (password: password123) - Developer');
        $this->command->info('- bob@company.com (password: password123) - Designer');
        $this->command->info('- alice@company.com (password: password123) - QA Tester');
    }
}
