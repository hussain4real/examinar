<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_session_id',
        'user_id',
        'started_at',
        'submitted_at',
        'score',
        'total_points',
        'status',
        'flag_count',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'score' => 'integer',
            'total_points' => 'integer',
            'flag_count' => 'integer',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(ExamSession::class, 'exam_session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function antiCheatLogs(): HasMany
    {
        return $this->hasMany(AntiCheatLog::class);
    }

    public function getPercentageAttribute(): ?float
    {
        if ($this->total_points === 0) {
            return null;
        }

        return round(($this->score / $this->total_points) * 100, 1);
    }

    public function getPassedAttribute(): ?bool
    {
        if ($this->score === null) {
            return null;
        }

        $passScore = $this->session->exam->pass_score;

        return $this->percentage >= $passScore;
    }
}
