<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterPelaporRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisterPelaporController extends Controller
{
    public function showRegistrationForm(): View
    {
        return view('auth.register-pelapor');
    }

    public function register(RegisterPelaporRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'nomor_induk' => $validated['nomor_induk'],
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status_akun' => 'pending', // Default pending, menunggu approval RTK
        ]);

        // Assign role pelapor
        $user->assignRole('pelapor');

        event(new Registered($user));

        // Auth::login($user);

        // ✅ Langsung redirect ke login dengan pesan sukses (TANPA login)
        return redirect()->route('login')
            ->with('success', '✅ Registrasi berhasil! Silakan tunggu verifikasi dari RTK. Anda akan dapat login setelah akun diaktifkan.');
    }
}
