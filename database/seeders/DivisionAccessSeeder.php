<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DivisionAccessSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('division_accesses')->insert([
            [
                'division_id' => 1,
                'access_id' => 1,
                'is_deleted' => false,
            ],
            [
                'division_id' => 1,
                'access_id' => 2,
                'is_deleted' => false,
            ],
            [
                'division_id' => 2,
                'access_id' => 1,
                'is_deleted' => false,
            ],
            [
                'division_id' => 2,
                'access_id' => 3,
                'is_deleted' => false,
            ],
            [
                'division_id' => 3,
                'access_id' => 1,
                'is_deleted' => false,
            ],
        ]);
    }
}
