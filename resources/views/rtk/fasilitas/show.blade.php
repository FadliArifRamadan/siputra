@extends('layouts.rtk')

@section('title', 'Detail Fasilitas')

@section('content')
<div class="py-4 sm:py-6">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Header Banner -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold font-heading text-[#2a2338] flex items-center gap-2">
                    <i data-lucide="file-text" class="w-6 h-6 text-[#f2723b]"></i> Detail Fasilitas
                </h1>
                <p class="text-xs sm:text-sm text-[#7a7189] mt-1">
                    Informasi lengkap, tautan QR Code, dan riwayat kondisi fasilitas.
                </p>
            </div>
            <a href="{{ route('rtk.fasilitas.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold bg-[#fbf7f0] text-[#2a2338] border border-[#ecdfd0] hover:bg-white hover:border-[#f2723b] transition-all shrink-0">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Daftar
            </a>
        </div>

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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Left Side: Informasi Utama Fasilitas -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm">
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-[#ecdfd0]">
                        <h3 class="font-bold text-sm font-heading text-[#2a2338] uppercase tracking-wider flex items-center gap-2">
                            <i data-lucide="info" class="w-4 h-4 text-[#f2723b]"></i> Informasi Fasilitas
                        </h3>
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border 
                            {{ $fasilitas->kategori === 'sarana' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200' }}">
                            {{ ucfirst($fasilitas->kategori) }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs sm:text-sm">
                        <div class="bg-[#fbf7f0]/60 p-3.5 rounded-xl border border-[#ecdfd0]/60">
                            <span class="text-[11px] font-medium text-[#7a7189] block">Nama Fasilitas</span>
                            <span class="font-bold text-[#2a2338] text-sm mt-0.5 block">{{ $fasilitas->nama_fasilitas }}</span>
                        </div>

                        <div class="bg-[#fbf7f0]/60 p-3.5 rounded-xl border border-[#ecdfd0]/60">
                            <span class="text-[11px] font-medium text-[#7a7189] block">Lokasi Detail</span>
                            <span class="font-semibold text-[#2a2338] mt-0.5 block">{{ $fasilitas->lokasi_display }}</span>
                        </div>

                        <div class="bg-[#fbf7f0]/60 p-3.5 rounded-xl border border-[#ecdfd0]/60">
                            <span class="text-[11px] font-medium text-[#7a7189] block">Stok Total</span>
                            <span class="font-semibold text-[#2a2338] mt-0.5 block">{{ $fasilitas->stok_total }} Unit</span>
                        </div>

                        <div class="bg-[#fbf7f0]/60 p-3.5 rounded-xl border border-[#ecdfd0]/60">
                            <span class="text-[11px] font-medium text-[#7a7189] block mb-1">Status Kondisi</span>
                            @php
                                $badgeClass = match($fasilitas->status_kondisi) {
                                    'baik' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                    'rusak_ringan' => 'bg-amber-100 text-amber-800 border-amber-300',
                                    'rusak_berat' => 'bg-rose-100 text-rose-800 border-rose-300',
                                    'maintenance' => 'bg-blue-100 text-blue-800 border-blue-300',
                                    default => 'bg-gray-100 text-gray-800 border-gray-300'
                                };
                            @endphp
                            <span class="px-2.5 py-0.5 inline-flex text-xs font-bold rounded-full border {{ $badgeClass }}">
                                {{ ucfirst(str_replace('_', ' ', $fasilitas->status_kondisi)) }}
                            </span>
                        </div>

                        <div class="bg-[#fbf7f0]/60 p-3.5 rounded-xl border border-[#ecdfd0]/60">
                            <span class="text-[11px] font-medium text-[#7a7189] block">Tanggal Dibuat</span>
                            <span class="font-medium text-[#2a2338] mt-0.5 block">{{ $fasilitas->created_at->format('d/m/Y H:i') }}</span>
                        </div>

                        <div class="bg-[#fbf7f0]/60 p-3.5 rounded-xl border border-[#ecdfd0]/60">
                            <span class="text-[11px] font-medium text-[#7a7189] block">Terakhir Diperbarui</span>
                            <span class="font-medium text-[#2a2338] mt-0.5 block">{{ $fasilitas->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-6 pt-4 border-t border-[#ecdfd0] flex flex-wrap gap-2 justify-end">
                        <a href="{{ route('rtk.fasilitas.edit', $fasilitas) }}" 
                           class="bg-[#191333] hover:bg-[#2f2559] text-white px-4 py-2 rounded-xl text-xs font-semibold transition-colors flex items-center gap-1.5 shadow-sm">
                            <i data-lucide="edit-2" class="w-3.5 h-3.5 text-[#fbb35f]"></i> Edit Fasilitas
                        </a>
                        <form action="{{ route('rtk.fasilitas.destroy', $fasilitas) }}" 
                              method="POST" class="inline-block"
                              onsubmit="return confirm('Yakin ingin menghapus fasilitas ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-xl text-xs font-semibold transition-colors flex items-center gap-1.5 shadow-sm">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Riwayat Status Section -->
                <div class="bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm">
                    <h3 class="text-sm font-bold font-heading text-[#2a2338] mb-4 uppercase tracking-wider flex items-center gap-2 pb-3 border-b border-[#ecdfd0]">
                        <i data-lucide="history" class="w-4 h-4 text-[#f2723b]"></i> Riwayat Status Fasilitas
                    </h3>
                    <x-status-timeline :logs="$fasilitas->statusLogs()->with('actor')->orderBy('created_at', 'asc')->get()" />
                </div>
            </div>

            <!-- Right Side: QR Code Section -->
            <div class="lg:col-span-1">
                <div class="bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm text-center sticky top-6">
                    <h3 class="font-bold text-sm font-heading text-[#2a2338] mb-4 uppercase tracking-wider flex items-center justify-center gap-2 pb-3 border-b border-[#ecdfd0]">
                        <i data-lucide="qr-code" class="w-4 h-4 text-[#f2723b]"></i> QR Code Fasilitas
                    </h3>
                    
                    @if ($fasilitas->qrCodePath)
                        <div class="bg-[#fbf7f0] p-4 rounded-xl border border-[#ecdfd0] inline-block shadow-inner w-full">
                            <img src="{{ $fasilitas->qrCodePath }}" 
                                 alt="QR Code {{ $fasilitas->nama_fasilitas }}"
                                 class="mx-auto rounded-lg max-w-[180px] bg-white p-2 border border-[#ecdfd0]">
                            <p class="text-xs font-bold text-[#2a2338] mt-3 line-clamp-1">{{ $fasilitas->nama_fasilitas }}</p>
                        </div>
                        <div class="mt-4 flex flex-col gap-2">
                            <a href="{{ route('rtk.fasilitas.download-qr', $fasilitas) }}" 
                               class="bg-[#191333] hover:bg-[#2f2559] text-white px-4 py-2.5 rounded-xl transition-all text-xs font-semibold flex items-center justify-center gap-2 shadow-sm">
                                <i data-lucide="download" class="w-3.5 h-3.5 text-[#fbb35f]"></i> Download QR Code
                            </a>
                            <form action="{{ route('rtk.fasilitas.regenerate-qr', $fasilitas) }}" method="POST" class="w-full">
                                @csrf
                                <button type="submit" class="w-full bg-white border border-[#ecdfd0] text-[#2a2338] hover:bg-[#fbf7f0] hover:border-[#f2723b] px-4 py-2 rounded-xl transition-all text-xs font-semibold flex items-center justify-center gap-2">
                                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-[#f2723b]"></i> Regenerate
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="bg-[#fbf7f0] p-8 rounded-xl border border-dashed border-[#ecdfd0] flex flex-col items-center justify-center">
                            <i data-lucide="qr-code" class="w-12 h-12 text-[#7a7189]/40 mb-2"></i>
                            <p class="text-xs font-medium text-[#7a7189]">Belum ada QR Code disetting</p>
                        </div>
                        <form action="{{ route('rtk.fasilitas.generate-qr', $fasilitas) }}" method="POST" class="mt-4">
                            @csrf
                            <button type="submit" class="w-full bg-[#f2723b] hover:bg-[#e05d25] text-white px-4 py-2.5 rounded-xl transition-all text-xs font-bold flex items-center justify-center gap-2 shadow-sm">
                                <i data-lucide="qr-code" class="w-4 h-4"></i> Generate QR Code
                            </button>
                        </form>
                    @endif
                    
                    <div class="mt-5 text-[11px] text-[#7a7189] border-t border-[#ecdfd0] pt-4 space-y-1 text-left bg-[#fbf7f0]/40 p-3 rounded-xl">
                        <p class="font-semibold text-[#2a2338]">💡 Petunjuk:</p>
                        <p>Scan QR Code untuk langsung menuju formulir pelaporan kerusakan fasilitas ini.</p>
                        <p class="text-[10px] text-[#7a7189] truncate mt-1">
                            <span class="font-mono bg-white px-1 py-0.5 rounded border border-[#ecdfd0]">{{ route('pelapor.pengajuan.create') }}?fasilitas_id={{ $fasilitas->id }}</span>
                        </p>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection