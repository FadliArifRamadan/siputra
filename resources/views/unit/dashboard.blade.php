@extends('layouts.unit')

@section('title', 'Dashboard UNIT')

@section('content')
    <div class="py-4 sm:py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Welcome Banner Khusus UNIT -->
            <div
                class="bg-[#191333] text-white p-6 sm:p-8 rounded-2xl border border-[#2a2338] shadow-sm relative overflow-hidden">
                <div class="relative z-10">
                    <span class="text-xs font-bold text-[#f2723b] uppercase tracking-wider mb-1 block">UNIT PORTAL</span>
                    <h1 class="text-2xl sm:text-3xl font-bold font-heading">Selamat Datang, {{ Auth::user()->nama }}!</h1>
                    <p class="text-xs sm:text-sm text-[#ecdfd0] mt-1 max-w-xl">
                        Panel kendali khusus unit untuk pelaporan kerusakan sarana prasarana serta pengajuan izin fasilitas
                        kampus.
                    </p>
                </div>
                <div class="absolute -right-8 -bottom-8 opacity-10 text-white">
                    <i data-lucide="layout-dashboard" class="w-48 h-48"></i>
                </div>
            </div>

            <!-- Metric Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl border border-[#ecdfd0] p-5 shadow-sm space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-[#7a7189]">Total Laporan</span>
                        <div
                            class="w-8 h-8 rounded-lg bg-[#fbf7f0] flex items-center justify-center text-[#191333] border border-[#ecdfd0]">
                            <i data-lucide="file-text" class="w-4 h-4 text-[#f2723b]"></i>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-[#191333]">{{ $statistik['total_laporan'] }}</div>
                </div>

                <div class="bg-white rounded-2xl border border-[#ecdfd0] p-5 shadow-sm space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-[#7a7189]">Laporan Pending</span>
                        <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center text-amber-700">
                            <i data-lucide="clock" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-amber-600">{{ $statistik['laporan_pending'] }}</div>
                </div>

                <div class="bg-white rounded-2xl border border-[#ecdfd0] p-5 shadow-sm space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-[#7a7189]">Total Peminjaman</span>
                        <div
                            class="w-8 h-8 rounded-lg bg-[#fbf7f0] flex items-center justify-center text-[#191333] border border-[#ecdfd0]">
                            <i data-lucide="calendar" class="w-4 h-4 text-[#f2723b]"></i>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-[#191333]">{{ $statistik['total_peminjaman'] }}</div>
                </div>

                <div class="bg-white rounded-2xl border border-[#ecdfd0] p-5 shadow-sm space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-[#7a7189]">Peminjaman Aktif</span>
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-700">
                            <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-emerald-600">{{ $statistik['peminjaman_aktif'] }}</div>
                </div>
            </div>

            <!-- Tabel Riwayat Terbaru -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Laporan Terakhir -->
                <div class="bg-white rounded-2xl border border-[#ecdfd0] p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-bold text-[#2a2338] flex items-center gap-2">
                            <i data-lucide="wrench" class="w-4 h-4 text-[#f2723b]"></i> Laporan Kerusakan Terakhir
                        </h2>
                        <a href="{{ route('pelapor.pengajuan.create') }}"
                            class="text-xs font-semibold text-[#f2723b] hover:underline">
                            + Buat Laporan
                        </a>
                    </div>
                    <div class="space-y-3">
                        @forelse ($laporanTerbaru as $lap)
                            <div
                                class="p-3 bg-[#fbf7f0] rounded-xl border border-[#ecdfd0] flex items-center justify-between text-xs">
                                <div>
                                    <div class="font-bold text-[#2a2338]">
                                        {{ $lap->fasilitas->nama_fasilitas ?? 'Fasilitas' }}</div>
                                    <div class="text-[#7a7189]">{{ Str::limit($lap->deskripsi, 40) }}</div>
                                </div>
                                <span
                                    class="px-2 py-1 rounded-lg text-[10px] font-bold uppercase
                                {{ $lap->status_laporan === 'selesai' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $lap->status_laporan }}
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-[#7a7189] py-4 text-center">Belum ada laporan kerusakan.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Peminjaman Terakhir -->
                <div class="bg-white rounded-2xl border border-[#ecdfd0] p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-bold text-[#2a2338] flex items-center gap-2">
                            <i data-lucide="calendar" class="w-4 h-4 text-[#f2723b]"></i> Peminjaman Fasilitas Terakhir
                        </h2>
                        <a href="{{ route('pelapor.peminjaman.create') }}"
                            class="text-xs font-semibold text-[#f2723b] hover:underline">
                            + Ajukan Pinjam
                        </a>
                    </div>
                    <div class="space-y-3">
                        @forelse ($peminjamanTerbaru as $pinjam)
                            <div
                                class="p-3 bg-[#fbf7f0] rounded-xl border border-[#ecdfd0] flex items-center justify-between text-xs">
                                <div>
                                    <div class="font-bold text-[#2a2338]">
                                        {{ $pinjam->tujuan_kegiatan ?? 'Peminjaman Ruang' }}</div>
                                    <div class="text-[#7a7189]">
                                        {{ \Carbon\Carbon::parse($pinjam->tanggal_mulai)->isoFormat('D MMM YYYY') }}</div>
                                </div>
                                <span
                                    class="px-2 py-1 rounded-lg text-[10px] font-bold uppercase
                                {{ in_array($pinjam->status_pinjam, ['disetujui', 'berjalan']) ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $pinjam->status_pinjam }}
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-[#7a7189] py-4 text-center">Belum ada riwayat peminjaman.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
