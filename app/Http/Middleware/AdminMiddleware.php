<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('warning', 'Silakan masuk terlebih dahulu.');
        }

        if (!Auth::user()->isAdmin()) {
            return redirect()->route('peminjaman.index')
                ->with('danger', 'ANDA TIDAK MEMILIKI HAK AKSES');
        }

        return $next($request);
    }
}
