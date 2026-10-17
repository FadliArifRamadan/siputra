@extends(auth()->check() && auth()->user()->hasRole('bikem') ? 'layouts.bikem' : 'layouts.pelapor')

@section('title', 'Detail Peminjaman Fasilitas')

@section('content')
<div class="py-4 sm:py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Top Bar Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-[#fbf7f0] border border-[#ecdfd0] rounded-xl text-[#f2723b]">
                    <i data-lucide="file-text" class="w-6 h-6"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold font-heading text-[#2a2338]">Detail Peminjaman Fasilitas</h1>
                    <p class="text-xs text-[#7a7189] mt-0.5">Diajukan pada {{ \Carbon\Carbon::parse($peminjaman->created_at)->format('d/m/Y H:i') }}</p>
                </div>
            </div>
            <a href="{{ route('pelapor.peminjaman.index') }}"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#7a7189] hover:text-[#191333] transition-colors bg-[#fbf7f0] border border-[#ecdfd0] px-3.5 py-2 rounded-xl shrink-0">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
            </a>
        </div>

        <!-- Main Card Information -->
        <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm p-6 sm:p-8 space-y-6">

            <!-- Alert Box Jika Status Perlu Revisi -->
            @if ($peminjaman->status_pinjam === 'perlu_revisi')
            <div class="p-5 bg-gradient-to-r from-orange-50 to-amber-50 rounded-2xl border-2 border-orange-300 shadow-sm space-y-3">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div class="p-2 bg-orange-100 text-orange-700 rounded-xl mt-0.5">
                            <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-orange-900 font-heading">Pengajuan Perlu Revisi dari BIKEM</h4>
                            <p class="text-xs text-orange-700 mt-1">BIKEM meminta perbaikan berkas/jadwal pada pengajuan ini sebelum dapat diteruskan ke RTK.</p>
                            @if ($peminjaman->catatan_bikem)
                            <div class="mt-2.5 p-3 bg-white/80 rounded-xl border border-orange-200 text-xs text-orange-950">
                                <span class="font-bold">Catatan BIKEM:</span> {{ $peminjaman->catatan_bikem }}
                            </div>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('pelapor.peminjaman.edit', $peminjaman) }}"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold rounded-xl shadow transition shrink-0">
                        <i data-lucide="edit-3" class="w-4 h-4"></i> Perbaiki Pengajuan Sekarang
                    </a>
                </div>
            </div>
            @endif

            <!-- Badges Summary Row -->
            <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-[#ecdfd0]">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-[#7a7189] uppercase tracking-wider mr-2">Status Peminjaman:</span>
                    @php
                        $badgeConfig = match($peminjaman->status_pinjam) {
                            'pending', 'menunggu_bikem' => ['class' => 'bg-amber-100 text-amber-800 border-amber-200', 'label' => 'Menunggu BIKEM'],
                            'perlu_revisi' => ['class' => 'bg-orange-100 text-orange-800 border-orange-200 animate-pulse', 'label' => 'Perlu Revisi BIKEM'],
                            'menunggu_rtk' => ['class' => 'bg-blue-100 text-blue-800 border-blue-200', 'label' => 'Disetujui BIKEM (Menunggu RTK)'],
                            'disetujui' => ['class' => 'bg-emerald-100 text-emerald-800 border-emerald-200', 'label' => 'Disetujui RTK (Selesai Validasi)'],
                            'ditolak' => ['class' => 'bg-rose-100 text-rose-800 border-rose-200', 'label' => 'Ditolak'],
                            'berjalan' => ['class' => 'bg-sky-100 text-sky-800 border-sky-200', 'label' => 'Sedang Berjalan'],
                            'selesai' => ['class' => 'bg-gray-100 text-gray-700 border-gray-200', 'label' => 'Selesai'],
                            default => ['class' => 'bg-gray-100 text-gray-700 border-gray-200', 'label' => ucfirst($peminjaman->status_pinjam)],
                        };
                    @endphp
                    <span class="px-3 py-1 inline-flex text-xs font-bold rounded-xl border {{ $badgeConfig['class'] }}">
                        {{ $badgeConfig['label'] }}
                    </span>
                </div>

                @if ($peminjaman->verifikasi_bikem_at)
                <div class="text-[11px] text-[#7a7189] bg-[#fbf7f0] px-3 py-1 rounded-lg border border-[#ecdfd0]">
                    Ditinjau BIKEM: <span class="font-semibold text-[#191333]">{{ $peminjaman->verifikasi_bikem_at->format('d/m/Y H:i') }}</span>
                </div>
                @endif
            </div>

            <!-- Detail Grid Info Utama -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="bg-[#fbf7f0] p-4 rounded-xl border border-[#ecdfd0] space-y-1">
                    <span class="text-[11px] font-bold text-[#7a7189] uppercase tracking-wider">Pemohon / Peminjam</span>
                    <p class="text-sm font-bold text-[#191333]">{{ $peminjaman->peminjam->nama ?? $peminjaman->peminjam->name ?? 'Tidak diketahui' }}</p>
                </div>

                <div class="bg-[#fbf7f0] p-4 rounded-xl border border-[#ecdfd0] space-y-1">
                    <span class="text-[11px] font-bold text-[#7a7189] uppercase tracking-wider">Total Item & Unit Dipinjam</span>
                    <p class="text-sm font-bold text-[#f2723b]">
                        {{ $peminjaman->items->count() }} Jenis Facility ( Total {{ $peminjaman->items->sum('jumlah_dipinjam') }} Unit )
                    </p>
                </div>

                <div class="bg-[#fbf7f0] p-4 rounded-xl border border-[#ecdfd0] space-y-1">
                    <span class="text-[11px] font-bold text-[#7a7189] uppercase tracking-wider">Waktu Mulai</span>
                    <p class="text-sm font-bold text-[#191333]">{{ \Carbon\Carbon::parse($peminjaman->waktu_mulai)->format('d/m/Y H:i') }} WIB</p>
                </div>

                <div class="bg-[#fbf7f0] p-4 rounded-xl border border-[#ecdfd0] space-y-1">
                    <span class="text-[11px] font-bold text-[#7a7189] uppercase tracking-wider">Waktu Selesai</span>
                    <p class="text-sm font-bold text-[#191333]">{{ \Carbon\Carbon::parse($peminjaman->waktu_selesai)->format('d/m/Y H:i') }} WIB</p>
                </div>

            </div>

            <!-- Daftar Fasilitas Dipinjam -->
            <div class="space-y-3">
                <h3 class="text-xs font-bold text-[#2a2338] uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="box" class="w-4 h-4 text-[#f2723b]"></i> Daftar Fasilitas Dipinjam
                </h3>
                <div class="bg-white rounded-xl border border-[#ecdfd0] overflow-hidden">
                    <table class="min-w-full divide-y divide-[#ecdfd0]">
                        <thead class="bg-[#fbf7f0]">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-[11px] font-bold text-[#7a7189] uppercase">No</th>
                                <th scope="col" class="px-4 py-3 text-left text-[11px] font-bold text-[#7a7189] uppercase">Nama Fasilitas</th>
                                <th scope="col" class="px-4 py-3 text-left text-[11px] font-bold text-[#7a7189] uppercase">Kategori</th>
                                <th scope="col" class="px-4 py-3 text-center text-[11px] font-bold text-[#7a7189] uppercase">Jumlah Unit</th>
                                <th scope="col" class="px-4 py-3 text-right text-[11px] font-bold text-[#7a7189] uppercase">Status Item</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#ecdfd0] text-xs">
                            @forelse ($peminjaman->items as $index => $item)
                            <tr class="hover:bg-[#fbf7f0]/40 transition">
                                <td class="px-4 py-3 text-[#7a7189] font-medium">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 font-semibold text-[#191333]">
                                    {{ $item->fasilitas->nama_fasilitas ?? 'Fasilitas Tidak Ditemukan' }}
                                </td>
                                <td class="px-4 py-3 text-[#7a7189]">
                                    {{ ucfirst($item->fasilitas->kategori ?? '-') }}
                                </td>
                                <td class="px-4 py-3 text-center font-bold text-[#f2723b]">
                                    {{ $item->jumlah_dipinjam }} unit
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span class="px-2.5 py-0.5 inline-flex text-[10px] font-bold rounded-lg
                                        {{ $item->status_item === 'pending' ? 'bg-amber-100 text-amber-800 border border-amber-200' : '' }}
                                        {{ $item->status_item === 'disetujui' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : '' }}
                                        {{ $item->status_item === 'ditolak' ? 'bg-rose-100 text-rose-800 border border-rose-200' : '' }}
                                        {{ $item->status_item === 'berjalan' ? 'bg-sky-100 text-sky-800 border border-sky-200' : '' }}
                                        {{ $item->status_item === 'selesai' ? 'bg-gray-100 text-gray-700 border border-gray-200' : '' }}">
                                        {{ ucfirst($item->status_item ?? $peminjaman->status_pinjam) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-gray-400 italic">
                                    Tidak ada rincian fasilitas.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Keperluan Detail -->
            <div class="space-y-2">
                <h3 class="text-xs font-bold text-[#2a2338] uppercase tracking-wider">Keperluan Peminjaman</h3>
                <div class="p-4 rounded-xl bg-[#fbf7f0] border border-[#ecdfd0] text-xs sm:text-sm text-[#2a2338] leading-relaxed whitespace-pre-line">
                    {{ $peminjaman->keperluan }}
                </div>
            </div>

            <!-- Berkas Pendukung -->
            <div class="space-y-2">
                <h3 class="text-xs font-bold text-[#2a2338] uppercase tracking-wider">Berkas Pendukung</h3>
                @if ($peminjaman->file_pendukung)
                <div class="p-4 rounded-xl bg-[#fbf7f0] border border-[#ecdfd0] flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5 overflow-hidden">
                        <i data-lucide="paperclip" class="w-4 h-4 text-[#f2723b] shrink-0"></i>
                        <a href="{{ Storage::url($peminjaman->file_pendukung) }}"
                            target="_blank"
                            class="text-xs sm:text-sm font-medium text-sky-700 hover:text-sky-900 hover:underline truncate">
                            {{ basename($peminjaman->file_pendukung) }}
                        </a>
                    </div>
                    <span class="text-[11px] text-[#7a7189] shrink-0 font-medium">
                        ({{ number_format(Storage::disk('public')->exists($peminjaman->file_pendukung) ? Storage::disk('public')->size($peminjaman->file_pendukung) / 1024 : 0, 2) }} KB)
                    </span>
                </div>
                @else
                <div class="p-4 rounded-xl bg-[#fbf7f0] border border-[#ecdfd0] text-xs text-[#7a7189] italic">
                    Tidak ada berkas pendukung yang dilampirkan.
                </div>
                @endif
            </div>

            <!-- Action Buttons Footer -->
            @php
                $isHariH = \Carbon\Carbon::parse($peminjaman->waktu_mulai)->isPast();
            @endphp

            <div class="pt-6 border-t border-[#ecdfd0] flex flex-wrap items-center justify-end gap-3">
                
                <!-- Tombol Cetak Surat (Tampil jika status disetujui, berjalan, atau selesai) -->
                @if (in_array($peminjaman->status_pinjam, ['disetujui', 'berjalan', 'selesai']))
                <a href="{{ route('surat.preview', $peminjaman->id) }}"
                    target="_blank"
                    class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-sm transition">
                    <i data-lucide="printer" class="w-3.5 h-3.5"></i> Cetak Surat Izin
                </a>
                @endif

                <!-- Tombol Edit & Hapus (Tampil jika status pending dan belum melewati waktu mulai) -->
                @if ($peminjaman->status_pinjam === 'pending' && !$isHariH)
                <a href="{{ route('pelapor.peminjaman.edit', $peminjaman) }}"
                    class="inline-flex items-center gap-1.5 bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-sm transition">
                    <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Edit Pengajuan
                </a>
                <form action="{{ route('pelapor.peminjaman.destroy', $peminjaman) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pengajuan ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1.5 bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-sm transition">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus Pengajuan
                    </button>
                </form>
                @endif

            </div>

            <!-- Informasi Tambahan untuk Status Tertentu -->
            @if ($isHariH && $peminjaman->status_pinjam === 'pending')
            <div class="pt-2">
                <div class="p-4 rounded-xl bg-[#fbf7f0] border border-[#ecdfd0] text-xs text-[#7a7189] flex items-center gap-2">
                    <i data-lucide="lock" class="w-4 h-4 text-amber-600 shrink-0"></i>
                    <span>Peminjaman ini telah memasuki hari pelaksanaan dan tidak dapat diubah lagi.</span>
                </div>
            </div>
            @elseif (!in_array($peminjaman->status_pinjam, ['pending']) && !in_array($peminjaman->status_pinjam, ['disetujui', 'berjalan', 'selesai']))
            <div class="pt-2">
                <div class="p-4 rounded-xl bg-[#fbf7f0] border border-[#ecdfd0] text-xs text-[#7a7189] flex items-center gap-2">
                    <i data-lucide="info" class="w-4 h-4 text-[#7a7189] shrink-0"></i>
                    <span>Pengajuan ini sudah diproses dan tidak dapat diubah lagi.</span>
                </div>
            </div>
            @endif

        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.lucide) {
            lucide.createIcons();
        }
    });
</script>
@endpush