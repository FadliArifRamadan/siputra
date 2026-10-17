@extends('layouts.rtk')

@section('title', 'Dashboard RTK')

@section('content')
<div class="py-4 sm:py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        
        <!-- Welcome Section -->
        <div class="relative overflow-hidden rounded-2xl p-6 sm:p-8 text-white shadow-xl" style="background: linear-gradient(135deg, #191333 0%, #2f2559 100%);">
            <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-[#f2723b]/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/10 text-[#fbb35f] border border-white/10 mb-2">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5"></i> RTK Administrator Panel
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-extrabold font-heading text-white tracking-tight">
                        Selamat datang, {{ Auth::user()->nama }}!
                    </h1>
                    <p class="text-white/70 text-sm sm:text-base mt-1 max-w-2xl">
                        Kelola seluruh fasilitas kampus, peminjaman, dan penugasan perbaikan dengan mudah dalam satu tempat.
                    </p>
                </div>
                <div class="hidden sm:flex items-center gap-2 bg-white/5 border border-white/10 rounded-xl p-3 backdrop-blur-sm shrink-0">
                    <i data-lucide="calendar" class="w-5 h-5 text-[#fbb35f]"></i>
                    <span class="text-xs font-medium text-white/80">{{ now()->locale('id')->isoFormat('D MMMM YYYY') }}</span>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div id="dashboard-loading" class="bg-white/80 backdrop-blur-md rounded-2xl border border-[#ecdfd0] shadow-sm p-12 text-center">
            <div class="inline-flex items-center justify-center p-4 bg-[#fbf7f0] rounded-2xl mb-3">
                <svg class="animate-spin h-8 w-8 text-[#f2723b]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <p class="text-sm font-semibold text-[#2a2338]">Memuat data dashboard...</p>
            <p class="text-xs text-[#7a7189] mt-1">Mengambil analitik dan rekapitulasi sistem terbaru</p>
        </div>

        <!-- Dashboard Content (hidden initially) -->
        <div id="dashboard-content" class="hidden space-y-6">

            <!-- 1. WIDGET "BUTUH TINDAKAN SEKARANG" -->
            <div>
                <h2 class="text-sm font-bold font-heading uppercase tracking-wider text-[#7a7189] mb-3 flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-[#f2723b]"></i> Perhatian Utama
                </h2>
                <div id="need-action-widget" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Akan diisi oleh JavaScript -->
                </div>
            </div>

            <!-- SLA Widget -->
            <div>
                <h2 class="text-sm font-bold font-heading uppercase tracking-wider text-[#7a7189] mb-3 flex items-center gap-2">
                    <i data-lucide="clock" class="w-4 h-4 text-[#f2723b]"></i> Status Target Penanganan (SLA)
                </h2>
                <div id="sla-widget" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Akan diisi JavaScript -->
                </div>
            </div>

            <!-- 2. STATISTIK RINGKAS -->
            <div>
                <h2 class="text-sm font-bold font-heading uppercase tracking-wider text-[#7a7189] mb-3 flex items-center gap-2">
                    <i data-lucide="bar-chart-3" class="w-4 h-4 text-[#f2723b]"></i> Ringkasan Performa
                </h2>
                <div id="stats-widget" class="grid grid-cols-2 md:grid-cols-5 gap-3 sm:gap-4">
                    <!-- Akan diisi oleh JavaScript -->
                </div>
            </div>

            <!-- 3. CHART ROW -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm p-5 sm:p-6 transition-shadow hover:shadow-md">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold font-heading text-[#2a2338] flex items-center gap-2">
                            <i data-lucide="trending-up" class="w-4 h-4 text-[#f2723b]"></i> Tren Laporan per Bulan
                        </h3>
                    </div>
                    <div class="relative" style="height: 260px;">
                        <canvas id="monthlyTrendChart"></canvas>
                    </div>
                </div>
                <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm p-5 sm:p-6 transition-shadow hover:shadow-md">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold font-heading text-[#2a2338] flex items-center gap-2">
                            <i data-lucide="pie-chart" class="w-4 h-4 text-[#f2723b]"></i> Distribusi Status Laporan
                        </h3>
                    </div>
                    <div class="relative" style="height: 260px;">
                        <canvas id="statusDistributionChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- 4. URGENSI + TOP FASILITAS -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm p-5 sm:p-6 transition-shadow hover:shadow-md">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold font-heading text-[#2a2338] flex items-center gap-2">
                            <i data-lucide="zap" class="w-4 h-4 text-[#f2723b]"></i> Distribusi Tingkat Urgensi
                        </h3>
                    </div>
                    <div class="relative" style="height: 260px;">
                        <canvas id="urgensiDistributionChart"></canvas>
                    </div>
                </div>
                <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm p-5 sm:p-6 transition-shadow hover:shadow-md">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold font-heading text-[#2a2338] flex items-center gap-2">
                            <i data-lucide="trophy" class="w-4 h-4 text-[#f2723b]"></i> Top 5 Fasilitas Tersering Dilaporkan
                        </h3>
                    </div>
                    <div id="top-facilities-list" class="space-y-2.5">
                        <!-- Akan diisi oleh JavaScript -->
                    </div>
                </div>
            </div>

            <!-- 5. BEBAN KERJA TEKNISI -->
            <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm p-5 sm:p-6 transition-shadow hover:shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold font-heading text-[#2a2338] flex items-center gap-2">
                        <i data-lucide="hard-hat" class="w-4 h-4 text-[#f2723b]"></i> Beban Kerja Teknisi Aktif
                    </h3>
                </div>
                <div id="teknisi-workload-list" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <!-- Akan diisi oleh JavaScript -->
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loadingEl = document.getElementById('dashboard-loading');
        const contentEl = document.getElementById('dashboard-content');

        fetch('{{ route("api.dashboard.rtk") }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                loadingEl.classList.add('hidden');
                contentEl.classList.remove('hidden');
                renderRtkDashboard(data);
            })
            .catch(error => {
                loadingEl.innerHTML = `
                <div class="text-rose-600 p-6 rounded-xl bg-rose-50 border border-rose-200">
                    <p class="text-base font-bold font-heading">Gagal memuat dashboard</p>
                    <p class="text-xs text-rose-500 mt-1">${error.message}</p>
                    <button onclick="location.reload()" class="mt-4 bg-[#191333] hover:bg-[#2f2559] text-white text-xs font-semibold px-4 py-2 rounded-lg transition-colors shadow-sm inline-flex items-center gap-2">
                        <i data-lucide="rotate-cw" class="w-3.5 h-3.5"></i> Muat Ulang
                    </button>
                </div>
            `;
                if (window.lucide) lucide.createIcons();
                console.error('Dashboard error:', error);
            });
    });

    function renderRtkDashboard(data) {
        // 1. Widget "Butuh Tindakan Sekarang"
        const needAction = data.need_action;
        const needActionWidget = document.getElementById('need-action-widget');

        needActionWidget.innerHTML = `
            <div class="bg-rose-50 border border-rose-200 p-4 rounded-xl shadow-sm transition-transform hover:-translate-y-0.5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-rose-700 uppercase tracking-wider">Total Tindakan</span>
                    <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-500"></i>
                </div>
                <div class="text-3xl font-extrabold text-rose-600 font-heading mt-2">${needAction.total}</div>
                <div class="text-xs text-rose-500 mt-1">Laporan/Permintaan perlu diproses</div>
            </div>
            <div class="bg-amber-50 border border-amber-200 p-4 rounded-xl shadow-sm transition-transform hover:-translate-y-0.5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-amber-700 uppercase tracking-wider">Akun Pending</span>
                    <i data-lucide="user-check" class="w-4 h-4 text-amber-500"></i>
                </div>
                <div class="text-3xl font-extrabold text-amber-600 font-heading mt-2">${needAction.pending_accounts}</div>
                <div class="text-xs text-amber-600 mt-1">User menunggu verifikasi</div>
            </div>
            <div class="bg-blue-50 border border-blue-200 p-4 rounded-xl shadow-sm transition-transform hover:-translate-y-0.5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-blue-700 uppercase tracking-wider">Laporan Pending</span>
                    <i data-lucide="file-text" class="w-4 h-4 text-blue-500"></i>
                </div>
                <div class="text-3xl font-extrabold text-blue-600 font-heading mt-2">${needAction.pending_laporan}</div>
                <div class="text-xs text-blue-600 mt-1">Pengajuan laporan baru</div>
            </div>
            <div class="bg-purple-50 border border-purple-200 p-4 rounded-xl shadow-sm transition-transform hover:-translate-y-0.5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-purple-700 uppercase tracking-wider">Peminjaman Pending</span>
                    <i data-lucide="key" class="w-4 h-4 text-purple-500"></i>
                </div>
                <div class="text-3xl font-extrabold text-purple-600 font-heading mt-2">${needAction.pending_peminjaman}</div>
                <div class="text-xs text-purple-600 mt-1">Izin pinjam fasilitas</div>
            </div>
        `;

        // 2. Statistik Ringkas
        const stats = data.stats;
        const statsWidget = document.getElementById('stats-widget');
        statsWidget.innerHTML = `
            <div class="bg-white border border-[#ecdfd0] p-4 rounded-xl text-center shadow-sm">
                <div class="text-xs font-medium text-[#7a7189]">Total Laporan</div>
                <div class="text-2xl font-bold font-heading text-[#2a2338] mt-1">${stats.total_laporan}</div>
            </div>
            <div class="bg-emerald-50/60 border border-emerald-200 p-4 rounded-xl text-center shadow-sm">
                <div class="text-xs font-semibold text-emerald-700">Selesai</div>
                <div class="text-2xl font-bold font-heading text-emerald-600 mt-1">${stats.laporan_selesai}</div>
            </div>
            <div class="bg-blue-50/60 border border-blue-200 p-4 rounded-xl text-center shadow-sm">
                <div class="text-xs font-semibold text-blue-700">Diproses</div>
                <div class="text-2xl font-bold font-heading text-blue-600 mt-1">${stats.laporan_diproses}</div>
            </div>
            <div class="bg-amber-50/60 border border-amber-200 p-4 rounded-xl text-center shadow-sm">
                <div class="text-xs font-semibold text-amber-700">Pending</div>
                <div class="text-2xl font-bold font-heading text-amber-600 mt-1">${stats.laporan_pending}</div>
            </div>
            <div class="bg-purple-50/60 border border-purple-200 p-4 rounded-xl text-center shadow-sm">
                <div class="text-xs font-semibold text-purple-700">Tingkat Penyelesaian</div>
                <div class="text-2xl font-bold font-heading text-purple-600 mt-1">${stats.completion_rate}%</div>
                <div class="text-[10px] text-purple-500 font-medium mt-0.5">${stats.avg_completion_days ? '⏱️ Rata-rata ' + stats.avg_completion_days + ' hari' : ''}</div>
            </div>
        `;

        // 3. Charts
        renderCharts(data);

        // 4. Top 5 Fasilitas
        const topFacilities = data.top_facilities;
        const topFacilitiesList = document.getElementById('top-facilities-list');
        if (topFacilities.length === 0) {
            topFacilitiesList.innerHTML = '<p class="text-[#7a7189] text-xs py-4 text-center">Belum ada data laporan per fasilitas.</p>';
        } else {
            topFacilitiesList.innerHTML = topFacilities.map((item, index) => `
                <div class="flex items-center justify-between p-3 rounded-xl bg-[#fbf7f0] border border-[#ecdfd0]/70 transition-colors hover:bg-white">
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-lg bg-[#191333] text-white text-xs font-bold flex items-center justify-center font-heading">${index + 1}</span>
                        <span class="text-xs sm:text-sm font-semibold text-[#2a2338]">${item.nama}</span>
                    </div>
                    <span class="text-xs font-bold text-[#f2723b] bg-[#f2723b]/10 px-2.5 py-1 rounded-full border border-[#f2723b]/20">${item.total} laporan</span>
                </div>
            `).join('');
        }

        // 5. Beban Kerja Teknisi
        const workload = data.teknisi_workload;
        const workloadList = document.getElementById('teknisi-workload-list');
        if (workload.length === 0) {
            workloadList.innerHTML = '<p class="text-[#7a7189] text-xs py-4 text-center sm:col-span-2 md:col-span-3">Belum ada teknisi terdaftar.</p>';
        } else {
            workloadList.innerHTML = workload.map(item => `
                <div class="p-4 rounded-xl border border-[#ecdfd0] bg-[#fbf7f0]/50 hover:bg-white transition-all shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-[#2a2338] truncate">${item.nama}</span>
                        <i data-lucide="user" class="w-4 h-4 text-[#7a7189]"></i>
                    </div>
                    <div class="flex items-baseline justify-between mt-1">
                        <span class="text-2xl font-extrabold font-heading ${item.total_tugas_aktif > 3 ? 'text-rose-600' : 'text-[#f2723b]'}">
                            ${item.total_tugas_aktif}
                        </span>
                        <span class="text-[11px] font-medium text-[#7a7189]">Tugas Aktif</span>
                    </div>
                </div>
            `).join('');
        }

        // 6. SLA Widget
        const sla = data.sla;
        const slaWidget = document.getElementById('sla-widget');

        let slaHtml = '';

        // Breached
        slaHtml += `
        <div class="bg-rose-50/70 p-4 rounded-2xl border border-rose-200">
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-bold text-sm font-heading text-rose-800 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-rose-600 animate-pulse"></span>
                    SLA Breached (${sla.breached_count})
                </h4>
                ${sla.breached_count > 0 ? `<span class="text-[10px] font-bold uppercase tracking-wider text-rose-600 bg-rose-100 px-2 py-0.5 rounded-full">Tindakan Segera!</span>` : ''}
            </div>
            ${sla.breached.length === 0 ? `
                <p class="text-xs text-[#7a7189] py-2">Tidak ada laporan yang melewati deadline.</p>
            ` : `
                <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                    ${sla.breached.map(item => `
                        <div class="bg-white p-3 rounded-xl border border-rose-200 shadow-sm transition-all hover:border-rose-400">
                            <div class="flex justify-between items-start gap-2">
                                <div>
                                    <p class="text-xs font-bold text-[#2a2338] line-clamp-1">${item.jenis_kerusakan}</p>
                                    <p class="text-[11px] text-[#7a7189] mt-0.5">${item.pelapor} • <span class="capitalize font-medium text-rose-600">${item.status}</span></p>
                                </div>
                                <span class="text-[10px] bg-rose-100 text-rose-700 font-bold px-2 py-0.5 rounded shrink-0">Target: ${item.target_selesai}</span>
                            </div>
                            <a href="${item.link}" class="text-[11px] font-semibold text-[#f2723b] hover:text-[#191333] mt-2 inline-flex items-center gap-1">
                                Lihat Detail <i data-lucide="arrow-right" class="w-3 h-3"></i>
                            </a>
                        </div>
                    `).join('')}
                </div>
            `}
        </div>
    `;

        // Warning
        slaHtml += `
        <div class="bg-amber-50/70 p-4 rounded-2xl border border-amber-200">
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-bold text-sm font-heading text-amber-800 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    Mendekati Deadline (${sla.warning_count})
                </h4>
                ${sla.warning_count > 0 ? `<span class="text-[10px] font-bold uppercase tracking-wider text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full">Perlu Perhatian</span>` : ''}
            </div>
            ${sla.warning.length === 0 ? `
                <p class="text-xs text-[#7a7189] py-2">Tidak ada laporan yang mendekati deadline.</p>
            ` : `
                <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                    ${sla.warning.map(item => `
                        <div class="bg-white p-3 rounded-xl border border-amber-200 shadow-sm transition-all hover:border-amber-400">
                            <div class="flex justify-between items-start gap-2">
                                <div>
                                    <p class="text-xs font-bold text-[#2a2338] line-clamp-1">${item.jenis_kerusakan}</p>
                                    <p class="text-[11px] text-[#7a7189] mt-0.5">${item.pelapor} • <span class="capitalize font-medium text-amber-600">${item.status}</span></p>
                                </div>
                                <span class="text-[10px] bg-amber-100 text-amber-800 font-bold px-2 py-0.5 rounded shrink-0">Target: ${item.target_selesai}</span>
                            </div>
                            <a href="${item.link}" class="text-[11px] font-semibold text-[#f2723b] hover:text-[#191333] mt-2 inline-flex items-center gap-1">
                                Lihat Detail <i data-lucide="arrow-right" class="w-3 h-3"></i>
                            </a>
                        </div>
                    `).join('')}
                </div>
            `}
        </div>
    `;

        slaWidget.innerHTML = slaHtml;
        if (window.lucide) lucide.createIcons();
    }

    function renderCharts(data) {
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = "#7a7189";

        // 1. Line Chart: Tren Laporan per Bulan
        const monthlyTrend = data.monthly_trend;
        const ctx1 = document.getElementById('monthlyTrendChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: monthlyTrend.map(item => item.month),
                datasets: [{
                    label: 'Jumlah Laporan',
                    data: monthlyTrend.map(item => item.total),
                    borderColor: '#f2723b',
                    backgroundColor: 'rgba(242, 114, 59, 0.12)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#191333',
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(236, 223, 208, 0.5)' },
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });

        // 2. Pie Chart: Distribusi Status
        const statusDist = data.status_distribution;
        const ctx2 = document.getElementById('statusDistributionChart').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Diproses', 'Selesai', 'Ditolak'],
                datasets: [{
                    data: [statusDist.pending, statusDist.diproses, statusDist.selesai, statusDist.ditolak],
                    backgroundColor: ['#f59e0b', '#3b82f6', '#10b981', '#f43f5e'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { font: { size: 11, weight: '500' }, boxWidth: 12 }
                    }
                },
                cutout: '65%'
            }
        });

        // 3. Bar Chart: Distribusi Urgensi
        const urgensiDist = data.urgensi_distribution;
        const ctx3 = document.getElementById('urgensiDistributionChart').getContext('2d');
        new Chart(ctx3, {
            type: 'bar',
            data: {
                labels: ['Rendah', 'Sedang', 'Tinggi'],
                datasets: [{
                    label: 'Jumlah Laporan',
                    data: [urgensiDist.rendah, urgensiDist.sedang, urgensiDist.tinggi],
                    backgroundColor: ['#10b981', '#f59e0b', '#f43f5e'],
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(236, 223, 208, 0.5)' },
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }
</script>
@endpush
@endsection