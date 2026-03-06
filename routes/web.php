<?php

use App\Http\Controllers\Student\ExamController;
use App\Http\Controllers\Student\LobbyController;
use App\Http\Controllers\Student\ResultController;
use App\Http\Middleware\StudentOnly;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        if (request()->user()->isStudent()) {
            return redirect()->route('student.lobby');
        }

        return redirect('/admin');
    })->name('dashboard');
});

// Student routes
Route::middleware(['auth', StudentOnly::class])->prefix('student')->group(function () {
    Route::get('lobby', LobbyController::class)->name('student.lobby');

    Route::get('exam/{examSession}', [ExamController::class, 'show'])->name('student.exam');
    Route::post('exam/{examSession}/answer', [ExamController::class, 'answer'])->name('student.exam.answer');
    Route::post('exam/{examSession}/submit', [ExamController::class, 'submit'])->name('student.exam.submit');
    Route::post('exam/{examSession}/anti-cheat', [ExamController::class, 'logAntiCheat'])->name('student.exam.anti-cheat');

    Route::get('results/{examAttempt}', ResultController::class)->name('student.results');
});

require __DIR__.'/settings.php';
