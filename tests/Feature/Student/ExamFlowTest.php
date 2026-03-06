<?php

use App\Models\Answer;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamSession;
use App\Models\Question;
use App\Models\User;

beforeEach(function () {
    $this->student = User::factory()->create(['role' => 'student']);
    $this->admin = User::factory()->create(['role' => 'admin']);

    $this->exam = Exam::factory()->create(['created_by' => $this->admin->id]);

    $this->questions = Question::factory()->count(3)->sequence(
        ['order' => 1, 'correct_answer' => 'Option A'],
        ['order' => 2, 'correct_answer' => 'Option B'],
        ['order' => 3, 'correct_answer' => 'Option A'],
    )->create(['exam_id' => $this->exam->id]);
});

// --- Lobby ---

it('shows the lobby for authenticated students', function () {
    $this->actingAs($this->student)
        ->get('/student/lobby')
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page->component('student/Lobby'));
});

it('redirects admins from student lobby', function () {
    $this->actingAs($this->admin)
        ->get('/student/lobby')
        ->assertRedirect('/admin');
});

it('redirects guests from student lobby', function () {
    $this->get('/student/lobby')
        ->assertRedirect('/login');
});

it('displays waiting and active sessions', function () {
    $waiting = ExamSession::factory()->create(['exam_id' => $this->exam->id, 'status' => 'waiting']);
    $active = ExamSession::factory()->active()->create(['exam_id' => $this->exam->id]);
    $completed = ExamSession::factory()->completed()->create(['exam_id' => $this->exam->id]);

    $this->actingAs($this->student)
        ->get('/student/lobby')
        ->assertInertia(fn ($page) => $page
            ->component('student/Lobby')
            ->has('sessions', 2)
        );
});

// --- Exam Start ---

it('can start an exam when session is active', function () {
    $session = ExamSession::factory()->active()->create(['exam_id' => $this->exam->id]);

    $this->actingAs($this->student)
        ->get("/student/exam/{$session->id}")
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('student/ExamTake')
            ->has('questions', 3)
            ->where('exam.title', $this->exam->title)
        );

    $this->assertDatabaseHas('exam_attempts', [
        'exam_session_id' => $session->id,
        'user_id' => $this->student->id,
        'status' => 'in_progress',
    ]);
});

it('redirects to lobby when session is not active', function () {
    $session = ExamSession::factory()->create(['exam_id' => $this->exam->id, 'status' => 'waiting']);

    $this->actingAs($this->student)
        ->get("/student/exam/{$session->id}")
        ->assertRedirect(route('student.lobby'));
});

it('resumes an existing in-progress attempt', function () {
    $session = ExamSession::factory()->active()->create(['exam_id' => $this->exam->id]);
    $attempt = ExamAttempt::factory()->create([
        'exam_session_id' => $session->id,
        'user_id' => $this->student->id,
        'total_points' => 3,
    ]);

    $this->actingAs($this->student)
        ->get("/student/exam/{$session->id}")
        ->assertSuccessful();

    // Should not create a duplicate attempt
    expect(ExamAttempt::where('exam_session_id', $session->id)
        ->where('user_id', $this->student->id)
        ->count())->toBe(1);
});

it('redirects to results if attempt is already submitted', function () {
    $session = ExamSession::factory()->active()->create(['exam_id' => $this->exam->id]);
    $attempt = ExamAttempt::factory()->submitted()->create([
        'exam_session_id' => $session->id,
        'user_id' => $this->student->id,
    ]);

    $this->actingAs($this->student)
        ->get("/student/exam/{$session->id}")
        ->assertRedirect(route('student.results', $attempt));
});

// --- Answer Saving ---

it('can save an answer', function () {
    $session = ExamSession::factory()->active()->create(['exam_id' => $this->exam->id]);
    ExamAttempt::factory()->create([
        'exam_session_id' => $session->id,
        'user_id' => $this->student->id,
    ]);

    $this->actingAs($this->student)
        ->postJson("/student/exam/{$session->id}/answer", [
            'question_id' => $this->questions[0]->id,
            'selected_answer' => 'Option B',
        ])
        ->assertSuccessful()
        ->assertJson(['saved' => true]);

    $this->assertDatabaseHas('answers', [
        'question_id' => $this->questions[0]->id,
        'selected_answer' => 'Option B',
    ]);
});

