<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition()
    {
        return [
            'categoryId' => 1, // Assuming categories exist
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'status' => $this->faker->randomElement(['pending', 'completed']),
            'dueDate' => $this->faker->dateTimeBetween('now', '+1 year'),
            'userId' => 12, // Fixed user ID
        ];
    }
}
