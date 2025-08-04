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
                'date' => now()->addDays(3)->format('Y-m-d'),
                'start_time' => '10:00:00',
                'end_time' => '12:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meeting_title' => 'Weekly Team Standup',
                'meeting_note' => 'Regular team progress review',
                'date' => now()->addDays(1)->format('Y-m-d'),
                'start_time' => '09:00:00',
                'end_time' => '10:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
