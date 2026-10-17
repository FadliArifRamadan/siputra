@extends('layouts.rtk')

@section('title', 'Detail Peminjaman')

@section('content')
<div class="py-4 sm:py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Header Card -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm">
            <div class="flex items-center gap-3">
                <div class="p-3 rounded-xl bg-[#f2723b]/10 text-[#f2723b]">
                    <i data-lucide="file-text" class="w-6 h-6"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold font-heading text-[#2a2338]">Detail Peminjaman</h1>
                    <p class="text-xs sm:text-sm text-[#7a7189] mt-0.5">Informasi lengkap permohonan peminjaman fasilitas kampus</p>
                </div>
            </div>
            <a href="{{ route('rtk.peminjaman.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#7a7189] hover:text-[#f2723b] bg-[#fbf7f0] border border-[#ecdfd0] px-4 py-2 rounded-lg transition-colors self-start sm:self-auto">
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

        <!-- Main Card Information -->
        <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm overflow-hidden p-6 sm:p-8 space-y-6">

            <!-- Status Badge Header -->
            <div class="flex items-center justify-between pb-4 border-b border-[#ecdfd0]">
                <span class="text-xs font-bold uppercase tracking-wider text-[#7a7189]">Status Permohonan</span>
                <div>
                    @php
                        $statusStyles = [
                            'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                            'menunggu_bikem' => 'bg-amber-100 text-amber-800 border-amber-200',
                            'perlu_revisi' => 'bg-orange-100 text-orange-800 border-orange-200',
                            'menunggu_rtk' => 'bg-blue-100 text-blue-800 border-blue-200',
                            'disetujui' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                            'ditolak' => 'bg-rose-100 text-rose-800 border-rose-200',
                            'berjalan' => 'bg-sky-100 text-sky-800 border-sky-200',
                            'selesai' => 'bg-slate-100 text-slate-800 border-slate-200'
                        ];
                        $statusLabel = match($peminjaman->status_pinjam) {
                            'pending', 'menunggu_bikem' => 'Menunggu BIKEM',
                            'perlu_revisi' => 'Perlu Revisi BIKEM',
                            'menunggu_rtk' => 'Menunggu Verifikasi RTK',
                            'disetujui' => 'Disetujui RTK',
                            'ditolak' => 'Ditolak',
                            'berjalan' => 'Berjalan',
                            'selesai' => 'Selesai',
                            default => ucfirst($peminjaman->status_pinjam),
                        };
                    @endphp
                    <span class="px-3 py-1 inline-flex text-xs font-bold uppercase tracking-wide rounded-full border {{ $statusStyles[$peminjaman->status_pinjam] ?? 'bg-slate-100 text-slate-800 border-slate-200' }}">
                        {{ $statusLabel }}
                    </span>
                </div>
            </div>

            <!-- Kartu Verifikasi BIKEM (Jika ada) -->
            @if ($peminjaman->verifikasi_bikem_at || $peminjaman->catatan_bikem)
            <div class="p-4 rounded-xl bg-blue-50/70 border border-blue-200 space-y-2">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-blue-900 flex items-center gap-1.5">
                        <i data-lucide="shield-check" class="w-4 h-4 text-blue-600"></i>
                        Verifikasi Kemahasiswaan (BIKEM)
                    </h3>
                    @if ($peminjaman->verifikasi_bikem_at)
                    <span class="text-[11px] text-blue-700 font-medium">
                        {{ $peminjaman->verifikasi_bikem_at->format('d/m/Y H:i') }} WIB
                    </span>
                    @endif
                </div>
                <div class="text-xs text-blue-950 space-y-1">
                    <p><span class="font-semibold">Diverifikasi oleh:</span> {{ $peminjaman->verifikasiBikemBy->nama ?? 'Petugas BIKEM' }}</p>
                    @if ($peminjaman->catatan_bikem)
                    <p class="mt-1"><span class="font-semibold">Catatan BIKEM:</span> {{ $peminjaman->catatan_bikem }}</p>
                    @endif
                </div>
            </div>
            @endif

            <!-- Informasi Peminjam & Waktu -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 rounded-xl bg-[#fbf7f0] border border-[#ecdfd0]/70">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-[#7a7189]">Peminjam</h3>
                    <p class="mt-1 text-sm font-semibold text-[#2a2338]">{{ $peminjaman->peminjam->nama ?? 'Tidak diketahui' }}</p>
                </div>

                <div class="p-4 rounded-xl bg-[#fbf7f0] border border-[#ecdfd0]/70">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-[#7a7189]">Waktu Mulai</h3>
                    <p class="mt-1 text-sm font-semibold text-[#2a2338] flex items-center gap-1.5">
                        <i data-lucide="calendar" class="w-4 h-4 text-[#0f8a82]"></i>
                        {{ $peminjaman->waktu_mulai->format('d/m/Y H:i') }}
                    </p>
                </div>

                <div class="p-4 rounded-xl bg-[#fbf7f0] border border-[#ecdfd0]/70">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-[#7a7189]">Waktu Selesai</h3>
                    <p class="mt-1 text-sm font-semibold text-[#2a2338] flex items-center gap-1.5">
                        <i data-lucide="calendar" class="w-4 h-4 text-[#f2723b]"></i>
                        {{ $peminjaman->waktu_selesai->format('d/m/Y H:i') }}
                    </p>
                </div>
            </div>

            <!-- ✅ TABEL DAFTAR FASILITAS YANG DIPINJAM (MULTI-ITEM) -->
            <div class="p-4 rounded-xl bg-[#fbf7f0] border border-[#ecdfd0]/70 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-[#7a7189]">Daftar Fasilitas / Barang Dipinjam</h3>
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-[#191333] text-white">
                        Total: {{ $peminjaman->items->sum('jumlah_dipinjam') }} Unit
                    </span>
                </div>
                
                <div class="overflow-x-auto rounded-lg border border-[#ecdfd0] bg-white">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-[#fbf7f0] text-[#2a2338] font-bold border-b border-[#ecdfd0]">
                            <tr>
                                <th class="p-3">No</th>
                                <th class="p-3">Nama Fasilitas</th>
                                <th class="p-3">Kategori</th>
                                <th class="p-3 text-center">Status Kondisi</th>
                                <th class="p-3 text-center">Jumlah Dipinjam</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#ecdfd0]">
                            @forelse($peminjaman->items as $index => $item)
                                <tr>
                                    <td class="p-3 font-mono text-center w-10">{{ $index + 1 }}</td>
                                    <td class="p-3 font-semibold text-[#2a2338]">
                                        {{ $item->fasilitas->nama_fasilitas ?? 'Fasilitas Tidak Ditemukan' }}
                                    </td>
                                    <td class="p-3 text-[#7a7189]">
                                        {{ ucfirst($item->fasilitas->kategori ?? '-') }}
                                    </td>
                                    <td class="p-3 text-center">
                                        @php
                                            $kondisi = $item->fasilitas->status_kondisi ?? 'baik';
                                        @endphp
                                        @if(in_array($kondisi, ['rusak_berat', 'maintenance']))
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700">
                                                {{ str_replace('_', ' ', strtoupper($kondisi)) }}
                                            </span>
                                        @elseif($kondisi === 'rusak_ringan')
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700">
                                                RUSAK RINGAN
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700">
                                                BAIK
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-center font-bold text-[#2a2338]">
                                        {{ $item->jumlah_dipinjam }} Unit
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-4 text-center text-[#7a7189] italic">
                                        Tidak ada item fasilitas terdaftar pada peminjaman ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Keperluan -->
            <div class="p-4 rounded-xl bg-[#fbf7f0] border border-[#ecdfd0]/70">
                <h3 class="text-xs font-bold uppercase tracking-wider text-[#7a7189] mb-1">Keperluan</h3>
                <p class="text-sm text-[#2a2338] leading-relaxed bg-white p-3 rounded-lg border border-[#ecdfd0] mt-1">{{ $peminjaman->keperluan }}</p>
            </div>

            <!-- FILE PENDUKUNG -->
            <div class="p-4 rounded-xl bg-[#fbf7f0] border border-[#ecdfd0]/70">
                <h3 class="text-xs font-bold uppercase tracking-wider text-[#7a7189] mb-2">📎 Berkas Pendukung</h3>
                @if ($peminjaman->file_pendukung)
                    <div class="flex items-center justify-between bg-white p-3 rounded-lg border border-[#ecdfd0]">
                        <div class="flex items-center gap-2 overflow-hidden">
                            <i data-lucide="paperclip" class="w-4 h-4 text-[#f2723b] shrink-0"></i>
                            <a href="{{ Storage::url($peminjaman->file_pendukung) }}" 
                               target="_blank"
                               class="text-xs sm:text-sm font-semibold text-sky-700 hover:text-sky-900 hover:underline truncate">
                                {{ basename($peminjaman->file_pendukung) }}
                            </a>
                        </div>
                        <span class="text-xs text-[#7a7189] shrink-0 font-medium ml-2">
                            ({{ number_format(Storage::disk('public')->exists($peminjaman->file_pendukung) ? Storage::disk('public')->size($peminjaman->file_pendukung) / 1024 : 0, 2) }} KB)
                        </span>
                    </div>
                @else
                    <p class="text-xs text-[#7a7189] italic">Tidak ada berkas pendukung yang dilampirkan.</p>
                @endif
            </div>

            <div class="p-4 rounded-xl bg-[#fbf7f0] border border-[#ecdfd0]/70 flex items-center justify-between">
                <h3 class="text-xs font-bold uppercase tracking-wider text-[#7a7189]">Tanggal Pengajuan</h3>
                <p class="text-xs font-semibold text-[#2a2338]">{{ $peminjaman->created_at->format('d/m/Y H:i') }}</p>
            </div>

            <!-- INFORMASI SURAT IZIN -->
            @if (in_array($peminjaman->status_pinjam, ['disetujui', 'berjalan', 'selesai']))
                <div class="border-t border-[#ecdfd0] pt-6">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-[#7a7189] mb-2 flex items-center gap-2">
                        <i data-lucide="file-text" class="w-4 h-4 text-[#0f8a82]"></i>
                        Surat Izin Peminjaman
                    </h3>
                    <p class="text-xs text-[#7a7189] mb-3">
                        Peminjam dapat mencetak surat izin peminjaman melalui halaman detail peminjaman milik akun peminjam.
                    </p>
                    <div class="bg-sky-50 border border-sky-200 p-3.5 rounded-xl flex items-start gap-2.5">
                        <i data-lucide="info" class="w-4 h-4 text-sky-600 shrink-0 mt-0.5"></i>
                        <p class="text-xs text-sky-800 leading-relaxed">
                            Surat izin otomatis digenerate dengan nomor surat dan format resmi UCIC setelah permohonan disetujui.
                        </p>
                    </div>
                </div>
            @endif

            <!-- TOMBOL AKSI -->
            <div class="border-t border-[#ecdfd0] pt-6">
                <h3 class="text-xs font-bold uppercase tracking-wider text-[#7a7189] mb-4">Aksi Manajemen</h3>
                <div class="flex flex-wrap gap-3">
                    @if (in_array($peminjaman->status_pinjam, ['menunggu_rtk', 'pending']))
                        <!-- Approve -->
                        <form action="{{ route('rtk.peminjaman.approve', $peminjaman) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status_pinjam" value="disetujui">
                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-semibold text-xs shadow-sm transition-all flex items-center gap-1.5" onclick="return confirm('Setujui permohonan peminjaman fasilitas ini?')">
                                <i data-lucide="check-circle-2" class="w-4 h-4"></i> Setujui Peminjaman
                            </button>
                        </form>
                        <!-- Reject dengan modal -->
                        <button onclick="showRejectModal({{ $peminjaman->id }})" 
                                class="bg-rose-600 hover:bg-rose-700 text-white px-5 py-2.5 rounded-xl font-semibold text-xs shadow-sm transition-all flex items-center gap-1.5">
                            <i data-lucide="x-circle" class="w-4 h-4"></i> Tolak
                        </button>
                    @elseif ($peminjaman->status_pinjam === 'menunggu_bikem')
                        <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-xs text-amber-800 flex items-center gap-2">
                            <i data-lucide="clock" class="w-4 h-4 text-amber-600 shrink-0"></i>
                            <span>Pengajuan ini sedang menunggu peninjauan dan persetujuan dari <strong>BIKEM / Kemahasiswaan</strong> terlebih dahulu sebelum dapat diverifikasi oleh RTK.</span>
                        </div>
                    @elseif ($peminjaman->status_pinjam === 'perlu_revisi')
                        <div class="p-3 bg-orange-50 rounded-xl border border-orange-200 text-xs text-orange-800 flex items-center gap-2">
                            <i data-lucide="alert-circle" class="w-4 h-4 text-orange-600 shrink-0"></i>
                            <span>Pengajuan ini sedang dalam status perbaikan/revisi oleh pemohon sesuai permintaan catatan BIKEM.</span>
                        </div>
                    @elseif ($peminjaman->status_pinjam === 'disetujui' && $peminjaman->waktu_mulai <= now())
                        <!-- Mulai -->
                        <form action="{{ route('rtk.peminjaman.start', $peminjaman) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white px-5 py-2.5 rounded-xl font-semibold text-xs shadow-sm transition-all flex items-center gap-1.5">
                                <i data-lucide="play-circle" class="w-4 h-4"></i> Mulai Peminjaman
                            </button>
                        </form>
                    @elseif ($peminjaman->status_pinjam === 'berjalan')
                        <!-- Selesai -->
                        <form action="{{ route('rtk.peminjaman.complete', $peminjaman) }}" method="POST" 
                              onsubmit="return confirm('Yakin ingin menandai peminjaman ini selesai?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="bg-[#191333] hover:bg-[#2f2559] text-white px-5 py-2.5 rounded-xl font-semibold text-xs shadow-sm transition-all flex items-center gap-1.5">
                                <i data-lucide="check-check" class="w-4 h-4"></i> Tandai Selesai
                            </button>
                        </form>
                    @else
                        <p class="text-xs text-[#7a7189] italic">Tidak ada aksi lanjutan yang tersedia untuk status ini.</p>
                    @endif
                </div>
            </div>

            <!-- Status Timeline -->
            <div class="border-t border-[#ecdfd0] pt-6">
                <h3 class="text-xs font-bold uppercase tracking-wider text-[#7a7189] mb-4 flex items-center gap-2">
                    <i data-lucide="history" class="w-4 h-4 text-[#f2723b]"></i>
                    Riwayat Status Peminjaman
                </h3>
                <x-status-timeline :logs="$peminjaman->statusLogs()->with('actor')->orderBy('created_at', 'asc')->get()" />
            </div>
        </div>

    </div>
</div>

<!-- MODAL REJECT -->
<div id="rejectModal" class="fixed inset-0 bg-[#191333]/60 backdrop-blur-sm hidden overflow-y-auto h-full w-full z-50 transition-all duration-300">
    <div class="relative top-20 mx-auto p-6 border border-[#ecdfd0] w-full max-w-md shadow-2xl rounded-2xl bg-white animate-in fade-in zoom-in duration-200">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-rose-100 text-rose-600">
                <i data-lucide="x-circle" class="w-6 h-6"></i>
            </div>
            <h3 class="text-lg font-bold font-heading text-[#2a2338] mt-4">Tolak Peminjaman</h3>
            <p class="text-xs text-[#7a7189] mt-1">Berikan alasan penolakan (opsional)</p>

            <form id="rejectForm" method="POST" class="mt-4 text-left">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status_pinjam" value="ditolak">
                <textarea name="catatan" rows="3" class="w-full rounded-xl border-[#ecdfd0] shadow-sm focus:border-rose-500 focus:ring-rose-500 text-xs text-[#2a2338] placeholder-[#7a7189]" placeholder="Tuliskan alasan penolakan..."></textarea>
                
                <div class="flex justify-end gap-2 mt-5">
                    <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-white border border-[#ecdfd0] text-[#2a2338] rounded-lg hover:bg-[#fbf7f0] text-xs font-semibold transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-semibold shadow-sm transition-colors flex items-center gap-1.5">
                        <i data-lucide="x-octagon" class="w-3.5 h-3.5"></i> Tolak Peminjaman
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function showRejectModal(id) {
        document.getElementById('rejectModal').classList.remove('hidden');
        document.getElementById('rejectForm').action = '{{ url("rtk/peminjaman") }}/' + id + '/reject';
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }

    document.addEventListener('click', function(event) {
        const modal = document.getElementById('rejectModal');
        if (event.target === modal) {
            closeRejectModal();
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeRejectModal();
        }
    });
</script>
@endpush