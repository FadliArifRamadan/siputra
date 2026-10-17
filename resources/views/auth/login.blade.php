<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'UCIC') }} - Masuk Sistem</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:600,700|inter:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        h1,
        h2,
        h3,
        h4,
        .font-heading {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 antialiased min-h-screen">

    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-12 bg-slate-50">

        <!-- Left Column: Visual Panel (Desktop) -->
        <div class="hidden lg:flex lg:col-span-5 xl:col-span-6 flex-col justify-between bg-slate-900 text-white p-12 relative overflow-hidden border-r border-slate-800">
            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#d97706_1px,transparent_1px)] [background-size:20px_20px]"></div>

            <!-- Brand Header -->
            <div class="relative z-10 flex items-center gap-3">
                <a href="/" class="flex items-center gap-3 group shrink-0">
                    <img src="{{ asset('images/logo-ucic.png') }}" alt="Logo UCIC" class="h-9 sm:h-10 w-auto object-contain transition-transform duration-300 group-hover:scale-105">
                    <div>
                        <span class="text-base sm:text-lg font-bold tracking-tight font-heading block leading-none text-white">UCIC FACILITY</span>
                        <span class="text-[10px] font-medium tracking-[0.15em] uppercase text-amber-400" >Management System</span>
                    </div>
                </a>
            </div>

            <!-- Content Middle -->
            <div class="relative z-10 space-y-6 max-w-lg">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs font-semibold uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span> Portal Akses Terpadu
                </div>
                <h1 class="text-3xl xl:text-4xl font-bold font-heading leading-tight text-white">
                    Sistem Layanan Fasilitas Kampus UCIC
                </h1>
                <p class="text-slate-400 text-sm leading-relaxed">
                    Masuk ke akun Anda untuk mengelola jadwal peminjaman ruang, melaporkan kendala teknis fasilitas, atau meninjau progres disposisi perbaikan.
                </p>
            </div>

            <!-- Footer Note -->
            <div class="relative z-10 text-xs text-slate-500 border-t border-slate-800 pt-6">
                Universitas Catur Insan Cendekia &copy; {{ date('Y') }}
            </div>
        </div>

        <!-- Right Column: Form Login Area -->
        <div class="lg:col-span-7 xl:col-span-6 flex items-center justify-center p-6 sm:p-12 md:p-16">
            <div class="w-full max-w-md space-y-8">

                <!-- Logo Mobile / Top Header -->
                <div class="text-left space-y-2">
                    <a href="/" class="inline-flex lg:hidden items-center gap-2 font-bold text-lg text-slate-900 font-heading mb-4">
                        <span class="w-8 h-8 rounded bg-amber-500 text-slate-950 flex items-center justify-center text-xs">UC</span> UCIC Facility
                    </a>
                    <h2 class="text-2xl sm:text-3xl font-bold font-heading text-slate-900">Selamat Datang Kembali</h2>
                    <p class="text-xs sm:text-sm text-slate-500">Silakan masukkan akun email dan password Anda.</p>
                </div>

                <!-- Session Status & Flash Messages -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                @if (session('error'))
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 flex-shrink-0"></i>
                    <div>{{ session('error') }}</div>
                </div>
                @endif

                @if (session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-medium flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
                    <div>{{ session('success') }}</div>
                </div>
                @endif

                <!-- Form Login -->
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Email Input -->
                    <div class="space-y-1.5">
                        <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Email <span class="text-rose-500">*</span>
                        </label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                            class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm text-slate-900 placeholder:text-slate-400 transition"
                            placeholder="nama@ucic.ac.id">
                        <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-rose-600" />
                    </div>

                    <!-- Password Input -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                Password <span class="text-rose-500">*</span>
                            </label>
                            @if (Route::has('password.request'))
                            <a class="text-xs font-semibold text-amber-600 hover:text-amber-700 transition" href="{{ route('password.request') }}">
                                Lupa Password?
                            </a>
                            @endif
                        </div>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm text-slate-900 placeholder:text-slate-400 transition"
                            placeholder="••••••••">
                        <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-rose-600" />
                    </div>


                    <!-- Submit Button -->
                    <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm transition shadow-lg shadow-slate-900/10 flex items-center justify-center gap-2">
                        Masuk ke Aplikasi <i data-lucide="arrow-right" class="w-4 h-4 text-amber-400"></i>
                    </button>
                </form>

                

            </div>
        </div>

    </div>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>