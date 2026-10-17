@extends('layouts.bikem')

@section('title', 'Detail Permohonan Peminjaman #' . $peminjaman->id)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

    <!-- Header Card -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm">
        <div class="flex items-center gap-3">
            <div class="p-3 rounded-xl bg-[#f2723b]/10 text-[#f2723b]">
                <i data-lucide="file-check-2" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold font-heading text-[#2a2338]">Verifikasi Peminjaman #{{ $peminjaman->id }}</h1>
                <p class="text-xs sm:text-sm text-[#7a7189] mt-0.5">Peninjauan permohonan fasilitas kemahasiswaan</p>
            </div>
        </div>
        <a href="{{ route('bikem.peminjaman.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#7a7189] hover:text-[#f2723b] bg-[#fbf7f0] border border-[#ecdfd0] px-4 py-2 rounded-xl transition self-start sm:self-auto">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
        </a>
    </div>

    <!-- Alert Flash Message -->
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

        <!-- Status Header -->
        <div class="flex items-center justify-between pb-4 border-b border-[#ecdfd0]">
            <span class="text-xs font-bold uppercase tracking-wider text-[#7a7189]">Status Permohonan Saat Ini</span>
            <div>
                @php
                    $badge = match($peminjaman->status_pinjam) {
                        'pending', 'menunggu_bikem' => ['class' => 'bg-amber-100 text-amber-800 border-amber-200', 'label' => 'Menunggu BIKEM'],
                        'perlu_revisi' => ['class' => 'bg-orange-100 text-orange-800 border-orange-200', 'label' => 'Perlu Revisi Pemohon'],
                        'menunggu_rtk' => ['class' => 'bg-blue-100 text-blue-800 border-blue-200', 'label' => 'Disetujui BIKEM (Menunggu RTK)'],
                        'disetujui' => ['class' => 'bg-emerald-100 text-emerald-800 border-emerald-200', 'label' => 'Disetujui Penuh RTK'],
                        'ditolak' => ['class' => 'bg-rose-100 text-rose-800 border-rose-200', 'label' => 'Ditolak'],
                        'berjalan' => ['class' => 'bg-sky-100 text-sky-800 border-sky-200', 'label' => 'Sedang Berjalan'],
                        'selesai' => ['class' => 'bg-gray-100 text-gray-700 border-gray-200', 'label' => 'Selesai'],
                        default => ['class' => 'bg-gray-100 text-gray-700 border-gray-200', 'label' => ucfirst($peminjaman->status_pinjam)],
                    };
                @endphp
                <span class="px-3 py-1 inline-flex text-xs font-bold uppercase tracking-wide rounded-xl border {{ $badge['class'] }}">
                    {{ $badge['label'] }}
                </span>
            </div>
        </div>

        <!-- Informasi Pemohon & Jadwal -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 rounded-xl bg-[#fbf7f0] border border-[#ecdfd0]/70 space-y-1">
                <span class="text-[11px] font-bold uppercase tracking-wider text-[#7a7189]">Pemohon / Mahasiswa</span>
                <p class="text-sm font-bold text-[#191333]">{{ $peminjaman->peminjam->nama ?? 'Mahasiswa' }}</p>
                <p class="text-xs text-[#7a7189]">NIM: {{ $peminjaman->peminjam->nomor_induk ?? '-' }}</p>
                <p class="text-[11px] text-[#7a7189]">{{ $peminjaman->peminjam->email ?? '-' }}</p>
            </div>

            <div class="p-4 rounded-xl bg-[#fbf7f0] border border-[#ecdfd0]/70 space-y-1">
                <span class="text-[11px] font-bold uppercase tracking-wider text-[#7a7189]">Waktu Mulai</span>
                <p class="text-sm font-bold text-[#191333] flex items-center gap-1.5">
                    <i data-lucide="calendar" class="w-4 h-4 text-[#0f8a82]"></i>
                    {{ $peminjaman->waktu_mulai->format('d/m/Y H:i') }} WIB
                </p>
            </div>

            <div class="p-4 rounded-xl bg-[#fbf7f0] border border-[#ecdfd0]/70 space-y-1">
                <span class="text-[11px] font-bold uppercase tracking-wider text-[#7a7189]">Waktu Selesai</span>
                <p class="text-sm font-bold text-[#191333] flex items-center gap-1.5">
                    <i data-lucide="calendar" class="w-4 h-4 text-[#f2723b]"></i>
                    {{ $peminjaman->waktu_selesai->format('d/m/Y H:i') }} WIB
                </p>
            </div>
        </div>

        <!-- Keperluan & Berkas Pendukung -->
        <div class="p-4 rounded-xl bg-[#fbf7f0] border border-[#ecdfd0]/70 space-y-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-[#7a7189]">Keperluan / Nama Kegiatan</h3>
            <p class="text-xs sm:text-sm text-[#2a2338] leading-relaxed bg-white p-3 rounded-lg border border-[#ecdfd0]">
                {{ $peminjaman->keperluan }}
            </p>
            @if ($peminjaman->catatan_peminjam)
            <div class="text-xs text-[#7a7189] mt-2">
                <span class="font-bold">Catatan Tambahan Mahasiswa:</span> {{ $peminjaman->catatan_peminjam }}
            </div>
            @endif
        </div>

        <!-- Berkas Pendukung / Proposal -->
        <div class="p-4 rounded-xl bg-[#fbf7f0] border border-[#ecdfd0]/70 flex items-center justify-between">
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-[#7a7189]">Berkas Pendukung / Proposal Kegiatan</h3>
                <p class="text-xs text-[#2a2338] mt-0.5">
                    {{ $peminjaman->file_pendukung ? 'Dokumen proposal telah dilampirkan oleh mahasiswa.' : 'Tidak ada berkas yang dilampirkan.' }}
                </p>
            </div>
            @if ($peminjaman->file_pendukung)
            <a href="{{ asset('storage/' . $peminjaman->file_pendukung) }}" target="_blank"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-[#191333] hover:bg-[#2f2559] transition shadow-sm">
                <i data-lucide="file-down" class="w-4 h-4 text-[#f2723b]"></i> Buka Berkas
            </a>
            @endif
        </div>

        <!-- Daftar Fasilitas yang Diminta -->
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-bold uppercase tracking-wider text-[#7a7189]">Daftar Sarana & Prasarana yang Diajukan</h3>
                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-[#191333] text-white">
                    Total: {{ $peminjaman->items->sum('jumlah_dipinjam') }} Unit
                </span>
            </div>

            <div class="overflow-x-auto rounded-xl border border-[#ecdfd0] bg-white">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#fbf7f0] text-[#2a2338] font-bold border-b border-[#ecdfd0]">
                        <tr>
                            <th class="p-3">No</th>
                            <th class="p-3">Nama Fasilitas</th>
                            <th class="p-3">Kategori</th>
                            <th class="p-3 text-center">Jumlah Diminta</th>
                            <th class="p-3">Lokasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#ecdfd0]">
                        @forelse ($peminjaman->items as $index => $item)
                        <tr class="hover:bg-[#fbf7f0]/40 transition">
                            <td class="p-3 text-[#7a7189] font-medium">{{ $loop->iteration }}</td>
                            <td class="p-3 font-semibold text-[#191333]">
                                {{ $item->fasilitas->nama_fasilitas ?? 'Fasilitas Tidak Ditemukan' }}
                            </td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-md {{ ($item->fasilitas->kategori ?? '') === 'prasarana' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst($item->fasilitas->kategori ?? '-') }}
                                </span>
                            </td>
                            <td class="p-3 text-center font-bold text-[#f2723b]">
                                {{ $item->jumlah_dipinjam }} unit
                            </td>
                            <td class="p-3 text-[#7a7189]">
                                {{ $item->fasilitas->lokasi_display ?? '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-4 text-center text-gray-400 italic">Tidak ada fasilitas dalam pengajuan ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Riwayat / Catatan BIKEM Jika Ada -->
        @if ($peminjaman->verifikasi_bikem_at || $peminjaman->catatan_bikem)
        <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-xs text-blue-950 space-y-1.5">
            <div class="flex items-center justify-between">
                <span class="font-bold text-blue-900 flex items-center gap-1">
                    <i data-lucide="shield-check" class="w-4 h-4 text-blue-600"></i> Catatan Verifikasi BIKEM
                </span>
                @if ($peminjaman->verifikasi_bikem_at)
                <span class="text-[11px] text-blue-700">{{ $peminjaman->verifikasi_bikem_at->format('d/m/Y H:i') }} WIB</span>
                @endif
            </div>
            @if ($peminjaman->verifikasiBikemBy)
            <p><span class="font-semibold">Petugas:</span> {{ $peminjaman->verifikasiBikemBy->nama }}</p>
            @endif
            @if ($peminjaman->catatan_bikem)
            <p class="p-2 bg-white/70 rounded-lg border border-blue-200 mt-1"><span class="font-semibold">Catatan:</span> {{ $peminjaman->catatan_bikem }}</p>
            @endif
        </div>
        @endif

        <!-- KEPUTUSAN & AKSI BIKEM -->
        <div class="border-t border-[#ecdfd0] pt-6 space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-[#7a7189]">Keputusan Verifikasi BIKEM</h3>

            @if (in_array($peminjaman->status_pinjam, ['pending', 'menunggu_bikem']))
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Opsi 1: Setujui -->
                <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-200 flex flex-col justify-between space-y-3">
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2 text-emerald-800 font-bold text-sm">
                            <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600"></i>
                            <span>1. Setujui Pengajuan</span>
                        </div>
                        <p class="text-xs text-emerald-900/80 leading-relaxed">
                            Validasi kegiatan kemahasiswaan disetujui. Pengajuan akan diteruskan ke Tim RTK untuk verifikasi ketersediaan fisik fasilitas.
                        </p>
                    </div>
                    <button onclick="openModal('approveModal')"
                        class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition flex items-center justify-center gap-1.5">
                        <i data-lucide="check" class="w-4 h-4"></i> Setujui & Teruskan
                    </button>
                </div>

                <!-- Opsi 2: Minta Revisi -->
                <div class="p-4 rounded-2xl bg-amber-50/50 border border-amber-200 flex flex-col justify-between space-y-3">
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2 text-amber-900 font-bold text-sm">
                            <i data-lucide="refresh-cw" class="w-5 h-5 text-amber-600"></i>
                            <span>2. Minta Revisi Mahasiswa</span>
                        </div>
                        <p class="text-xs text-[#7a7189] leading-relaxed">
                            Minta mahasiswa memperbaiki proposal atau jam/jadwal. Mahasiswa <strong>dapat mengedit</strong> formulirnya kembali.
                        </p>
                    </div>
                    <button onclick="openModal('revisiModal')"
                        class="w-full py-2.5 rounded-xl text-xs font-semibold transition flex items-center justify-center gap-1.5 shadow-sm hover:brightness-95"
                        style="background-color: #fef3c7; color: #92400e; border: 1px solid #fcd34d;">
                        <i data-lucide="edit-3" class="w-4 h-4"></i> Minta Revisi
                    </button>
                </div>

                <!-- Opsi 3: Tolak -->
                <div class="p-4 rounded-2xl bg-rose-50/60 border border-rose-200 flex flex-col justify-between space-y-3">
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2 text-rose-900 font-bold text-sm">
                            <i data-lucide="x-circle" class="w-5 h-5 text-rose-600"></i>
                            <span>3. Tolak Pengajuan</span>
                        </div>
                        <p class="text-xs text-rose-950/80 leading-relaxed">
                            Kegiatan tidak diizinkan. Status ditolak bersifat <strong>final</strong> dan mahasiswa tidak dapat mengeditnya.
                        </p>
                    </div>
                    <button onclick="openModal('rejectModal')"
                        class="w-full py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-sm transition flex items-center justify-center gap-1.5">
                        <i data-lucide="x" class="w-4 h-4"></i> Tolak Pengajuan
                    </button>
                </div>
            </div>
            @elseif ($peminjaman->status_pinjam === 'perlu_revisi')
            <div class="p-5 bg-amber-50/60 rounded-2xl border border-amber-200 space-y-4">
                <div class="flex items-start gap-3">
                    <div class="p-2 bg-amber-100 text-amber-700 rounded-xl">
                        <i data-lucide="clock" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-amber-950">Menunggu Perbaikan oleh Mahasiswa</h4>
                        <p class="text-xs text-amber-800 mt-0.5">Pengajuan saat ini berada di tangan mahasiswa untuk direvisi sesuai catatan BIKEM.</p>
                        @if ($peminjaman->catatan_bikem)
                        <div class="mt-2.5 p-3 bg-white/90 rounded-xl border border-amber-200 text-xs text-amber-950">
                            <span class="font-bold">Catatan Revisi Terakhir:</span> {{ $peminjaman->catatan_bikem }}
                        </div>
                        @endif
                    </div>
                </div>

                <div class="flex flex-wrap gap-2 pt-2 border-t border-amber-200">
                    <button onclick="openModal('revisiModal')"
                        class="px-4 py-2 rounded-xl text-xs font-semibold shadow-sm transition flex items-center gap-1.5 hover:brightness-95"
                        style="background-color: #fef3c7; color: #92400e; border: 1px solid #fcd34d;">
                        <i data-lucide="edit" class="w-4 h-4"></i> Perbarui Catatan Revisi
                    </button>
                    <button onclick="openModal('approveModal')"
                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition flex items-center gap-1.5">
                        <i data-lucide="check-circle-2" class="w-4 h-4"></i> Langsung Setujui (ke RTK)
                    </button>
                    <button onclick="openModal('rejectModal')"
                        class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-sm transition flex items-center gap-1.5">
                        <i data-lucide="x-circle" class="w-4 h-4"></i> Tolak Pengajuan
                    </button>
                </div>
            </div>
            @elseif ($peminjaman->status_pinjam === 'ditolak')
            <div class="p-5 bg-rose-50 rounded-2xl border-2 border-rose-200 space-y-4">
                <div class="flex items-start gap-3">
                    <div class="p-2 bg-rose-100 text-rose-700 rounded-xl">
                        <i data-lucide="x-octagon" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-rose-950">Pengajuan Berstatus Ditolak</h4>
                        <p class="text-xs text-rose-800 mt-0.5">Pengajuan ini sebelumnya telah ditolak. Mahasiswa tidak dapat mengedit permohonan yang ditolak.</p>
                        @if ($peminjaman->catatan_bikem)
                        <div class="mt-2.5 p-3 bg-white/90 rounded-xl border border-rose-200 text-xs text-rose-950">
                            <span class="font-bold">Alasan Penolakan:</span> {{ $peminjaman->catatan_bikem }}
                        </div>
                        @endif
                    </div>
                </div>

                <div class="pt-2 border-t border-rose-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <p class="text-xs text-rose-800">
                        Ingin memberikan kesempatan mahasiswa untuk memperbaiki dan mengirim ulang?
                    </p>
                    <button onclick="openModal('revisiModal')"
                        class="px-4 py-2 rounded-xl text-xs font-semibold shadow-sm transition flex items-center gap-1.5 shrink-0 self-start sm:self-auto hover:brightness-95"
                        style="background-color: #fef3c7; color: #92400e; border: 1px solid #fcd34d;">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i> Buka Kembali untuk Revisi
                    </button>
                </div>
            </div>
            @elseif ($peminjaman->status_pinjam === 'menunggu_rtk')
            <div class="p-4 bg-blue-50 rounded-2xl border border-blue-200 flex items-center gap-3">
                <div class="p-2 bg-blue-100 text-blue-700 rounded-xl">
                    <i data-lucide="send" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-blue-950">Telah Disetujui BIKEM & Diteruskan ke RTK</h4>
                    <p class="text-xs text-blue-800 mt-0.5">Tahap validasi kemahasiswaan telah selesai. Saat ini menunggu verifikasi ketersediaan fasilitas fisik oleh Tim RTK.</p>
                </div>
            </div>
            @else
            <div class="p-3 bg-slate-100 rounded-xl text-xs text-[#7a7189] italic">
                Pengajuan ini telah selesai diproses dengan status saat ini: <strong>{{ ucfirst($peminjaman->status_pinjam) }}</strong>.
            </div>
            @endif
        </div>

        <!-- Status History Timeline -->
        <div class="border-t border-[#ecdfd0] pt-6">
            <h3 class="text-xs font-bold uppercase tracking-wider text-[#7a7189] mb-4 flex items-center gap-2">
                <i data-lucide="history" class="w-4 h-4 text-[#f2723b]"></i> Riwayat Status Pengajuan
            </h3>
            <x-status-timeline :logs="$peminjaman->statusLogs()->with('actor')->orderBy('created_at', 'asc')->get()" />
        </div>

    </div>

</div>

<!-- ============================================== -->
<!-- MODAL SETUJUI -->
<!-- ============================================== -->
<div id="approveModal" class="fixed inset-0 bg-[#191333]/60 backdrop-blur-sm hidden overflow-y-auto h-full w-full z-50 transition-all duration-300">
    <div class="relative top-20 mx-auto p-6 border border-[#ecdfd0] w-full max-w-md shadow-2xl rounded-2xl bg-white">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-emerald-100 text-emerald-600">
                <i data-lucide="check-circle-2" class="w-6 h-6"></i>
            </div>
            <h3 class="text-lg font-bold font-heading text-[#2a2338] mt-3">Setujui Pengajuan Peminjaman</h3>
            <p class="text-xs text-[#7a7189] mt-1">Pengajuan akan diteruskan ke tim RTK untuk verifikasi ketersediaan dan kesiapan fasilitas.</p>

            <form action="{{ route('bikem.peminjaman.approve', $peminjaman) }}" method="POST" class="mt-4 text-left space-y-3">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block text-xs font-bold text-[#2a2338] uppercase mb-1">Catatan Tambahan BIKEM (Opsional)</label>
                    <textarea name="catatan" rows="3" class="w-full rounded-xl border-[#ecdfd0] text-xs p-3 focus:ring-2 focus:ring-emerald-500" placeholder="Catatan atau instruksi untuk pemohon / RTK..."></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeModal('approveModal')" class="px-4 py-2 bg-white border border-[#ecdfd0] text-[#2a2338] rounded-xl hover:bg-[#fbf7f0] text-xs font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm flex items-center gap-1">
                        <i data-lucide="check" class="w-4 h-4"></i> Konfirmasi Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- MODAL REVISI -->
<!-- ============================================== -->
<div id="revisiModal" class="fixed inset-0 bg-[#191333]/60 backdrop-blur-sm hidden overflow-y-auto h-full w-full z-50 transition-all duration-300">
    <div class="relative top-20 mx-auto p-6 border border-[#ecdfd0] w-full max-w-md shadow-2xl rounded-2xl bg-white">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full" style="background-color: #fef3c7; color: #92400e;">
                <i data-lucide="refresh-cw" class="w-6 h-6"></i>
            </div>
            <h3 class="text-lg font-bold font-heading text-[#2a2338] mt-3">Minta Revisi Pemohon</h3>
            <p class="text-xs text-[#7a7189] mt-1">Berikan catatan yang jelas mengenai apa yang harus diperbaiki oleh mahasiswa (proposal, jadwal, dll).</p>

            <form action="{{ route('bikem.peminjaman.revisi', $peminjaman) }}" method="POST" class="mt-4 text-left space-y-3">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block text-xs font-bold text-[#2a2338] uppercase mb-1">Catatan Revisi <span class="text-rose-500">*</span></label>
                    <textarea name="catatan" rows="4" required class="w-full rounded-xl border-[#ecdfd0] text-xs p-3 focus:ring-2 focus:ring-amber-500" placeholder="Tuliskan instruksi revisi secara rinci..."></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeModal('revisiModal')" class="px-4 py-2 bg-white border border-[#ecdfd0] text-[#2a2338] rounded-xl hover:bg-[#fbf7f0] text-xs font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-white rounded-xl text-xs font-bold shadow-sm flex items-center gap-1 transition" style="background-color: #d97706;">
                        <i data-lucide="send" class="w-4 h-4"></i> Kirim Permintaan Revisi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- MODAL TOLAK -->
<!-- ============================================== -->
<div id="rejectModal" class="fixed inset-0 bg-[#191333]/60 backdrop-blur-sm hidden overflow-y-auto h-full w-full z-50 transition-all duration-300">
    <div class="relative top-20 mx-auto p-6 border border-[#ecdfd0] w-full max-w-md shadow-2xl rounded-2xl bg-white">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-rose-100 text-rose-600">
                <i data-lucide="x-circle" class="w-6 h-6"></i>
            </div>
            <h3 class="text-lg font-bold font-heading text-[#2a2338] mt-3">Tolak Pengajuan Peminjaman</h3>
            <p class="text-xs text-[#7a7189] mt-1">Berikan alasan mengapa permohonan peminjaman ini tidak dapat disetujui.</p>

            <form action="{{ route('bikem.peminjaman.reject', $peminjaman) }}" method="POST" class="mt-4 text-left space-y-3">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block text-xs font-bold text-[#2a2338] uppercase mb-1">Alasan Penolakan <span class="text-rose-500">*</span></label>
                    <textarea name="catatan" rows="3" required class="w-full rounded-xl border-[#ecdfd0] text-xs p-3 focus:ring-2 focus:ring-rose-500" placeholder="Tuliskan alasan penolakan..."></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeModal('rejectModal')" class="px-4 py-2 bg-white border border-[#ecdfd0] text-[#2a2338] rounded-xl hover:bg-[#fbf7f0] text-xs font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-sm flex items-center gap-1">
                        <i data-lucide="x-octagon" class="w-4 h-4"></i> Tolak Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            ['approveModal', 'revisiModal', 'rejectModal'].forEach(closeModal);
        }
    });
</script>
@endpush
