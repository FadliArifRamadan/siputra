@extends('layouts.rtk')

@section('title', 'Edit Fasilitas')

@section('content')
<div class="py-4 sm:py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Header Banner -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold font-heading text-[#2a2338] flex items-center gap-2">
                    <i data-lucide="edit-3" class="w-6 h-6 text-[#f2723b]"></i> Edit Fasilitas
                </h1>
                <p class="text-xs sm:text-sm text-[#7a7189] mt-1">
                    Perbarui informasi, spesifikasi, dan status fisik dari fasilitas kampus.
                </p>
            </div>
            <a href="{{ route('rtk.fasilitas.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold bg-[#fbf7f0] text-[#2a2338] border border-[#ecdfd0] hover:bg-white hover:border-[#f2723b] transition-all shrink-0">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm overflow-hidden p-6 sm:p-8">
            <form action="{{ route('rtk.fasilitas.update', $fasilitas) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Fasilitas -->
                    <div class="md:col-span-2">
                        <label for="nama_fasilitas" class="block text-xs font-bold text-[#2a2338] uppercase tracking-wider mb-2">
                            Nama Fasilitas <span class="text-rose-500">*</span>
                        </label>
                        <input type="text"
                            name="nama_fasilitas"
                            id="nama_fasilitas"
                            value="{{ old('nama_fasilitas', $fasilitas->nama_fasilitas) }}"
                            class="w-full rounded-xl border-[#ecdfd0] text-xs sm:text-sm text-[#2a2338] focus:border-[#f2723b] focus:ring-[#f2723b] shadow-sm @error('nama_fasilitas') border-rose-500 focus:border-rose-500 focus:ring-rose-500 @enderror"
                            required>
                        @error('nama_fasilitas')
                        <p class="mt-1 text-xs text-rose-500 font-medium flex items-center gap-1">
                            <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <!-- Kategori -->
                    <div>
                        <label for="kategori" class="block text-xs font-bold text-[#2a2338] uppercase tracking-wider mb-2">
                            Kategori <span class="text-rose-500">*</span>
                        </label>
                        <select name="kategori"
                            id="kategori"
                            class="w-full rounded-xl border-[#ecdfd0] text-xs sm:text-sm text-[#2a2338] focus:border-[#f2723b] focus:ring-[#f2723b] shadow-sm @error('kategori') border-rose-500 focus:border-rose-500 focus:ring-rose-500 @enderror"
                            required>
                            <option value="sarana" {{ old('kategori', $fasilitas->kategori) === 'sarana' ? 'selected' : '' }}>Sarana (Barang)</option>
                            <option value="prasarana" {{ old('kategori', $fasilitas->kategori) === 'prasarana' ? 'selected' : '' }}>Prasarana (Ruangan)</option>
                        </select>
                        @error('kategori')
                        <p class="mt-1 text-xs text-rose-500 font-medium flex items-center gap-1">
                            <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <!-- Lokasi Detail -->
                    <div class="mb-4">
                        <label for="lokasi_id" class="block text-sm font-medium text-gray-700">
                            Lokasi <span class="text-red-500">*</span>
                        </label>
                        <select name="lokasi_id"
                            id="lokasi_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('lokasi_id') border-red-500 @enderror"
                            required>
                            <option value="">-- Pilih Lokasi --</option>
                            @foreach ($lokasi as $kampus => $items)
                            <optgroup label="{{ $kampus }}">
                                @foreach ($items as $item)
                                <option value="{{ $item->id }}" {{ old('lokasi_id', $fasilitas->lokasi_id) == $item->id ? 'selected' : '' }}>
                                    {{ $item->lantai }} - {{ $item->kode_ruangan }}: {{ $item->nama_ruangan }}
                                </option>
                                @endforeach
                            </optgroup>
                            @endforeach
                        </select>
                        @error('lokasi_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Stok Total -->
                    <div>
                        <label for="stok_total" class="block text-xs font-bold text-[#2a2338] uppercase tracking-wider mb-2">
                            Stok Total <span class="text-rose-500">*</span>
                        </label>
                        <input type="number"
                            name="stok_total"
                            id="stok_total"
                            value="{{ old('stok_total', $fasilitas->stok_total) }}"
                            min="1"
                            class="w-full rounded-xl border-[#ecdfd0] text-xs sm:text-sm text-[#2a2338] focus:border-[#f2723b] focus:ring-[#f2723b] shadow-sm @error('stok_total') border-rose-500 focus:border-rose-500 focus:ring-rose-500 @enderror"
                            required>
                        @error('stok_total')
                        <p class="mt-1 text-xs text-rose-500 font-medium flex items-center gap-1">
                            <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <!-- Status Kondisi -->
                    <div>
                        <label for="status_kondisi" class="block text-xs font-bold text-[#2a2338] uppercase tracking-wider mb-2">
                            Status Kondisi <span class="text-rose-500">*</span>
                        </label>
                        <select name="status_kondisi"
                            id="status_kondisi"
                            class="w-full rounded-xl border-[#ecdfd0] text-xs sm:text-sm text-[#2a2338] focus:border-[#f2723b] focus:ring-[#f2723b] shadow-sm @error('status_kondisi') border-rose-500 focus:border-rose-500 focus:ring-rose-500 @enderror"
                            required>
                            <option value="baik" {{ old('status_kondisi', $fasilitas->status_kondisi) === 'baik' ? 'selected' : '' }}>Baik</option>
                            <option value="rusak_ringan" {{ old('status_kondisi', $fasilitas->status_kondisi) === 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                            <option value="rusak_berat" {{ old('status_kondisi', $fasilitas->status_kondisi) === 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                            <option value="maintenance" {{ old('status_kondisi', $fasilitas->status_kondisi) === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                        @error('status_kondisi')
                        <p class="mt-1 text-xs text-rose-500 font-medium flex items-center gap-1">
                            <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>
                </div>

                <!-- Opsi Tambahan -->
                <div class="p-4 bg-[#fbf7f0] rounded-xl border border-[#ecdfd0]/80">
                    <label class="block text-xs font-bold text-[#2a2338] mb-2 uppercase tracking-wider">
                        Opsi Tambahan
                    </label>
                    <label class="inline-flex items-center gap-2 text-xs font-medium text-[#2a2338] cursor-pointer">
                        <input type="checkbox"
                            name="generate_qr"
                            value="1"
                            {{ old('generate_qr', true) ? 'checked' : '' }}
                            class="rounded border-[#ecdfd0] text-[#f2723b] focus:ring-[#f2723b] shadow-sm">
                        <span>Generate QR Code setelah diperbarui</span>
                    </label>
                    <p class="mt-1 text-[11px] text-[#7a7189]">QR Code akan otomatis disinkronkan dan tersedia di halaman detail fasilitas.</p>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between pt-4 border-t border-[#ecdfd0]">
                    <a href="{{ route('rtk.fasilitas.index') }}"
                        class="text-xs font-semibold text-[#7a7189] hover:text-[#2a2338] transition-colors flex items-center gap-1">
                        <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Batal
                    </a>
                    <button type="submit"
                        class="bg-[#191333] hover:bg-[#2f2559] text-white px-6 py-2.5 rounded-xl transition-all text-xs font-bold shadow-md hover:shadow-lg flex items-center gap-2">
                        <i data-lucide="save" class="w-4 h-4 text-[#fbb35f]"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection