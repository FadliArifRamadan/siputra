@extends('layouts.rtk')

@section('title', 'Riwayat Status Fasilitas')

@section('content')
<div class="py-4 sm:py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Header Banner -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold font-heading text-[#2a2338] flex items-center gap-2">
                    <i data-lucide="history" class="w-6 h-6 text-[#f2723b]"></i> Riwayat Status Fasilitas
                </h1>
                <p class="text-xs sm:text-sm text-[#7a7189] mt-1">
                    Jejak rekam kronologis perubahan kondisi dan penanganan fasilitas.
                </p>
            </div>
            <a href="{{ route('rtk.fasilitas.show', $fasilitas) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold bg-[#fbf7f0] text-[#2a2338] border border-[#ecdfd0] hover:bg-white hover:border-[#f2723b] transition-all shrink-0">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Detail
            </a>
        </div>

        <!-- Info Fasilitas Ringkas -->
        <div class="bg-white p-5 rounded-2xl border border-[#ecdfd0] shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-[#fbf7f0] rounded-xl border border-[#ecdfd0]/80">
                    <i data-lucide="box" class="w-6 h-6 text-[#191333]"></i>
                </div>
                <div>
                    <h3 class="font-bold text-sm sm:text-base font-heading text-[#2a2338]">{{ $fasilitas->nama_fasilitas }}</h3>
                    <p class="text-xs text-[#7a7189] mt-0.5">Lokasi: {{ $fasilitas->lokasi_display }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2 bg-[#fbf7f0] px-3.5 py-2 rounded-xl border border-[#ecdfd0]">
                <span class="text-xs text-[#7a7189] font-medium">Status Saat Ini:</span>
                @php
                    $badgeClass = match($fasilitas->status_kondisi) {
                        'baik' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                        'rusak_ringan' => 'bg-amber-100 text-amber-800 border-amber-300',
                        'rusak_berat' => 'bg-rose-100 text-rose-800 border-rose-300',
                        'maintenance' => 'bg-blue-100 text-blue-800 border-blue-300',
                        default => 'bg-gray-100 text-gray-800 border-gray-300'
                    };
                @endphp
                <span class="px-2.5 py-0.5 text-xs font-bold rounded-full border {{ $badgeClass }}">
                    {{ ucfirst(str_replace('_', ' ', $fasilitas->status_kondisi)) }}
                </span>
            </div>
        </div>

        <!-- Timeline Section Card -->
        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-[#ecdfd0] shadow-sm">
            <h3 class="text-xs font-bold font-heading text-[#2a2338] uppercase tracking-wider mb-6 flex items-center gap-2 pb-3 border-b border-[#ecdfd0]">
                <i data-lucide="clock" class="w-4 h-4 text-[#f2723b]"></i> Timeline Perubahan
            </h3>

            <x-status-timeline :logs="$fasilitas->statusLogs()->with('actor')->orderBy('created_at', 'asc')->get()" />
        </div>

    </div>
</div>
@endsection