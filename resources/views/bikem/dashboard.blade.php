@extends('layouts.bikem')

@section('title', 'Dashboard BIKEM')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

    <!-- Welcome Hero Section -->
    <div class="relative overflow-hidden rounded-2xl p-6 sm:p-8 text-white shadow-xl" style="background: linear-gradient(135deg, #191333 0%, #2f2559 100%);">
        <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-[#f2723b]/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/10 text-[#fbb35f] border border-white/10 mb-2">
                    <i data-lucide="graduation-cap" class="w-3.5 h-3.5"></i> Panel Biro Kemahasiswaan
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold font-heading text-white tracking-tight">
                    Selamat datang, {{ Auth::user()->nama }}!
                </h1>
                <p class="text-white/75 text-xs sm:text-sm mt-1 max-w-2xl">
                    Verifikasi proposal dan izin penggunaan fasilitas kampus untuk kegiatan mahasiswa dan ormawa secara terpusat.
                </p>
                <div class="flex flex-wrap items-center gap-2.5 mt-4">
                    <a href="{{ route('pelapor.pengajuan.create') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold bg-[#f2723b] hover:bg-[#d95d28] text-white px-3.5 py-2 rounded-xl transition shadow-sm">
                        <i data-lucide="wrench" class="w-3.5 h-3.5"></i> Lapor Kerusakan
                    </a>
                    <a href="{{ route('pelapor.peminjaman.create') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold bg-white/15 hover:bg-white/25 text-white border border-white/20 px-3.5 py-2 rounded-xl transition shadow-sm">
                        <i data-lucide="calendar-plus" class="w-3.5 h-3.5"></i> Ajukan Peminjaman
                    </a>
                    <a href="{{ route('pelapor.pengajuan.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-white/80 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl transition">
                        <i data-lucide="file-text" class="w-3.5 h-3.5"></i> Riwayat Laporan
                    </a>
                    <a href="{{ route('pelapor.peminjaman.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-white/80 hover:text-white hover:bg-white/10 px-3 py-2 rounded-xl transition">
                        <i data-lucide="calendar" class="w-3.5 h-3.5"></i> Riwayat Peminjaman
                    </a>
                </div>
            </div>
            <div class="flex items-center gap-2 bg-white/5 border border-white/10 rounded-xl p-3 backdrop-blur-sm shrink-0">
                <i data-lucide="calendar" class="w-5 h-5 text-[#fbb35f]"></i>
                <span class="text-xs font-medium text-white/90">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</span>
            </div>
        </div>
    </div>

    <!-- 4 Actionable Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Menunggu BIKEM -->
        <a href="{{ route('bikem.peminjaman.index', ['status' => 'menunggu_bikem']) }}"
            class="group bg-white rounded-2xl border-2 border-amber-300 p-5 shadow-sm hover:shadow-md hover:border-amber-400 transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <div class="p-3 bg-amber-50 text-amber-600 rounded-xl">
                    <i data-lucide="clock" class="w-6 h-6"></i>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 uppercase tracking-wider">
                    Perlu Ditinjau
                </span>
            </div>
            <div class="mt-4">
                <p class="text-xs font-bold text-[#7a7189] uppercase tracking-wider">Menunggu Persetujuan</p>
                <div class="flex items-baseline justify-between mt-1">
                    <p class="text-3xl font-extrabold font-heading text-amber-900">{{ $stats['menunggu_bikem'] }}</p>
                    <span class="text-xs font-medium text-amber-700 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                        Review <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </span>
                </div>
            </div>
        </a>

        <!-- Perlu Revisi -->
        <a href="{{ route('bikem.peminjaman.index', ['status' => 'perlu_revisi']) }}"
            class="group bg-white rounded-2xl border border-orange-200 p-5 shadow-sm hover:shadow-md hover:border-orange-300 transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <div class="p-3 bg-orange-50 text-orange-600 rounded-xl">
                    <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-orange-100 text-orange-800 uppercase tracking-wider">
                    Revisi Pemohon
                </span>
            </div>
            <div class="mt-4">
                <p class="text-xs font-bold text-[#7a7189] uppercase tracking-wider">Sedang Direvisi</p>
                <div class="flex items-baseline justify-between mt-1">
                    <p class="text-3xl font-extrabold font-heading text-orange-900">{{ $stats['perlu_revisi'] }}</p>
                    <span class="text-xs font-medium text-orange-700 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                        Pantau <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </span>
                </div>
            </div>
        </a>

        <!-- Menunggu RTK -->
        <a href="{{ route('bikem.peminjaman.index', ['status' => 'menunggu_rtk']) }}"
            class="group bg-white rounded-2xl border border-blue-200 p-5 shadow-sm hover:shadow-md hover:border-blue-300 transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                    <i data-lucide="send" class="w-6 h-6"></i>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 uppercase tracking-wider">
                    Lolos BIKEM
                </span>
            </div>
            <div class="mt-4">
                <p class="text-xs font-bold text-[#7a7189] uppercase tracking-wider">Menunggu RTK</p>
                <div class="flex items-baseline justify-between mt-1">
                    <p class="text-3xl font-extrabold font-heading text-blue-900">{{ $stats['menunggu_rtk'] }}</p>
                    <span class="text-xs font-medium text-blue-700 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                        Lihat <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </span>
                </div>
            </div>
        </a>

        <!-- Selesai / Terlaksana -->
        <a href="{{ route('bikem.peminjaman.index', ['status' => 'all']) }}"
            class="group bg-white rounded-2xl border border-emerald-200 p-5 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                    <i data-lucide="award" class="w-6 h-6"></i>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase tracking-wider">
                    Total Selesai
                </span>
            </div>
            <div class="mt-4">
                <p class="text-xs font-bold text-[#7a7189] uppercase tracking-wider">Kegiatan Terlaksana</p>
                <div class="flex items-baseline justify-between mt-1">
                    <p class="text-3xl font-extrabold font-heading text-emerald-900">{{ $stats['selesai'] }}</p>
                    <span class="text-xs font-medium text-emerald-700 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                        Riwayat <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </span>
                </div>
            </div>
        </a>
    </div>

    <!-- Main Section: Antrean Pengajuan & Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Antrean Pengajuan Prioritas (2 Kolom) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm p-6">
                <div class="flex items-center justify-between pb-4 border-b border-[#ecdfd0]">
                    <div class="flex items-center gap-2">
                        <div class="p-2 bg-amber-100 text-amber-700 rounded-lg">
                            <i data-lucide="inbox" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold font-heading text-[#2a2338]">Antrean Menunggu Persetujuan BIKEM</h2>
                            <p class="text-xs text-[#7a7189]">Pengajuan izin penggunaan fasilitas yang siap diverifikasi</p>
                        </div>
                    </div>
                    <a href="{{ route('bikem.peminjaman.index', ['status' => 'menunggu_bikem']) }}"
                        class="text-xs font-bold text-[#f2723b] hover:text-[#d95d28] transition">
                        Lihat Semua &rarr;
                    </a>
                </div>

                <div class="divide-y divide-[#ecdfd0] mt-2">
                    @forelse ($antreanPeminjaman as $item)
                    <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-[#fbf7f0]/40 p-2 rounded-xl transition">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-xs text-[#191333]">{{ $item->peminjam->nama ?? 'Mahasiswa' }}</span>
                                <span class="text-[11px] text-[#7a7189]">({{ $item->peminjam->nomor_induk ?? '-' }})</span>
                                <span class="px-2 py-0.5 text-[10px] font-bold bg-amber-100 text-amber-800 rounded-md">
                                    {{ $item->items->count() }} Fasilitas
                                </span>
                            </div>
                            <p class="text-xs font-medium text-[#2a2338] line-clamp-1">
                                <i data-lucide="bookmark" class="w-3.5 h-3.5 inline text-[#f2723b] mr-1"></i>
                                {{ $item->keperluan }}
                            </p>
                            <p class="text-[11px] text-[#7a7189] flex items-center gap-1.5">
                                <i data-lucide="calendar" class="w-3 h-3 text-[#0f8a82]"></i>
                                {{ $item->waktu_mulai->format('d M Y, H:i') }} - {{ $item->waktu_selesai->format('H:i') }} WIB
                            </p>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            @if ($item->file_pendukung)
                            <a href="{{ asset('storage/' . $item->file_pendukung) }}" target="_blank"
                                class="p-2 text-[#7a7189] hover:text-[#191333] bg-[#fbf7f0] border border-[#ecdfd0] rounded-xl text-xs transition" title="Lihat Berkas Proposal">
                                <i data-lucide="file-text" class="w-4 h-4"></i>
                            </a>
                            @endif
                            <a href="{{ route('bikem.peminjaman.show', $item) }}"
                                class="inline-flex items-center gap-1 text-xs font-bold text-white bg-[#191333] hover:bg-[#2f2559] px-3.5 py-2 rounded-xl transition shadow-sm">
                                Review & Aksi <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="py-12 text-center">
                        <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-3">
                            <i data-lucide="check-circle" class="w-6 h-6"></i>
                        </div>
                        <h3 class="text-sm font-bold text-[#2a2338]">Semua Bersih!</h3>
                        <p class="text-xs text-[#7a7189] mt-1">Tidak ada pengajuan peminjaman mahasiswa yang menunggu persetujuan saat ini.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Grafik Tren Kegiatan Bulanan -->
            <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-base font-bold font-heading text-[#2a2338]">Tren Pengajuan Kegiatan Kemahasiswaan</h3>
                        <p class="text-xs text-[#7a7189]">Volume peminjaman fasilitas 6 bulan terakhir</p>
                    </div>
                    <div class="p-2 bg-[#fbf7f0] text-[#f2723b] rounded-lg">
                        <i data-lucide="trending-up" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="relative" style="height: 250px;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Sidebar Kanan: Sedang Direvisi & Top Fasilitas (1 Kolom) -->
        <div class="space-y-6">

            <!-- Pengajuan yang Sedang Direvisi Mahasiswa -->
            <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-[#ecdfd0]">
                    <div class="flex items-center gap-2">
                        <div class="p-1.5 bg-orange-100 text-orange-700 rounded-lg">
                            <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-sm font-bold font-heading text-[#2a2338]">Menunggu Revisi</h3>
                    </div>
                    <span class="text-xs font-bold text-orange-600">{{ count($antreanRevisi) }}</span>
                </div>

                <div class="space-y-3">
                    @forelse ($antreanRevisi as $item)
                    <div class="p-3 bg-[#fbf7f0] rounded-xl border border-[#ecdfd0] text-xs space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-[#191333]">{{ $item->peminjam->nama ?? 'Mahasiswa' }}</span>
                            <span class="text-[10px] text-[#7a7189]">{{ $item->updated_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-[#7a7189] line-clamp-1">{{ $item->keperluan }}</p>
                        @if ($item->catatan_bikem)
                        <div class="p-2 bg-orange-50 border border-orange-200 rounded-lg text-[11px] text-orange-900">
                            <span class="font-bold">Catatan:</span> {{ Str::limit($item->catatan_bikem, 60) }}
                        </div>
                        @endif
                        <div class="text-right">
                            <a href="{{ route('bikem.peminjaman.show', $item) }}" class="text-[11px] font-bold text-[#f2723b] hover:underline">
                                Lihat Detail &rarr;
                            </a>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-[#7a7189] text-center py-4 italic">Tidak ada pengajuan yang sedang direvisi.</p>
                    @endforelse
                </div>
            </div>

            <!-- Top 5 Fasilitas Terpopuler -->
            <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-[#ecdfd0]">
                    <div class="flex items-center gap-2">
                        <div class="p-1.5 bg-[#fbf7f0] text-[#f2723b] rounded-lg">
                            <i data-lucide="flame" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-sm font-bold font-heading text-[#2a2338]">Fasilitas Terfavorit</h3>
                    </div>
                </div>

                <div class="space-y-3">
                    @forelse ($topFasilitas as $top)
                    <div class="flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-[#fbf7f0] border border-[#ecdfd0] text-[#191333] font-bold text-[10px] flex items-center justify-center">
                                {{ $loop->iteration }}
                            </span>
                            <div>
                                <span class="font-semibold text-[#191333] block">{{ $top->fasilitas->nama_fasilitas ?? 'Fasilitas' }}</span>
                                <span class="text-[10px] text-[#7a7189]">{{ ucfirst($top->fasilitas->kategori ?? '-') }}</span>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 bg-amber-50 text-amber-800 font-bold rounded-lg border border-amber-200">
                            {{ $top->total_dipinjam }}x
                        </span>
                    </div>
                    @empty
                    <p class="text-xs text-[#7a7189] text-center py-4 italic">Belum ada data peminjaman.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('trendChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($months) !!},
                    datasets: [{
                        label: 'Pengajuan Peminjaman',
                        data: {!! json_encode($trendData) !!},
                        borderColor: '#f2723b',
                        backgroundColor: 'rgba(242, 114, 59, 0.08)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#f2723b',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                font: {
                                    size: 11
                                }
                            },
                            grid: {
                                color: '#f5ede1'
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: 11
                                }
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
