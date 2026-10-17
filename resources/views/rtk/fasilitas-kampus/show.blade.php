@extends('layouts.rtk')

@section('title', 'Detail Fasilitas Kampus - ' . $fasilitas->nama_fasilitas)

@section('content')
<div class="py-4 sm:py-6">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Breadcrumb & Top Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('rtk.fasilitas-kampus.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#7a7189] hover:text-[#f2723b] transition-colors mb-2">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali ke Daftar Fasilitas Kampus
                </a>
                <div class="flex items-center gap-2 flex-wrap">
                    @if ($fasilitas->lokasi && $fasilitas->lokasi->kampus)
                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold text-white shadow-sm uppercase tracking-wider" style="background: linear-gradient(135deg, var(--sunset-500), var(--sunset-400));">
                        {{ $fasilitas->lokasi->kampus }}
                    </span>
                    @endif
                    @if ($fasilitas->is_landing_page)
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        <i data-lucide="globe" class="w-3 h-3"></i> Tampil di Landing Page
                    </span>
                    @endif
                </div>
                <h1 class="text-xl sm:text-2xl font-bold font-heading text-[#2a2338] mt-1">
                    {{ $fasilitas->nama_fasilitas }}
                </h1>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('rtk.fasilitas-kampus.edit', $fasilitas) }}" class="px-4 py-2 rounded-xl bg-white border border-[#ecdfd0] text-xs font-bold text-[#2a2338] hover:border-[#f2723b] hover:text-[#f2723b] transition-colors flex items-center gap-1.5 shadow-sm">
                    <i data-lucide="edit-2" class="w-4 h-4"></i> Edit Ruangan
                </a>
                <a href="{{ route('rtk.fasilitas-kampus.download-qr', $fasilitas) }}" class="px-4 py-2 rounded-xl text-white text-xs font-bold shadow-md transition-all hover:-translate-y-0.5 flex items-center gap-1.5" style="background: linear-gradient(135deg, var(--sunset-500), var(--sunset-400));">
                    <i data-lucide="download" class="w-4 h-4"></i> Download QR Code
                </a>
            </div>
        </div>

        @if (session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-r-xl shadow-sm flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500 shrink-0"></i>
            <span class="text-xs sm:text-sm font-medium">{{ session('success') }}</span>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Kolom Kiri: Foto Ruangan & Informasi Teknis (8 cols) -->
            <div class="lg:col-span-8 space-y-6">

                <!-- Card Foto Ruangan -->
                <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm overflow-hidden">
                    <div class="relative w-full h-[320px] sm:h-[420px] bg-slate-900 overflow-hidden">
                        <img src="{{ $fasilitas->foto_url }}" alt="{{ $fasilitas->nama_fasilitas }}" onerror="this.onerror=null; this.src='{{ asset('images/hero-1.jpg') }}';" class="w-full h-full object-cover">
                        <div class="absolute bottom-4 left-4 z-10 hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-lg bg-black/60 backdrop-blur-sm border border-white/10 text-white text-xs font-medium">
                            <i data-lucide="camera" class="w-3.5 h-3.5 text-[#f2723b]"></i>
                            <span>Foto Fisik: {{ $fasilitas->nama_fasilitas }}</span>
                        </div>
                    </div>

                    <div class="p-5 sm:p-6 space-y-4">
                        <div>
                            <h2 class="text-sm font-bold uppercase tracking-wider text-[#7a7189] mb-1">Deskripsi Ruangan</h2>
                            <p class="text-xs sm:text-sm leading-relaxed text-[#2a2338]">
                                {{ $fasilitas->deskripsi ?: 'Tidak ada deskripsi khusus yang ditambahkan.' }}
                            </p>
                        </div>

                        <div class="h-px bg-[#f2ece2]"></div>

                        <!-- Fasilitas di Dalam Ruangan -->
                        <div>
                            <h2 class="text-sm font-bold uppercase tracking-wider text-[#7a7189] mb-2.5">Fasilitas di Dalam Ruangan</h2>
                            @if (count($fasilitas->fasilitas_list) > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach ($fasilitas->fasilitas_list as $fItem)
                                <span class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-[#fbf7f0] border border-[#ecdfd0] text-[#2a2338] flex items-center gap-1.5">
                                    <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-500"></i>
                                    <span>{{ $fItem }}</span>
                                </span>
                                @endforeach
                            </div>
                            @else
                            <p class="text-xs text-[#7a7189] italic">Belum ada daftar perlengkapan ruangan yang dicatat.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Card Riwayat Laporan Kerusakan Ruangan -->
                <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm p-5 sm:p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm sm:text-base font-bold font-heading text-[#2a2338] flex items-center gap-2">
                            <i data-lucide="alert-triangle" class="w-4 h-4 text-[#f2723b]"></i> Riwayat Laporan Kerusakan di Ruangan Ini
                        </h2>
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-[#fbf7f0] border border-[#ecdfd0] text-[#7a7189]">
                            {{ $fasilitas->pengajuanKerusakan->count() }} Laporan
                        </span>
                    </div>

                    @if ($fasilitas->pengajuanKerusakan->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-[#fbf7f0] text-[#7a7189] font-bold border-b border-[#ecdfd0]">
                                <tr>
                                    <th class="py-2.5 px-3">Tanggal</th>
                                    <th class="py-2.5 px-3">Pelapor</th>
                                    <th class="py-2.5 px-3">Kerusakan</th>
                                    <th class="py-2.5 px-3">Status</th>
                                    <th class="py-2.5 px-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#f2ece2]">
                                @foreach ($fasilitas->pengajuanKerusakan as $laporan)
                                <tr>
                                    <td class="py-2.5 px-3 text-[#7a7189] whitespace-nowrap">{{ $laporan->created_at->format('d/m/Y') }}</td>
                                    <td class="py-2.5 px-3 font-semibold text-[#2a2338]">{{ $laporan->pelapor->name ?? '-' }}</td>
                                    <td class="py-2.5 px-3 text-[#2a2338] max-w-xs truncate">{{ $laporan->jenis_kerusakan ?? $laporan->deskripsi }}</td>
                                    <td class="py-2.5 px-3 whitespace-nowrap">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $laporan->status_laporan === 'selesai' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ ucfirst($laporan->status_laporan) }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3 text-right whitespace-nowrap">
                                        <a href="{{ route('rtk.laporan-kerusakan.show', $laporan) }}" class="text-[#f2723b] hover:underline font-bold">Detail</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-xs text-[#7a7189] italic text-center py-4">Belum ada laporan kerusakan yang tercatat untuk ruangan ini.</p>
                    @endif
                </div>

            </div>

            <!-- Kolom Kanan: Frame QR Code & Cetak Label (4 cols) -->
            <div class="lg:col-span-4 space-y-6">

                <!-- Card QR Code Fisik Ruangan -->
                <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm p-5 sm:p-6 text-center space-y-4">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#f2723b]">Stiker Pintu Ruangan</span>
                        <h3 class="text-base font-bold font-heading text-[#2a2338] mt-0.5">QR Code Pelaporan</h3>
                        <p class="text-[11px] text-[#7a7189] mt-1 leading-relaxed">
                            Cetak dan tempelkan QR Code ini di dekat pintu ruangan agar mahasiswa dan dosen dapat langsung scan jika ada fasilitas bermasalah.
                        </p>
                    </div>

                    <!-- Frame QR Display -->
                    <div id="print-area" class="p-5 bg-white border-2 border-[#191333] rounded-2xl mx-auto max-w-[240px] shadow-sm space-y-3">
                        <div class="border-b border-[#191333] pb-2">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#191333] block">UCIC FACILITY</span>
                            <span class="text-[9px] font-semibold text-[#7a7189] block">Lapor Cepat Kerusakan Ruang</span>
                        </div>

                        <!-- Gambar SVG QR -->
                        <div class="flex justify-center py-1">
                            @if ($fasilitas->qr_code_path)
                            <img src="{{ $fasilitas->qr_code_path }}" alt="QR Code {{ $fasilitas->nama_fasilitas }}" class="w-40 h-40">
                            @else
                            <div class="w-40 h-40 bg-[#fbf7f0] flex items-center justify-center text-xs text-[#7a7189]">
                                QR Code Loading...
                            </div>
                            @endif
                        </div>

                        <div class="border-t border-[#191333] pt-2">
                            <span class="text-xs font-bold text-[#191333] block truncate">{{ $fasilitas->nama_fasilitas }}</span>
                            <span class="text-[9px] font-medium text-[#7a7189] block">{{ $fasilitas->lokasi_display }}</span>
                        </div>
                    </div>

                    <!-- Action QR Buttons -->
                    <div class="space-y-2 pt-2">
                        <a href="{{ route('rtk.fasilitas-kampus.download-qr', $fasilitas) }}" class="w-full py-2.5 px-4 rounded-xl text-white text-xs font-bold shadow-md transition-all flex items-center justify-center gap-2" style="background: linear-gradient(135deg, var(--sunset-500), var(--sunset-400));">
                            <i data-lucide="download" class="w-4 h-4"></i> Download File SVG
                        </a>
                        <button onclick="window.print()" type="button" class="w-full py-2 px-4 rounded-xl bg-[#fbf7f0] hover:bg-[#ecdfd0] border border-[#ecdfd0] text-xs font-bold text-[#2a2338] transition-colors flex items-center justify-center gap-2">
                            <i data-lucide="printer" class="w-4 h-4"></i> Cetak Stiker QR
                        </button>
                        <form action="{{ route('rtk.fasilitas-kampus.generate-qr', $fasilitas) }}" method="POST" class="pt-1">
                            @csrf
                            <button type="submit" class="text-[11px] text-[#7a7189] hover:text-[#f2723b] font-medium transition-colors">
                                Generate Ulang QR Code
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Card Rincian Status Lokasi -->
                <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm p-5 space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-[#7a7189]">Informasi Master Data</h3>
                    
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between py-1 border-b border-[#f2ece2]">
                            <span class="text-[#7a7189]">Kondisi</span>
                            <span class="font-bold capitalize text-emerald-600">{{ str_replace('_', ' ', $fasilitas->status_kondisi) }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-[#f2ece2]">
                            <span class="text-[#7a7189]">Kampus</span>
                            <span class="font-semibold text-[#2a2338]">{{ $fasilitas->lokasi->kampus ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-[#f2ece2]">
                            <span class="text-[#7a7189]">Lantai</span>
                            <span class="font-semibold text-[#2a2338]">Lantai {{ $fasilitas->lokasi->lantai ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-[#f2ece2]">
                            <span class="text-[#7a7189]">Kode Ruang</span>
                            <span class="font-bold text-[#2a2338]">{{ $fasilitas->lokasi->kode_ruangan ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span class="text-[#7a7189]">Terdaftar Sejak</span>
                            <span class="text-[#2a2338]">{{ $fasilitas->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>
@endsection
