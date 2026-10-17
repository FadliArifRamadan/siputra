@extends('layouts.rtk')

@section('title', 'Tambah Lokasi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Navigation Header -->
    <div class="flex items-center justify-between bg-white p-6 rounded-2xl shadow-sm border border-[#f5ede1]">
        <div class="flex items-center gap-3">
            <div class="p-3 rounded-xl bg-[#f2723b]/10 text-[#f2723b]">
                <i data-lucide="map-pin-plus" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold font-heading text-[#191333]">Tambah Lokasi</h1>
                <p class="text-xs text-[#7a7189] mt-0.5">Tambahkan data lokasi atau ruangan baru ke dalam sistem</p>
            </div>
        </div>
        <a href="{{ route('rtk.lokasi.index') }}" 
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-[#2a2338] bg-[#f5ede1]/70 hover:bg-[#f5ede1] border border-[#f5ede1] transition-all duration-200">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali</span>
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-[#f5ede1] p-6 sm:p-8">
        <form action="{{ route('rtk.lokasi.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Grid 1: Kampus & Lantai -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kampus -->
                <div class="space-y-1.5">
                    <label for="kampus" class="block text-xs font-bold uppercase tracking-wider text-[#191333]">
                        Kampus <span class="text-rose-500">*</span>
                    </label>
                    <select name="kampus" 
                            id="kampus"
                            class="w-full rounded-xl border border-[#f5ede1] bg-[#fbf7f0]/40 text-xs text-[#2a2338] px-3.5 py-2.5 focus:border-[#f2723b] focus:ring-2 focus:ring-[#f2723b]/20 focus:outline-none transition-all duration-200 @error('kampus') border-rose-500 bg-rose-50/30 @enderror"
                            required>
                        <option value="">Pilih Kampus</option>
                        <option value="Kampus 1" {{ old('kampus') == 'Kampus 1' ? 'selected' : '' }}>Kampus 1</option>
                        <option value="Kampus 2" {{ old('kampus') == 'Kampus 2' ? 'selected' : '' }}>Kampus 2</option>
                    </select>
                    @error('kampus')
                        <p class="text-xs text-rose-600 font-medium flex items-center gap-1 mt-1">
                            <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <!-- Lantai -->
                <div class="space-y-1.5">
                    <label for="lantai" class="block text-xs font-bold uppercase tracking-wider text-[#191333]">
                        Lantai <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="lantai" 
                           id="lantai"
                           value="{{ old('lantai') }}"
                           class="w-full rounded-xl border border-[#f5ede1] bg-[#fbf7f0]/40 text-xs text-[#2a2338] px-3.5 py-2.5 focus:border-[#f2723b] focus:ring-2 focus:ring-[#f2723b]/20 focus:outline-none transition-all duration-200 @error('lantai') border-rose-500 bg-rose-50/30 @enderror"
                           placeholder="Contoh: Lantai 1"
                           required>
                    @error('lantai')
                        <p class="text-xs text-rose-600 font-medium flex items-center gap-1 mt-1">
                            <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>
            </div>

            <!-- Grid 2: Kode Ruangan & Nama Ruangan -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kode Ruangan -->
                <div class="space-y-1.5">
                    <label for="kode_ruangan" class="block text-xs font-bold uppercase tracking-wider text-[#191333]">
                        Kode Ruangan
                    </label>
                    <input type="text" 
                           name="kode_ruangan" 
                           id="kode_ruangan"
                           value="{{ old('kode_ruangan') }}"
                           class="w-full rounded-xl border border-[#f5ede1] bg-[#fbf7f0]/40 text-xs text-[#2a2338] px-3.5 py-2.5 focus:border-[#f2723b] focus:ring-2 focus:ring-[#f2723b]/20 focus:outline-none transition-all duration-200 @error('kode_ruangan') border-rose-500 bg-rose-50/30 @enderror"
                           placeholder="Contoh: A.1">
                    @error('kode_ruangan')
                        <p class="text-xs text-rose-600 font-medium flex items-center gap-1 mt-1">
                            <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <!-- Nama Ruangan -->
                <div class="space-y-1.5">
                    <label for="nama_ruangan" class="block text-xs font-bold uppercase tracking-wider text-[#191333]">
                        Nama Ruangan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="nama_ruangan" 
                           id="nama_ruangan"
                           value="{{ old('nama_ruangan') }}"
                           class="w-full rounded-xl border border-[#f5ede1] bg-[#fbf7f0]/40 text-xs text-[#2a2338] px-3.5 py-2.5 focus:border-[#f2723b] focus:ring-2 focus:ring-[#f2723b]/20 focus:outline-none transition-all duration-200 @error('nama_ruangan') border-rose-500 bg-rose-50/30 @enderror"
                           placeholder="Contoh: Ruang Rapat, Auditorium"
                           required>
                    @error('nama_ruangan')
                        <p class="text-xs text-rose-600 font-medium flex items-center gap-1 mt-1">
                            <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>
            </div>

            <!-- Keterangan -->
            <div class="space-y-1.5">
                <label for="keterangan" class="block text-xs font-bold uppercase tracking-wider text-[#191333]">
                    Keterangan
                </label>
                <textarea name="keterangan" 
                          id="keterangan"
                          rows="3"
                          class="w-full rounded-xl border border-[#f5ede1] bg-[#fbf7f0]/40 text-xs text-[#2a2338] p-3.5 focus:border-[#f2723b] focus:ring-2 focus:ring-[#f2723b]/20 focus:outline-none transition-all duration-200 @error('keterangan') border-rose-500 bg-rose-50/30 @enderror"
                          placeholder="Catatan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                @error('keterangan')
                    <p class="text-xs text-rose-600 font-medium flex items-center gap-1 mt-1">
                        <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 border-t border-[#f5ede1] flex items-center justify-between">
                <a href="{{ route('rtk.lokasi.index') }}" 
                   class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#7a7189] hover:text-[#191333] transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    <span>Kembali</span>
                </a>
                <button type="submit" 
                        class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-xs font-semibold text-white shadow-md hover:shadow-lg transition-all duration-200"
                        style="background-color: var(--sunset-500);">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Simpan Lokasi</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection