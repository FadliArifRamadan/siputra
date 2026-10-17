@extends('layouts.rtk')

@section('title', 'Manajemen Lokasi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-[#f5ede1]">
        <div class="flex items-center gap-3">
            <div class="p-3 rounded-xl bg-[#f2723b]/10 text-[#f2723b]">
                <i data-lucide="map-pin" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold font-heading text-[#191333]">Manajemen Lokasi</h1>
                <p class="text-xs text-[#7a7189] mt-0.5">Kelola data kampus, lantai, dan ruangan</p>
            </div>
        </div>
        <a href="{{ route('rtk.lokasi.create') }}" 
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-white shadow-md hover:shadow-lg transition-all duration-200 shrink-0"
           style="background-color: var(--sunset-500);">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Tambah Lokasi</span>
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

    <!-- Tables Grouped by Kampus -->
    <div class="space-y-6">
        @foreach ($lokasi as $kampus => $items)
            <div class="bg-white rounded-2xl shadow-sm border border-[#f5ede1] overflow-hidden">
                <!-- Kampus Header -->
                <div class="px-6 py-4 bg-[#fbf7f0]/80 border-b border-[#f5ede1] flex items-center gap-2">
                    <i data-lucide="building-2" class="w-5 h-5 text-[#f2723b]"></i>
                    <h3 class="text-sm font-bold text-[#191333] tracking-wide">{{ $kampus }}</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-[#f5ede1] bg-[#fbf7f0]/30 text-[11px] font-bold uppercase tracking-wider text-[#7a7189]">
                                <th class="px-6 py-3.5">Lantai</th>
                                <th class="px-6 py-3.5">Kode Ruangan</th>
                                <th class="px-6 py-3.5">Nama Ruangan</th>
                                <th class="px-6 py-3.5">Keterangan</th>
                                <th class="px-6 py-3.5">Status</th>
                                <th class="px-6 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#f5ede1] text-xs font-medium text-[#2a2338]">
                            @forelse ($items as $item)
                                <tr class="hover:bg-[#fbf7f0]/40 transition-colors">
                                    <td class="px-6 py-4 font-semibold text-[#191333]">{{ $item->lantai }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-0.5 rounded bg-[#fbf7f0] border border-[#f5ede1] font-mono text-[11px] text-[#2a2338]">
                                            {{ $item->kode_ruangan ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-[#191333]">{{ $item->nama_ruangan }}</td>
                                    <td class="px-6 py-4 text-[#7a7189]">{{ $item->keterangan ?? '-' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold border {{ $item->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200/80' : 'bg-rose-50 text-rose-700 border-rose-200/80' }}">
                                            {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="inline-flex items-center gap-2 justify-end">
                                            <a href="{{ route('rtk.lokasi.edit', $item) }}" 
                                               class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 transition-all duration-200">
                                                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                                <span>Edit</span>
                                            </a>
                                            <form action="{{ route('rtk.lokasi.destroy', $item) }}" 
                                                  method="POST" 
                                                  class="inline-block"
                                                  onsubmit="return confirm('Yakin ingin menghapus lokasi ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition-all duration-200">
                                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                    <span>Hapus</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-[#7a7189]">
                                        <div class="flex flex-col items-center justify-center gap-1">
                                            <i data-lucide="map-pin-off" class="w-6 h-6 text-[#7a7189]/60"></i>
                                            <p class="text-xs font-medium">Belum ada lokasi di {{ $kampus }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection