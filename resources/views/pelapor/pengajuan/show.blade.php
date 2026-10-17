@extends(auth()->check() && auth()->user()->hasRole('bikem') ? 'layouts.bikem' : 'layouts.pelapor')

@section('title', 'Detail Laporan Kerusakan')

@section('content')
<div class="py-4 sm:py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Top Bar Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-[#fbf7f0] border border-[#ecdfd0] rounded-xl text-[#f2723b]">
                    <i data-lucide="file-badge" class="w-6 h-6"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold font-heading text-[#2a2338]">Detail Laporan Kerusakan</h1>
                    <p class="text-xs text-[#7a7189] mt-0.5">Laporan dibuat pada {{ $pengajuan->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
            <a href="{{ route('pelapor.pengajuan.index') }}" 
               class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#7a7189] hover:text-[#191333] transition-colors bg-[#fbf7f0] border border-[#ecdfd0] px-3.5 py-2 rounded-xl shrink-0">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
            </a>
        </div>

        <!-- Main Card Information -->
        <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm p-6 sm:p-8 space-y-6">

            <!-- Badges Summary Row -->
            <div class="flex flex-wrap items-center gap-2 pb-4 border-b border-[#ecdfd0]">
                <span class="text-xs font-bold text-[#7a7189] uppercase tracking-wider mr-2">Status Laporan:</span>
                <span class="px-3 py-1 inline-flex text-xs font-bold rounded-xl
                    {{ $pengajuan->status_laporan === 'pending' ? 'bg-amber-100 text-amber-800 border border-amber-200' : '' }}
                    {{ $pengajuan->status_laporan === 'ditolak' ? 'bg-rose-100 text-rose-800 border border-rose-200' : '' }}
                    {{ $pengajuan->status_laporan === 'diproses' ? 'bg-sky-100 text-sky-800 border border-sky-200' : '' }}
                    {{ $pengajuan->status_laporan === 'selesai' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : '' }}">
                    {{ ucfirst($pengajuan->status_laporan) }}
                </span>

                <span class="px-3 py-1 inline-flex text-xs font-bold rounded-xl
                    {{ $pengajuan->tingkat_urgensi === 'tinggi' ? 'bg-rose-100 text-rose-800' : '' }}
                    {{ $pengajuan->tingkat_urgensi === 'sedang' ? 'bg-amber-100 text-amber-800' : '' }}
                    {{ $pengajuan->tingkat_urgensi === 'rendah' ? 'bg-emerald-100 text-emerald-800' : '' }}">
                    Urgensi: {{ ucfirst($pengajuan->tingkat_urgensi) }}
                </span>
            </div>

            <!-- Detail Grid Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-[#fbf7f0] p-4 rounded-xl border border-[#ecdfd0] space-y-1">
                    <span class="text-[11px] font-bold text-[#7a7189] uppercase tracking-wider">Jenis Kerusakan</span>
                    <p class="text-sm font-bold text-[#191333]">{{ $pengajuan->jenis_kerusakan }}</p>
                </div>

                <div class="bg-[#fbf7f0] p-4 rounded-xl border border-[#ecdfd0] space-y-1">
                    <span class="text-[11px] font-bold text-[#7a7189] uppercase tracking-wider">Fasilitas Terkait</span>
                    <p class="text-sm font-bold text-[#191333]">
                        @if ($pengajuan->fasilitas)
                            {{ $pengajuan->fasilitas->nama_fasilitas }}
                            <span class="text-xs font-normal text-[#7a7189]">({{ ucfirst($pengajuan->fasilitas->kategori) }})</span>
                        @else
                            <span class="text-[#7a7189] italic font-normal">Tidak terdaftar</span>
                        @endif
                    </p>
                </div>

                <div class="bg-[#fbf7f0] p-4 rounded-xl border border-[#ecdfd0] space-y-1">
                    <span class="text-[11px] font-bold text-[#7a7189] uppercase tracking-wider">Lokasi Detail</span>
                    <p class="text-sm font-bold text-[#191333]">{{ $pengajuan->lokasi_detail }}</p>
                </div>

                @if ($pengajuan->fasilitas)
                <div class="bg-[#fbf7f0] p-4 rounded-xl border border-[#ecdfd0] space-y-1">
                    <span class="text-[11px] font-bold text-[#7a7189] uppercase tracking-wider">Status Fasilitas</span>
                    <div>
                        <span class="px-2.5 py-0.5 inline-flex text-xs font-bold rounded-lg
                            {{ $pengajuan->fasilitas->status_kondisi === 'baik' ? 'bg-emerald-100 text-emerald-800' : '' }}
                            {{ $pengajuan->fasilitas->status_kondisi === 'rusak_ringan' ? 'bg-amber-100 text-amber-800' : '' }}
                            {{ $pengajuan->fasilitas->status_kondisi === 'rusak_berat' ? 'bg-rose-100 text-rose-800' : '' }}
                            {{ $pengajuan->fasilitas->status_kondisi === 'maintenance' ? 'bg-sky-100 text-sky-800' : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $pengajuan->fasilitas->status_kondisi)) }}
                        </span>
                    </div>
                </div>
                @endif
            </div>

            <!-- Deskripsi Detail -->
            <div class="space-y-2">
                <h3 class="text-xs font-bold text-[#2a2338] uppercase tracking-wider">Deskripsi Laporan</h3>
                <div class="p-4 rounded-xl bg-[#fbf7f0] border border-[#ecdfd0] text-xs sm:text-sm text-[#2a2338] leading-relaxed whitespace-pre-line">
                    {{ $pengajuan->deskripsi }}
                </div>
            </div>

            <!-- Foto Kerusakan -->
            <div class="space-y-2">
                <h3 class="text-xs font-bold text-[#2a2338] uppercase tracking-wider">Foto Kerusakan</h3>
                @if ($pengajuan->foto_kerusakan)
                    <div class="rounded-2xl border border-[#ecdfd0] overflow-hidden max-w-lg bg-[#fbf7f0]">
                        <img src="{{ Storage::url($pengajuan->foto_kerusakan) }}" alt="Foto Kerusakan" class="w-full h-auto object-cover max-h-96">
                    </div>
                @else
                    <p class="text-xs text-[#7a7189] italic">Tidak ada foto yang dilampirkan.</p>
                @endif
            </div>

            <!-- Catatan RTK (If Exists) -->
            @if ($pengajuan->catatan_rtk)
            <div class="p-4 rounded-2xl bg-sky-50 border-l-4 border-sky-500 space-y-1">
                <h3 class="text-xs font-bold text-sky-900 flex items-center gap-2">
                    <i data-lucide="message-square" class="w-4 h-4"></i> Catatan Petugas / RTK
                </h3>
                <p class="text-xs sm:text-sm text-sky-800 leading-relaxed">{{ $pengajuan->catatan_rtk }}</p>
            </div>
            @endif

            <!-- Penugasan Teknisi Info (If Exists) -->
            @if ($pengajuan->penugasanTeknisi)
            <div class="p-4 rounded-2xl bg-emerald-50 border-l-4 border-emerald-500 space-y-2">
                <h3 class="text-xs font-bold text-emerald-900 flex items-center gap-2">
                    <i data-lucide="wrench" class="w-4 h-4"></i> Penugasan Teknisi
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs text-emerald-800">
                    <p><strong>Teknisi:</strong> {{ $pengajuan->penugasanTeknisi->teknisi->nama ?? 'Tidak diketahui' }}</p>
                    <p><strong>Jadwal Penanganan:</strong> {{ \Carbon\Carbon::parse($pengajuan->penugasanTeknisi->jadwal_penanganan)->format('d/m/Y H:i') }}</p>
                    @if ($pengajuan->penugasanTeknisi->instruksi_kerja)
                        <p class="sm:col-span-2"><strong>Instruksi Kerja:</strong> {{ $pengajuan->penugasanTeknisi->instruksi_kerja }}</p>
                    @endif
                </div>
            </div>
            @endif

            <!-- Target Penyelesaian SLA Progress (If Applicable) -->
            @if ($pengajuan->target_selesai && !in_array($pengajuan->status_laporan, ['selesai', 'ditolak']))
            <div class="p-4 rounded-2xl bg-[#fbf7f0] border border-[#ecdfd0] space-y-3">
                <div class="flex items-center justify-between text-xs font-bold text-[#2a2338]">
                    <span class="flex items-center gap-1.5"><i data-lucide="timer" class="w-4 h-4 text-[#f2723b]"></i> Target SLA Penyelesaian</span>
                    <span class="font-mono text-[#7a7189]">{{ $pengajuan->target_selesai->format('d/m/Y H:i') }}</span>
                </div>
                <div class="w-full bg-[#ecdfd0] rounded-full h-2">
                    @php
                        $progress = $pengajuan->getSlaProgress();
                        $slaDisplay = $pengajuan->getSlaDisplay();
                        $color = match($slaDisplay['color']) {
                            'green' => 'bg-emerald-500',
                            'yellow' => 'bg-amber-500',
                            'red' => 'bg-rose-500',
                            default => 'bg-[#7a7189]',
                        };
                    @endphp
                    @if ($progress !== null)
                        <div class="{{ $color }} h-2 rounded-full transition-all duration-300" style="width: {{ min(100, $progress) }}%"></div>
                        <div class="flex justify-between mt-1.5 text-[10px] text-[#7a7189] font-semibold">
                            <span>Mulai</span>
                            <span class="text-[#191333]">{{ $slaDisplay['label'] }}</span>
                            <span>Target</span>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Timeline Riwayat Status Laporan -->
            <div class="pt-6 border-t border-[#ecdfd0] space-y-4">
                <h3 class="text-xs font-bold text-[#2a2338] uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="history" class="w-4 h-4 text-[#f2723b]"></i> Riwayat Status Laporan
                </h3>
                <x-status-timeline :logs="$pengajuan->statusLogs()->with('actor')->orderBy('created_at', 'asc')->get()" />
            </div>

            <!-- Timeline Riwayat Status Fasilitas -->
            @if ($pengajuan->fasilitas)
            <div class="pt-4 border-t border-[#ecdfd0] space-y-4">
                <h3 class="text-xs font-bold text-[#7a7189] uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="box" class="w-4 h-4 text-[#7a7189]"></i> Riwayat Status Fasilitas: {{ $pengajuan->fasilitas->nama_fasilitas }}
                </h3>
                <x-status-timeline :logs="$pengajuan->fasilitas->statusLogs()->with('actor')->orderBy('created_at', 'asc')->get()" />
            </div>
            @endif

            <!-- Action Buttons Footer -->
            @if (!in_array($pengajuan->status_laporan, ['diproses', 'selesai']))
            <div class="pt-6 border-t border-[#ecdfd0] flex items-center justify-end gap-3">
                <a href="{{ route('pelapor.pengajuan.edit', $pengajuan) }}" 
                   class="inline-flex items-center gap-1.5 bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-sm transition">
                    <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Edit Laporan
                </a>
                <form action="{{ route('pelapor.pengajuan.destroy', $pengajuan) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus laporan ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1.5 bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-sm transition">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus Laporan
                    </button>
                </form>
            </div>
            @endif

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