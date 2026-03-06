<?php

namespace Database\Factories;

use App\Models\ExamSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExamAttempt>
 */
class ExamAttemptFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'exam_session_id' => ExamSession::factory(),
            'user_id' => User::factory(),
            'started_at' => now(),
            'submitted_at' => null,
            'score' => null,
            'total_points' => 0,
            'status' => 'in_progress',
            'flag_count' => 0,
        ];
    }

    public function submitted(): static
    {
        return $this->state(fn () => [
            'status' => 'submitted',
            'submitted_at' => now(),
            'score' => 3,
            'total_points' => 5,
        ]);
    }
}
