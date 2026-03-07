<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use App\Models\ExamSession;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        $attempts = $user->examAttempts()
            ->with('session.exam:id,title,pass_score')
            ->whereIn('status', ['submitted', 'graded'])
            ->latest('submitted_at')
            ->get();

        $activeSessions = ExamSession::query()
            ->with('exam:id,title,duration_minutes')
            ->where('status', 'active')
            ->count();

        $totalExams = $attempts->count();
        $averageScore = $totalExams > 0
            ? round($attempts->avg('percentage'), 1)
            : null;
        $passedCount = $attempts->filter(fn (ExamAttempt $a) => $a->passed)->count();

        return Inertia::render('Dashboard', [
            'stats' => [
                'total_exams' => $totalExams,
                'average_score' => $averageScore,
                'passed_count' => $passedCount,
                'active_sessions' => $activeSessions,
            ],
            'recentAttempts' => Inertia::defer(fn () => $attempts->take(5)->map(fn (ExamAttempt $a) => [
                'id' => $a->id,
                'exam_title' => $a->session->exam->title,
                'score' => $a->score,
                'total_points' => $a->total_points,
                'percentage' => $a->percentage,
                'passed' => $a->passed,
                'flag_count' => $a->flag_count,
                'submitted_at' => $a->submitted_at?->diffForHumans(),
            ])),
        ]);
    }
}
