<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;


class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // ✅ CEK: Jika status_akun belum active, logout dan redirect dengan pesan
        if ($user->status_akun !== 'active') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Akun Anda belum aktif. Silakan hubungi RTK untuk verifikasi.');
        }

        // ✅ Redirect berdasarkan role
        if ($user->hasRole('rtk')) {
            return redirect()->intended(route('rtk.dashboard'));
        } elseif ($user->hasRole('teknisi')) {
            return redirect()->intended(route('teknisi.dashboard'));
        } elseif ($user->hasRole(['unit', 'UNIT'])) {
            return redirect()->intended(route('unit.dashboard'));
        } elseif ($user->hasRole('pelapor')) {
            return redirect()->intended(route('pelapor.dashboard'));
        }

        return redirect()->intended('/dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
