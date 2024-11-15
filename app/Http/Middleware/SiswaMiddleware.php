<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class SiswaMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->isSiswa()) {
            return $next($request);
        }

        return redirect('/dashboard')->with('error', 'Anda tidak memiliki akses siswa');
    }
}
