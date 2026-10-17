@extends('layouts.teknisi')

@section('title', 'Riwayat Pinjaman Alat')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-[#f5ede1]">
        <div class="flex items-center gap-3">
            <div class="p-3 rounded-xl bg-[#f2723b]/10 text-[#f2723b]">
                <i data-lucide="wrench" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold font-heading text-[#191333]">Riwayat Pinjaman Alat</h1>
                <p class="text-xs text-[#7a7189] mt-0.5">Kelola dan tinjau daftar pengajuan peminjaman alat teknisi</p>
            </div>
        </div>
        <a href="{{ route('teknisi.peminjaman-alat.create') }}" 
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-white shadow-md hover:shadow-lg transition-all duration-200 shrink-0"
           style="background-color: var(--sunset-500);">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Ajukan Pinjaman Alat</span>
        </a>
    </div>

    <!-- Alert Notifications -->
    @if (session('success'))
        <div class="p-4 rounded-xl border border-emerald-200 bg-emerald-50/80 text-emerald-800 text-sm flex items-center gap-3 shadow-sm">
            <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0"></i>
            <div class="font-medium">{{ session('success') }}</div>
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 rounded-xl border border-rose-200 bg-rose-50/80 text-rose-800 text-sm flex items-center gap-3 shadow-sm">
            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0"></i>
            <div class="font-medium">{{ session('error') }}</div>
        </div>
    @endif

    <!-- Data Table / Empty State Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-[#f5ede1] overflow-hidden">
        @if ($peminjaman->isEmpty())
            <div class="text-center py-12 px-4">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-[#fbf7f0] flex items-center justify-center text-[#7a7189]">
                    <i data-lucide="inbox" class="w-8 h-8"></i>
                </div>
                <h3 class="text-base font-bold text-[#191333]">Belum ada pengajuan</h3>
                <p class="text-xs text-[#7a7189] mt-1 max-w-sm mx-auto">Anda belum pernah mengajukan pinjaman alat. Silakan buat pengajuan baru jika diperlukan.</p>
                <a href="{{ route('teknisi.peminjaman-alat.create') }}" 
                   class="inline-flex items-center gap-1.5 mt-4 text-xs font-semibold text-[#f2723b] hover:text-[#f7924f] transition-colors">
                    <span>Ajukan pinjaman pertama Anda</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-[#f5ede1] bg-[#fbf7f0]/60 text-[11px] font-bold uppercase tracking-wider text-[#7a7189]">
                            <th class="px-6 py-4">ID</th>
                            <th class="px-6 py-4">Daftar Alat</th>
                            <th class="px-6 py-4 text-center">Total Unit</th>
                            <th class="px-6 py-4">Mulai</th>
                            <th class="px-6 py-4">Selesai</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#f5ede1] text-xs font-medium text-[#2a2338]">
                        @foreach ($peminjaman as $item)
                            <tr class="hover:bg-[#fbf7f0]/40 transition-colors">
                                <td class="px-6 py-4 font-semibold text-[#191333]">#{{ $item->id }}</td>
                                <!-- ✅ SINKRONISASI DAFTAR ALAT (MULTI-ITEM) -->
                                <td class="px-6 py-4">
                                    @if($item->items && $item->items->count() > 0)
                                        <div class="font-semibold text-[#191333]">
                                            {{ $item->items->first()->fasilitas->nama_fasilitas ?? '-' }}
                                        </div>
                                        @if($item->items->count() > 1)
                                            <span class="text-[10px] text-[#7a7189]">
                                                +{{ $item->items->count() - 1 }} alat lainnya
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-[#7a7189] italic">-</span>
                                    @endif
                                </td>
                                <!-- ✅ SINKRONISASI TOTAL UNIT -->
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-[#f5ede1] text-[#2a2338] text-[11px] font-semibold">
                                        {{ $item->items->sum('jumlah_dipinjam') }} unit
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-[#7a7189]">
                                    {{ $item->waktu_mulai->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-[#7a7189]">
                                    {{ $item->waktu_selesai->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusClasses = [
                                            'pending'   => 'bg-amber-50 text-amber-700 border-amber-200/80',
                                            'disetujui' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
                                            'ditolak'   => 'bg-rose-50 text-rose-700 border-rose-200/80',
                                            'berjalan'  => 'bg-sky-50 text-sky-700 border-sky-200/80',
                                            'selesai'   => 'bg-slate-100 text-slate-700 border-slate-200',
                                        ];
                                        $badgeClass = $statusClasses[$item->status_pinjam] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold border capitalize {{ $badgeClass }}">
                                        {{ ucfirst($item->status_pinjam) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('teknisi.peminjaman-alat.show', $item) }}" 
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-[#191333] bg-[#f5ede1]/60 hover:bg-[#f5ede1] transition-all duration-200">
                                        <span>Detail</span>
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($peminjaman->hasPages())
                <div class="px-6 py-4 border-t border-[#f5ede1] bg-[#fbf7f0]/30">
                    {{ $peminjaman->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection