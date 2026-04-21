<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login
        if (!auth()->check()) {
            return redirect()->route('superadmin.login');
        }

        // Cek apakah user adalah super admin
        if (!auth()->user()->is_super_admin) {
            return redirect('/dashboard')->with('error', 'Akses ditolak. Anda bukan super admin.');
        }

        // Cek apakah email sudah verified
        if (!auth()->user()->email_verified_at) {
            return redirect()->route('verification.notice');
        }

        return $next($request);
    }
}
