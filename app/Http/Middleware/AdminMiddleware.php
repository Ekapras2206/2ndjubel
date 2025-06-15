<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->isAdmin()) { // Memanggil method isAdmin() dari model User
            return $next($request);
        }

        // Redirect atau tampilkan error jika bukan admin
        return redirect('/home')->with('error', 'Anda tidak memiliki akses sebagai administrator.');
    }
}