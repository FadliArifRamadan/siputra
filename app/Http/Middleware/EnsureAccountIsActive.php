<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        // ✅ CEK: Jika user tidak login, lewati (biarkan middleware auth yang menangani)
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // ✅ Jika status_akun = 'active', lanjutkan
        if ($user->status_akun === 'active') {
            return $next($request);
        }

        // ✅ Jika status_akun = 'pending' atau 'suspended'
        // Hapus session, logout, dan redirect ke login dengan pesan
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('error', 'Akun Anda belum aktif atau ditangguhkan. Silakan hubungi RTK.');
    }
}