@extends('layouts.teknisi')

@section('title', 'Dashboard IT Support & Helpdesk')

@section('content')
<div class="py-4 sm:py-6 space-y-6">

    <!-- Welcome Hero Card -->
    <div class="bg-[#191333] text-white rounded-2xl p-6 sm:p-8 shadow-sm border border-[#2a2338] relative overflow-hidden">
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="p-3.5 bg-[#2a2338] border border-[#ecdfd0]/20 rounded-2xl text-[#f2723b] shrink-0">
                    <i data-lucide="wrench" class="w-7 h-7"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold font-heading">Halo, {{ Auth::user()->nama }}! 👋</h1>
                    <p class="text-xs sm:text-sm text-[#ecdfd0]/80 mt-1">
                        Selamat datang di dashboard IT Support & Helpdesk. Pantau tugas penanganan dan tingkatkan efisiensi perbaikan.
                    </p>
                </div>
            </div>
            <a href="{{ route('teknisi.tugas.index') }}" 
               class="inline-flex items-center justify-center gap-2 bg-[#f2723b] hover:bg-[#e05d26] text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow-sm transition shrink-0">
                <i data-lucide="list-todo" class="w-4 h-4"></i> Lihat Semua Tugas
            </a>
        </div>
        <!-- Subtle decorative glow -->
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-[#f2723b]/10 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <!-- Loading State Card -->
    <div id="dashboard-loading" class="text-center py-16 bg-white rounded-2xl border border-[#ecdfd0] shadow-sm">
        <div class="p-3 bg-[#fbf7f0] rounded-full w-14 h-14 mx-auto flex items-center justify-center text-[#f2723b] mb-3">
            <i data-lucide="loader-2" class="w-7 h-7 animate-spin"></i>
        </div>
        <p class="text-xs sm:text-sm font-semibold text-[#191333]">Memuat data dashboard...</p>
        <p class="text-xs text-[#7a7189] mt-0.5">Mohon tunggu sebentar.</p>
    </div>

    <!-- Dashboard Content Wrapper (Initially Hidden) -->
    <div id="dashboard-content" class="hidden space-y-6">
        
        <!-- Stats Cards Grid -->
        <div id="stats-cards" class="grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-6">
            <!-- Akan diisi secara dinamis oleh JavaScript -->
        </div>

        <!-- Overdue Alert Banner -->
        <div id="overdue-warning" class="hidden bg-rose-50 border border-rose-200 p-4 rounded-2xl shadow-sm">
            <!-- Akan diisi secara dinamis oleh JavaScript -->
        </div>

        <!-- Priority Tasks Card -->
        <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm overflow-hidden">
            <div class="p-5 sm:p-6 border-b border-[#ecdfd0] flex items-center justify-between gap-4">
                <div class="flex items-center gap-2.5">
                    <div class="p-2 bg-[#fbf7f0] rounded-lg text-[#f2723b]">
                        <i data-lucide="alert-octagon" class="w-5 h-5"></i>
                    </div>
                    <h2 class="font-bold font-heading text-sm sm:text-base text-[#2a2338]">
                        Tugas Prioritas
                    </h2>
                    <span id="priority-count" class="text-xs font-bold bg-[#f2723b]/10 text-[#f2723b] px-2.5 py-0.5 rounded-full border border-[#f2723b]/20">
                        0
                    </span>
                </div>
                <a href="{{ route('teknisi.tugas.index') }}" class="text-xs font-bold text-[#f2723b] hover:underline flex items-center gap-1">
                    Lihat Semua <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            <!-- Tasks Container -->
            <div id="priority-tasks" class="p-5 sm:p-6 divide-y divide-[#ecdfd0]">
                <!-- Akan diisi secara dinamis oleh JavaScript -->
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.lucide) { lucide.createIcons(); }

        const loadingEl = document.getElementById('dashboard-loading');
        const contentEl = document.getElementById('dashboard-content');

        fetch('{{ route("api.dashboard.teknisi") }}', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Gagal memuat data dari server.');
            return response.json();
        })
        .then(data => {
            loadingEl.classList.add('hidden');
            contentEl.classList.remove('hidden');
            renderTeknisiDashboard(data);
            if (window.lucide) { lucide.createIcons(); }
        })
        .catch(error => {
            loadingEl.innerHTML = `
                <div class="text-rose-600 space-y-3 p-4">
                    <div class="p-3 bg-rose-50 rounded-full w-12 h-12 mx-auto flex items-center justify-center text-rose-600">
                        <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold">Gagal memuat dashboard</p>
                        <p class="text-xs text-[#7a7189] mt-1">${error.message}</p>
                    </div>
                    <button onclick="location.reload()" class="inline-flex items-center gap-1.5 bg-[#191333] text-white px-4 py-2 rounded-xl text-xs font-semibold hover:bg-[#2a2338] transition">
                        <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i> Muat Ulang
                    </button>
                </div>
            `;
            if (window.lucide) { lucide.createIcons(); }
            console.error('Dashboard error:', error);
        });
    });

    function renderTeknisiDashboard(data) {
        const stats = data.stats;

        // Render Stats Cards
        const statsContainer = document.getElementById('stats-cards');
        statsContainer.innerHTML = `
            <div class="bg-white rounded-2xl border border-[#ecdfd0] p-4 text-center shadow-sm">
                <span class="text-[11px] font-bold text-[#7a7189] uppercase tracking-wider block mb-1">Total Tugas</span>
                <div class="text-2xl sm:text-3xl font-extrabold text-[#191333]">${stats.total_tugas}</div>
            </div>
            <div class="bg-white rounded-2xl border border-[#ecdfd0] p-4 text-center shadow-sm">
                <span class="text-[11px] font-bold text-[#7a7189] uppercase tracking-wider block mb-1">Sedang Dikerjakan</span>
                <div class="text-2xl sm:text-3xl font-extrabold text-amber-600">${stats.tugas_aktif}</div>
            </div>
            <div class="bg-white rounded-2xl border border-[#ecdfd0] p-4 text-center shadow-sm">
                <span class="text-[11px] font-bold text-[#7a7189] uppercase tracking-wider block mb-1">Selesai Bulan Ini</span>
                <div class="text-2xl sm:text-3xl font-extrabold text-emerald-600">${stats.tugas_selesai_bulan_ini}</div>
            </div>
            <div class="bg-white rounded-2xl border border-[#ecdfd0] p-4 text-center shadow-sm">
                <span class="text-[11px] font-bold text-[#7a7189] uppercase tracking-wider block mb-1">Rata-rata Penyelesaian</span>
                <div class="text-2xl sm:text-3xl font-extrabold ${stats.avg_completion_days ? 'text-[#f2723b]' : 'text-[#7a7189]'}">
                    ${stats.avg_completion_days ? stats.avg_completion_days + ' <span class="text-xs font-normal">hari</span>' : '-'}
                </div>
            </div>
        `;

        // Render Overdue Warning
        const overdueWarning = document.getElementById('overdue-warning');
        if (data.overdue_count > 0) {
            overdueWarning.classList.remove('hidden');
            overdueWarning.innerHTML = `
                <div class="flex items-center gap-3 text-rose-800">
                    <div class="p-2 bg-rose-100 rounded-xl text-rose-600 shrink-0">
                        <i data-lucide="alert-circle" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm font-bold">${data.overdue_count} tugas telah melewati tenggat waktu!</p>
                        <p class="text-[11px] sm:text-xs text-rose-700">Segera tinjau dan selesaikan tugas penanganan yang tertunda.</p>
                    </div>
                </div>
            `;
        } else {
            overdueWarning.classList.add('hidden');
        }

        // Render Priority Tasks
        const priorityTasks = data.prioritas_tugas;
        const tasksContainer = document.getElementById('priority-tasks');
        const priorityCount = document.getElementById('priority-count');
        priorityCount.textContent = priorityTasks.length;

        if (!priorityTasks || priorityTasks.length === 0) {
            tasksContainer.innerHTML = `
                <div class="text-center py-10 text-[#7a7189] space-y-2">
                    <div class="p-3 bg-emerald-50 rounded-full w-12 h-12 mx-auto flex items-center justify-center text-emerald-600">
                        <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                    </div>
                    <p class="text-xs sm:text-sm font-semibold text-[#191333]">Semua tugas prioritas selesai!</p>
                    <p class="text-xs text-[#7a7189]">Tidak ada daftar antrean pekerjaan yang memerlukan perhatian khusus saat ini.</p>
                </div>
            `;
        } else {
            tasksContainer.innerHTML = priorityTasks.map(task => {
                const urgencyBadge = {
                    'tinggi': 'bg-rose-100 text-rose-800 border-rose-200',
                    'sedang': 'bg-amber-100 text-amber-800 border-amber-200',
                    'rendah': 'bg-emerald-100 text-emerald-800 border-emerald-200'
                };
                const isOverdue = task.is_overdue;

                return `
                    <div class="py-4 first:pt-0 last:pb-0 ${isOverdue ? 'bg-rose-50/60 -mx-5 px-5 sm:-mx-6 sm:px-6 rounded-xl' : ''}">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-1.5">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-bold text-[#2a2338] text-xs sm:text-sm">
                                        ${task.jenis_kerusakan}
                                    </span>
                                    <span class="px-2.5 py-0.5 text-[10px] font-bold uppercase rounded-xl border ${urgencyBadge[task.tingkat_urgensi] || 'bg-gray-100 text-gray-800 border-gray-200'}">
                                        ${task.tingkat_urgensi}
                                    </span>
                                    ${isOverdue ? '<span class="px-2 py-0.5 text-[10px] font-bold bg-rose-200 text-rose-800 rounded-xl border border-rose-300 flex items-center gap-1"><i data-lucide="clock-alert" class="w-3 h-3"></i> Overdue</span>' : ''}
                                </div>
                                <p class="text-xs text-[#7a7189] flex items-center gap-1.5">
                                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-[#f2723b] shrink-0"></i> ${task.lokasi}
                                </p>
                                <div class="flex flex-wrap items-center gap-3 text-[11px] text-[#7a7189]">
                                    <span class="flex items-center gap-1"><i data-lucide="user" class="w-3 h-3"></i> ${task.pelapor}</span>
                                    <span class="flex items-center gap-1"><i data-lucide="calendar" class="w-3 h-3"></i> ${task.jadwal}</span>
                                    ${!isOverdue ? `<span class="text-emerald-700 font-semibold">${task.days_until_deadline} hari tersisa</span>` : ''}
                                </div>
                            </div>
                            <div class="flex items-center sm:self-center shrink-0">
                                <a href="${task.link}" class="inline-flex items-center gap-1.5 bg-[#fbf7f0] hover:bg-[#191333] text-[#191333] hover:text-white border border-[#ecdfd0] text-xs font-semibold px-3 py-1.5 rounded-xl transition-all">
                                    <i data-lucide="eye" class="w-3.5 h-3.5 text-[#f2723b]"></i>
                                    <span>Detail</span>
                                </a>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }
    }
</script>
@endpush