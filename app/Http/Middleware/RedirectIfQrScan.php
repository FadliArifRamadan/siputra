<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfQrScan
{
    public function handle(Request $request, Closure $next)
    {
        // Jika ada parameter fasilitas_id di URL (dari scan QR)
        if ($request->has('fasilitas_id') && $request->query('fasilitas_id')) {
            // Jika user belum login, redirect ke login dengan return_url
            if (!auth()->check()) {
                return redirect()->route('login')
                    ->with('info', 'Silakan login terlebih dahulu untuk melaporkan kerusakan.')
                    ->with('redirect_to', $request->fullUrl());
            }
        }

        return $next($request);
    }
}