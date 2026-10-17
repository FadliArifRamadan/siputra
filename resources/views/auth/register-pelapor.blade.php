<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'UCIC') }} - Registrasi Pelapor</title>

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

            <div class="relative z-10 flex items-center gap-3">
                <a href="/" class="flex items-center gap-3 group shrink-0">
                    <img src="{{ asset('images/logo-ucic.png') }}" alt="Logo UCIC" class="h-9 sm:h-10 w-auto object-contain transition-transform duration-300 group-hover:scale-105">
                    <div>
                        <span class="text-base sm:text-lg font-bold tracking-tight font-heading block leading-none text-white">UCIC FACILITY</span>
                        <span class="text-[10px] font-medium tracking-[0.15em] uppercase text-amber-400">Management System</span>
                    </div>
                </a>
            </div>

            <!-- Content Middle -->
            <div class="relative z-10 space-y-6 max-w-lg">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs font-semibold uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span> Pendaftaran Pelapor Baru
                </div>
                <h1 class="text-3xl xl:text-4xl font-bold font-heading leading-tight text-white">
                    Bergabung dengan Layanan Fasilitas Kampus
                </h1>
                <p class="text-slate-400 text-sm leading-relaxed">
                    Daftarkan akun Anda untuk mempermudah pengajuan peminjaman sarana prasarana serta melaporkan kendala fasilitas di lingkungan UCIC secara akurat.
                </p>
            </div>

            <!-- Footer Note -->
            <div class="relative z-10 text-xs text-slate-500 border-t border-slate-800 pt-6">
                Universitas Catur Insan Cendekia &copy; {{ date('Y') }}
            </div>
        </div>

        <!-- Right Column: Form Register Area -->
        <div class="lg:col-span-7 xl:col-span-6 flex items-center justify-center p-6 sm:p-12 md:p-16">
            <div class="w-full max-w-md space-y-8">

                <!-- Logo Mobile / Top Header -->
                <div class="text-left space-y-2">
                    <a href="/" class="inline-flex lg:hidden items-center gap-2 font-bold text-lg text-slate-900 font-heading mb-4">
                        <img src="{{ asset('images/logo-ucic.png') }}" alt="Logo UCIC" class="h-8 w-auto"> UCIC Facility
                    </a>
                    <h2 class="text-2xl sm:text-3xl font-bold font-heading text-slate-900">Buat Akun Pelapor</h2>
                    <p class="text-xs sm:text-sm text-slate-500">Isi data diri Anda dengan benar. Akun akan diverifikasi oleh unit RTK.</p>
                </div>

                <!-- Session Status & Flash Messages -->
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

                <!-- Registration Form -->
                <form method="POST" action="{{ route('register.pelapor') }}" class="space-y-4">
                    @csrf

                    <!-- Nomor Induk -->
                    <div class="space-y-1.5">
                        <label for="nomor_induk" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Nomor Induk (NIM / NIP / NIDN) <span class="text-rose-500">*</span>
                        </label>
                        <input id="nomor_induk" type="text" name="nomor_induk" value="{{ old('nomor_induk') }}" required autofocus
                            class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm text-slate-900 placeholder:text-slate-400 transition @error('nomor_induk') border-rose-500 @enderror"
                            placeholder="Contoh: 2023101001">
                        @error('nomor_induk')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nama Lengkap -->
                    <div class="space-y-1.5">
                        <label for="nama" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Nama Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <input id="nama" type="text" name="nama" value="{{ old('nama') }}" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm text-slate-900 placeholder:text-slate-400 transition @error('nama') border-rose-500 @enderror"
                            placeholder="Nama lengkap sesuai identitas">
                        @error('nama')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div class="space-y-1.5">
                        <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                            Email Kampus <span class="text-rose-500">*</span>
                        </label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm text-slate-900 placeholder:text-slate-400 transition @error('email') border-rose-500 @enderror"
                            placeholder="nama@ucic.ac.id">
                        @error('email')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password & Konfirmasi Password Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                Password <span class="text-rose-500">*</span>
                            </label>
                            <input id="password" type="password" name="password" required autocomplete="new-password"
                                class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm text-slate-900 placeholder:text-slate-400 transition @error('password') border-rose-500 @enderror"
                                placeholder="Min. 8 karakter">
                            @error('password')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                Konfirmasi <span class="text-rose-500">*</span>
                            </label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                                class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm text-slate-900 placeholder:text-slate-400 transition"
                                placeholder="Ulangi password">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm transition shadow-lg shadow-slate-900/10 flex items-center justify-center gap-2 pt-2">
                        Daftar Sebagai Pelapor <i data-lucide="arrow-right" class="w-4 h-4 text-amber-400"></i>
                    </button>
                </form>

                <!-- Footer Link -->
                <div class="pt-6 border-t border-slate-200 text-center space-y-2">
                    <p class="text-xs text-slate-500">
                        Sudah memiliki akun?
                        <a href="{{ route('login') }}" class="font-bold text-amber-600 hover:text-amber-700 transition">
                            Masuk ke Aplikasi
                        </a>
                    </p>
                    <p class="text-[11px] text-slate-400">
                        Akun baru akan langsung tersimpan dengan status <span class="font-semibold text-amber-600">Pending</span> hingga disetujui RTK.
                    </p>
                </div>

            </div>
        </div>

    </div>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>