<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GuideController
{
    public function __invoke(Request $request): Response
    {
        return Inertia::render('student/Guide');
    }
}
