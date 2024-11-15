<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CekKelompok
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, $kelompok)
    {
        $user = Auth::user();

        // Cek apakah user terdaftar dan kelompoknya sesuai
        if ($user && $user->kelompok == $kelompok) {
            return $next($request);
        }

        // Jika tidak sesuai, redirect ke halaman error atau dashboard
        return redirect('/unauthorized');
    }
}
