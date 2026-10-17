@extends('layouts.rtk')

@section('title', 'Rekapitulasi Bulanan')

@section('content')
<div class="py-4 sm:py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Header Banner & Action Bar -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold font-heading text-[#2a2338] flex items-center gap-2">
                    <i data-lucide="bar-chart-3" class="w-6 h-6 text-[#f2723b]"></i> Rekapitulasi Bulanan
                </h1>
                <p class="text-xs sm:text-sm text-[#7a7189] mt-1">
                    Pantau statistik dan ekspor rekap data laporan kerusakan, penugasan teknisi, serta peminjaman fasilitas.
                </p>
            </div>

            <!-- Filter & Action Controls -->
            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Filter Form -->
                <form action="{{ route('rtk.laporan.rekapitulasi') }}" method="GET" class="flex items-center gap-2">
                    <select name="bulan" class="rounded-xl border-[#ecdfd0] shadow-sm focus:border-[#f2723b] focus:ring-[#f2723b] text-xs font-semibold text-[#2a2338]">
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                                {{ Carbon\Carbon::createFromDate(null, $i, 1)->locale('id')->isoFormat('MMMM') }}
                            </option>
                        @endfor
                    </select>
                    <select name="tahun" class="rounded-xl border-[#ecdfd0] shadow-sm focus:border-[#f2723b] focus:ring-[#f2723b] text-xs font-semibold text-[#2a2338]">
                        @for ($i = date('Y') - 5; $i <= date('Y'); $i++)
                            <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                    <button type="submit" class="bg-[#191333] hover:bg-[#2f2559] text-white px-4 py-2 rounded-xl transition-colors text-xs font-semibold shadow-sm flex items-center gap-1.5">
                        <i data-lucide="filter" class="w-3.5 h-3.5 text-[#f2723b]"></i> Filter
                    </button>
                </form>

                <!-- Ekspor Dropdown (Alpine.js) -->
                <div class="relative inline-block" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="bg-[#f2723b] hover:bg-[#d95f2a] text-white px-4 py-2 rounded-xl transition-colors text-xs font-semibold shadow-sm flex items-center gap-1.5">
                        <i data-lucide="download" class="w-3.5 h-3.5"></i> Ekspor Data
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5"></i>
                    </button>
                    <div x-show="open"
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-lg z-50 border border-[#ecdfd0] overflow-hidden py-1">
                        
                        <div class="px-3 py-2 text-[10px] font-bold text-[#7a7189] uppercase tracking-wider bg-[#fbf7f0] border-b border-[#ecdfd0]">
                            Multi-Sheet Excel
                        </div>
                        <a href="{{ route('rtk.laporan.export-excel', ['bulan' => $bulan, 'tahun' => $tahun, 'format' => 'xlsx']) }}"
                           class="flex items-center gap-2 px-4 py-2.5 text-xs text-[#2a2338] hover:bg-[#fbf7f0] transition-colors font-medium">
                            <i data-lucide="file-spreadsheet" class="w-4 h-4 text-emerald-600"></i> Excel (.xlsx) - Semua Data
                        </a>

                        <div class="px-3 py-2 text-[10px] font-bold text-[#7a7189] uppercase tracking-wider bg-[#fbf7f0] border-y border-[#ecdfd0] mt-1">
                            Per Jenis Laporan
                        </div>
                        <a href="{{ route('rtk.laporan.export-kerusakan', ['bulan' => $bulan, 'tahun' => $tahun, 'format' => 'xlsx']) }}"
                           class="flex items-center gap-2 px-4 py-2 text-xs text-[#2a2338] hover:bg-[#fbf7f0] transition-colors font-medium">
                            <i data-lucide="wrench" class="w-3.5 h-3.5 text-[#f2723b]"></i> Laporan Kerusakan (Excel)
                        </a>
                        <a href="{{ route('rtk.laporan.export-kerusakan', ['bulan' => $bulan, 'tahun' => $tahun, 'format' => 'csv']) }}"
                           class="flex items-center gap-2 px-4 py-2 text-xs text-[#2a2338] hover:bg-[#fbf7f0] transition-colors font-medium">
                            <i data-lucide="file-text" class="w-3.5 h-3.5 text-[#7a7189]"></i> Laporan Kerusakan (CSV)
                        </a>
                        <a href="{{ route('rtk.laporan.export-peminjaman', ['bulan' => $bulan, 'tahun' => $tahun, 'format' => 'xlsx']) }}"
                           class="flex items-center gap-2 px-4 py-2 text-xs text-[#2a2338] hover:bg-[#fbf7f0] transition-colors font-medium">
                            <i data-lucide="clipboard-list" class="w-3.5 h-3.5 text-sky-600"></i> Peminjaman (Excel)
                        </a>
                        <a href="{{ route('rtk.laporan.export-peminjaman', ['bulan' => $bulan, 'tahun' => $tahun, 'format' => 'csv']) }}"
                           class="flex items-center gap-2 px-4 py-2 text-xs text-[#2a2338] hover:bg-[#fbf7f0] transition-colors font-medium">
                            <i data-lucide="file-text" class="w-3.5 h-3.5 text-[#7a7189]"></i> Peminjaman (CSV)
                        </a>
                        <a href="{{ route('rtk.laporan.export-penugasan', ['bulan' => $bulan, 'tahun' => $tahun, 'format' => 'xlsx']) }}"
                           class="flex items-center gap-2 px-4 py-2 text-xs text-[#2a2338] hover:bg-[#fbf7f0] transition-colors font-medium">
                            <i data-lucide="user-check" class="w-3.5 h-3.5 text-purple-600"></i> Penugasan Teknisi (Excel)
                        </a>
                        <a href="{{ route('rtk.laporan.export-penugasan', ['bulan' => $bulan, 'tahun' => $tahun, 'format' => 'csv']) }}"
                           class="flex items-center gap-2 px-4 py-2 text-xs text-[#2a2338] hover:bg-[#fbf7f0] transition-colors font-medium">
                            <i data-lucide="file-text" class="w-3.5 h-3.5 text-[#7a7189]"></i> Penugasan Teknisi (CSV)
                        </a>

                        <div class="border-t border-[#ecdfd0] mt-1 pt-1">
                            <a href="{{ route('rtk.laporan.export-pdf', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
                               class="flex items-center gap-2 px-4 py-2.5 text-xs text-rose-600 hover:bg-rose-50 transition-colors font-semibold">
                                <i data-lucide="file-check-2" class="w-4 h-4 text-rose-500"></i> PDF Rekapitulasi Resmi
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Fast PDF Export Link -->
                <a href="{{ route('rtk.laporan.export-pdf', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
                   class="bg-white border border-[#ecdfd0] text-[#2a2338] hover:bg-[#fbf7f0] px-3.5 py-2 rounded-xl transition-colors text-xs font-semibold flex items-center gap-1.5 shadow-sm">
                    <i data-lucide="file-text" class="w-3.5 h-3.5 text-rose-500"></i> Cetak PDF
                </a>
            </div>
        </div>

        @if (session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-r-xl shadow-sm flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500 shrink-0"></i>
            <span class="text-xs sm:text-sm font-medium">{{ session('success') }}</span>
        </div>
        @endif

        <!-- Grid Cards Statistik -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-[#ecdfd0] shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-[#7a7189] uppercase tracking-wider">Total Laporan</span>
                    <div class="p-2 bg-[#fbf7f0] rounded-xl">
                        <i data-lucide="file-text" class="w-4 h-4 text-[#f2723b]"></i>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-[#2a2338]">{{ $statistik['total_laporan'] }}</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-[#ecdfd0] shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-[#7a7189] uppercase tracking-wider">Total Penugasan</span>
                    <div class="p-2 bg-[#fbf7f0] rounded-xl">
                        <i data-lucide="wrench" class="w-4 h-4 text-[#191333]"></i>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-[#2a2338]">{{ $statistik['total_penugasan'] }}</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-[#ecdfd0] shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-[#7a7189] uppercase tracking-wider">Total Peminjaman</span>
                    <div class="p-2 bg-[#fbf7f0] rounded-xl">
                        <i data-lucide="box" class="w-4 h-4 text-[#f2723b]"></i>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-[#2a2338]">{{ $statistik['total_peminjaman'] }}</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-[#ecdfd0] shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-[#7a7189] uppercase tracking-wider">Laporan Selesai</span>
                    <div class="p-2 bg-emerald-50 rounded-xl">
                        <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-emerald-600">{{ $statistik['laporan_selesai'] }}</div>
            </div>
        </div>

        <!-- Section 1: Detail Laporan Kerusakan -->
        <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm overflow-hidden p-5 sm:p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold text-[#2a2338] flex items-center gap-2">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-[#f2723b]"></i> Laporan Kerusakan
                </h2>
                <span class="text-xs text-[#7a7189] font-medium">Bulan Ini</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#ecdfd0] text-xs sm:text-sm">
                    <thead class="bg-[#fbf7f0]">
                        <tr>
                            <th class="px-4 py-3 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px] w-12">No</th>
                            <th class="px-4 py-3 text-left font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Pelapor</th>
                            <th class="px-4 py-3 text-left font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Jenis Kerusakan</th>
                            <th class="px-4 py-3 text-left font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Lokasi</th>
                            <th class="px-4 py-3 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Status</th>
                            <th class="px-4 py-3 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#ecdfd0] bg-white text-[#2a2338]">
                        @forelse ($laporanKerusakan as $item)
                        <tr class="hover:bg-[#fbf7f0]/50 transition-colors">
                            <td class="px-4 py-3 text-center font-semibold text-[#7a7189]">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 font-semibold">{{ $item->pelapor->nama ?? '-' }}</td>
                            <td class="px-4 py-3 font-medium">{{ $item->jenis_kerusakan }}</td>
                            <td class="px-4 py-3 text-[#7a7189]">{{ $item->lokasi_detail }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2.5 py-1 inline-flex text-[11px] font-bold rounded-lg
                                            {{ $item->status_laporan === 'pending' ? 'bg-amber-100 text-amber-800' : '' }}
                                            {{ $item->status_laporan === 'ditolak' ? 'bg-rose-100 text-rose-800' : '' }}
                                            {{ $item->status_laporan === 'diproses' ? 'bg-sky-100 text-sky-800' : '' }}
                                            {{ $item->status_laporan === 'selesai' ? 'bg-emerald-100 text-emerald-800' : '' }}">
                                    {{ ucfirst($item->status_laporan) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center text-[#7a7189] font-mono text-xs">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-[#7a7189] italic">Tidak ada data laporan kerusakan pada periode ini</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 2: Detail Penugasan Teknisi -->
        <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm overflow-hidden p-5 sm:p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold text-[#2a2338] flex items-center gap-2">
                    <i data-lucide="wrench" class="w-5 h-5 text-[#191333]"></i> Penugasan Teknisi
                </h2>
                <span class="text-xs text-[#7a7189] font-medium">Bulan Ini</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#ecdfd0] text-xs sm:text-sm">
                    <thead class="bg-[#fbf7f0]">
                        <tr>
                            <th class="px-4 py-3 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px] w-12">No</th>
                            <th class="px-4 py-3 text-left font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Jenis Kerusakan</th>
                            <th class="px-4 py-3 text-left font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Teknisi Bertugas</th>
                            <th class="px-4 py-3 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Jadwal Penanganan</th>
                            <th class="px-4 py-3 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Status Laporan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#ecdfd0] bg-white text-[#2a2338]">
                        @forelse ($penugasan as $item)
                        <tr class="hover:bg-[#fbf7f0]/50 transition-colors">
                            <td class="px-4 py-3 text-center font-semibold text-[#7a7189]">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 font-semibold">{{ $item->pengajuanKerusakan->jenis_kerusakan ?? '-' }}</td>
                            <td class="px-4 py-3 font-medium text-[#191333]">{{ $item->teknisi->nama ?? '-' }}</td>
                            <td class="px-4 py-3 text-center text-[#7a7189] font-mono text-xs">{{ $item->jadwal_penanganan->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2.5 py-1 inline-flex text-[11px] font-bold rounded-lg 
                                            {{ ($item->pengajuanKerusakan->status_laporan ?? 'pending') === 'pending' ? 'bg-amber-100 text-amber-800' : '' }}
                                            {{ ($item->pengajuanKerusakan->status_laporan ?? '') === 'diproses' ? 'bg-sky-100 text-sky-800' : '' }}
                                            {{ ($item->pengajuanKerusakan->status_laporan ?? '') === 'selesai' ? 'bg-emerald-100 text-emerald-800' : '' }}">
                                    {{ ucfirst($item->pengajuanKerusakan->status_laporan ?? '-') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-[#7a7189] italic">Tidak ada data penugasan teknisi pada periode ini</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 3: Detail Peminjaman Fasilitas -->
        <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm overflow-hidden p-5 sm:p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold text-[#2a2338] flex items-center gap-2">
                    <i data-lucide="box" class="w-5 h-5 text-[#f2723b]"></i> Peminjaman Fasilitas
                </h2>
                <span class="text-xs text-[#7a7189] font-medium">Bulan Ini</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#ecdfd0] text-xs sm:text-sm">
                    <thead class="bg-[#fbf7f0]">
                        <tr>
                            <th class="px-4 py-3 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px] w-12">No</th>
                            <th class="px-4 py-3 text-left font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Nama Fasilitas</th>
                            <th class="px-4 py-3 text-left font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Peminjam</th>
                            <th class="px-4 py-3 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Jumlah</th>
                            <th class="px-4 py-3 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Rentang Waktu</th>
                            <th class="px-4 py-3 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Status Peminjaman</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#ecdfd0] bg-white text-[#2a2338]">
                        @forelse ($peminjaman as $item)
                        <tr class="hover:bg-[#fbf7f0]/50 transition-colors">
                            <td class="px-4 py-3 text-center font-semibold text-[#7a7189]">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 font-semibold">
                                @if($item->items && $item->items->count() > 0)
                                    <div class="space-y-0.5">
                                        @foreach($item->items as $detailItem)
                                            <div>{{ $detailItem->fasilitas->nama_fasilitas ?? 'Fasilitas' }} <span class="text-xs text-[#7a7189] font-normal">({{ $detailItem->jumlah_dipinjam }} unit)</span></div>
                                        @endforeach
                                    </div>
                                @else
                                    {{ $item->fasilitas->nama_fasilitas ?? '-' }}
                                @endif
                            </td>
                            <td class="px-4 py-3 font-medium">{{ $item->peminjam->nama ?? '-' }}</td>
                            <td class="px-4 py-3 text-center font-mono font-bold text-[#2a2338]">
                                {{ $item->items && $item->items->isNotEmpty() ? $item->items->sum('jumlah_dipinjam') : ($item->jumlah_dipinjam ?? 1) }}
                            </td>
                            <td class="px-4 py-3 text-center text-[#7a7189] font-mono text-xs">
                                {{ $item->waktu_mulai->format('d/m/Y H:i') }}
                                <span class="text-[#f2723b] font-bold mx-1">s/d</span>
                                {{ $item->waktu_selesai->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2.5 py-1 inline-flex text-[11px] font-bold rounded-lg 
                                            {{ $item->status_pinjam === 'pending' ? 'bg-amber-100 text-amber-800' : '' }}
                                            {{ $item->status_pinjam === 'disetujui' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                            {{ $item->status_pinjam === 'ditolak' ? 'bg-rose-100 text-rose-800' : '' }}
                                            {{ $item->status_pinjam === 'berjalan' ? 'bg-indigo-100 text-indigo-800' : '' }}
                                            {{ $item->status_pinjam === 'selesai' ? 'bg-slate-100 text-slate-700' : '' }}">
                                    {{ ucfirst($item->status_pinjam) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-[#7a7189] italic">Tidak ada data peminjaman fasilitas pada periode ini</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.lucide) {
            lucide.createIcons();
        }
    });
</script>
@endpush