<?php

namespace Database\Factories;

use App\Models\Exam;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExamSession>
 */
class ExamSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'exam_id' => Exam::factory(),
            'status' => 'waiting',
            'started_at' => null,
            'ended_at' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status' => 'active',
            'started_at' => now(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => 'completed',
            'started_at' => now()->subHour(),
            'ended_at' => now(),
        ]);
    }
}
