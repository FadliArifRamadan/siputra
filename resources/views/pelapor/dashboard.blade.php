@extends('layouts.pelapor')

@section('title', 'Dashboard Pelapor')

@section('content')
<div class="py-4 sm:py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Welcome Banner -->
        <div class="bg-[#191333] text-white p-6 sm:p-8 rounded-2xl border border-[#2a2338] shadow-sm relative overflow-hidden">
            <div class="relative z-10">
                <span class="text-xs font-bold text-[#f2723b] uppercase tracking-wider mb-1 block">Pelapor Portal</span>
                <h1 class="text-2xl sm:text-3xl font-bold font-heading">Selamat Datang, {{ Auth::user()->nama }}!</h1>
                <p class="text-xs sm:text-sm text-[#ecdfd0] mt-1 max-w-xl">
                    Pantau status laporan kerusakan fasilitas dan riwayat pengajuan peminjaman Anda secara langsung di sini.
                </p>
            </div>
            <div class="absolute -right-8 -bottom-8 opacity-10 text-white">
                <i data-lucide="layout-dashboard" class="w-48 h-48"></i>
            </div>
        </div>

        <!-- Loading State -->
        <div id="dashboard-loading" class="text-center py-12 bg-white rounded-2xl border border-[#ecdfd0] shadow-sm">
            <div class="inline-flex items-center gap-3 px-4 py-2 bg-[#fbf7f0] rounded-xl border border-[#ecdfd0] text-[#191333]">
                <i data-lucide="loader-2" class="w-5 h-5 text-[#f2723b] animate-spin"></i>
                <span class="text-xs font-semibold">Memuat data dashboard...</span>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div id="dashboard-content" class="hidden space-y-6">

            <!-- Stats Cards Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl border border-[#ecdfd0] p-5 shadow-sm space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-[#7a7189]">Total Laporan</span>
                        <div class="w-8 h-8 rounded-lg bg-[#fbf7f0] flex items-center justify-center text-[#191333] border border-[#ecdfd0]">
                            <i data-lucide="file-text" class="w-4 h-4 text-[#f2723b]"></i>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-[#191333]" id="laporan-total">0</div>
                </div>

                <div class="bg-white rounded-2xl border border-[#ecdfd0] p-5 shadow-sm space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-[#7a7189]">Laporan Pending</span>
                        <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center text-amber-700">
                            <i data-lucide="clock" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-amber-600" id="laporan-pending">0</div>
                </div>

                <div class="bg-white rounded-2xl border border-[#ecdfd0] p-5 shadow-sm space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-[#7a7189]">Total Peminjaman</span>
                        <div class="w-8 h-8 rounded-lg bg-[#fbf7f0] flex items-center justify-center text-[#191333] border border-[#ecdfd0]">
                            <i data-lucide="calendar" class="w-4 h-4 text-[#f2723b]"></i>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-[#191333]" id="peminjaman-total">0</div>
                </div>

                <div class="bg-white rounded-2xl border border-[#ecdfd0] p-5 shadow-sm space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-[#7a7189]">Peminjaman Aktif</span>
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-700">
                            <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-emerald-600" id="peminjaman-active">0</div>
                </div>
            </div>

            <!-- Breakdown Detail Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Status Laporan Card -->
                <div class="bg-white rounded-2xl border border-[#ecdfd0] p-6 shadow-sm space-y-4">
                    <div class="flex items-center gap-2">
                        <i data-lucide="wrench" class="w-5 h-5 text-[#f2723b]"></i>
                        <h2 class="text-base font-bold text-[#2a2338]">Status Laporan Kerusakan</h2>
                    </div>
                    <div id="laporan-detail" class="grid grid-cols-2 gap-3">
                        <!-- Populated by JavaScript -->
                    </div>
                </div>

                <!-- Status Peminjaman Card -->
                <div class="bg-white rounded-2xl border border-[#ecdfd0] p-6 shadow-sm space-y-4">
                    <div class="flex items-center gap-2">
                        <i data-lucide="calendar-check" class="w-5 h-5 text-[#f2723b]"></i>
                        <h2 class="text-base font-bold text-[#2a2338]">Status Peminjaman Ruang</h2>
                    </div>
                    <div id="peminjaman-detail" class="grid grid-cols-2 gap-3">
                        <!-- Populated by JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Activity Timeline Card -->
            <div class="bg-white rounded-2xl border border-[#ecdfd0] p-6 shadow-sm space-y-4">
                <div class="flex items-center gap-2">
                    <i data-lucide="history" class="w-5 h-5 text-[#f2723b]"></i>
                    <h2 class="text-base font-bold text-[#2a2338]">Aktivitas Terbaru</h2>
                </div>
                <div id="timeline-container" class="space-y-3">
                    <!-- Populated by JavaScript -->
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loadingEl = document.getElementById('dashboard-loading');
        const contentEl = document.getElementById('dashboard-content');

        fetch('{{ route("api.dashboard.pelapor") }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Gagal memuat data dashboard');
                return response.json();
            })
            .then(data => {
                loadingEl.classList.add('hidden');
                contentEl.classList.remove('hidden');
                renderPelaporDashboard(data);
                if (window.lucide) { lucide.createIcons(); }
            })
            .catch(error => {
                loadingEl.innerHTML = `
                    <div class="text-center p-6 space-y-3">
                        <i data-lucide="alert-triangle" class="w-8 h-8 text-rose-500 mx-auto"></i>
                        <p class="text-xs sm:text-sm font-semibold text-rose-800">Gagal Memuat Dashboard</p>
                        <p class="text-xs text-[#7a7189]">${error.message}</p>
                        <button onclick="location.reload()" class="bg-[#191333] text-white text-xs px-4 py-2 rounded-xl font-semibold hover:bg-[#2f2559] transition">
                            Muat Ulang
                        </button>
                    </div>
                `;
                if (window.lucide) { lucide.createIcons(); }
                console.error('Dashboard error:', error);
            });
    });

    function renderPelaporDashboard(data) {
        const laporan = data.laporan_stats;
        const peminjaman = data.peminjaman_stats;

        // Populate Stats
        document.getElementById('laporan-total').textContent = laporan.total;
        document.getElementById('laporan-pending').textContent = laporan.pending;
        document.getElementById('peminjaman-total').textContent = peminjaman.total;
        document.getElementById('peminjaman-active').textContent = (peminjaman.berjalan || 0) + (peminjaman.disetujui || 0);

        // Laporan Breakdown
        const laporanDetail = document.getElementById('laporan-detail');
        laporanDetail.innerHTML = `
            <div class="bg-amber-50 p-3 rounded-xl border border-amber-200 text-center">
                <div class="text-lg font-bold text-amber-800">${laporan.pending}</div>
                <div class="text-[11px] font-semibold text-amber-700">Pending</div>
            </div>
            <div class="bg-sky-50 p-3 rounded-xl border border-sky-200 text-center">
                <div class="text-lg font-bold text-sky-800">${laporan.diproses}</div>
                <div class="text-[11px] font-semibold text-sky-700">Diproses</div>
            </div>
            <div class="bg-emerald-50 p-3 rounded-xl border border-emerald-200 text-center">
                <div class="text-lg font-bold text-emerald-800">${laporan.selesai}</div>
                <div class="text-[11px] font-semibold text-emerald-700">Selesai</div>
            </div>
            <div class="bg-rose-50 p-3 rounded-xl border border-rose-200 text-center">
                <div class="text-lg font-bold text-rose-800">${laporan.ditolak}</div>
                <div class="text-[11px] font-semibold text-rose-700">Ditolak</div>
            </div>
        `;

        // Peminjaman Breakdown
        const peminjamanDetail = document.getElementById('peminjaman-detail');
        peminjamanDetail.innerHTML = `
            <div class="bg-amber-50 p-3 rounded-xl border border-amber-200 text-center">
                <div class="text-lg font-bold text-amber-800">${peminjaman.pending}</div>
                <div class="text-[11px] font-semibold text-amber-700">Pending</div>
            </div>
            <div class="bg-emerald-50 p-3 rounded-xl border border-emerald-200 text-center">
                <div class="text-lg font-bold text-emerald-800">${peminjaman.disetujui}</div>
                <div class="text-[11px] font-semibold text-emerald-700">Disetujui</div>
            </div>
            <div class="bg-sky-50 p-3 rounded-xl border border-sky-200 text-center">
                <div class="text-lg font-bold text-sky-800">${peminjaman.berjalan}</div>
                <div class="text-[11px] font-semibold text-sky-700">Berjalan</div>
            </div>
            <div class="bg-[#fbf7f0] p-3 rounded-xl border border-[#ecdfd0] text-center">
                <div class="text-lg font-bold text-[#2a2338]">${peminjaman.selesai}</div>
                <div class="text-[11px] font-semibold text-[#7a7189]">Selesai</div>
            </div>
        `;

        // Timeline Container
        const timeline = data.timeline;
        const timelineContainer = document.getElementById('timeline-container');

        if (!timeline || timeline.length === 0) {
            timelineContainer.innerHTML = `
                <div class="text-center py-8 text-[#7a7189] bg-[#fbf7f0] rounded-xl border border-[#ecdfd0] space-y-1">
                    <p class="text-xs font-semibold text-[#2a2338]">Belum Ada Aktivitas</p>
                    <p class="text-[11px]">Mulai dengan membuat laporan kerusakan atau pengajuan peminjaman baru.</p>
                </div>
            `;
        } else {
            timelineContainer.innerHTML = timeline.map(item => {
                const isLaporan = item.type === 'laporan';
                const iconName = isLaporan ? 'wrench' : 'calendar-check';

                return `
                    <a href="${item.link}" class="flex items-center justify-between p-3.5 rounded-xl bg-[#fbf7f0] hover:bg-[#ecdfd0]/40 border border-[#ecdfd0] transition-colors group">
                        <div class="flex items-start gap-3">
                            <div class="p-2 rounded-lg bg-white border border-[#ecdfd0] text-[#f2723b] shrink-0 mt-0.5">
                                <i data-lucide="${iconName}" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-[#2a2338] group-hover:text-[#f2723b] transition-colors">${item.title}</h3>
                                <p class="text-xs text-[#7a7189] mt-0.5">${item.message}</p>
                                <div class="flex items-center gap-2 mt-1.5 text-[10px] font-medium text-[#7a7189]">
                                    <span>Oleh: ${item.actor}</span>
                                    <span>•</span>
                                    <span>${item.time_ago}</span>
                                </div>
                            </div>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-[#7a7189] group-hover:translate-x-0.5 transition-transform shrink-0"></i>
                    </a>
                `;
            }).join('');
        }

        if (window.lucide) { lucide.createIcons(); }
    }
</script>
@endpush
@endsection