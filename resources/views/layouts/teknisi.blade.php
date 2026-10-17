<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'UCIC') - {{ config('app.name', 'UCIC') }}</title>

    <!-- DataTables & FontAwesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:500,600,700,800|inter:400,500,600&display=swap" rel="stylesheet" />

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --ink-950: #191333;
            --ink-900: #241c47;
            --ink-800: #2f2559;
            --sunset-500: #f2723b;
            --sunset-400: #f7924f;
            --sunset-300: #fbb35f;
            --cream-50: #fbf7f0;
            --cream-100: #f5ede1;
            --stone-ink: #2a2338;
            --stone-muted: #7a7189;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--cream-50);
            color: var(--stone-ink);
        }

        h1,
        h2,
        h3,
        h4,
        .font-heading {
            font-family: 'Plus Jakarta Sans', sans-serif;
            letter-spacing: -0.02em;
        }

        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--cream-100);
        }

        ::-webkit-scrollbar-thumb {
            background: #d8c9b8;
            border-radius: 999px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--sunset-400);
        }
    </style>

    @stack('styles')
</head>

<body class="antialiased selection:bg-[#f2723b] selection:text-white" x-data="{ mobileMenuOpen: false }">
    <div class="min-h-screen flex flex-col pb-16 sm:pb-0" style="background-color: var(--cream-50);">
        <!-- Navigation -->
        <nav class="sticky top-0 z-50 backdrop-blur-md border-b text-white shadow-lg shadow-[#19133315]" style="background-color: rgba(25,19,51,0.94); border-color: rgba(255,255,255,0.1);">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-18 sm:h-20">
                    <!-- Logo & Brand -->
                    <div class="flex items-center gap-6">
                        <a href="{{ route('teknisi.dashboard') }}" class="flex items-center gap-3 group shrink-0">
                            <img src="{{ asset('images/logo-ucic.png') }}" alt="Logo UCIC" class="h-9 sm:h-10 w-auto object-contain transition-transform duration-300 group-hover:scale-105" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <span class="hidden text-xl font-bold font-heading text-white tracking-tight">🏛️ UCIC</span>
                            <div>
                                <span class="text-base font-bold tracking-tight font-heading block leading-none text-white">UCIC FACILITY</span>
                                <span class="text-[10px] font-medium tracking-[0.15em] uppercase" style="color: var(--sunset-300);">IT Support & Helpdesk</span>
                            </div>
                        </a>

                        <!-- Menu Navigasi Desktop -->
                        <div class="hidden md:flex items-center gap-1.5">
                            <a href="{{ route('teknisi.dashboard') }}" 
                               class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 {{ request()->routeIs('teknisi.dashboard') ? 'bg-white/15 text-white border border-white/10 shadow-sm' : 'text-white/70 hover:text-white hover:bg-white/10' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('teknisi.laporan-kerusakan.index') }}" 
                               class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 {{ request()->routeIs('teknisi.laporan-kerusakan.*') ? 'bg-white/15 text-white border border-white/10 shadow-sm' : 'text-white/70 hover:text-white hover:bg-white/10' }}">
                                Laporan Kerusakan
                            </a>
                            <a href="{{ route('teknisi.fasilitas.index') }}" 
                               class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 {{ request()->routeIs('teknisi.fasilitas.*') ? 'bg-white/15 text-white border border-white/10 shadow-sm' : 'text-white/70 hover:text-white hover:bg-white/10' }}">
                                Fasilitas
                            </a>
                            <a href="{{ route('teknisi.peminjaman-alat.index') }}" 
                               class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 {{ request()->routeIs('teknisi.peminjaman-alat.*') ? 'bg-white/15 text-white border border-white/10 shadow-sm' : 'text-white/70 hover:text-white hover:bg-white/10' }}">
                                Pinjaman Alat
                            </a>
                        </div>
                    </div>

                    <!-- Right Side Items -->
                    <div class="flex items-center gap-3">
                        <!-- Notifikasi -->
                        <a href="{{ route('notifications.index') }}" class="p-2 rounded-lg text-white/80 hover:text-white hover:bg-white/10 transition-colors duration-200 relative">
                            <i data-lucide="bell" class="w-5 h-5"></i>
                            <span id="notification-badge" class="hidden absolute -top-0.5 -right-0.5 text-white text-[10px] font-bold rounded-full h-4 min-w-[16px] px-1 flex items-center justify-center border border-[#191333]" style="background-color: var(--sunset-500);">
                                0
                            </span>
                        </a>

                        <div class="h-5 w-px bg-white/15 hidden sm:block"></div>

                        <!-- User Info -->
                        <span class="text-xs font-semibold text-white/90 hidden sm:inline-block">
                            {{ Auth::user()->nama }}
                        </span>

                        <!-- Logout Button -->
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-rose-300 hover:text-rose-200 hover:bg-rose-500/15 border border-rose-500/20 transition-all duration-200 flex items-center gap-1.5">
                                <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                                <span class="hidden sm:inline">Logout</span>
                            </button>
                        </form>

                        <!-- Mobile Menu Button -->
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="sm:hidden p-2 rounded-lg text-white/80 hover:text-white hover:bg-white/10 transition-colors focus:outline-none">
                            <i data-lucide="menu" x-show="!mobileMenuOpen" class="w-6 h-6"></i>
                            <i data-lucide="x" x-show="mobileMenuOpen" class="w-6 h-6" x-cloak></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Nav Dropdown -->
            <div x-show="mobileMenuOpen" x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2"
                class="sm:hidden border-t border-white/10 px-4 pt-3 pb-5 space-y-1" style="background-color: var(--ink-950);">
                <div class="pb-2 mb-2 border-b border-white/10 px-3 pt-1">
                    <p class="text-xs font-bold text-white">{{ Auth::user()->nama }}</p>
                    <p class="text-[10px] text-white/60">{{ Auth::user()->email }}</p>
                </div>
                <a href="{{ route('teknisi.dashboard') }}" class="block py-2 px-3 rounded-lg text-xs font-medium {{ request()->routeIs('teknisi.dashboard') ? 'bg-white/15 text-white font-semibold' : 'text-white/70 hover:text-white hover:bg-white/10' }}">Dashboard</a>
                <a href="{{ route('teknisi.laporan-kerusakan.index') }}" class="block py-2 px-3 rounded-lg text-xs font-medium {{ request()->routeIs('teknisi.laporan-kerusakan.*') ? 'bg-white/15 text-white font-semibold' : 'text-white/70 hover:text-white hover:bg-white/10' }}">Laporan Kerusakan</a>
                <a href="{{ route('teknisi.fasilitas.index') }}" class="block py-2 px-3 rounded-lg text-xs font-medium {{ request()->routeIs('teknisi.fasilitas.*') ? 'bg-white/15 text-white font-semibold' : 'text-white/70 hover:text-white hover:bg-white/10' }}">Fasilitas</a>
                <a href="{{ route('teknisi.peminjaman-alat.index') }}" class="block py-2 px-3 rounded-lg text-xs font-medium {{ request()->routeIs('teknisi.peminjaman-alat.*') ? 'bg-white/15 text-white font-semibold' : 'text-white/70 hover:text-white hover:bg-white/10' }}">Pinjaman Alat</a>
                <a href="{{ route('profile.edit') }}" class="block py-2 px-3 rounded-lg text-xs font-medium {{ request()->routeIs('profile.*') ? 'bg-white/15 text-white font-semibold' : 'text-white/70 hover:text-white hover:bg-white/10' }}">Profil</a>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="flex-grow py-6 sm:py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                @yield('content')
            </div>
        </main>

        <!-- Mobile Bottom Navigation -->
        <nav class="fixed bottom-0 left-0 right-0 border-t backdrop-blur-md sm:hidden z-50" style="background-color: rgba(25,19,51,0.96); border-color: rgba(255,255,255,0.1);">
            <div class="flex justify-around py-2">
                <a href="{{ route('teknisi.dashboard') }}" class="flex flex-col items-center gap-0.5 text-[10px] font-medium {{ request()->routeIs('teknisi.dashboard') ? 'text-[#fbb35f]' : 'text-white/60 hover:text-white' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('teknisi.laporan-kerusakan.index') }}" class="flex flex-col items-center gap-0.5 text-[10px] font-medium {{ request()->routeIs('teknisi.laporan-kerusakan.*') ? 'text-[#fbb35f]' : 'text-white/60 hover:text-white' }}">
                    <i data-lucide="wrench" class="w-5 h-5"></i>
                    <span>Laporan</span>
                </a>
                <a href="{{ route('teknisi.fasilitas.index') }}" class="flex flex-col items-center gap-0.5 text-[10px] font-medium {{ request()->routeIs('teknisi.fasilitas.*') ? 'text-[#fbb35f]' : 'text-white/60 hover:text-white' }}">
                    <i data-lucide="boxes" class="w-5 h-5"></i>
                    <span>Fasilitas</span>
                </a>
                <a href="{{ route('teknisi.peminjaman-alat.index') }}" class="flex flex-col items-center gap-0.5 text-[10px] font-medium {{ request()->routeIs('teknisi.peminjaman-alat.*') ? 'text-[#fbb35f]' : 'text-white/60 hover:text-white' }}">
                    <i data-lucide="hammer" class="w-5 h-5"></i>
                    <span>Alat</span>
                </a>
            </div>
        </nav>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.lucide) lucide.createIcons();
        });

        function updateNotificationBadge() {
            fetch('{{ route("notifications.unread-count") }}')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('notification-badge');
                    if (data.count > 0) {
                        badge.textContent = data.count > 99 ? '99+' : data.count;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                })
                .catch(() => {});
        }
        document.addEventListener('DOMContentLoaded', updateNotificationBadge);
    </script>
    @stack('scripts')
</body>

</html>