it('validates answer input', function () {
    $session = ExamSession::factory()->active()->create(['exam_id' => $this->exam->id]);
    ExamAttempt::factory()->create([
        'exam_session_id' => $session->id,
        'user_id' => $this->student->id,
    ]);

    $this->actingAs($this->student)
        ->postJson("/student/exam/{$session->id}/answer", [])
        ->assertUnprocessable();
});

// --- Submission & Grading ---

it('can submit an exam and get graded', function () {
    $session = ExamSession::factory()->active()->create(['exam_id' => $this->exam->id]);
    $attempt = ExamAttempt::factory()->create([
        'exam_session_id' => $session->id,
        'user_id' => $this->student->id,
        'total_points' => 3,
    ]);

    // Answer 2 out of 3 correctly
    Answer::factory()->create([
        'exam_attempt_id' => $attempt->id,
        'question_id' => $this->questions[0]->id,
        'selected_answer' => 'Option A', // correct
    ]);
    Answer::factory()->create([
        'exam_attempt_id' => $attempt->id,
        'question_id' => $this->questions[1]->id,
        'selected_answer' => 'Option B', // correct
    ]);
    Answer::factory()->create([
        'exam_attempt_id' => $attempt->id,
        'question_id' => $this->questions[2]->id,
        'selected_answer' => 'Option C', // wrong
    ]);

    $this->actingAs($this->student)
        ->post("/student/exam/{$session->id}/submit")
        ->assertRedirect(route('student.results', $attempt));

    $attempt->refresh();
    expect($attempt->status)->toBe('submitted')
        ->and($attempt->score)->toBe(2)
        ->and($attempt->submitted_at)->not->toBeNull();
});

// --- Results ---

it('can view results of own submitted attempt', function () {
    $session = ExamSession::factory()->active()->create(['exam_id' => $this->exam->id]);
    $attempt = ExamAttempt::factory()->submitted()->create([
        'exam_session_id' => $session->id,
        'user_id' => $this->student->id,
    ]);

    $this->actingAs($this->student)
        ->get("/student/results/{$attempt->id}")
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('student/Results')
            ->has('attempt')
            ->has('exam')
            ->has('answers')
        );
});

it('cannot view results of another student', function () {
    $other = User::factory()->create(['role' => 'student']);
    $session = ExamSession::factory()->active()->create(['exam_id' => $this->exam->id]);
    $attempt = ExamAttempt::factory()->submitted()->create([
        'exam_session_id' => $session->id,
        'user_id' => $other->id,
    ]);

    $this->actingAs($this->student)
        ->get("/student/results/{$attempt->id}")
        ->assertForbidden();
});

// --- Anti-Cheat ---

it('can log anti-cheat events', function () {
    $session = ExamSession::factory()->active()->create(['exam_id' => $this->exam->id]);
    ExamAttempt::factory()->create([
        'exam_session_id' => $session->id,
        'user_id' => $this->student->id,
    ]);

    $this->actingAs($this->student)
        ->postJson("/student/exam/{$session->id}/anti-cheat", [
            'event_type' => 'tab_switch',
            'details' => ['timestamp' => now()->toISOString()],
        ])
        ->assertSuccessful()
        ->assertJson(['logged' => true]);

    $this->assertDatabaseHas('anti_cheat_logs', [
        'event_type' => 'tab_switch',
    ]);
});

it('increments flag count on anti-cheat event', function () {
    $session = ExamSession::factory()->active()->create(['exam_id' => $this->exam->id]);
    $attempt = ExamAttempt::factory()->create([
        'exam_session_id' => $session->id,
        'user_id' => $this->student->id,
    ]);

    $this->actingAs($this->student)
        ->postJson("/student/exam/{$session->id}/anti-cheat", [
            'event_type' => 'right_click',
        ]);

    expect($attempt->fresh()->flag_count)->toBe(1);
});
