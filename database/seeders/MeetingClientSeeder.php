<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MeetingClientSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('meeting_clients')->insert([
            [
                'meeting_id' => 1,
                'client_id' => 1,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meeting_id' => 1,
                'client_id' => 2,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
