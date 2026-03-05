<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || $request->user()->role !== 'student') {
            if ($request->user()?->isAdmin()) {
                return redirect('/admin');
            }

            abort(403, 'Students only.');
        }

        return $next($request);
    }
}
