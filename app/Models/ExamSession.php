<?php

namespace App\Models;

use App\Events\ExamSessionEnded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'started_at',
        'ended_at',
        'status',
    ];

    protected static function booted(): void
    {
        static::saving(function (ExamSession $session) {
            if ($session->isDirty('status')) {
                if ($session->status === 'active' && ! $session->started_at) {
                    $session->started_at = now();
                }

                if ($session->status === 'completed' && ! $session->ended_at) {
                    $session->ended_at = now();
                }
            }
        });

        static::saved(function (ExamSession $session) {
            if ($session->wasChanged('status') && $session->status === 'completed') {
                $session->gradeInProgressAttempts();
                ExamSessionEnded::dispatch($session);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function isWaiting(): bool
    {
        return $this->status === 'waiting';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Grade all in-progress attempts when the session ends.
     */
    public function gradeInProgressAttempts(): void
    {
        $this->loadMissing('exam.questions');
        $questions = $this->exam->questions->keyBy('id');

        $attempts = $this->attempts()
            ->where('status', 'in_progress')
            ->with('answers')
            ->get();

        foreach ($attempts as $attempt) {
            $score = 0;

            foreach ($attempt->answers as $answer) {
                $question = $questions->get($answer->question_id);

                if (! $question) {
                    continue;
                }

                $isCorrect = strtolower(trim($answer->selected_answer)) === strtolower(trim($question->correct_answer));
                $answer->update(['is_correct' => $isCorrect]);

                if ($isCorrect) {
                    $score += $question->pivot->points;
                }
            }

            $attempt->update([
                'score' => $score,
                'submitted_at' => now(),
                'status' => 'submitted',
            ]);
        }
    }
}
