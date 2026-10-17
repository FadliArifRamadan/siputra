@extends('layouts.rtk')

@section('title', 'Manajemen Fasilitas Kampus')

@section('content')
<div class="py-4 sm:py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Header Banner -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold font-heading text-[#2a2338] flex items-center gap-2.5">
                    <div class="p-2 rounded-xl text-white" style="background: linear-gradient(135deg, var(--sunset-500), var(--sunset-400));">
                        <i data-lucide="building-2" class="w-5 h-5"></i>
                    </div>
                    <span>Manajemen Fasilitas Kampus</span>
                </h1>
                <p class="text-xs sm:text-sm text-[#7a7189] mt-1">
                    Kelola data ruangan, gedung, dan sarana fisik kampus. Terhubung langsung dengan <strong>Landing Page</strong> dan dilengkapi <strong>QR Code</strong> pelaporan kerusakan.
                </p>
            </div>
            <a href="{{ route('rtk.fasilitas-kampus.create') }}" class="inline-flex items-center justify-center gap-2 text-white px-5 py-2.5 rounded-xl font-semibold text-xs transition-all duration-150 shadow-md shrink-0 hover:-translate-y-0.5" style="background: linear-gradient(135deg, var(--sunset-500), var(--sunset-400));">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Fasilitas Kampus
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

        <!-- Summary Stats Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white p-4 rounded-2xl border border-[#ecdfd0] shadow-sm">
                <span class="text-[11px] font-bold uppercase tracking-wider text-[#7a7189] block">Total Ruangan</span>
                <span class="text-2xl font-bold font-heading text-[#2a2338] mt-1 block">{{ $totalKampus }}</span>
                <span class="text-[11px] text-[#7a7189] mt-0.5 block">Fasilitas terdata</span>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-[#ecdfd0] shadow-sm">
                <span class="text-[11px] font-bold uppercase tracking-wider text-[#7a7189] block">Kampus 1</span>
                <span class="text-2xl font-bold font-heading text-[#2a2338] mt-1 block">{{ $totalKampus1 }}</span>
                <span class="text-[11px] text-[#7a7189] mt-0.5 block">Jl. Kesambi</span>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-[#ecdfd0] shadow-sm">
                <span class="text-[11px] font-bold uppercase tracking-wider text-[#7a7189] block">Kampus 2</span>
                <span class="text-2xl font-bold font-heading text-[#2a2338] mt-1 block">{{ $totalKampus2 }}</span>
                <span class="text-[11px] text-[#7a7189] mt-0.5 block">Gedung Baru</span>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-[#ecdfd0] shadow-sm">
                <span class="text-[11px] font-bold uppercase tracking-wider text-[#7a7189] block">Landing Page</span>
                <span class="text-2xl font-bold font-heading text-[#f2723b] mt-1 block">{{ $totalLandingPage }}</span>
                <span class="text-[11px] text-[#7a7189] mt-0.5 block">Aktif ditampilkan</span>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm p-4 sm:p-5">
            <form method="GET" action="{{ route('rtk.fasilitas-kampus.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                    <div>
                        <label class="block text-xs font-bold text-[#2a2338] mb-1">Cari Ruangan</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / kode ruangan / fasilitas..." class="w-full rounded-xl border-[#ecdfd0] text-xs font-medium text-[#2a2338] focus:border-[#f2723b] focus:ring-[#f2723b]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#2a2338] mb-1">Kampus</label>
                        <select name="kampus" class="w-full rounded-xl border-[#ecdfd0] text-xs font-medium text-[#2a2338] focus:border-[#f2723b] focus:ring-[#f2723b]">
                            <option value="">Semua Kampus</option>
                            <option value="Kampus 1" {{ request('kampus') == 'Kampus 1' ? 'selected' : '' }}>Kampus 1 (Jl. Kesambi)</option>
                            <option value="Kampus 2" {{ request('kampus') == 'Kampus 2' ? 'selected' : '' }}>Kampus 2 (Gedung Baru)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#2a2338] mb-1">Status Kondisi</label>
                        <select name="status_kondisi" class="w-full rounded-xl border-[#ecdfd0] text-xs font-medium text-[#2a2338] focus:border-[#f2723b] focus:ring-[#f2723b]">
                            <option value="">Semua Status</option>
                            <option value="baik" {{ request('status_kondisi') == 'baik' ? 'selected' : '' }}>Baik</option>
                            <option value="rusak_ringan" {{ request('status_kondisi') == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                            <option value="rusak_berat" {{ request('status_kondisi') == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                            <option value="maintenance" {{ request('status_kondisi') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 bg-[#2a2338] hover:bg-[#191333] text-white py-2.5 px-4 rounded-xl text-xs font-bold transition-colors flex items-center justify-center gap-1.5 shadow-sm">
                            <i data-lucide="filter" class="w-3.5 h-3.5"></i> Filter
                        </button>
                        @if (request()->hasAny(['search', 'kampus', 'status_kondisi', 'landing_page']))
                        <a href="{{ route('rtk.fasilitas-kampus.index') }}" class="bg-[#fbf7f0] hover:bg-[#ecdfd0] text-[#7a7189] py-2.5 px-3 rounded-xl text-xs font-semibold transition-colors border border-[#ecdfd0]">
                            Reset
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Daftar Fasilitas Kampus Cards -->
        @if ($fasilitas->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ($fasilitas as $item)
            <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm overflow-hidden flex flex-col justify-between hover:shadow-md transition-shadow group">
                <div>
                    <!-- Foto Ruangan / Thumbnail -->
                    <div class="relative h-44 w-full bg-slate-900 overflow-hidden">
                        <img src="{{ $item->foto_url }}" alt="{{ $item->nama_fasilitas }}" onerror="this.onerror=null; this.src='{{ asset('images/hero-1.jpg') }}';" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        
                        <!-- Badges Kampus & Status -->
                        <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
                            @if ($item->lokasi && $item->lokasi->kampus)
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold text-white shadow-sm" style="background: linear-gradient(135deg, var(--sunset-500), var(--sunset-400));">
                                {{ $item->lokasi->kampus }}
                            </span>
                            @endif
                            @if ($item->lokasi && $item->lokasi->lantai)
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-black/60 backdrop-blur-sm text-white">
                                Lt. {{ $item->lokasi->lantai }}
                            </span>
                            @endif
                        </div>

                        <!-- Badge Landing Page -->
                        <div class="absolute top-3 right-3">
                            @if ($item->is_landing_page)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-emerald-500 text-white shadow-sm" title="Tampil di Landing Page">
                                <i data-lucide="globe" class="w-3 h-3"></i> Landing Page
                            </span>
                            @endif
                        </div>

                        <!-- Floating Code -->
                        @if ($item->lokasi && $item->lokasi->kode_ruangan)
                        <div class="absolute bottom-3 right-3 px-2 py-0.5 rounded-md text-[10px] font-bold bg-white/90 text-[#191333] shadow-sm">
                            {{ $item->lokasi->kode_ruangan }}
                        </div>
                        @endif
                    </div>

                    <!-- Body Info -->
                    <div class="p-5 space-y-3">
                        <div>
                            <h3 class="text-base font-bold font-heading text-[#2a2338] line-clamp-1 group-hover:text-[#f2723b] transition-colors">
                                {{ $item->nama_fasilitas }}
                            </h3>
                            <p class="text-xs text-[#7a7189] mt-0.5 line-clamp-1">
                                {{ $item->lokasi_display }}
                            </p>
                        </div>

                        @if ($item->deskripsi)
                        <p class="text-xs text-[#7a7189] line-clamp-2 leading-relaxed">
                            {{ $item->deskripsi }}
                        </p>
                        @endif

                        <!-- Fasilitas Chips -->
                        @if (count($item->fasilitas_list) > 0)
                        <div class="flex flex-wrap gap-1 pt-1">
                            @foreach (array_slice($item->fasilitas_list, 0, 3) as $fItem)
                            <span class="text-[10px] font-medium px-2 py-0.5 rounded-md bg-[#fbf7f0] border border-[#ecdfd0] text-[#2a2338]">
                                {{ $fItem }}
                            </span>
                            @endforeach
                            @if (count($item->fasilitas_list) > 3)
                            <span class="text-[10px] font-bold text-[#f2723b] self-center">
                                +{{ count($item->fasilitas_list) - 3 }} lainnya
                            </span>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Footer Card / Action Buttons -->
                <div class="p-4 border-t border-[#f2ece2] bg-[#fdfaf6] flex items-center justify-between gap-2">
                    <div class="flex items-center gap-1.5">
                        <span class="inline-block w-2 h-2 rounded-full {{ $item->status_kondisi === 'baik' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                        <span class="text-[11px] font-semibold text-[#7a7189] capitalize">
                            {{ str_replace('_', ' ', $item->status_kondisi) }}
                        </span>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('rtk.fasilitas-kampus.download-qr', $item) }}" title="Download QR Code Ruangan" class="p-1.5 rounded-lg bg-white border border-[#ecdfd0] text-[#2a2338] hover:text-[#f2723b] hover:border-[#f2723b] transition-colors shadow-2xl">
                            <i data-lucide="qr-code" class="w-4 h-4"></i>
                        </a>
                        <a href="{{ route('rtk.fasilitas-kampus.show', $item) }}" title="Detail & QR Code" class="p-1.5 rounded-lg bg-white border border-[#ecdfd0] text-[#2a2338] hover:text-[#f2723b] transition-colors">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </a>
                        <a href="{{ route('rtk.fasilitas-kampus.edit', $item) }}" title="Edit Fasilitas" class="p-1.5 rounded-lg bg-white border border-[#ecdfd0] text-[#2a2338] hover:text-amber-600 transition-colors">
                            <i data-lucide="edit-2" class="w-4 h-4"></i>
                        </a>
                        <form action="{{ route('rtk.fasilitas-kampus.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus fasilitas kampus ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Hapus" class="p-1.5 rounded-lg bg-white border border-[#ecdfd0] text-rose-500 hover:bg-rose-50 transition-colors">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $fasilitas->links() }}
        </div>
        @else
        <!-- Empty State -->
        <div class="bg-white rounded-2xl border border-[#ecdfd0] p-12 text-center shadow-sm space-y-4">
            <div class="w-16 h-16 rounded-full bg-[#fbf7f0] border border-[#ecdfd0] flex items-center justify-center mx-auto text-[#f2723b]">
                <i data-lucide="building" class="w-8 h-8"></i>
            </div>
            <div class="max-w-md mx-auto">
                <h3 class="text-base font-bold font-heading text-[#2a2338]">Belum Ada Data Fasilitas Kampus</h3>
                <p class="text-xs text-[#7a7189] mt-1 leading-relaxed">
                    Mulai daftarkan ruangan kelas, laboratorium, ruang dosen, atau fasilitas fisik lainnya di Kampus 1 dan Kampus 2. Data ini akan otomatis tampil di Landing Page dan memiliki QR Code.
                </p>
            </div>
            <div>
                <a href="{{ route('rtk.fasilitas-kampus.create') }}" class="inline-flex items-center gap-2 text-white px-5 py-2.5 rounded-xl font-semibold text-xs transition-all shadow-md hover:-translate-y-0.5" style="background: linear-gradient(135deg, var(--sunset-500), var(--sunset-400));">
                    <i data-lucide="plus" class="w-4 h-4"></i> Tambah Fasilitas Kampus Pertama
                </a>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
