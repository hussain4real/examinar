<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@examinar.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $students = collect();
        for ($i = 1; $i <= 5; $i++) {
            $students->push(User::create([
                'name' => "Student {$i}",
                'email' => "student{$i}@examinar.com",
                'password' => Hash::make('password'),
                'role' => 'student',
            ]));
        }

        $exam = Exam::create([
            'title' => 'Sample General Knowledge Quiz',
            'description' => '<p>A sample exam to test the system. Contains MCQ and True/False questions.</p>',
            'duration_minutes' => 30,
            'pass_score' => 60,
            'shuffle_questions' => true,
            'status' => 'published',
            'created_by' => $admin->id,
        ]);

        Question::create([
            'exam_id' => $exam->id,
            'type' => 'mcq',
            'body' => '<p>What is the capital of France?</p>',
            'options' => [['text' => 'London'], ['text' => 'Paris'], ['text' => 'Berlin'], ['text' => 'Madrid']],
            'correct_answer' => 'Paris',
            'points' => 2,
            'order' => 1,
        ]);

        Question::create([
            'exam_id' => $exam->id,
            'type' => 'mcq',
            'body' => '<p>Which planet is known as the Red Planet?</p>',
            'options' => [['text' => 'Venus'], ['text' => 'Mars'], ['text' => 'Jupiter'], ['text' => 'Saturn']],
            'correct_answer' => 'Mars',
            'points' => 2,
            'order' => 2,
        ]);

        Question::create([
            'exam_id' => $exam->id,
            'type' => 'true_false',
            'body' => '<p>The Earth revolves around the Sun.</p>',
            'options' => null,
            'correct_answer' => 'true',
            'points' => 1,
            'order' => 3,
        ]);

        Question::create([
            'exam_id' => $exam->id,
            'type' => 'mcq',
            'body' => '<p>What is 12 × 12?</p>',
            'options' => [['text' => '124'], ['text' => '144'], ['text' => '132'], ['text' => '156']],
            'correct_answer' => '144',
            'points' => 1,
            'order' => 4,
        ]);

        Question::create([
            'exam_id' => $exam->id,
            'type' => 'true_false',
            'body' => '<p>Water boils at 90°C at sea level.</p>',
            'options' => null,
            'correct_answer' => 'false',
            'points' => 1,
            'order' => 5,
        ]);
    }
}
