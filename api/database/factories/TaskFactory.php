<?php

namespace Database\Factories;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'duration' => fake()->numberBetween(10, 300),
            'is_done' => fake()->boolean(20),
            'assignees' => $this->generateAssignees(),
            'name' => fake()->sentence(3),
            'priority' => fake()->randomElement(['alacsony', 'normál', 'magas']),
            'scheduled_day' => $this->generateWeekday(),
        ];
    }

    /**
     * Generate a weekday date
     */
    private function generateWeekday(): string
    {
        do {
            $date = Carbon::now()->addDays(fake()->numberBetween(1, 30));
        } while ($date->isWeekend());

        return $date->toDateString();
    }

    /**
     * Generate an array of random names (max 4)
     */
    private function generateAssignees(): false|string
    {
        $names = ['Alice', 'Bob', 'Charlie', 'David', 'Eve', 'Frank', 'Grace', 'Hannah'];
        shuffle($names);
        return json_encode(array_slice($names, 0, fake()->numberBetween(1, 4)));
    }
}
