<?php

namespace Database\Factories;

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
            'type' => 'mcq',
            'body' => fake()->sentence().'?',
            'options' => [['text' => 'Option A'], ['text' => 'Option B'], ['text' => 'Option C'], ['text' => 'Option D']],
            'correct_answer' => 'Option A',
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
