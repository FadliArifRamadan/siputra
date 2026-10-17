@extends('layouts.teknisi')

@section('title', 'Daftar Fasilitas')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-[#f5ede1]">
        <div class="flex items-center gap-3">
            <div class="p-3 rounded-xl bg-[#f2723b]/10 text-[#f2723b]">
                <i data-lucide="package" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold font-heading text-[#191333]">Daftar Fasilitas</h1>
                <p class="text-xs text-[#7a7189] mt-0.5">Kelola dan lihat seluruh daftar fasilitas & peralatan yang terdata</p>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-[#f5ede1] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-[#f5ede1] bg-[#fbf7f0]/60 text-[11px] font-bold uppercase tracking-wider text-[#7a7189]">
                        <th class="px-6 py-4">Nama Fasilitas</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Lokasi</th>
                        <th class="px-6 py-4">Stok</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#f5ede1] text-xs font-medium text-[#2a2338]">
                    @foreach ($fasilitas as $item)
                        <tr class="hover:bg-[#fbf7f0]/40 transition-colors">
                            <td class="px-6 py-4 font-semibold text-[#191333]">
                                {{ $item->nama_fasilitas }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-lg bg-[#fbf7f0] border border-[#f5ede1] text-[#2a2338]">
                                    {{ ucfirst($item->kategori) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-[#7a7189]">
                                {{ $item->lokasi_detail }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-[#191333]">
                                {{ $item->stok_total }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusClasses = [
                                        'baik'         => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
                                        'rusak_ringan' => 'bg-amber-50 text-amber-700 border-amber-200/80',
                                        'rusak_berat'  => 'bg-rose-50 text-rose-700 border-rose-200/80',
                                        'maintenance'  => 'bg-sky-50 text-sky-700 border-sky-200/80',
                                    ];
                                    $badgeClass = $statusClasses[$item->status_kondisi] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold border capitalize {{ $badgeClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $item->status_kondisi)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('teknisi.fasilitas.show', $item) }}" 
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

        @if ($fasilitas->hasPages())
            <div class="px-6 py-4 border-t border-[#f5ede1] bg-[#fbf7f0]/30">
                {{ $fasilitas->links() }}
            </div>
        @endif
    </div>
</div>
@endsection