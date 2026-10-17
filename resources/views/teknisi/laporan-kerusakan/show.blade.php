@extends('layouts.teknisi')

@section('title', 'Detail Laporan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Navigation & Title Header -->
    <div class="flex items-center justify-between bg-white p-6 rounded-2xl shadow-sm border border-[#f5ede1]">
        <div class="flex items-center gap-3">
            <div class="p-3 rounded-xl bg-[#f2723b]/10 text-[#f2723b]">
                <i data-lucide="file-text" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold font-heading text-[#191333]">Detail Laporan Kerusakan</h1>
                <p class="text-xs text-[#7a7189] mt-0.5">Rincian informasi laporan kerusakan #{{ $pengajuan->id }}</p>
            </div>
        </div>
        <a href="{{ route('teknisi.laporan-kerusakan.index') }}" 
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-[#2a2338] bg-[#f5ede1]/70 hover:bg-[#f5ede1] border border-[#f5ede1] transition-all duration-200">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali</span>
        </a>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-[#f5ede1] p-6 space-y-6">
        <!-- Status Banner -->
        <div class="flex flex-wrap items-center justify-between gap-3 p-4 rounded-xl bg-[#fbf7f0] border border-[#f5ede1]">
            <span class="text-xs font-medium text-[#7a7189]">Status Laporan Saat Ini</span>
            
            <div class="flex items-center gap-2 flex-wrap">
                @php
                    $statusClasses = [
                        'pending'        => 'bg-amber-50 text-amber-700 border-amber-200',
                        'diterima'       => 'bg-sky-50 text-sky-700 border-sky-200',
                        'dikerjakan'     => 'bg-purple-50 text-purple-700 border-purple-200',
                        'menunggu_parts' => 'bg-orange-50 text-orange-700 border-orange-200',
                        'diproses'       => 'bg-sky-50 text-sky-700 border-sky-200',
                        'selesai'        => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    ];
                    $badgeClass = $statusClasses[$pengajuan->status_laporan] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border capitalize {{ $badgeClass }}">
                    Status: {{ ucfirst(str_replace('_', ' ', $pengajuan->status_laporan)) }}
                </span>

                {{-- Tampilkan info fasilitas (Penyesuaian dari Code 2) --}}
                @if ($pengajuan->fasilitas)
                    @php
                        $fasilitasClasses = [
                            'baik'         => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'rusak_ringan' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'rusak_berat'  => 'bg-rose-50 text-rose-700 border-rose-200',
                            'maintenance'  => 'bg-sky-50 text-sky-700 border-sky-200',
                        ];
                        $fasilitasBadge = $fasilitasClasses[$pengajuan->fasilitas->status_kondisi] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border capitalize {{ $fasilitasBadge }}">
                        Fasilitas: {{ ucfirst(str_replace('_', ' ', $pengajuan->fasilitas->status_kondisi)) }}
                    </span>
                @endif
            </div>
        </div>

        <!-- Detail Grid Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="p-4 rounded-xl bg-white border border-[#f5ede1]">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-[#7a7189] block mb-1">Pelapor</span>
                <p class="text-sm font-bold text-[#191333] flex items-center gap-1.5">
                    <i data-lucide="user" class="w-4 h-4 text-[#f2723b]"></i>
                    {{ $pengajuan->pelapor->nama ?? 'Tidak diketahui' }}
                </p>
            </div>
            <div class="p-4 rounded-xl bg-white border border-[#f5ede1]">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-[#7a7189] block mb-1">Fasilitas</span>
                <p class="text-sm font-bold text-[#191333] flex items-center gap-1.5">
                    <i data-lucide="box" class="w-4 h-4 text-[#f2723b]"></i>
                    {{ $pengajuan->fasilitas->nama_fasilitas ?? '-' }}
                </p>
            </div>
            <div class="p-4 rounded-xl bg-white border border-[#f5ede1]">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-[#7a7189] block mb-1">Jenis Kerusakan</span>
                <p class="text-sm font-semibold text-[#2a2338] flex items-center gap-1.5">
                    <i data-lucide="alert-triangle" class="w-4 h-4 text-[#f2723b]"></i>
                    {{ $pengajuan->jenis_kerusakan }}
                </p>
            </div>
            <div class="p-4 rounded-xl bg-white border border-[#f5ede1]">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-[#7a7189] block mb-1">Lokasi</span>
                <p class="text-sm font-semibold text-[#2a2338] flex items-center gap-1.5">
                    <i data-lucide="map-pin" class="w-4 h-4 text-[#f2723b]"></i>
                    {{ $pengajuan->lokasi_detail }}
                </p>
            </div>
            <div class="md:col-span-2 p-4 rounded-xl bg-[#fbf7f0]/60 border border-[#f5ede1]">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-[#7a7189] block mb-1">Deskripsi</span>
                <p class="text-xs leading-relaxed text-[#2a2338] whitespace-pre-line">{{ $pengajuan->deskripsi }}</p>
            </div>

            @if ($pengajuan->foto_kerusakan)
                <div class="md:col-span-2 p-4 rounded-xl bg-white border border-[#f5ede1] space-y-2">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-[#7a7189] block">Foto Kerusakan</span>
                    <div class="relative overflow-hidden rounded-xl border border-[#f5ede1] bg-[#fbf7f0] max-w-md">
                        <img src="{{ Storage::url($pengajuan->foto_kerusakan) }}" 
                             alt="Foto Kerusakan" 
                             class="w-full object-cover max-h-72 hover:scale-105 transition-transform duration-300">
                    </div>
                </div>
            @endif
        </div>

        <!-- Action / Status Message Section (Penyesuaian dari Code 2) -->
        <div class="pt-6 border-t border-[#f5ede1]">
            @if ($pengajuan->status_laporan !== 'selesai')
                <div class="flex justify-end">
                    <a href="{{ route('teknisi.tugas.show', $pengajuan->penugasanTeknisi->id ?? 0) }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-semibold text-white shadow-md hover:shadow-lg transition-all duration-200"
                       style="background-color: var(--sunset-500, #f2723b);">
                        <i data-lucide="arrow-right-circle" class="w-4 h-4"></i>
                        <span>Lihat Tugas & Update Progress</span>
                    </a>
                </div>
            @else
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center gap-2">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                    <span>Laporan ini sudah selesai. Tidak ada tindakan lebih lanjut yang diperlukan.</span>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection