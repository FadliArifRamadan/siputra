@extends('layouts.teknisi')

@section('title', 'Detail Tugas')

@section('content')
<div class="py-4 sm:py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Header Action Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-[#fbf7f0] border border-[#ecdfd0] rounded-xl text-[#f2723b]">
                    <i data-lucide="wrench" class="w-6 h-6"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold font-heading text-[#2a2338]">Detail Penanganan Tugas</h1>
                    <p class="text-xs text-[#7a7189]">ID Penugasan #{{ $penugasan->id }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('teknisi.tugas.index') }}"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#7a7189] hover:text-[#191333] transition-colors bg-[#fbf7f0] border border-[#ecdfd0] px-3.5 py-2 rounded-xl">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
                </a>
                
                {{-- Hanya tampilkan tombol aksi jika laporan belum selesai --}}
                @if ($penugasan->pengajuanKerusakan->status_laporan !== 'selesai')
                <a href="{{ route('teknisi.tugas.close', $penugasan) }}"
                    class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-sm transition">
                    <i data-lucide="check-circle-2" class="w-4 h-4"></i> Selesaikan Tugas
                </a>
                @endif
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

        @if ($penugasan->pengajuanKerusakan->fasilitas &&
        $penugasan->pengajuanKerusakan->fasilitas->status_kondisi === 'baik' &&
        $penugasan->pengajuanKerusakan->status_laporan !== 'selesai')
        <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-600 mr-3"></i>
                <div>
                    <p class="font-semibold text-green-800">✅ Fasilitas Sudah Baik</p>
                    <p class="text-sm text-green-600">
                        Status fasilitas "{{ $penugasan->pengajuanKerusakan->fasilitas->nama_fasilitas }}" sudah "baik".
                        Laporan ini akan otomatis selesai.
                    </p>
                    <form action="{{ route('teknisi.tugas.close', $penugasan) }}" method="GET" class="mt-2">
                        <button type="submit" class="bg-green-600 text-white px-4 py-1 rounded-md hover:bg-green-700 transition text-sm">
                            Tutup Laporan (tanpa bukti)
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif

        {{-- Tampilkan peringatan jika laporan sudah selesai (Dari Code 2) --}}
        @if ($penugasan->pengajuanKerusakan->status_laporan === 'selesai')
        <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-600 mr-3"></i>
                <div>
                    <p class="font-semibold text-green-800">✅ Laporan Sudah Selesai</p>
                    <p class="text-sm text-green-600">
                        Laporan ini sudah selesai karena status fasilitas sudah "baik". 
                        Tidak perlu tindakan lebih lanjut.
                    </p>
                    <a href="{{ route('teknisi.laporan-kerusakan.show', $penugasan->pengajuan_id) }}" 
                       class="inline-block mt-2 text-sm text-green-700 hover:underline font-medium">
                        Lihat Detail Laporan &rarr;
                    </a>
                </div>
            </div>
        </div>
        @endif

        <div class="bg-white p-4 rounded-2xl border border-[#ecdfd0] shadow-sm flex flex-wrap items-center justify-between gap-4">
            <!-- Status Badge -->
            <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full uppercase tracking-wider
                {{ $penugasan->pengajuanKerusakan->status_laporan === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                {{ $penugasan->pengajuanKerusakan->status_laporan === 'diterima' ? 'bg-blue-100 text-blue-800' : '' }}
                {{ $penugasan->pengajuanKerusakan->status_laporan === 'dikerjakan' ? 'bg-purple-100 text-purple-800' : '' }}
                {{ $penugasan->pengajuanKerusakan->status_laporan === 'menunggu_parts' ? 'bg-orange-100 text-orange-800' : '' }}
                {{ $penugasan->pengajuanKerusakan->status_laporan === 'diproses' ? 'bg-blue-100 text-blue-800' : '' }}
                {{ $penugasan->pengajuanKerusakan->status_laporan === 'selesai' ? 'bg-green-100 text-green-800' : '' }}">
                Status: {{ ucfirst($penugasan->pengajuanKerusakan->status_laporan) }}
            </span>

            {{-- Hanya tampilkan form Update Progress jika laporan belum selesai --}}
            @if ($penugasan->pengajuanKerusakan->status_laporan !== 'selesai' && !empty($progressOptions))
            <form action="{{ route('teknisi.tugas.update-progress', $penugasan) }}" method="POST" class="inline-block">
                @csrf
                @method('PATCH')
                <div class="flex flex-wrap items-center gap-2">
                    <select name="status_laporan" class="rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-xs">
                        <option value="">Update Progress</option>
                        @foreach ($progressOptions as $option)
                        <option value="{{ $option }}">
                            {{ ucfirst(str_replace('_', ' ', $option)) }}
                        </option>
                        @endforeach
                    </select>
                    <input type="text" name="catatan" placeholder="Catatan (opsional)"
                        class="rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-xs w-48">
                    <button type="submit" class="bg-[#f2723b] hover:bg-[#e05d25] text-white px-4 py-2 rounded-xl transition text-xs font-semibold shadow-sm">
                        Update Progress
                    </button>
                </div>
            </form>
            @endif
        </div>

        <!-- Main Info Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Information: Laporan Kerusakan -->
            <div class="bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-[#ecdfd0] text-[#191333]">
                    <i data-lucide="file-text" class="w-5 h-5 text-[#f2723b]"></i>
                    <h2 class="font-bold font-heading text-sm uppercase tracking-wider">Informasi Laporan</h2>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="flex justify-between py-1 border-b border-[#ecdfd0]/50">
                        <span class="text-[#7a7189]">Jenis Kerusakan:</span>
                        <span class="font-bold text-[#191333]">{{ $penugasan->pengajuanKerusakan->jenis_kerusakan }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-[#ecdfd0]/50">
                        <span class="text-[#7a7189]">Lokasi:</span>
                        <span class="font-bold text-[#191333]">{{ $penugasan->pengajuanKerusakan->lokasi_detail }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-[#ecdfd0]/50">
                        <span class="text-[#7a7189]">Pelapor:</span>
                        <span class="font-bold text-[#191333]">{{ $penugasan->pengajuanKerusakan->pelapor->nama ?? 'Tidak diketahui' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-[#ecdfd0]/50">
                        <span class="text-[#7a7189]">Tingkat Urgensi:</span>
                        <span class="px-2.5 py-0.5 text-[10px] font-bold uppercase rounded-xl border
                            {{ $penugasan->pengajuanKerusakan->tingkat_urgensi === 'tinggi' ? 'bg-rose-100 text-rose-800 border-rose-200' : '' }}
                            {{ $penugasan->pengajuanKerusakan->tingkat_urgensi === 'sedang' ? 'bg-amber-100 text-amber-800 border-amber-200' : '' }}
                            {{ $penugasan->pengajuanKerusakan->tingkat_urgensi === 'rendah' ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : '' }}">
                            {{ ucfirst($penugasan->pengajuanKerusakan->tingkat_urgensi) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-[#ecdfd0]/50">
                        <span class="text-[#7a7189]">Status Laporan:</span>
                        <span class="px-2.5 py-0.5 text-[10px] font-bold uppercase rounded-xl border
                            {{ $penugasan->pengajuanKerusakan->status_laporan === 'pending' ? 'bg-amber-100 text-amber-800 border-amber-200' : '' }}
                            {{ $penugasan->pengajuanKerusakan->status_laporan === 'diproses' ? 'bg-sky-100 text-sky-800 border-sky-200' : '' }}
                            {{ $penugasan->pengajuanKerusakan->status_laporan === 'selesai' ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : '' }}">
                            {{ ucfirst($penugasan->pengajuanKerusakan->status_laporan) }}
                        </span>
                    </div>
                    @if ($penugasan->pengajuanKerusakan->fasilitas)
                    <div class="flex justify-between py-1">
                        <span class="text-[#7a7189]">Fasilitas Terkait:</span>
                        <span class="font-bold text-[#191333]">
                            {{ $penugasan->pengajuanKerusakan->fasilitas->nama_fasilitas }}
                            <span class="text-[10px] text-[#7a7189] font-normal">({{ ucfirst(str_replace('_', ' ', $penugasan->pengajuanKerusakan->fasilitas->status_kondisi ?? 'N/A')) }})</span>
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Information: Detail Penugasan -->
            <div class="bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-[#ecdfd0] text-[#191333]">
                    <i data-lucide="user-check" class="w-5 h-5 text-[#f2723b]"></i>
                    <h2 class="font-bold font-heading text-sm uppercase tracking-wider">Informasi Penugasan</h2>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="flex justify-between py-1 border-b border-[#ecdfd0]/50">
                        <span class="text-[#7a7189]">IT Support Penanggung Jawab:</span>
                        <span class="font-bold text-[#191333]">{{ $penugasan->teknisi->nama ?? 'Tidak diketahui' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-[#ecdfd0]/50">
                        <span class="text-[#7a7189]">Jadwal Penanganan:</span>
                        <span class="font-bold text-[#191333]">{{ $penugasan->jadwal_penanganan->format('d/m/Y H:i') }} WIB</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-[#ecdfd0]/50">
                        <span class="text-[#7a7189]">Tanggal Ditugaskan:</span>
                        <span class="font-bold text-[#191333]">{{ $penugasan->created_at->format('d/m/Y H:i') }} WIB</span>
                    </div>
                    @if ($penugasan->instruksi_kerja)
                    <div class="pt-2">
                        <span class="text-[#7a7189] block mb-1">Instruksi Kerja Admin:</span>
                        <div class="p-3 bg-[#fbf7f0] rounded-xl border border-[#ecdfd0] font-medium text-[#191333]">
                            {{ $penugasan->instruksi_kerja }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- Description Card -->
        <div class="bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm space-y-2">
            <h3 class="text-xs font-bold text-[#2a2338] uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="align-left" class="w-4 h-4 text-[#f2723b]"></i> Deskripsi Kerusakan Pelapor
            </h3>
            <p class="text-xs sm:text-sm text-[#2a2338] bg-[#fbf7f0] p-4 rounded-xl border border-[#ecdfd0] leading-relaxed">
                {{ $penugasan->pengajuanKerusakan->deskripsi }}
            </p>
        </div>

        <!-- Initial Damage Photo -->
        @if ($penugasan->pengajuanKerusakan->foto_kerusakan)
        <div class="bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm space-y-3">
            <h3 class="text-xs font-bold text-[#2a2338] uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="image" class="w-4 h-4 text-[#f2723b]"></i> Foto Bukti Kerusakan Awal
            </h3>
            <div class="p-3 bg-[#fbf7f0] rounded-2xl border border-[#ecdfd0] inline-block">
                <img src="{{ Storage::url($penugasan->pengajuanKerusakan->foto_kerusakan) }}"
                    alt="Foto Kerusakan"
                    class="rounded-xl max-h-80 object-cover shadow-sm">
            </div>
        </div>
        @endif

        <!-- Repair Proof Section (if available) -->
        @if ($penugasan->buktiPerbaikan)
        <div class="bg-emerald-50/50 p-6 rounded-2xl border border-emerald-200/80 shadow-sm space-y-4">
            <div class="flex items-center gap-2 text-emerald-900 pb-3 border-b border-emerald-200">
                <i data-lucide="shield-check" class="w-5 h-5 text-emerald-600"></i>
                <h3 class="font-bold font-heading text-sm uppercase tracking-wider">Hasil Bukti Perbaikan</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                <div class="space-y-3">
                    <div>
                        <span class="text-[#7a7189] font-medium block mb-1">Keterangan/Catatan Teknisi:</span>
                        <div class="p-3 bg-white rounded-xl border border-emerald-200 text-[#191333] leading-relaxed">
                            {{ $penugasan->buktiPerbaikan->keterangan_selesai }}
                        </div>
                    </div>
                    <div>
                        <span class="text-[#7a7189] font-medium">Tanggal Selesai Penanganan:</span>
                        <p class="font-bold text-[#191333] mt-0.5">
                            {{ \Carbon\Carbon::parse($penugasan->buktiPerbaikan->tanggal_selesai)->format('d/m/Y H:i') }} WIB
                        </p>
                    </div>
                </div>

                @if ($penugasan->buktiPerbaikan->foto_selesai)
                <div>
                    <span class="text-[#7a7189] font-medium block mb-1">Foto Hasil Perbaikan:</span>
                    <div class="p-2 bg-white rounded-xl border border-emerald-200 inline-block">
                        <img src="{{ Storage::url($penugasan->buktiPerbaikan->foto_selesai) }}"
                            alt="Foto Perbaikan"
                            class="rounded-lg max-h-52 object-cover shadow-sm">
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Timeline Status Laporan -->
        <div class="bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-[#2a2338] uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="history" class="w-4 h-4 text-[#f2723b]"></i> Riwayat Status Laporan
            </h3>
            <div class="pt-2">
                <x-status-timeline :logs="$penugasan->pengajuanKerusakan->statusLogs()->with('actor')->orderBy('created_at', 'asc')->get()" />
            </div>
        </div>

        <!-- Timeline Status Fasilitas (jika ada) -->
        @if ($penugasan->pengajuanKerusakan->fasilitas)
        <div class="bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-[#2a2338] uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="box" class="w-4 h-4 text-[#f2723b]"></i>
                Riwayat Status Fasilitas: {{ $penugasan->pengajuanKerusakan->fasilitas->nama_fasilitas }}
            </h3>
            <div class="pt-2">
                <x-status-timeline :logs="$penugasan->pengajuanKerusakan->fasilitas->statusLogs()->with('actor')->orderBy('created_at', 'asc')->get()" />
            </div>
        </div>
        @endif

        <!-- Footer Action Buttons -->
        <div class="flex items-center justify-end gap-3 pt-4">
            {{-- Hanya tampilkan tombol jika laporan belum selesai --}}
            @if ($penugasan->pengajuanKerusakan->status_laporan !== 'selesai')
            <a href="{{ route('teknisi.tugas.close', $penugasan) }}"
                class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl text-xs font-semibold shadow-sm transition">
                <i data-lucide="check" class="w-4 h-4"></i> Selesaikan Tugas
            </a>
            @endif
            <a href="{{ route('teknisi.tugas.index') }}"
                class="px-5 py-2.5 text-xs font-semibold text-[#7a7189] hover:text-[#191333] transition bg-[#fbf7f0] border border-[#ecdfd0] rounded-xl">
                Kembali ke Daftar
            </a>
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