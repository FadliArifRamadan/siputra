<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'UCIC') }} - Management Fasilitas Kampus Terpadu</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:500,600,700,800|inter:400,500,600&display=swap" rel="stylesheet" />

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- AOS (Animate On Scroll) -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css" />
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js" defer></script>

    <!-- Swiper (untuk viewer Denah Kampus) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js" defer></script>

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        :root {
            /* ===== Palet baru: hangat, berlapis, tidak monoton ===== */
            --ink-950: #191333;
            --ink-900: #241c47;
            --ink-800: #2f2559;
            --sunset-500: #f2723b;
            --sunset-400: #f7924f;
            --sunset-300: #fbb35f;
            --plum-600: #7c3aed;
            --plum-500: #9152f0;
            --teal-600: #0f8a82;
            --teal-500: #14b8a6;
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

        /* ===== Refined scrollbar (nuansa hangat) ===== */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: var(--cream-100);
        }

        ::-webkit-scrollbar-thumb {
            background: #d8c9b8;
            border-radius: 999px;
            border: 2px solid var(--cream-100);
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--sunset-400);
        }

        /* ===== Ambient glow orbs multihue (kedalaman, tetap halus) ===== */
        .glow-sunset {
            background: radial-gradient(circle, rgba(242, 114, 59, 0.32) 0%, rgba(242, 114, 59, 0) 70%);
            filter: blur(55px);
        }

        .glow-plum {
            background: radial-gradient(circle, rgba(124, 58, 237, 0.28) 0%, rgba(124, 58, 237, 0) 70%);
            filter: blur(55px);
        }

        .glow-teal {
            background: radial-gradient(circle, rgba(20, 184, 166, 0.22) 0%, rgba(20, 184, 166, 0) 70%);
            filter: blur(55px);
        }

        .bg-ink-gradient {
            background: linear-gradient(160deg, var(--ink-950) 0%, var(--ink-900) 55%, var(--ink-800) 100%);
        }

        .text-gradient-sunset {
            background: linear-gradient(90deg, var(--sunset-300), var(--sunset-500));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        /* ===== Ken Burns slow zoom untuk hero slider ===== */
        @keyframes kenburns {
            0% {
                transform: scale(1);
            }

            100% {
                transform: scale(1.08);
            }
        }

        .animate-kenburns {
            animation: kenburns 8s ease-out forwards;
        }

        /* ===== Subtle premium hover ===== */
        .lift-hover {
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.3s ease;
        }

        .lift-hover:hover {
            transform: translateY(-3px) scale(1.01);
            box-shadow: 0 20px 40px -14px rgba(36, 28, 71, 0.16);
        }

        .icon-nudge svg {
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .icon-nudge:hover svg {
            transform: translateX(4px);
        }

        /* ===== Denah tab aktif ===== */
        .denah-tab {
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            background-color: transparent;
            border: 1px solid transparent;
            color: var(--stone-muted);
        }

        .denah-tab.is-active {
            background: linear-gradient(135deg, var(--sunset-500), var(--sunset-400));
            color: #fff;
            box-shadow: 0 8px 18px -6px rgba(242, 114, 59, 0.45);
            border-color: transparent;
        }

        .denah-tab:not(.is-active):hover {
            background-color: var(--cream-50);
            color: var(--stone-ink);
        }

        /* ===== Fasilitas Kampus Tabs & Styling ===== */
        .campus-btn {
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .campus-btn.is-active {
            background: linear-gradient(135deg, var(--ink-950), var(--ink-900));
            color: #ffffff !important;
            box-shadow: 0 8px 18px -6px rgba(25, 19, 51, 0.45);
            border-color: transparent;
        }

        .campus-btn:not(.is-active) {
            background-color: var(--cream-100);
            color: var(--stone-muted);
            border-color: #ecdfd0;
        }

        .campus-btn:not(.is-active):hover {
            background-color: #ede2d3;
            color: var(--stone-ink);
        }

        .fasilitas-sub-btn {
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            color: var(--stone-muted);
            background-color: transparent;
            border: 1px solid transparent;
        }

        .fasilitas-sub-btn.is-active {
            background: linear-gradient(135deg, var(--sunset-500), var(--sunset-400));
            color: #ffffff !important;
            font-weight: 600;
            box-shadow: 0 6px 14px -5px rgba(242, 114, 59, 0.45);
        }

        .fasilitas-sub-btn:not(.is-active):hover {
            background-color: var(--cream-50);
            color: var(--stone-ink);
        }

        @media (min-width: 1024px) {
            .fasilitas-col-left {
                grid-column: span 4 / span 4 !important;
            }
            .fasilitas-col-right {
                grid-column: span 8 / span 8 !important;
            }
        }

        /* ===== Alur langkah - garis penghubung ===== */
        .step-connector {
            background-image: repeating-linear-gradient(90deg, #e7d9c8 0, #e7d9c8 6px, transparent 6px, transparent 14px);
        }

        /* ===== Modal Detail Event Kalender (dipakai oleh app.js) ===== */
        #eventModal {
            position: fixed;
            inset: 0;
            z-index: 100;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            background: rgba(25, 19, 51, 0.55);
            backdrop-filter: blur(4px);
        }

        #eventModal.active {
            display: flex;
        }

        #eventModal .modal-panel {
            position: relative;
            width: 100%;
            max-width: 420px;
            background: #fff;
            border-radius: 1.25rem;
            padding: 1.75rem;
            box-shadow: 0 30px 60px -20px rgba(25, 19, 51, 0.45);
            animation: modalPop 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes modalPop {
            from {
                opacity: 0;
                transform: scale(0.96) translateY(8px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        #eventModal .modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 2rem;
            height: 2rem;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--stone-muted);
            background: var(--cream-100);
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        #eventModal .modal-close:hover {
            background: #f2ece2;
            color: var(--stone-ink);
        }

        .event-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            font-size: 1.05rem;
            color: var(--stone-ink);
            margin-bottom: 1rem;
            padding-bottom: 0.85rem;
            padding-right: 2rem;
            border-bottom: 1px solid #f2ece2;
        }

        .event-info p {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 1rem;
            font-size: 0.85rem;
            margin-bottom: 0.65rem;
        }

        .event-info .label {
            color: var(--stone-muted);
            font-weight: 600;
            white-space: nowrap;
        }

        .event-info .value {
            color: var(--stone-ink);
            font-weight: 600;
            text-align: right;
        }

        .event-divider {
            border: none;
            border-top: 1px dashed #ecdfd0;
            margin: 0.85rem 0;
        }

        .event-status {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: capitalize;
            background: var(--cream-100);
            color: var(--stone-ink);
        }

        .event-status.disetujui {
            background: rgba(20, 184, 166, 0.15);
            color: var(--teal-600);
        }

        .event-status.berjalan {
            background: rgba(242, 114, 59, 0.15);
            color: var(--sunset-500);
        }

        .event-status.selesai {
            background: rgba(100, 116, 139, 0.16);
            color: #475569;
        }

        .event-status.pending {
            background: rgba(242, 114, 59, 0.15);
            color: var(--sunset-500);
        }

        .event-status.ditolak,
        .event-status.dibatalkan {
            background: rgba(225, 29, 72, 0.12);
            color: #e11d48;
        }

        /* ===== Respect reduced motion ===== */
        @media (prefers-reduced-motion: reduce) {

            [data-aos],
            .animate-kenburns,
            .lift-hover,
            .icon-nudge svg,
            #eventModal .modal-panel {
                animation: none !important;
                transition: none !important;
                opacity: 1 !important;
                transform: none !important;
            }
        }

        /* Styling Dasar FullCalendar UCIC Theme */
        .fc {
            --fc-border-color: #ecdfd0;
            --fc-button-bg-color: var(--ink-900);
            --fc-button-border-color: var(--ink-900);
            --fc-button-hover-bg-color: var(--ink-800);
            --fc-button-hover-border-color: var(--ink-800);
            --fc-button-active-bg-color: var(--sunset-500);
            --fc-button-active-border-color: var(--sunset-500);
            --fc-event-bg-color: var(--teal-600);
            --fc-event-border-color: var(--teal-600);
        }

        .fc-daygrid-dot-event.fc-status-disetujui .fc-daygrid-event-dot {
            border-color: var(--teal-500) !important;
        }

        .fc-daygrid-dot-event.fc-status-berjalan .fc-daygrid-event-dot {
            border-color: var(--sunset-500) !important;
        }

        .fc-daygrid-dot-event.fc-status-selesai .fc-daygrid-event-dot {
            border-color: #64748b !important;
        }

        .fc-daygrid-dot-event.fc-status-selesai {
            color: #64748b;
        }

        .fc .fc-toolbar-title {
            font-size: 1rem !important;
            font-weight: 700;
            color: var(--stone-ink);
        }

        @media (max-width: 639px) {
            .fc .fc-toolbar {
                flex-direction: column !important;
                gap: 0.75rem !important;
                align-items: center !important;
            }

            .fc .fc-toolbar-title {
                font-size: 0.95rem !important;
                text-align: center;
            }

            .fc .fc-button {
                padding: 0.35rem 0.6rem !important;
                font-size: 0.75rem !important;
            }

            .fc .fc-daygrid-day-frame {
                min-height: 55px !important;
            }

            .fc-event-title {
                font-size: 0.65rem !important;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .fc-col-header-cell-cushion {
                font-size: 0.7rem !important;
                padding: 2px !important;
            }
        }
    </style>
</head>

<body class="antialiased selection:bg-[#f2723b] selection:text-white" x-data="{ mobileMenuOpen: false }" style="background-color: var(--cream-50); color: var(--stone-ink);">

    <!-- ============ NAVBAR ============ -->
    <nav class="sticky top-0 z-50 backdrop-blur-md border-b border-white/10 text-white shadow-lg shadow-[#19133360]" style="background-color: rgba(25,19,51,0.92);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-18 sm:h-20">
                <a href="/" class="flex items-center gap-3 group shrink-0">
                    <img src="{{ asset('images/logo-ucic.png') }}" alt="Logo UCIC" class="h-9 sm:h-10 w-auto object-contain transition-transform duration-300 group-hover:scale-105">
                    <div>
                        <span class="text-base sm:text-lg font-bold tracking-tight font-heading block leading-none text-white">UCIC FACILITY</span>
                        <span class="text-[10px] font-medium tracking-[0.15em] uppercase" style="color: var(--sunset-300);">Management System</span>
                    </div>
                </a>

                <div class="hidden md:flex items-center gap-8 text-sm font-medium">
                    <a href="#statistik" class="text-white/70 hover:text-white transition-colors duration-200 py-1 border-b-2 border-transparent hover:border-[color:var(--sunset-400)]">Statistik</a>
                    <a href="#denah" class="text-white/70 hover:text-white transition-colors duration-200 py-1 border-b-2 border-transparent hover:border-[color:var(--sunset-400)]">Denah Kampus</a>
                    <a href="#fasilitas" class="text-white/70 hover:text-white transition-colors duration-200 py-1 border-b-2 border-transparent hover:border-[color:var(--sunset-400)]">Fasilitas Kampus</a>
                    <a href="#jadwal" class="text-white/70 hover:text-white transition-colors duration-200 py-1 border-b-2 border-transparent hover:border-[color:var(--sunset-400)]">Jadwal Fasilitas</a>
                    <a href="#layanan" class="text-white/70 hover:text-white transition-colors duration-200 py-1 border-b-2 border-transparent hover:border-[color:var(--sunset-400)]">Layanan</a>
                </div>

                <div class="hidden md:flex items-center gap-3">
                    @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm font-semibold rounded-lg bg-white/10 hover:bg-white/15 text-white border border-white/15 transition-all duration-200 flex items-center gap-2">
                        <i data-lucide="layout-dashboard" class="w-4 h-4" style="color: var(--sunset-400);"></i> Dashboard
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-3 py-2 text-sm font-medium text-white/60 hover:text-rose-300 transition-colors duration-200">Logout</button>
                    </form>
                    @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold text-white/80 hover:text-white transition-colors duration-200">Masuk</a>
                    @endauth
                </div>

                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-lg text-white/70 hover:text-white hover:bg-white/10 transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-[color:var(--sunset-400)]">
                    <i data-lucide="menu" x-show="!mobileMenuOpen" class="w-6 h-6"></i>
                    <i data-lucide="x" x-show="mobileMenuOpen" class="w-6 h-6" x-cloak></i>
                </button>
            </div>
        </div>

        <div x-show="mobileMenuOpen" x-cloak
            x-transition:enter="transition ease-out duration-250"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="md:hidden border-t border-white/10 px-4 pt-3 pb-6 space-y-1" style="background-color: var(--ink-950);">
            <a href="#statistik" @click="mobileMenuOpen = false" class="block py-2.5 px-3 rounded-lg text-white/80 hover:text-white hover:bg-white/10 transition-colors duration-200">Statistik</a>
            <a href="#denah" @click="mobileMenuOpen = false" class="block py-2.5 px-3 rounded-lg text-white/80 hover:text-white hover:bg-white/10 transition-colors duration-200">Denah Kampus</a>
            <a href="#fasilitas" @click="mobileMenuOpen = false" class="block py-2.5 px-3 rounded-lg text-white/80 hover:text-white hover:bg-white/10 transition-colors duration-200">Fasilitas Kampus</a>
            <a href="#jadwal" @click="mobileMenuOpen = false" class="block py-2.5 px-3 rounded-lg text-white/80 hover:text-white hover:bg-white/10 transition-colors duration-200">Jadwal Fasilitas</a>
            <a href="#layanan" @click="mobileMenuOpen = false" class="block py-2.5 px-3 rounded-lg text-white/80 hover:text-white hover:bg-white/10 transition-colors duration-200">Layanan</a>
            <div class="pt-4 mt-3 border-t border-white/10 flex flex-col gap-2">
                @auth
                <a href="{{ route('dashboard') }}" class="w-full text-center py-2.5 rounded-lg text-white font-semibold transition-colors duration-200" style="background: linear-gradient(135deg, var(--sunset-500), var(--sunset-400));">Dashboard</a>
                @else
                <a href="{{ route('login') }}" class="w-full text-center py-2.5 rounded-lg bg-white/10 text-white font-semibold transition-colors duration-200 hover:bg-white/15">Masuk</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- ============ HERO : Satu Kanvas Visual Utuh (bukan split) ============ -->
    <section id="hero" class="relative overflow-hidden min-h-[560px] sm:min-h-[640px] lg:min-h-[92vh] flex items-center"
        x-data="{
            activeSlide: 0,
            slides: [
                '{{ asset('images/hero-1.jpg') }}',
                '{{ asset('images/hero-2.webp') }}',
                '{{ asset('images/hero-3.webp') }}'
            ],
            timer: null,
            startAutoSlide() {
                this.timer = setInterval(() => { this.activeSlide = (this.activeSlide + 1) % this.slides.length; }, 5500);
            }
        }" x-init="startAutoSlide()">

        <!-- Layer Foto (background tunggal, bukan kolom terpisah) -->
        <template x-for="(slide, index) in slides" :key="index">
            <div x-show="activeSlide === index"
                x-transition:enter="transition opacity duration-1000 ease-in-out"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition opacity duration-1000 ease-in-out"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute inset-0 w-full h-full overflow-hidden">
                <img :src="slide" alt="Gedung & Fasilitas Kampus UCIC" class="w-full h-full object-cover animate-kenburns">
            </div>
        </template>

        <!-- Overlay gradasi hangat berlapis, bukan hitam datar -->
        <div class="absolute inset-0" style="background: linear-gradient(100deg, rgba(25,19,51,0.94) 0%, rgba(36,28,71,0.85) 38%, rgba(36,28,71,0.45) 65%, rgba(36,28,71,0.15) 100%);"></div>
        <div class="absolute inset-0" style="background: linear-gradient(0deg, rgba(25,19,51,0.75) 0%, transparent 45%);"></div>
        <div class="glow-sunset absolute top-10 right-[-100px] w-[460px] h-[460px] rounded-full pointer-events-none"></div>
        <div class="glow-plum absolute bottom-[-120px] right-1/4 w-[420px] h-[420px] rounded-full pointer-events-none"></div>

        <!-- Konten Hero (satu lapis visual, terpusat kiri) -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20 w-full">
            <div class="max-w-2xl text-white" data-aos="fade-up">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border text-[11px] sm:text-xs font-semibold uppercase tracking-wider backdrop-blur-sm"
                    style="background: rgba(242,114,59,0.16); border-color: rgba(242,114,59,0.4); color: var(--sunset-300);">
                    <span class="w-1.5 h-1.5 rounded-full" style="background-color: var(--sunset-400);"></span>
                    Facility Management System
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold font-heading leading-[1.08] mt-6" data-aos="fade-up" data-aos-delay="80">
                    Layanan Fasilitas
                    <span class="block text-gradient-sunset">& Sarana Kampus UCIC</span>
                </h1>

                <p class="text-white/75 text-sm sm:text-base leading-relaxed mt-6 max-w-lg" data-aos="fade-up" data-aos-delay="140">
                    Sistem terpadu pengajuan peminjaman ruangan, sarana prasarana, serta pelaporan kendala teknis fasilitas secara transparan dan terintegrasi.
                </p>

                <div class="flex flex-wrap gap-3 mt-9" data-aos="fade-up" data-aos-delay="200">
                    <a href="{{ route('login') }}" class="icon-nudge px-6 py-3.5 rounded-xl text-white font-bold text-sm transition-all duration-200 hover:-translate-y-0.5 flex items-center gap-2" style="background: linear-gradient(135deg, var(--sunset-500), var(--sunset-400)); box-shadow: 0 14px 28px -10px rgba(242,114,59,0.55);">
                        Akses Sistem <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                    <a href="#jadwal" class="px-6 py-3.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold text-sm backdrop-blur-md border border-white/20 hover:border-white/30 transition-all duration-200 hover:-translate-y-0.5">
                        Lihat Ketersediaan Ruang
                    </a>
                </div>
            </div>
        </div>

        <!-- Indikator slide - kontrol kecil, tidak bersaing dengan konten -->
        <div class="absolute bottom-6 right-6 z-20 flex gap-2">
            <template x-for="(slide, index) in slides" :key="index">
                <button @click="activeSlide = index"
                    :class="activeSlide === index ? 'w-7' : 'w-2 bg-white/40 hover:bg-white/70'"
                    :style="activeSlide === index ? 'background-color: var(--sunset-400)' : ''"
                    class="h-2 rounded-full transition-all duration-300"></button>
            </template>
        </div>
    </section>

    <!-- ============ MAIN CONTENT ============ -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-0 pb-20 sm:pb-24 space-y-16 sm:space-y-20">

        <!-- Section: Statistik Ringkas (di bawah hero, section sendiri) -->
        <div id="statistik" class="mt-10 sm:mt-14 relative z-30 grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5 bg-white rounded-2xl sm:rounded-3xl shadow-2xl p-5 sm:p-7 scroll-mt-24" style="box-shadow: 0 30px 60px -20px rgba(25,19,51,0.35);" data-aos="fade-up">
            <div class="lift-hover flex items-center gap-4 p-4 rounded-2xl border" style="border-color: #ecdfd0;">
                <div class="p-3 rounded-xl shrink-0" style="background-color: rgba(242,114,59,0.1); color: var(--sunset-500);"><i data-lucide="building" class="w-6 h-6"></i></div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider" style="color: var(--stone-muted);">Total Fasilitas</p>
                    <h3 class="text-2xl font-bold font-heading leading-tight" style="color: var(--stone-ink);">50+ Unit</h3>
                    <p class="text-xs" style="color: var(--stone-muted);">Ruangan, Lab, & Peralatan Kampus</p>
                </div>
            </div>

            <div class="lift-hover flex items-center gap-4 p-4 rounded-2xl border" style="border-color: #ecdfd0;">
                <div class="p-3 rounded-xl shrink-0" style="background-color: rgba(20,184,166,0.12); color: var(--teal-600);"><i data-lucide="check-circle-2" class="w-6 h-6"></i></div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider" style="color: var(--stone-muted);">Peminjaman Selesai</p>
                    <h3 class="text-2xl font-bold font-heading leading-tight" style="color: var(--stone-ink);">120+ Kegiatan</h3>
                    <p class="text-xs" style="color: var(--stone-muted);">Terlayani dengan baik</p>
                </div>
            </div>

            <div class="lift-hover flex items-center gap-4 p-4 rounded-2xl border" style="border-color: #ecdfd0;">
                <div class="p-3 rounded-xl shrink-0" style="background-color: rgba(124,58,237,0.1); color: var(--plum-600);"><i data-lucide="clock" class="w-6 h-6"></i></div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider" style="color: var(--stone-muted);">Respons Perbaikan</p>
                    <h3 class="text-2xl font-bold font-heading leading-tight" style="color: var(--stone-ink);">&lt; 24 Jam</h3>
                    <p class="text-xs" style="color: var(--stone-muted);">SLA Penanganan Tingkat Tinggi</p>
                </div>
            </div>
        </div>

        <!-- Section: Denah Kampus (Bento: Tab kiri, Viewer kanan) -->
        <section id="denah" class="scroll-mt-24" data-aos="fade-up">
            <div class="mb-6 flex items-center gap-2.5">
                <div class="w-1.5 h-6 rounded-full" style="background: linear-gradient(180deg, var(--sunset-500), var(--sunset-300));"></div>
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold font-heading" style="color: var(--stone-ink);">Denah Interaktif Lokasi Kampus</h2>
                    <p class="text-sm mt-1" style="color: var(--stone-muted);">Eksplorasi tata letak fasilitas di Kampus 1 dan Kampus 2 UCIC</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6">
                <!-- Tab Navigation Container (Unified Clean Card) -->
                <div class="lg:col-span-3 bg-white border rounded-2xl p-2.5 flex lg:flex-col gap-1.5 overflow-x-auto lg:overflow-visible shadow-sm" style="border-color: #ecdfd0;">
                    <button data-denah-tab class="denah-tab is-active shrink-0 flex items-center gap-2.5 text-left px-4 py-3 rounded-xl text-sm font-semibold whitespace-nowrap">
                        <i data-lucide="map-pin" class="w-4 h-4 shrink-0"></i> Kampus 1
                    </button>
                    <button data-denah-tab class="denah-tab shrink-0 flex items-center gap-2.5 text-left px-4 py-3 rounded-xl text-sm font-semibold whitespace-nowrap">
                        <i data-lucide="layers" class="w-4 h-4 shrink-0"></i> Kampus 2 - Lantai 1
                    </button>
                    <button data-denah-tab class="denah-tab shrink-0 flex items-center gap-2.5 text-left px-4 py-3 rounded-xl text-sm font-semibold whitespace-nowrap">
                        <i data-lucide="layers" class="w-4 h-4 shrink-0"></i> Kampus 2 - Lantai 2
                    </button>
                    <button data-denah-tab class="denah-tab shrink-0 flex items-center gap-2.5 text-left px-4 py-3 rounded-xl text-sm font-semibold whitespace-nowrap">
                        <i data-lucide="layers" class="w-4 h-4 shrink-0"></i> Kampus 2 - Lantai 3
                    </button>
                </div>

                <!-- Denah Swiper Viewer -->
                <div class="lg:col-span-9 relative">
                    <div class="denah-swiper swiper rounded-2xl overflow-hidden border bg-white min-h-[320px] sm:min-h-[480px] shadow-sm" style="border-color: #ecdfd0;">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide flex items-center justify-center p-3 sm:p-6">
                                <img src="{{ asset('images/denah-kampus1.png') }}" alt="Denah Kampus 1 UCIC" class="w-full h-auto max-h-[560px] object-contain rounded-lg">
                            </div>
                            <div class="swiper-slide flex items-center justify-center p-3 sm:p-6">
                                <img src="{{ asset('images/denah-kampus2-lantai1.png') }}" alt="Denah Kampus 2 Lantai 1 UCIC" class="w-full h-auto max-h-[560px] object-contain rounded-lg">
                            </div>
                            <div class="swiper-slide flex items-center justify-center p-3 sm:p-6">
                                <img src="{{ asset('images/denah-kampus2-lantai2.png') }}" alt="Denah Kampus 2 Lantai 2 UCIC" class="w-full h-auto max-h-[560px] object-contain rounded-lg">
                            </div>
                            <div class="swiper-slide flex items-center justify-center p-3 sm:p-6">
                                <img src="{{ asset('images/denah-kampus2-lantai3.png') }}" alt="Denah Kampus 2 Lantai 3 UCIC" class="w-full h-auto max-h-[560px] object-contain rounded-lg">
                            </div>
                        </div>
                    </div>

                    <button class="denah-prev absolute left-3 top-1/2 -translate-y-1/2 z-10 w-9 h-9 rounded-full bg-white/90 hover:bg-white shadow-md border flex items-center justify-center transition-colors duration-200" style="border-color: #ecdfd0;">
                        <i data-lucide="chevron-left" class="w-4 h-4" style="color: var(--stone-ink);"></i>
                    </button>
                    <button class="denah-next absolute right-3 top-1/2 -translate-y-1/2 z-10 w-9 h-9 rounded-full bg-white/90 hover:bg-white shadow-md border flex items-center justify-center transition-colors duration-200" style="border-color: #ecdfd0;">
                        <i data-lucide="chevron-right" class="w-4 h-4" style="color: var(--stone-ink);"></i>
                    </button>
                    <div class="denah-pagination flex justify-center gap-1.5 mt-4"></div>
                </div>
            </div>
        </section>

        <!-- Section: Fasilitas Kampus (Bento: Tab Daftar Ruangan Kiri, Viewer Foto Ruangan Kanan) -->
        @php
            $dbRooms = [];
            $k1List = [];
            $k2List = [];

            if (isset($fasilitasKampus) && $fasilitasKampus->isNotEmpty()) {
                foreach ($fasilitasKampus as $room) {
                    $rId = 'room_' . $room->id;
                    $kampusKey = ($room->lokasi && $room->lokasi->kampus === 'Kampus 2') ? 'kampus2' : 'kampus1';
                    $rData = [
                        'id' => $rId,
                        'name' => $room->nama_fasilitas,
                        'campus' => $room->lokasi->kampus ?? ($kampusKey === 'kampus2' ? 'Kampus 2' : 'Kampus 1'),
                        'location' => $room->lokasi_display,
                        'desc' => $room->deskripsi ?: 'Fasilitas dan sarana prasarana resmi Universitas Catur Insan Cendekia.',
                        'facilities' => count($room->fasilitas_list) > 0 ? $room->fasilitas_list : ['Kondisi Baik', 'Fasilitas Lengkap'],
                        'image' => $room->foto_url,
                        'badge' => ($room->lokasi && $room->lokasi->kode_ruangan) ? $room->lokasi->kode_ruangan : (($room->lokasi && $room->lokasi->lantai) ? 'Lt. ' . $room->lokasi->lantai : 'Lt. 1'),
                    ];
                    $dbRooms[$rId] = $rData;
                    if ($kampusKey === 'kampus2') {
                        $k2List[] = $rData;
                    } else {
                        $k1List[] = $rData;
                    }
                }
            }
        @endphp
        <section id="fasilitas" class="scroll-mt-24" data-aos="fade-up"
            x-data="{
                campus: '{{ count($k1List) > 0 ? 'kampus1' : (count($k2List) > 0 ? 'kampus2' : 'kampus1') }}',
                k1Rooms: {{ \Illuminate\Support\Js::from($k1List) }},
                k2Rooms: {{ \Illuminate\Support\Js::from($k2List) }},
                rooms: {{ \Illuminate\Support\Js::from($dbRooms) }},
                activeRoomId: '{{ count($k1List) > 0 ? $k1List[0]['id'] : (count($k2List) > 0 ? $k2List[0]['id'] : '') }}',
                get currentRoom() {
                    if (this.rooms && this.activeRoomId && this.rooms[this.activeRoomId]) {
                        return this.rooms[this.activeRoomId];
                    }
                    if (this.campus === 'kampus1' && this.k1Rooms.length > 0) return this.k1Rooms[0];
                    if (this.campus === 'kampus2' && this.k2Rooms.length > 0) return this.k2Rooms[0];
                    return null;
                },
                setCampus(c) {
                    this.campus = c;
                    if (c === 'kampus1' && this.k1Rooms.length > 0) {
                        this.activeRoomId = this.k1Rooms[0].id;
                    } else if (c === 'kampus2' && this.k2Rooms.length > 0) {
                        this.activeRoomId = this.k2Rooms[0].id;
                    } else {
                        this.activeRoomId = '';
                    }
                    this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
                },
                setRoom(id) {
                    this.activeRoomId = id;
                    this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
                }
            }">

            <div class="mb-6 flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-1.5 h-6 rounded-full" style="background: linear-gradient(180deg, var(--sunset-500), var(--plum-600));"></div>
                    <div>
                        <h2 class="text-xl sm:text-2xl font-bold font-heading" style="color: var(--stone-ink);">Galeri Fasilitas & Ruangan Kampus</h2>
                        <p class="text-sm mt-1" style="color: var(--stone-muted);">Pilih ruangan di sisi kiri untuk melihat visual foto fisik dan sarana di dalamnya</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs font-semibold px-3 py-1.5 rounded-full border" style="background-color: var(--cream-100); border-color: #ecdfd0; color: var(--stone-ink);">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Status: Foto Fisik Terverifikasi</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6 items-start">
                <!-- Kolom Kiri: Tab Pilihan Kampus & Daftar Ruangan -->
                <div class="lg:col-span-4 min-w-0 w-full fasilitas-col-left bg-white border rounded-2xl p-4 sm:p-5 shadow-sm space-y-4" style="border-color: #ecdfd0;">
                    
                    <!-- Pilihan Kampus (Kampus 1 & Kampus 2) -->
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider mb-2.5" style="color: var(--stone-muted);">Pilih Kampus</p>
                        <div class="grid grid-cols-2 gap-2 p-1 rounded-xl border" style="background-color: var(--cream-50); border-color: #ecdfd0;">
                            <button type="button" @click="setCampus('kampus1')"
                                :class="campus === 'kampus1' ? 'campus-btn is-active' : 'campus-btn'"
                                class="py-2.5 px-3 rounded-lg text-xs sm:text-sm font-bold flex flex-col items-center gap-1 border border-transparent transition-all">
                                <div class="flex items-center gap-1.5">
                                    <i data-lucide="building-2" class="w-4 h-4"></i>
                                    <span>Kampus 1</span>
                                </div>
                                <span class="text-[10px] font-medium opacity-75">Jl. Kesambi</span>
                            </button>

                            <button type="button" @click="setCampus('kampus2')"
                                :class="campus === 'kampus2' ? 'campus-btn is-active' : 'campus-btn'"
                                class="py-2.5 px-3 rounded-lg text-xs sm:text-sm font-bold flex flex-col items-center gap-1 border border-transparent transition-all">
                                <div class="flex items-center gap-1.5">
                                    <i data-lucide="building" class="w-4 h-4"></i>
                                    <span>Kampus 2</span>
                                </div>
                                <span class="text-[10px] font-medium opacity-75">Gedung Baru</span>
                            </button>
                        </div>
                    </div>

                    <div class="h-px" style="background-color: #f2ece2;"></div>

                    <!-- Header List Ruangan -->
                    <div class="flex items-center justify-between px-1">
                        <span class="text-[11px] font-bold uppercase tracking-wider" style="color: var(--stone-muted);">Daftar Ruangan</span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full" style="background-color: var(--cream-100); color: var(--sunset-500);" x-text="(campus === 'kampus1' ? k1Rooms.length : k2Rooms.length) + ' Ruangan'"></span>
                    </div>

                    <!-- Daftar Ruangan Kampus 1 (Scrollable Vertikal) -->
                    <div x-show="campus === 'kampus1'" class="max-h-[480px] overflow-y-auto space-y-1.5 pr-1" style="scrollbar-width: thin;">
                        <template x-for="r in k1Rooms" :key="r.id">
                            <button type="button" @click="setRoom(r.id)"
                                :class="activeRoomId === r.id ? 'fasilitas-sub-btn is-active' : 'fasilitas-sub-btn'"
                                class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs sm:text-sm text-left transition-all">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <i data-lucide="door-closed" class="w-4 h-4 shrink-0"></i>
                                    <span class="truncate" x-text="r.name"></span>
                                </div>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-white/20 shrink-0" x-text="r.badge"></span>
                            </button>
                        </template>
                        <template x-if="k1Rooms.length === 0">
                            <div class="py-12 px-4 text-center">
                                <div class="w-10 h-10 mx-auto mb-2 rounded-full flex items-center justify-center" style="background-color: var(--cream-100); color: var(--stone-muted);">
                                    <i data-lucide="inbox" class="w-5 h-5"></i>
                                </div>
                                <p class="text-xs text-[color:var(--stone-muted)] font-medium">Belum ada fasilitas terdaftar di Kampus 1.</p>
                            </div>
                        </template>
                    </div>

                    <!-- Daftar Ruangan Kampus 2 (Scrollable Vertikal) -->
                    <div x-show="campus === 'kampus2'" class="max-h-[480px] overflow-y-auto space-y-1.5 pr-1" style="scrollbar-width: thin;" x-cloak>
                        <template x-for="r in k2Rooms" :key="r.id">
                            <button type="button" @click="setRoom(r.id)"
                                :class="activeRoomId === r.id ? 'fasilitas-sub-btn is-active' : 'fasilitas-sub-btn'"
                                class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs sm:text-sm text-left transition-all">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <i data-lucide="door-closed" class="w-4 h-4 shrink-0"></i>
                                    <span class="truncate" x-text="r.name"></span>
                                </div>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-white/20 shrink-0" x-text="r.badge"></span>
                            </button>
                        </template>
                        <template x-if="k2Rooms.length === 0">
                            <div class="py-12 px-4 text-center">
                                <div class="w-10 h-10 mx-auto mb-2 rounded-full flex items-center justify-center" style="background-color: var(--cream-100); color: var(--stone-muted);">
                                    <i data-lucide="inbox" class="w-5 h-5"></i>
                                </div>
                                <p class="text-xs text-[color:var(--stone-muted)] font-medium">Belum ada fasilitas terdaftar di Kampus 2.</p>
                            </div>
                        </template>
                    </div>

                </div>

                <!-- Kolom Kanan: Viewer Foto Ruangan (Persis Seperti Viewer Denah Kampus) -->
                <div class="lg:col-span-8 min-w-0 w-full fasilitas-col-right">
                    <div class="bg-white border rounded-2xl shadow-sm overflow-hidden flex flex-col justify-between" style="border-color: #ecdfd0;">
                        
                        <template x-if="currentRoom">
                            <div>
                                <!-- Header Bar Viewer Ruangan -->
                                <div class="p-5 sm:p-6 border-b flex flex-col sm:flex-row sm:items-center justify-between gap-4" style="border-color: #f2ece2; background-color: var(--cream-50);">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider text-white" style="background: linear-gradient(135deg, var(--sunset-500), var(--sunset-400));" x-text="currentRoom.campus"></span>
                                            <span class="px-2.5 py-0.5 rounded-md text-[11px] font-semibold" style="background-color: var(--cream-100); color: var(--stone-ink);" x-text="currentRoom.location"></span>
                                        </div>
                                        <h3 class="text-xl sm:text-2xl font-bold font-heading text-[color:var(--stone-ink)]" x-text="currentRoom.name"></h3>
                                        <p class="text-xs sm:text-sm leading-relaxed text-[color:var(--stone-muted)] max-w-2xl" x-text="currentRoom.desc"></p>
                                    </div>
                                    
                                    <div class="shrink-0">
                                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full border" style="background-color: #fff; border-color: #ecdfd0; color: #10b981;">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            <span>Tersedia</span>
                                        </span>
                                    </div>
                                </div>

                                <!-- Frame Foto Ruangan (Satu Foto Penuh Tanpa Ditimpa) -->
                                <div class="relative w-full h-[360px] sm:h-[480px] bg-neutral-900 overflow-hidden">
                                    <img :src="currentRoom.image" 
                                         :alt="currentRoom.name" 
                                         x-on:error="$event.target.src = '{{ asset('images/hero-1.jpg') }}'"
                                         class="w-full h-full object-cover transition-all duration-300">

                                    <!-- Badge Floating Info Foto di Pojok Kiri Bawah -->
                                    <div class="absolute bottom-4 left-4 z-10 hidden sm:flex items-center gap-2 px-3.5 py-2 rounded-xl backdrop-blur-md bg-black/60 border border-white/20 text-white">
                                        <i data-lucide="camera" class="w-4 h-4 text-[color:var(--sunset-400)]"></i>
                                        <span class="text-xs font-semibold" x-text="'Foto: ' + currentRoom.name"></span>
                                    </div>
                                </div>

                                <!-- Footer Bar: Fasilitas Ruangan & Tombol Aksi -->
                                <div class="p-4 sm:p-6 border-t flex flex-col sm:flex-row sm:items-center justify-between gap-4" style="border-color: #f2ece2; background-color: #fff;">
                                    <div>
                                        <p class="text-[11px] font-bold uppercase tracking-wider mb-2" style="color: var(--stone-muted);">Fasilitas di Dalam Ruangan</p>
                                        <div class="flex flex-wrap gap-1.5">
                                            <template x-for="(item, idx) in currentRoom.facilities" :key="idx">
                                                <span class="text-xs font-semibold px-2.5 py-1 rounded-lg border flex items-center gap-1.5" style="background-color: var(--cream-50); border-color: #ecdfd0; color: var(--stone-ink);">
                                                    <i data-lucide="check" class="w-3 h-3 text-emerald-500"></i>
                                                    <span x-text="item"></span>
                                                </span>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2.5 shrink-0 pt-2 sm:pt-0">
                                        <a href="#jadwal" class="icon-nudge px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white transition-all duration-200 flex items-center gap-2 shadow-md" style="background: linear-gradient(135deg, var(--sunset-500), var(--sunset-400));">
                                            <i data-lucide="calendar" class="w-4 h-4"></i> Cek Jadwal Ruangan
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template x-if="!currentRoom">
                            <div class="py-24 px-6 text-center flex flex-col items-center justify-center space-y-3 min-h-[460px]">
                                <div class="w-14 h-14 rounded-2xl flex items-center justify-center shadow-sm" style="background-color: var(--cream-100); color: var(--sunset-500);">
                                    <i data-lucide="building-2" class="w-7 h-7"></i>
                                </div>
                                <h4 class="text-base font-bold font-heading" style="color: var(--stone-ink);">Belum Ada Fasilitas yang Ditampilkan</h4>
                                <p class="text-xs sm:text-sm max-w-md mx-auto" style="color: var(--stone-muted);">
                                    Ruangan dan fasilitas yang ditandai <strong>"Tampilkan di Landing Page"</strong> pada akun RTK akan muncul di sini beserta foto fisik dan kelengkapannya.
                                </p>
                            </div>
                        </template>

                    </div>
                </div>
            </div>
        </section>

        <!-- Section: Kalender Peminjaman Fasilitas -->
        <section id="jadwal" class="bg-white rounded-2xl shadow-sm border p-4 sm:p-8 space-y-6 scroll-mt-24" style="border-color: #ecdfd0;" data-aos="fade-up">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-5 border-b" style="border-color: #f2ece2;">
                <div>
                    <div class="flex items-center gap-2.5">
                        <div class="w-1.5 h-6 rounded-full" style="background: linear-gradient(180deg, var(--teal-600), var(--teal-500));"></div>
                        <h2 class="text-xl sm:text-2xl font-bold font-heading" style="color: var(--stone-ink);">Jadwal Penggunaan Fasilitas</h2>
                    </div>
                    <p class="text-xs mt-1.5 ml-4" style="color: var(--stone-muted);">Pantau ketersediaan ruangan & barang secara real-time sebelum mengajukan peminjaman</p>
                </div>
                <div class="flex flex-wrap items-center gap-4 text-xs font-medium px-3.5 py-2 rounded-lg border" style="color: var(--stone-ink); background-color: var(--cream-100); border-color: #ecdfd0;">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full" style="background-color: var(--teal-500); box-shadow: 0 0 0 4px rgba(20,184,166,0.18);"></span> Disetujui
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full" style="background-color: var(--sunset-500); box-shadow: 0 0 0 4px rgba(242,114,59,0.18);"></span> Berjalan
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full" style="background-color: #64748b; box-shadow: 0 0 0 4px rgba(100,116,139,0.18);"></span> Selesai
                    </div>
                </div>
            </div>

            <div class="p-2 sm:p-4 rounded-xl border overflow-hidden" style="background-color: var(--cream-50); border-color: #ecdfd0;">
                <div id="calendar" class="w-full text-xs sm:text-sm"></div>
            </div>
        </section>

        <!-- Section: Panduan Layanan (diperkaya: intro + alur + 3 kartu) -->
        <section id="layanan" class="scroll-mt-24">
            <div class="max-w-xl mb-10" data-aos="fade-up">
                <p class="text-xs font-bold uppercase tracking-widest" style="color: var(--plum-600);">Panduan Layanan</p>
                <h2 class="text-2xl sm:text-3xl font-bold font-heading mt-2" style="color: var(--stone-ink);">Apa yang Bisa Kamu Lakukan di Sini?</h2>
                <p class="text-sm mt-2" style="color: var(--stone-muted);">Dari melapor kerusakan sampai mengajukan peminjaman ruang — semua terhubung dalam satu alur kerja yang sama.</p>
            </div>

            <!-- Alur Pengajuan -->
            <div class="bg-white rounded-2xl border p-6 sm:p-8 mb-8 sm:mb-10" style="border-color: #ecdfd0;" data-aos="fade-up">
                <h3 class="text-sm font-bold uppercase tracking-wider mb-6" style="color: var(--stone-muted);">Alur Pengajuan</h3>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-0 relative">
                    <div class="hidden lg:block absolute top-6 left-[12%] right-[12%] h-px step-connector"></div>

                    <div class="relative flex flex-col items-center text-center gap-3" data-aos="fade-up" data-aos-delay="0">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold font-heading relative z-10" style="background: linear-gradient(135deg, var(--sunset-500), var(--sunset-400));">1</div>
                        <p class="text-sm font-semibold" style="color: var(--stone-ink);">Ajukan</p>
                        <p class="text-xs" style="color: var(--stone-muted);">Isi formulir laporan atau peminjaman secara online</p>
                    </div>
                    <div class="relative flex flex-col items-center text-center gap-3" data-aos="fade-up" data-aos-delay="80">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold font-heading relative z-10" style="background: linear-gradient(135deg, var(--teal-600), var(--teal-500));">2</div>
                        <p class="text-sm font-semibold" style="color: var(--stone-ink);">Verifikasi</p>
                        <p class="text-xs" style="color: var(--stone-muted);">Sistem mengecek ketersediaan & bentrok jadwal otomatis</p>
                    </div>
                    <div class="relative flex flex-col items-center text-center gap-3" data-aos="fade-up" data-aos-delay="160">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold font-heading relative z-10" style="background: linear-gradient(135deg, var(--plum-600), var(--plum-500));">3</div>
                        <p class="text-sm font-semibold" style="color: var(--stone-ink);">Persetujuan</p>
                        <p class="text-xs" style="color: var(--stone-muted);">Petugas RTK meninjau & menyetujui pengajuan</p>
                    </div>
                    <div class="relative flex flex-col items-center text-center gap-3" data-aos="fade-up" data-aos-delay="240">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold font-heading relative z-10" style="background: linear-gradient(135deg, var(--ink-900), var(--ink-800));">4</div>
                        <p class="text-sm font-semibold" style="color: var(--stone-ink);">Gunakan</p>
                        <p class="text-xs" style="color: var(--stone-muted);">Status & progres bisa dipantau langsung dari dashboard</p>
                    </div>
                </div>
            </div>

            <!-- Bento kartu layanan -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6">
                <div class="lift-hover lg:col-span-5 group bg-white rounded-2xl border p-6 sm:p-8 flex flex-col justify-between relative overflow-hidden" style="border-color: #ecdfd0;" data-aos="fade-up">
                    <div class="absolute -top-10 -right-10 w-32 h-32 rounded-full transition-colors duration-500" style="background-color: rgba(242,114,59,0.06);"></div>
                    <div class="space-y-4 relative">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center transition-transform duration-300 group-hover:scale-110" style="background-color: rgba(242,114,59,0.1); color: var(--sunset-500); border: 1px solid rgba(242,114,59,0.25);">
                            <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                        </div>
                        <h3 class="text-xl font-bold font-heading" style="color: var(--stone-ink);">Pelaporan Kerusakan Fasilitas</h3>
                        <p class="text-sm leading-relaxed" style="color: var(--stone-muted);">
                            Menemukan fasilitas rusak, AC tidak dingin, atau sarana kelas bermasalah? Laporkan langsung melalui sistem, unggah bukti foto, dan pantau status perbaikan oleh teknisi secara transparan.
                        </p>
                    </div>
                    <div class="pt-6 relative">
                        @auth
                        <a href="{{ route('pelapor.pengajuan.index') }}" class="icon-nudge inline-flex items-center gap-2 text-sm font-bold transition-colors duration-200" style="color: var(--sunset-500);">
                            Buat Laporan Baru <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                        @else
                        <a href="{{ route('login') }}" class="icon-nudge inline-flex items-center gap-2 text-sm font-bold transition-colors duration-200" style="color: var(--stone-ink);">
                            Login untuk Melapor <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                        @endauth
                    </div>
                </div>

                <div class="lift-hover lg:col-span-4 group text-white rounded-2xl p-6 sm:p-8 flex flex-col justify-between relative overflow-hidden" style="background: linear-gradient(160deg, var(--ink-950), var(--ink-800));" data-aos="fade-up" data-aos-delay="100">
                    <div class="absolute -top-10 -right-10 w-32 h-32 rounded-full transition-colors duration-500" style="background-color: rgba(20,184,166,0.15);"></div>
                    <div class="space-y-4 relative">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center transition-transform duration-300 group-hover:scale-110" style="background-color: rgba(20,184,166,0.15); color: var(--teal-500); border: 1px solid rgba(20,184,166,0.3);">
                            <i data-lucide="calendar-check" class="w-6 h-6"></i>
                        </div>
                        <h3 class="text-xl font-bold font-heading text-white">Peminjaman Ruang & Alat</h3>
                        <p class="text-sm leading-relaxed text-white/65">
                            Dukung kegiatan akademik, himpunan, dan unit kegiatan mahasiswa dengan mengajukan peminjaman fasilitas secara resmi. Cek bentrok jadwal secara otomatis oleh sistem.
                        </p>
                    </div>
                    <div class="pt-6 relative">
                        @auth
                        <a href="{{ route('pelapor.peminjaman.index') }}" class="icon-nudge inline-flex items-center gap-2 text-sm font-bold transition-colors duration-200" style="color: var(--teal-500);">
                            Ajukan Peminjaman <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                        @else
                        <a href="{{ route('login') }}" class="icon-nudge inline-flex items-center gap-2 text-sm font-bold text-white hover:text-white/80 transition-colors duration-200">
                            Login untuk Meminjam <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                        @endauth
                    </div>
                </div>

                <!-- Kartu bantuan/kontak - mengisi bento, memakai data kontak yang sama dengan footer -->
                <div class="lift-hover lg:col-span-3 rounded-2xl p-6 sm:p-8 flex flex-col justify-between relative overflow-hidden border" style="background-color: var(--cream-100); border-color: #ecdfd0;" data-aos="fade-up" data-aos-delay="200">
                    <div class="space-y-4 relative">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: rgba(124,58,237,0.1); color: var(--plum-600); border: 1px solid rgba(124,58,237,0.25);">
                            <i data-lucide="headset" class="w-6 h-6"></i>
                        </div>
                        <h3 class="text-lg font-bold font-heading" style="color: var(--stone-ink);">Butuh Bantuan?</h3>
                        <p class="text-sm leading-relaxed" style="color: var(--stone-muted);">Hubungi tim RTK jika mengalami kendala saat mengajukan.</p>
                    </div>
                    <div class="pt-6 space-y-2 relative text-sm">
                        <a href="mailto:rtk@ucic.ac.id" class="flex items-center gap-2 font-semibold hover:opacity-75 transition-opacity duration-200" style="color: var(--plum-600);">
                            <i data-lucide="mail" class="w-4 h-4"></i> rtk@ucic.ac.id
                        </a>
                        <a href="tel:0231206543" class="flex items-center gap-2 font-semibold hover:opacity-75 transition-opacity duration-200" style="color: var(--plum-600);">
                            <i data-lucide="phone" class="w-4 h-4"></i> (0231) 206543
                        </a>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- ============ FOOTER ============ -->
    <footer class="border-t border-white/10 text-white/50 py-14" style="background-color: var(--ink-950);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-10">
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-ucic.png') }}" alt="Logo UCIC" class="h-10 w-auto object-contain transition-transform duration-300 hover:scale-105">
                    <span class="text-base font-bold font-heading text-white">UCIC FACILITY</span>
                </div>
                <p class="text-xs leading-relaxed max-w-xs text-white/50">
                    Sistem Informasi Manajemen Fasilitas Terpadu Universitas Catur Insan Cendekia (UCIC).
                </p>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-white font-heading mb-4">Tautan Cepat</h4>
                <ul class="space-y-2.5 text-xs">
                    <li><a href="#denah" class="transition-colors duration-200 inline-flex items-center gap-1.5" onmouseover="this.style.color='var(--sunset-400)'" onmouseout="this.style.color=''"><i data-lucide="chevron-right" class="w-3 h-3"></i>Denah Lokasi Kampus</a></li>
                    <li><a href="#fasilitas" class="transition-colors duration-200 inline-flex items-center gap-1.5" onmouseover="this.style.color='var(--sunset-400)'" onmouseout="this.style.color=''"><i data-lucide="chevron-right" class="w-3 h-3"></i>Fasilitas Kampus</a></li>
                    <li><a href="#jadwal" class="transition-colors duration-200 inline-flex items-center gap-1.5" onmouseover="this.style.color='var(--sunset-400)'" onmouseout="this.style.color=''"><i data-lucide="chevron-right" class="w-3 h-3"></i>Kalender Peminjaman</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-white font-heading mb-4">Kontak RTK</h4>
                <p class="text-xs leading-relaxed text-white/50">
                    Gedung Utama Kampus 1 UCIC<br>
                    Email: rtk@ucic.ac.id<br>
                    Telp: (0231) 206543
                </p>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-10 pt-6 border-t border-white/10 text-center text-xs text-white/40">
            &copy; {{ date('Y') }} UCIC Facility Management. All rights reserved.
        </div>
    </footer>

    <!-- ============ MODAL DETAIL EVENT KALENDER (dipakai oleh app.js) ============ -->
    <div id="eventModal">
        <div class="modal-panel">
            <button type="button" onclick="closeEventModal()" class="modal-close" aria-label="Tutup">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
            <div id="eventDetailContent"></div>
        </div>
    </div>

    <!-- ============ SCRIPTS ============ -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.lucide) lucide.createIcons();

            if (window.AOS) {
                AOS.init({
                    duration: 650,
                    easing: 'ease-out-cubic',
                    once: true,
                    offset: 60
                });
            }

            if (window.Swiper) {
                const denahSwiper = new Swiper('.denah-swiper', {
                    speed: 400,
                    effect: 'fade',
                    fadeEffect: {
                        crossFade: true
                    },
                    navigation: {
                        nextEl: '.denah-next',
                        prevEl: '.denah-prev'
                    },
                    pagination: {
                        el: '.denah-pagination',
                        clickable: true,
                        bulletClass: 'w-2 h-2 rounded-full bg-[#e7d9c8] cursor-pointer transition-all duration-300',
                        bulletActiveClass: '!w-6 !bg-[#f2723b]'
                    },
                });

                const denahTabs = document.querySelectorAll('[data-denah-tab]');
                denahTabs.forEach((btn, idx) => {
                    btn.addEventListener('click', () => denahSwiper.slideTo(idx));
                });
                denahSwiper.on('slideChange', () => {
                    denahTabs.forEach((btn, idx) => {
                        btn.classList.toggle('is-active', idx === denahSwiper.activeIndex);
                    });
                });
            }

        });
    </script>
</body>

</html>