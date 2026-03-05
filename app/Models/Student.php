<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Student extends User
{
    protected $table = 'users';

    protected static function booted(): void
    {
        static::addGlobalScope('student', function (Builder $builder) {
            $builder->where('role', 'student');
        });

        static::creating(function (Student $student) {
            $student->role = 'student';
        });
    }
}
