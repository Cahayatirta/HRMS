<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tasks')->insert([
            [
                'task_name' => 'Project Planning',
                'task_description' => 'Create detailed project plan for Q1',
                'deadline' => '2025-02-28',
                'status' => 'pending',
                'parent_task_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'task_name' => 'Database Design',
                'task_description' => 'Design database schema for new feature',
                'deadline' => '2025-02-15',
                'status' => 'in_progress',
                'parent_task_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'task_name' => 'Marketing Campaign',
                'task_description' => 'Develop marketing strategy for new product',
                'deadline' => '2025-03-10',
                'status' => 'pending',
                'parent_task_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
