@extends('layouts.teknisi')

@section('title', 'Detail Pinjaman Alat')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Navigation & Title Header -->
    <div class="flex items-center justify-between bg-white p-6 rounded-2xl shadow-sm border border-[#f5ede1]">
        <div class="flex items-center gap-3">
            <div class="p-3 rounded-xl bg-[#f2723b]/10 text-[#f2723b]">
                <i data-lucide="file-text" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold font-heading text-[#191333]">Detail Pinjaman Alat</h1>
                <p class="text-xs text-[#7a7189] mt-0.5">Informasi rincian dan verifikasi peminjaman alat #{{ $peminjaman->id }}</p>
            </div>
        </div>
        <a href="{{ route('teknisi.peminjaman-alat.index') }}" 
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
        <!-- Status Banner -->
        <div class="flex items-center justify-between p-4 rounded-xl bg-[#fbf7f0] border border-[#f5ede1]">
            <span class="text-xs font-medium text-[#7a7189]">Status Pengajuan Saat Ini</span>
            @php
                $statusClasses = [
                    'pending'   => 'bg-amber-50 text-amber-700 border-amber-200',
                    'disetujui' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'ditolak'   => 'bg-rose-50 text-rose-700 border-rose-200',
                    'berjalan'  => 'bg-sky-50 text-sky-700 border-sky-200',
                    'selesai'   => 'bg-slate-100 text-slate-700 border-slate-200',
                ];
                $badgeClass = $statusClasses[$peminjaman->status_pinjam] ?? 'bg-slate-100 text-slate-700 border-slate-200';
            @endphp
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border capitalize {{ $badgeClass }}">
                {{ ucfirst($peminjaman->status_pinjam) }}
            </span>
        </div>

        <!-- Detail Informasi Waktu & Keperluan -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-4 rounded-xl bg-white border border-[#f5ede1]">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-[#7a7189] block mb-1">Waktu Mulai</span>
                <p class="text-sm font-semibold text-[#2a2338] flex items-center gap-1.5 mt-0.5">
                    <i data-lucide="calendar-check" class="w-4 h-4 text-[#f2723b]"></i>
                    {{ $peminjaman->waktu_mulai->format('d/m/Y H:i') }}
                </p>
            </div>
            <div class="p-4 rounded-xl bg-white border border-[#f5ede1]">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-[#7a7189] block mb-1">Waktu Selesai</span>
                <p class="text-sm font-semibold text-[#2a2338] flex items-center gap-1.5 mt-0.5">
                    <i data-lucide="calendar-x" class="w-4 h-4 text-[#f2723b]"></i>
                    {{ $peminjaman->waktu_selesai->format('d/m/Y H:i') }}
                </p>
            </div>
            <div class="md:col-span-2 p-4 rounded-xl bg-[#fbf7f0]/60 border border-[#f5ede1]">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-[#7a7189] block mb-1">Keperluan</span>
                <p class="text-xs leading-relaxed text-[#2a2338] whitespace-pre-line">{{ $peminjaman->keperluan }}</p>
            </div>
        </div>

        <!-- ✅ TABEL DAFTAR ALAT DIPINJAM (MULTI-ITEM) -->
        <div class="p-4 rounded-xl bg-[#fbf7f0]/60 border border-[#f5ede1] space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-[#7a7189]">Daftar Alat Dipinjam</span>
                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-[#191333] text-white">
                    Total: {{ $peminjaman->items->sum('jumlah_dipinjam') }} Unit
                </span>
            </div>
            
            <div class="overflow-x-auto rounded-xl border border-[#f5ede1] bg-white">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#fbf7f0] text-[#7a7189] font-bold border-b border-[#f5ede1] text-[11px] uppercase">
                        <tr>
                            <th class="p-3 text-center">No</th>
                            <th class="p-3">Nama Alat</th>
                            <th class="p-3 text-center">Kondisi Alat</th>
                            <th class="p-3 text-center">Jumlah Dipinjam</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#f5ede1]">
                        @forelse($peminjaman->items as $index => $item)
                            <tr>
                                <td class="p-3 text-center font-mono w-10 text-[#7a7189]">{{ $index + 1 }}</td>
                                <td class="p-3 font-semibold text-[#191333]">
                                    {{ $item->fasilitas->nama_fasilitas ?? 'Alat Tidak Ditemukan' }}
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
                                <td class="p-3 text-center font-bold text-[#191333]">
                                    {{ $item->jumlah_dipinjam }} Unit
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-4 text-center text-[#7a7189] italic">
                                    Tidak ada item alat terdaftar pada peminjaman ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ✅ VERIFIKASI SERAH TERIMA -->
        @if ($peminjaman->status_pinjam === 'disetujui' && !$peminjaman->isSudahDiverifikasiSerahTerima())
            <div class="p-5 rounded-xl border border-emerald-200 bg-emerald-50/50 space-y-3">
                <div class="flex items-center gap-2 text-emerald-800">
                    <i data-lucide="package-check" class="w-5 h-5 text-emerald-600"></i>
                    <h3 class="text-xs font-bold uppercase tracking-wider">Verifikasi Serah Terima</h3>
                </div>
                <p class="text-xs text-emerald-700">Barang siap diserahkan ke peminjam. Pilih dan verifikasi kondisi barang:</p>
                
                <form action="{{ route('teknisi.peminjaman-alat.serah-terima', $peminjaman) }}" method="POST" class="flex flex-wrap items-center gap-3 pt-1">
                    @csrf
                    @method('PATCH')
                    <select name="kondisi" class="rounded-xl border border-emerald-300 bg-white text-xs text-[#2a2338] px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none" required>
                        <option value="">-- Pilih Kondisi --</option>
                        <option value="baik">✅ Baik</option>
                        <option value="rusak">❌ Rusak</option>
                    </select>
                    <input type="text" name="catatan" placeholder="Catatan (opsional)" 
                           class="rounded-xl border border-emerald-300 bg-white text-xs text-[#2a2338] px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none w-full sm:w-64">
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition-all duration-200">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>Verifikasi Serah Terima</span>
                    </button>
                </form>
            </div>
        @endif

        <!-- ✅ VERIFIKASI PENGEMBALIAN -->
        @if ($peminjaman->status_pinjam === 'berjalan' && $peminjaman->isSudahDiverifikasiSerahTerima() && !$peminjaman->isSudahDiverifikasiPengembalian())
            <div class="p-5 rounded-xl border border-amber-200 bg-amber-50/50 space-y-3">
                <div class="flex items-center gap-2 text-amber-900">
                    <i data-lucide="rotate-ccw" class="w-5 h-5 text-amber-600"></i>
                    <h3 class="text-xs font-bold uppercase tracking-wider">Verifikasi Pengembalian</h3>
                </div>
                <p class="text-xs text-amber-800">Barang dikembalikan oleh peminjam. Verifikasi kondisi pengembalian barang:</p>
                
                <form action="{{ route('teknisi.peminjaman-alat.pengembalian', $peminjaman) }}" method="POST" class="flex flex-wrap items-center gap-3 pt-1">
                    @csrf
                    @method('PATCH')
                    <select name="kondisi" class="rounded-xl border border-amber-300 bg-white text-xs text-[#2a2338] px-3 py-2 focus:ring-2 focus:ring-amber-500 focus:outline-none" required>
                        <option value="">-- Pilih Kondisi --</option>
                        <option value="baik">✅ Baik</option>
                        <option value="rusak">❌ Rusak</option>
                    </select>
                    <input type="text" name="catatan" placeholder="Catatan (opsional)" 
                           class="rounded-xl border border-amber-300 bg-white text-xs text-[#2a2338] px-3 py-2 focus:ring-2 focus:ring-amber-500 focus:outline-none w-full sm:w-64">
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-amber-600 hover:bg-amber-700 shadow-sm transition-all duration-200">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>Verifikasi Pengembalian</span>
                    </button>
                </form>
            </div>
        @endif

        <!-- Informasi Verifikasi (jika sudah) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @if ($peminjaman->isSudahDiverifikasiSerahTerima())
                <div class="p-4 rounded-xl bg-[#fbf7f0] border border-[#f5ede1] space-y-1.5">
                    <div class="flex items-center gap-2 text-[#191333]">
                        <i data-lucide="clipboard-check" class="w-4 h-4 text-emerald-600"></i>
                        <h3 class="text-xs font-bold uppercase tracking-wider">Catatan Serah Terima</h3>
                    </div>
                    <div class="text-xs text-[#7a7189] space-y-1 pt-1">
                        <p>Kondisi: <span class="font-semibold text-[#191333] uppercase">{{ $peminjaman->kondisi_serah_terima }}</span></p>
                        @if ($peminjaman->catatan_serah_terima)
                            <p>Catatan: <span class="text-[#2a2338]">{{ $peminjaman->catatan_serah_terima }}</span></p>
                        @endif
                        <p>Diverifikasi oleh: <span class="text-[#2a2338] font-medium">{{ $peminjaman->verifikasiSerahTerimaBy->nama ?? 'Tidak diketahui' }}</span></p>
                        <p>Waktu: <span class="text-[#2a2338] font-medium">{{ $peminjaman->verifikasi_serah_terima_at->format('d/m/Y H:i') }}</span></p>
                    </div>
                </div>
            @endif

            @if ($peminjaman->isSudahDiverifikasiPengembalian())
                <div class="p-4 rounded-xl bg-[#fbf7f0] border border-[#f5ede1] space-y-1.5">
                    <div class="flex items-center gap-2 text-[#191333]">
                        <i data-lucide="archive-restore" class="w-4 h-4 text-amber-600"></i>
                        <h3 class="text-xs font-bold uppercase tracking-wider">Catatan Pengembalian</h3>
                    </div>
                    <div class="text-xs text-[#7a7189] space-y-1 pt-1">
                        <p>Kondisi: <span class="font-semibold text-[#191333] uppercase">{{ $peminjaman->kondisi_pengembalian }}</span></p>
                        @if ($peminjaman->catatan_pengembalian)
                            <p>Catatan: <span class="text-[#2a2338]">{{ $peminjaman->catatan_pengembalian }}</span></p>
                        @endif
                        <p>Diverifikasi oleh: <span class="text-[#2a2338] font-medium">{{ $peminjaman->verifikasiPengembalianBy->nama ?? 'Tidak diketahui' }}</span></p>
                        <p>Waktu: <span class="text-[#2a2338] font-medium">{{ $peminjaman->verifikasi_pengembalian_at->format('d/m/Y H:i') }}</span></p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Riwayat Status Timeline -->
        <div class="pt-6 border-t border-[#f5ede1]">
            <h3 class="text-xs font-bold uppercase tracking-wider text-[#191333] mb-4 flex items-center gap-2">
                <i data-lucide="history" class="w-4 h-4 text-[#f2723b]"></i>
                <span>Riwayat Status</span>
            </h3>
            <x-status-timeline :logs="$peminjaman->statusLogs()->with('actor')->orderBy('created_at', 'asc')->get()" />
        </div>
    </div>
</div>
@endsection