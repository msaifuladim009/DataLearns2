<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class GuruMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->isGuru()) {
            return $next($request);
        }

        return redirect('/dashboard')->with('error', 'Anda tidak memiliki akses guru');
    }
}
