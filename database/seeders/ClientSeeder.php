<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('clients')->insert([
            [
                'name' => 'PT. Tech Solutions',
                'phone_number' => '02112345678',
                'email' => 'contact@techsolutions.com',
                'address' => 'Jl. Gatot Subroto No. 100, Jakarta',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'CV. Digital Startup',
                'phone_number' => '02187654321',
                'email' => 'info@digitalstartup.com',
                'address' => 'Jl. Kemang Raya No. 200, Jakarta',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
