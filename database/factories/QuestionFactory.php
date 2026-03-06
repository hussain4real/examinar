<?php

namespace Database\Factories;

use App\Models\Exam;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
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
            'type' => 'mcq',
            'body' => fake()->sentence().'?',
            'options' => [['text' => 'Option A'], ['text' => 'Option B'], ['text' => 'Option C'], ['text' => 'Option D']],
            'correct_answer' => 'Option A',
            'points' => 1,
            'order' => 1,
        ];
    }

    public function trueFalse(): static
    {
        return $this->state(fn () => [
            'type' => 'true_false',
            'options' => null,
            'correct_answer' => 'True',
        ]);
    }
}
