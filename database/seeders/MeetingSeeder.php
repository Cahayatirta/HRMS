<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MeetingSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('meetings')->insert([
            [
                'meeting_title' => 'Project Kickoff Meeting',
                'meeting_note' => 'Initial project discussion and planning',
                'start_time' => now()->addDays(3)->setTime(10, 0, 0)->format('Y-m-d H:i:s'),
                'end_time' => now()->addDays(3)->setTime(12, 0, 0)->format('Y-m-d H:i:s'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meeting_title' => 'Weekly Team Standup',
                'meeting_note' => 'Regular team progress review',
                'start_time' => now()->addDays(1)->setTime(9, 0, 0)->format('Y-m-d H:i:s'),
                'end_time' => now()->addDays(1)->setTime(10, 0, 0)->format('Y-m-d H:i:s'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
