<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ExamSession;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LobbyController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $sessions = ExamSession::query()
            ->with('exam:id,title,description,duration_minutes,pass_score')
            ->withCount(['attempts as student_count'])
            ->whereIn('status', ['waiting', 'active'])
            ->latest()
            ->get();

        $pastAttempts = $request->user()
            ->examAttempts()
            ->with(['session.exam:id,title,pass_score'])
            ->whereIn('status', ['submitted', 'graded'])
            ->latest('submitted_at')
            ->limit(5)
            ->get();

        return Inertia::render('student/Lobby', [
            'sessions' => $sessions,
            'pastAttempts' => $pastAttempts,
        ]);
    }
}
