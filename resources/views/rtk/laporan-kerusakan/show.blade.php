@extends('layouts.rtk')

@section('title', 'Detail Laporan Kerusakan')

@section('content')
<div class="py-4 sm:py-6">
    <div class="max-w-5xl mx-auto space-y-6">

        <!-- Top Header Card -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-[#fbf7f0] border border-[#ecdfd0] rounded-xl text-[#f2723b]">
                    <i data-lucide="file-text" class="w-6 h-6"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold font-heading text-[#2a2338]">Detail Laporan Kerusakan</h1>
                    <p class="text-xs text-[#7a7189]">Laporan #{{ $laporanKerusakan->id }} &bull; Informasi detail dan penugasan teknisi</p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('rtk.laporan-kerusakan.index') }}" 
                   class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#7a7189] hover:text-[#191333] transition-colors bg-[#fbf7f0] border border-[#ecdfd0] px-3.5 py-2 rounded-xl">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
                </a>
                @if ($laporanKerusakan->status_laporan === 'pending')
                    <a href="{{ route('rtk.penugasan.create', ['pengajuan_id' => $laporanKerusakan->id]) }}" 
                       class="inline-flex items-center gap-1.5 bg-[#f2723b] hover:bg-[#e05d26] text-white text-xs font-semibold px-4 py-2 rounded-xl shadow-sm transition">
                        <i data-lucide="wrench" class="w-4 h-4"></i> Tugaskan Teknisi
                    </a>
                @endif
                <form action="{{ route('rtk.laporan-kerusakan.destroy', $laporanKerusakan->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan kerusakan ini? Tindakan ini tidak dapat dibatalkan.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold px-4 py-2 rounded-xl shadow-sm transition">
                        <i data-lucide="trash-2" class="w-4 h-4"></i> Hapus
                    </button>
                </form>
            </div>
        </div>

        <!-- Alert Flashes -->
        @if (session('success'))
        <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-medium">
            <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0"></i>
            <div>{{ session('success') }}</div>
        </div>
        @endif

        @if (session('error'))
        <div class="flex items-center gap-3 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs font-medium">
            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0"></i>
            <div>{{ session('error') }}</div>
        </div>
        @endif

        <!-- Status Badges Bar -->
        <div class="flex flex-wrap items-center gap-2 bg-white p-4 rounded-2xl border border-[#ecdfd0] shadow-sm text-xs">
            <span class="font-bold text-[#7a7189] mr-1 uppercase tracking-wider text-[10px]">Indikator:</span>
            
            <!-- Status Laporan -->
            <span class="px-3 py-1 font-bold rounded-xl border
                {{ $laporanKerusakan->status_laporan === 'pending' ? 'bg-amber-100 text-amber-800 border-amber-200' : '' }}
                {{ $laporanKerusakan->status_laporan === 'diproses' ? 'bg-sky-100 text-sky-800 border-sky-200' : '' }}
                {{ $laporanKerusakan->status_laporan === 'selesai' ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : '' }}
                {{ $laporanKerusakan->status_laporan === 'ditolak' ? 'bg-rose-100 text-rose-800 border-rose-200' : '' }}">
                Status: {{ ucfirst($laporanKerusakan->status_laporan) }}
            </span>

            <!-- Urgensi -->
            <span class="px-3 py-1 font-bold rounded-xl border
                {{ $laporanKerusakan->tingkat_urgensi === 'tinggi' ? 'bg-rose-100 text-rose-800 border-rose-200' : '' }}
                {{ $laporanKerusakan->tingkat_urgensi === 'sedang' ? 'bg-amber-100 text-amber-800 border-amber-200' : '' }}
                {{ $laporanKerusakan->tingkat_urgensi === 'rendah' ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : '' }}">
                Urgensi: {{ ucfirst($laporanKerusakan->tingkat_urgensi) }}
            </span>

            <!-- SLA Status -->
            <span class="px-3 py-1 font-bold rounded-xl border flex items-center gap-1
                {{ $laporanKerusakan->sla_status === 'on_track' ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : '' }}
                {{ $laporanKerusakan->sla_status === 'warning' ? 'bg-amber-100 text-amber-800 border-amber-200' : '' }}
                {{ $laporanKerusakan->sla_status === 'breached' ? 'bg-rose-100 text-rose-800 border-rose-200' : '' }}">
                <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                SLA: {{ $laporanKerusakan->sla_status === 'on_track' ? 'On Track' : ($laporanKerusakan->sla_status === 'warning' ? 'Warning' : 'Breached') }}
            </span>
        </div>

        <!-- Detail Information Card -->
        <div class="bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm space-y-6">
            <div class="flex items-center gap-2 pb-3 border-b border-[#ecdfd0] text-[#191333]">
                <i data-lucide="info" class="w-5 h-5 text-[#f2723b]"></i>
                <h2 class="font-bold font-heading text-sm uppercase tracking-wider">Rincian Informasi Pengajuan</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                <div class="space-y-1">
                    <span class="text-[#7a7189] font-medium block">Pelapor</span>
                    <p class="text-sm font-bold text-[#191333]">{{ $laporanKerusakan->pelapor->nama ?? 'Tidak diketahui' }}</p>
                    <p class="text-[11px] text-[#7a7189]">{{ $laporanKerusakan->pelapor->nomor_induk ?? '-' }}</p>
                </div>

                <div class="space-y-1">
                    <span class="text-[#7a7189] font-medium block">Fasilitas Terkait</span>
                    <p class="text-sm font-bold text-[#191333]">{{ $laporanKerusakan->fasilitas->nama_fasilitas ?? '-' }}</p>
                </div>

                {{-- FITUR TAMBAHAN: STATUS FASILITAS --}}
                @php $statusFasilitas = $laporanKerusakan->fasilitas->status_kondisi ?? ''; @endphp
                <div class="space-y-1">
                    <span class="text-[#7a7189] font-medium block">Status Kondisi Fasilitas</span>
                    <div class="mt-1 flex items-center gap-2 flex-wrap">
                        <span class="px-2.5 py-1 inline-flex text-xs font-semibold rounded-lg
                            {{ $statusFasilitas === 'baik' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : '' }}
                            {{ $statusFasilitas === 'rusak_ringan' ? 'bg-amber-100 text-amber-800 border border-amber-200' : '' }}
                            {{ $statusFasilitas === 'rusak_berat' ? 'bg-rose-100 text-rose-800 border border-rose-200' : '' }}
                            {{ $statusFasilitas === 'maintenance' ? 'bg-sky-100 text-sky-800 border border-sky-200' : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $statusFasilitas ?: 'Tidak diketahui')) }}
                        </span>
                        
                        @if ($statusFasilitas === 'baik' && $laporanKerusakan->status_laporan !== 'selesai')
                            <span class="text-xs text-emerald-600 font-medium flex items-center gap-1">
                                ✅ Fasilitas sudah baik, laporan akan otomatis selesai
                            </span>
                        @endif
                    </div>
                </div>

                <div class="space-y-1">
                    <span class="text-[#7a7189] font-medium block">Jenis Kerusakan</span>
                    <p class="text-sm font-bold text-[#191333]">{{ $laporanKerusakan->jenis_kerusakan }}</p>
                </div>

                <div class="space-y-1">
                    <span class="text-[#7a7189] font-medium block">Lokasi Detail</span>
                    <p class="text-sm font-bold text-[#191333]">{{ $laporanKerusakan->lokasi_detail }}</p>
                </div>

                <div class="md:col-span-2 space-y-1">
                    <span class="text-[#7a7189] font-medium block">Deskripsi Kendala</span>
                    <div class="p-4 bg-[#fbf7f0] rounded-xl border border-[#ecdfd0] text-[#2a2338] leading-relaxed">
                        {{ $laporanKerusakan->deskripsi }}
                    </div>
                </div>

                <div class="space-y-1">
                    <span class="text-[#7a7189] font-medium block">Target Selesai (SLA)</span>
                    <p class="text-sm font-bold text-[#191333]">
                        {{ $laporanKerusakan->target_selesai ? $laporanKerusakan->target_selesai->format('d/m/Y H:i') . ' WIB' : '-' }}
                    </p>
                </div>

                <div class="space-y-1">
                    <span class="text-[#7a7189] font-medium block">Tanggal Laporan Masuk</span>
                    <p class="text-sm font-bold text-[#191333]">{{ $laporanKerusakan->created_at->format('d/m/Y H:i') }} WIB</p>
                </div>

                @if ($laporanKerusakan->catatan_rtk)
                <div class="md:col-span-2 p-4 bg-sky-50 border border-sky-200 rounded-xl space-y-1">
                    <span class="text-xs font-bold text-sky-900 flex items-center gap-1">
                        <i data-lucide="message-square" class="w-4 h-4 text-sky-600"></i> Catatan RTK
                    </span>
                    <p class="text-xs text-sky-800">{{ $laporanKerusakan->catatan_rtk }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Initial Damage Photo -->
        @if ($laporanKerusakan->foto_kerusakan)
        <div class="bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm space-y-3">
            <h3 class="text-xs font-bold text-[#2a2338] uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="image" class="w-4 h-4 text-[#f2723b]"></i> Foto Bukti Kerusakan
            </h3>
            <div class="p-3 bg-[#fbf7f0] rounded-2xl border border-[#ecdfd0] inline-block">
                <img src="{{ Storage::url($laporanKerusakan->foto_kerusakan) }}" 
                     alt="Foto Kerusakan" 
                     class="rounded-xl max-h-80 object-cover shadow-sm">
            </div>
        </div>
        @endif

        <!-- Penugasan Teknisi Section -->
        @if ($laporanKerusakan->penugasanTeknisi)
        <div class="bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-[#ecdfd0] text-[#191333]">
                <i data-lucide="user-check" class="w-5 h-5 text-[#f2723b]"></i>
                <h3 class="font-bold font-heading text-sm uppercase tracking-wider">Informasi Penugasan Teknisi</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <div class="space-y-1">
                    <span class="text-[#7a7189]">Teknisi Ditugaskan:</span>
                    <p class="font-bold text-[#191333]">{{ $laporanKerusakan->penugasanTeknisi->teknisi->nama ?? 'Tidak diketahui' }}</p>
                </div>
                <div class="space-y-1">
                    <span class="text-[#7a7189]">Jadwal Penanganan:</span>
                    <p class="font-bold text-[#191333]">{{ $laporanKerusakan->penugasanTeknisi->jadwal_penanganan->format('d/m/Y H:i') }} WIB</p>
                </div>
                @if ($laporanKerusakan->penugasanTeknisi->instruksi_kerja)
                <div class="md:col-span-2 space-y-1">
                    <span class="text-[#7a7189]">Instruksi Kerja:</span>
                    <p class="p-3 bg-[#fbf7f0] rounded-xl border border-[#ecdfd0] text-[#191333] font-medium">{{ $laporanKerusakan->penugasanTeknisi->instruksi_kerja }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Bukti Perbaikan -->
        @if ($laporanKerusakan->status_laporan === 'selesai' && $laporanKerusakan->penugasanTeknisi && $laporanKerusakan->penugasanTeknisi->buktiPerbaikan)
        <div class="bg-emerald-50/50 p-6 rounded-2xl border border-emerald-200/80 shadow-sm space-y-4">
            <div class="flex items-center gap-2 text-emerald-900 pb-3 border-b border-emerald-200">
                <i data-lucide="shield-check" class="w-5 h-5 text-emerald-600"></i>
                <h3 class="font-bold font-heading text-sm uppercase tracking-wider">Bukti Perbaikan Selesai</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                <div class="space-y-3">
                    <div>
                        <span class="text-[#7a7189] font-medium block mb-1">Catatan Penyelesaian Teknisi:</span>
                        <div class="p-3 bg-white rounded-xl border border-emerald-200 text-[#191333] leading-relaxed">
                            {{ $laporanKerusakan->penugasanTeknisi->buktiPerbaikan->keterangan_selesai }}
                        </div>
                    </div>
                    <div>
                        <span class="text-[#7a7189] font-medium">Tanggal Selesai:</span>
                        <p class="font-bold text-[#191333] mt-0.5">
                            {{ \Carbon\Carbon::parse($laporanKerusakan->penugasanTeknisi->buktiPerbaikan->tanggal_selesai)->format('d/m/Y H:i') }} WIB
                        </p>
                    </div>
                </div>

                @if ($laporanKerusakan->penugasanTeknisi->buktiPerbaikan->foto_selesai)
                <div>
                    <span class="text-[#7a7189] font-medium block mb-1">Foto Bukti Perbaikan:</span>
                    <div class="p-2 bg-white rounded-xl border border-emerald-200 inline-block">
                        <img src="{{ Storage::url($laporanKerusakan->penugasanTeknisi->buktiPerbaikan->foto_selesai) }}" 
                             alt="Foto Perbaikan" 
                             class="rounded-lg max-h-52 object-cover shadow-sm">
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Timeline Status Log -->
        <div class="bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-[#2a2338] uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="history" class="w-4 h-4 text-[#f2723b]"></i> Riwayat Status Laporan
            </h3>
            <div class="pt-2">
                <x-status-timeline :logs="$laporanKerusakan->statusLogs()->with('actor')->orderBy('created_at', 'asc')->get()" />
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.lucide) { lucide.createIcons(); }
    });
</script>
@endpush