<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;

class TaskSeeder extends Seeder
{
    public function run()
    {
        // Generate 100 tasks with userId = 12
        Task::factory()->count(100)->create();
    }
}
    