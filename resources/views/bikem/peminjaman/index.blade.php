@extends('layouts.bikem')

@section('title', 'Daftar Peminjaman Mahasiswa')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm">
        <div class="flex items-center gap-3">
            <div class="p-3 bg-[#fbf7f0] border border-[#ecdfd0] rounded-xl text-[#f2723b]">
                <i data-lucide="calendar-check" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold font-heading text-[#2a2338]">Peminjaman Fasilitas Mahasiswa</h1>
                <p class="text-xs sm:text-sm text-[#7a7189] mt-0.5">Tinjau dan verifikasi pengajuan izin fasilitas untuk kegiatan kemahasiswaan</p>
            </div>
        </div>
    </div>

    <!-- Alert Flash Message -->
    @if (session('success'))
    <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-r-xl shadow-sm flex items-center gap-3">
        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500 shrink-0"></i>
        <span class="text-xs sm:text-sm font-medium">{{ session('success') }}</span>
    </div>
    @endif

    @if (session('error'))
    <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-800 p-4 rounded-r-xl shadow-sm flex items-center gap-3">
        <i data-lucide="alert-circle" class="w-5 h-5 text-rose-500 shrink-0"></i>
        <span class="text-xs sm:text-sm font-medium">{{ session('error') }}</span>
    </div>
    @endif

    <!-- Card Container -->
    <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm overflow-hidden p-6 space-y-6">

        <!-- Status Filter Tabs -->
        <div class="flex flex-wrap items-center gap-2 pb-4 border-b border-[#ecdfd0]">
            <a href="{{ route('bikem.peminjaman.index', ['status' => 'menunggu_bikem', 'search' => $search]) }}"
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $status === 'menunggu_bikem' ? 'bg-amber-100 text-amber-900 border border-amber-300 shadow-sm' : 'bg-[#fbf7f0] text-[#7a7189] hover:text-[#191333]' }}">
                <i data-lucide="clock" class="w-3.5 h-3.5"></i> Menunggu BIKEM
                <span class="px-1.5 py-0.5 text-[10px] rounded-full bg-amber-200 text-amber-900">{{ $counts['menunggu_bikem'] }}</span>
            </a>

            <a href="{{ route('bikem.peminjaman.index', ['status' => 'perlu_revisi', 'search' => $search]) }}"
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $status === 'perlu_revisi' ? 'bg-orange-100 text-orange-900 border border-orange-300 shadow-sm' : 'bg-[#fbf7f0] text-[#7a7189] hover:text-[#191333]' }}">
                <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i> Perlu Revisi
                <span class="px-1.5 py-0.5 text-[10px] rounded-full bg-orange-200 text-orange-900">{{ $counts['perlu_revisi'] }}</span>
            </a>

            <a href="{{ route('bikem.peminjaman.index', ['status' => 'menunggu_rtk', 'search' => $search]) }}"
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $status === 'menunggu_rtk' ? 'bg-blue-100 text-blue-900 border border-blue-300 shadow-sm' : 'bg-[#fbf7f0] text-[#7a7189] hover:text-[#191333]' }}">
                <i data-lucide="send" class="w-3.5 h-3.5"></i> Menunggu RTK
                <span class="px-1.5 py-0.5 text-[10px] rounded-full bg-blue-200 text-blue-900">{{ $counts['menunggu_rtk'] }}</span>
            </a>

            <a href="{{ route('bikem.peminjaman.index', ['status' => 'disetujui', 'search' => $search]) }}"
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $status === 'disetujui' ? 'bg-emerald-100 text-emerald-900 border border-emerald-300 shadow-sm' : 'bg-[#fbf7f0] text-[#7a7189] hover:text-[#191333]' }}">
                <i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i> Disetujui
                <span class="px-1.5 py-0.5 text-[10px] rounded-full bg-emerald-200 text-emerald-900">{{ $counts['disetujui'] }}</span>
            </a>

            <a href="{{ route('bikem.peminjaman.index', ['status' => 'ditolak', 'search' => $search]) }}"
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $status === 'ditolak' ? 'bg-rose-100 text-rose-900 border border-rose-300 shadow-sm' : 'bg-[#fbf7f0] text-[#7a7189] hover:text-[#191333]' }}">
                <i data-lucide="x-circle" class="w-3.5 h-3.5"></i> Ditolak
                <span class="px-1.5 py-0.5 text-[10px] rounded-full bg-rose-200 text-rose-900">{{ $counts['ditolak'] }}</span>
            </a>

            <a href="{{ route('bikem.peminjaman.index', ['status' => 'all', 'search' => $search]) }}"
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $status === 'all' ? 'bg-[#191333] text-white shadow-sm' : 'bg-[#fbf7f0] text-[#7a7189] hover:text-[#191333]' }}">
                Semua ({{ $counts['all'] }})
            </a>
        </div>

        <!-- Search Bar -->
        <form method="GET" action="{{ route('bikem.peminjaman.index') }}" class="flex items-center gap-3">
            <input type="hidden" name="status" value="{{ $status }}">
            <div class="relative flex-1">
                <i data-lucide="search" class="w-4 h-4 text-[#7a7189] absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                <input type="text" name="search" value="{{ $search }}"
                    placeholder="Cari berdasarkan nama pemohon, NIM, atau keperluan kegiatan..."
                    class="w-full bg-[#fbf7f0] text-xs sm:text-sm pl-10 pr-4 py-2.5 rounded-xl border border-[#ecdfd0] focus:ring-2 focus:ring-[#f2723b] transition">
            </div>
            <button type="submit" class="px-4 py-2.5 bg-[#191333] hover:bg-[#2f2559] text-white text-xs font-bold rounded-xl shadow transition">
                Cari
            </button>
            @if ($search)
            <a href="{{ route('bikem.peminjaman.index', ['status' => $status]) }}" class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-[#2a2338] text-xs font-semibold rounded-xl transition">
                Reset
            </a>
            @endif
        </form>

        <!-- Data Table -->
        <div class="overflow-x-auto rounded-xl border border-[#ecdfd0]">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#fbf7f0] text-[#2a2338] font-bold border-b border-[#ecdfd0]">
                    <tr>
                        <th class="p-4 w-12 text-center">No</th>
                        <th class="p-4">Pemohon</th>
                        <th class="p-4">Kegiatan / Keperluan</th>
                        <th class="p-4">Fasilitas (Unit)</th>
                        <th class="p-4">Jadwal Penggunaan</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 text-center">Berkas</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#ecdfd0]">
                    @forelse ($peminjaman as $index => $item)
                    <tr class="hover:bg-[#fbf7f0]/50 transition">
                        <td class="p-4 text-center text-[#7a7189] font-medium">
                            {{ $peminjaman->firstItem() + $index }}
                        </td>
                        <td class="p-4">
                            <div class="font-bold text-[#191333]">{{ $item->peminjam->nama ?? 'Mahasiswa' }}</div>
                            <span class="text-[11px] text-[#7a7189]">{{ $item->peminjam->nomor_induk ?? '-' }}</span>
                        </td>
                        <td class="p-4 max-w-xs">
                            <div class="font-semibold text-[#2a2338] line-clamp-1">{{ $item->keperluan }}</div>
                            <span class="text-[10px] text-[#7a7189]">Diajukan: {{ $item->created_at->format('d/m/Y H:i') }}</span>
                        </td>
                        <td class="p-4">
                            @if ($item->items && $item->items->count() > 0)
                                <div class="font-medium text-[#191333]">{{ $item->items->first()->fasilitas->nama_fasilitas ?? 'Fasilitas' }}</div>
                                @if ($item->items->count() > 1)
                                    <span class="text-[10px] text-[#f2723b] font-medium">+ {{ $item->items->count() - 1 }} lainnya (Total {{ $item->items->sum('jumlah_dipinjam') }} unit)</span>
                                @else
                                    <span class="text-[10px] text-[#7a7189]">({{ $item->items->first()->jumlah_dipinjam }} unit)</span>
                                @endif
                            @else
                                <span class="text-gray-400 italic">Tidak ada item</span>
                            @endif
                        </td>
                        <td class="p-4 leading-tight">
                            <span class="font-bold text-[#191333] block">{{ $item->waktu_mulai->format('d/m/Y H:i') }}</span>
                            <span class="text-[10px] text-[#7a7189]">s/d {{ $item->waktu_selesai->format('d/m/Y H:i') }}</span>
                        </td>
                        <td class="p-4 text-center">
                            @php
                                $badge = match($item->status_pinjam) {
                                    'pending', 'menunggu_bikem' => ['class' => 'bg-amber-100 text-amber-800 border-amber-200', 'label' => 'Menunggu BIKEM'],
                                    'perlu_revisi' => ['class' => 'bg-orange-100 text-orange-800 border-orange-200 animate-pulse', 'label' => 'Perlu Revisi'],
                                    'menunggu_rtk' => ['class' => 'bg-blue-100 text-blue-800 border-blue-200', 'label' => 'Menunggu RTK'],
                                    'disetujui' => ['class' => 'bg-emerald-100 text-emerald-800 border-emerald-200', 'label' => 'Disetujui'],
                                    'ditolak' => ['class' => 'bg-rose-100 text-rose-800 border-rose-200', 'label' => 'Ditolak'],
                                    'berjalan' => ['class' => 'bg-sky-100 text-sky-800 border-sky-200', 'label' => 'Berjalan'],
                                    'selesai' => ['class' => 'bg-gray-100 text-gray-700 border-gray-200', 'label' => 'Selesai'],
                                    default => ['class' => 'bg-gray-100 text-gray-700 border-gray-200', 'label' => ucfirst($item->status_pinjam)],
                                };
                            @endphp
                            <span class="px-2.5 py-1 inline-flex text-[10px] font-bold rounded-xl border {{ $badge['class'] }}">
                                {{ $badge['label'] }}
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            @if ($item->file_pendukung)
                            <a href="{{ asset('storage/' . $item->file_pendukung) }}" target="_blank"
                                class="inline-flex items-center gap-1 text-[#f2723b] hover:text-[#d95d28] font-bold text-xs" title="Lihat Berkas">
                                <i data-lucide="file-text" class="w-4 h-4"></i> 📎
                            </a>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            @if (in_array($item->status_pinjam, ['pending', 'menunggu_bikem']))
                            <a href="{{ route('bikem.peminjaman.show', $item) }}"
                                class="inline-flex items-center gap-1 text-xs font-bold text-white bg-[#f2723b] hover:bg-[#d95d28] px-3.5 py-1.5 rounded-xl transition shadow-sm">
                                <i data-lucide="clipboard-check" class="w-3.5 h-3.5"></i> Verifikasi
                            </a>
                            @else
                            <a href="{{ route('bikem.peminjaman.show', $item) }}"
                                class="inline-flex items-center gap-1 text-xs font-bold text-white bg-[#191333] hover:bg-[#2f2559] px-3.5 py-1.5 rounded-xl transition shadow-sm">
                                <i data-lucide="eye" class="w-3.5 h-3.5"></i> Detail
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center text-xs text-[#7a7189]">
                            <i data-lucide="inbox" class="w-8 h-8 text-gray-300 mx-auto mb-2"></i>
                            Tidak ada pengajuan peminjaman dalam kategori ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pt-4">
            {{ $peminjaman->links() }}
        </div>

    </div>

</div>
@endsection
