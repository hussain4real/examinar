<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'duration_minutes',
        'pass_score',
        'shuffle_questions',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'shuffle_questions' => 'boolean',
            'duration_minutes' => 'integer',
            'pass_score' => 'integer',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class)
            ->withPivot(['points', 'order'])
            ->withTimestamps()
            ->orderByPivot('order');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ExamSession::class);
    }

    public function getTotalPointsAttribute(): int
    {
        return $this->questions()->sum('exam_question.points');
    }
}
