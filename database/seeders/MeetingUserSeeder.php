<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MeetingUserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('meeting_users')->insert([
            [
                'meeting_id' => 1,
                'user_id' => 1,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meeting_id' => 1,
                'user_id' => 2,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meeting_id' => 2,
                'user_id' => 2,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meeting_id' => 2,
                'user_id' => 3,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
