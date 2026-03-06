<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'body',
        'options',
        'correct_answer',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
        ];
    }

    public function exams(): BelongsToMany
    {
        return $this->belongsToMany(Exam::class)
            ->withPivot(['points', 'order'])
            ->withTimestamps();
    }
}
