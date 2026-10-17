@extends('layouts.teknisi')

@section('title', 'Detail Fasilitas')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Navigation & Title Header -->
    <div class="flex items-center justify-between bg-white p-6 rounded-2xl shadow-sm border border-[#f5ede1]">
        <div class="flex items-center gap-3">
            <div class="p-3 rounded-xl bg-[#f2723b]/10 text-[#f2723b]">
                <i data-lucide="box" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold font-heading text-[#191333]">Detail Fasilitas</h1>
                <p class="text-xs text-[#7a7189] mt-0.5">Informasi rincian & riwayat kondisi fasilitas</p>
            </div>
        </div>
        <a href="{{ route('teknisi.dashboard') }}" 
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-[#2a2338] bg-[#f5ede1]/70 hover:bg-[#f5ede1] border border-[#f5ede1] transition-all duration-200">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali</span>
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

    <!-- Main Detail Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-[#f5ede1] p-6 space-y-6">
        <!-- Detail Grid Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-4 rounded-xl bg-[#fbf7f0]/50 border border-[#f5ede1]">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-[#7a7189] block mb-1">Nama Fasilitas</span>
                <p class="text-base font-bold text-[#191333] flex items-center gap-2">
                    <i data-lucide="package" class="w-4 h-4 text-[#f2723b]"></i>
                    {{ $fasilitas->nama_fasilitas }}
                </p>
            </div>
            <div class="p-4 rounded-xl bg-[#fbf7f0]/50 border border-[#f5ede1]">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-[#7a7189] block mb-1">Kategori</span>
                <p class="text-base font-bold text-[#191333] flex items-center gap-2">
                    <i data-lucide="tag" class="w-4 h-4 text-[#f2723b]"></i>
                    {{ ucfirst($fasilitas->kategori) }}
                </p>
            </div>
            <div class="p-4 rounded-xl bg-[#fbf7f0]/50 border border-[#f5ede1]">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-[#7a7189] block mb-1">Lokasi</span>
                <p class="text-base font-bold text-[#191333] flex items-center gap-2">
                    <i data-lucide="map-pin" class="w-4 h-4 text-[#f2723b]"></i>
                    {{ $fasilitas->lokasi_detail }}
                </p>
            </div>
            <div class="p-4 rounded-xl bg-[#fbf7f0]/50 border border-[#f5ede1]">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-[#7a7189] block mb-1">Stok Total</span>
                <p class="text-base font-bold text-[#191333] flex items-center gap-2">
                    <i data-lucide="layers" class="w-4 h-4 text-[#f2723b]"></i>
                    {{ $fasilitas->stok_total }} Unit
                </p>
            </div>
            <div class="md:col-span-2 p-4 rounded-xl bg-[#fbf7f0]/50 border border-[#f5ede1] flex items-center justify-between">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-[#7a7189]">Status Kondisi Saat Ini</span>
                @php
                    $statusClasses = [
                        'baik'         => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'rusak_ringan' => 'bg-amber-50 text-amber-700 border-amber-200',
                        'rusak_berat'  => 'bg-rose-50 text-rose-700 border-rose-200',
                        'maintenance'  => 'bg-sky-50 text-sky-700 border-sky-200',
                    ];
                    $badgeClass = $statusClasses[$fasilitas->status_kondisi] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border capitalize {{ $badgeClass }}">
                    {{ ucfirst(str_replace('_', ' ', $fasilitas->status_kondisi)) }}
                </span>
            </div>
        </div>

        <!-- ✅ UPDATE STATUS FASILITAS -->
        @if ($fasilitas->status_kondisi !== 'baik')
            <div class="p-5 rounded-2xl bg-amber-50/70 border border-amber-200/80 space-y-3">
                <div class="flex items-center gap-2 text-amber-900 font-bold text-sm">
                    <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-600 shrink-0"></i>
                    <span>Perbarui Status Fasilitas</span>
                </div>
                
                <form action="{{ route('teknisi.fasilitas.update-status', $fasilitas) }}" method="POST" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    @csrf
                    @method('PATCH')
                    
                    <select name="status_kondisi" class="rounded-xl border border-amber-200 bg-white text-xs text-[#2a2338] px-3.5 py-2.5 focus:border-[#f2723b] focus:ring-2 focus:ring-[#f2723b]/20 focus:outline-none transition-all duration-200" required>
                        <option value="">Pilih Status Baru</option>
                        <option value="baik">✅ Baik</option>
                        <option value="rusak_ringan">⚠️ Rusak Ringan</option>
                        <option value="rusak_berat">❌ Rusak Berat</option>
                        <option value="maintenance">🛠️ Maintenance</option>
                    </select>

                    <input type="text" 
                           name="catatan" 
                           placeholder="Catatan (opsional)" 
                           class="flex-1 rounded-xl border border-amber-200 bg-white text-xs text-[#2a2338] px-3.5 py-2.5 focus:border-[#f2723b] focus:ring-2 focus:ring-[#f2723b]/20 focus:outline-none transition-all duration-200">

                    <button type="submit" 
                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs font-semibold text-white shadow-sm hover:shadow transition-all duration-200 shrink-0"
                            style="background-color: var(--sunset-500);">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span>Update Status</span>
                    </button>
                </form>

                <p class="text-[11px] text-amber-700/90 flex items-center gap-1.5">
                    <i data-lucide="info" class="w-3.5 h-3.5 text-amber-600 shrink-0"></i>
                    <span>Status fasilitas ini akan diperbarui dan dicatat secara otomatis ke dalam riwayat.</span>
                </p>
            </div>
        @endif

        <!-- Riwayat Status -->
        <div class="pt-6 border-t border-[#f5ede1] space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-[#191333] flex items-center gap-2">
                <i data-lucide="history" class="w-4 h-4 text-[#f2723b]"></i>
                <span>Riwayat Status Fasilitas</span>
            </h3>
            <div class="p-4 rounded-xl bg-[#fbf7f0]/40 border border-[#f5ede1]">
                <x-status-timeline :logs="$fasilitas->statusLogs()->with('actor')->orderBy('created_at', 'asc')->get()" />
            </div>
        </div>
    </div>
</div>
@endsection