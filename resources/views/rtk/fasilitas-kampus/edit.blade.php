@extends('layouts.rtk')

@section('title', 'Edit Fasilitas Kampus')

@section('content')
<div class="py-4 sm:py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Breadcrumb & Header -->
        <div class="flex items-center justify-between gap-4">
            <div>
                <a href="{{ route('rtk.fasilitas-kampus.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#7a7189] hover:text-[#f2723b] transition-colors mb-2">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali ke Daftar Fasilitas Kampus
                </a>
                <h1 class="text-xl sm:text-2xl font-bold font-heading text-[#2a2338]">
                    Edit Fasilitas Kampus: {{ $fasilitas->nama_fasilitas }}
                </h1>
                <p class="text-xs sm:text-sm text-[#7a7189] mt-0.5">
                    Perbarui informasi ruangan, foto fisik, atau pengaturan tampil di Landing Page.
                </p>
            </div>
            <div class="shrink-0 flex items-center gap-2">
                <a href="{{ route('rtk.fasilitas-kampus.show', $fasilitas) }}" class="px-4 py-2 rounded-xl bg-[#fbf7f0] border border-[#ecdfd0] text-xs font-bold text-[#2a2338] hover:bg-[#ecdfd0] transition-colors flex items-center gap-1.5">
                    <i data-lucide="eye" class="w-4 h-4 text-[#f2723b]"></i> Detail & QR
                </a>
            </div>
        </div>

        @if ($errors->any())
        <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-800 p-4 rounded-r-xl shadow-sm space-y-1">
            <div class="flex items-center gap-2 font-bold text-xs">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-500"></i> Terjadi kesalahan input:
            </div>
            <ul class="list-disc list-inside text-xs space-y-0.5 ml-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Formulir Input -->
        <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm p-6 sm:p-8">
            <form action="{{ route('rtk.fasilitas-kampus.update', $fasilitas) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Section 1: Informasi Dasar Ruangan -->
                <div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-[#f2723b] mb-4 flex items-center gap-2">
                        <i data-lucide="info" class="w-4 h-4"></i> Informasi Ruangan
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                        <!-- Nama Fasilitas / Ruangan -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-[#2a2338] mb-1.5">
                                Nama Ruangan / Fasilitas <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="nama_fasilitas" value="{{ old('nama_fasilitas', $fasilitas->nama_fasilitas) }}" required
                                class="w-full rounded-xl border-[#ecdfd0] text-xs font-medium text-[#2a2338] focus:border-[#f2723b] focus:ring-[#f2723b] p-3">
                        </div>

                        <!-- Dropdown Relasi Lokasi Master -->
                        <div>
                            <label class="block text-xs font-bold text-[#2a2338] mb-1.5">
                                Lokasi / Kampus Terkait
                            </label>
                            <select name="lokasi_id" class="w-full rounded-xl border-[#ecdfd0] text-xs font-medium text-[#2a2338] focus:border-[#f2723b] focus:ring-[#f2723b] p-3">
                                <option value="">-- Pilih dari Master Lokasi (Opsional) --</option>
                                @foreach ($lokasi as $kampusName => $lokasiGroup)
                                <optgroup label="{{ $kampusName }}">
                                    @foreach ($lokasiGroup as $loc)
                                    <option value="{{ $loc->id }}" {{ old('lokasi_id', $fasilitas->lokasi_id) == $loc->id ? 'selected' : '' }}>
                                        Lt. {{ $loc->lantai }} • {{ $loc->kode_ruangan }} - {{ $loc->nama_ruangan }}
                                    </option>
                                    @endforeach
                                </optgroup>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status Kondisi -->
                        <div>
                            <label class="block text-xs font-bold text-[#2a2338] mb-1.5">
                                Status Kondisi <span class="text-rose-500">*</span>
                            </label>
                            <select name="status_kondisi" required class="w-full rounded-xl border-[#ecdfd0] text-xs font-medium text-[#2a2338] focus:border-[#f2723b] focus:ring-[#f2723b] p-3">
                                <option value="baik" {{ old('status_kondisi', $fasilitas->status_kondisi) == 'baik' ? 'selected' : '' }}>Baik (Siap Digunakan)</option>
                                <option value="rusak_ringan" {{ old('status_kondisi', $fasilitas->status_kondisi) == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                <option value="rusak_berat" {{ old('status_kondisi', $fasilitas->status_kondisi) == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                                <option value="maintenance" {{ old('status_kondisi', $fasilitas->status_kondisi) == 'maintenance' ? 'selected' : '' }}>Dalam Perawatan (Maintenance)</option>
                            </select>
                        </div>

                        <!-- Deskripsi Singkat -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-[#2a2338] mb-1.5">
                                Deskripsi Ruangan (Untuk Landing Page)
                            </label>
                            <textarea name="deskripsi" rows="2" class="w-full rounded-xl border-[#ecdfd0] text-xs font-medium text-[#2a2338] focus:border-[#f2723b] focus:ring-[#f2723b] p-3">{{ old('deskripsi', $fasilitas->deskripsi) }}</textarea>
                        </div>

                        <!-- Fasilitas di Dalam Ruangan (Chips/List) -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-[#2a2338] mb-1.5">
                                Fasilitas di Dalam Ruangan <span class="text-[11px] font-normal text-[#7a7189]">(Pisahkan dengan tanda koma)</span>
                            </label>
                            <input type="text" name="fasilitas_ruangan" value="{{ old('fasilitas_ruangan', $fasilitas->fasilitas_ruangan) }}"
                                class="w-full rounded-xl border-[#ecdfd0] text-xs font-medium text-[#2a2338] focus:border-[#f2723b] focus:ring-[#f2723b] p-3">
                        </div>
                    </div>
                </div>

                <div class="h-px bg-[#f2ece2]"></div>

                <!-- Section 2: Upload / Ubah Foto Ruangan Fisik -->
                <div x-data="{ photoPreview: '{{ $fasilitas->foto ? $fasilitas->foto_url : '' }}' }">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-[#f2723b] mb-4 flex items-center gap-2">
                        <i data-lucide="camera" class="w-4 h-4"></i> Foto Fisik Ruangan
                    </h2>

                    <div class="space-y-3">
                        <label class="block text-xs font-bold text-[#2a2338]">
                            Foto Saat Ini / Ganti Foto (Format: JPG, PNG, WEBP • Maks. 5MB)
                        </label>

                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                            <!-- Preview Box -->
                            <div class="w-48 h-32 rounded-xl bg-slate-900 border-2 border-[#ecdfd0] overflow-hidden flex items-center justify-center relative shrink-0">
                                <template x-if="photoPreview">
                                    <img :src="photoPreview" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!photoPreview">
                                    <div class="text-center p-3 text-white/50 space-y-1">
                                        <i data-lucide="image" class="w-6 h-6 mx-auto"></i>
                                        <span class="text-[10px] block">Belum ada foto</span>
                                    </div>
                                </template>
                            </div>

                            <!-- Input File -->
                            <div class="flex-1 space-y-2">
                                <input type="file" name="foto" accept="image/*"
                                    @change="
                                        const reader = new FileReader();
                                        reader.onload = (e) => { photoPreview = e.target.result; if(window.lucide) lucide.createIcons(); };
                                        reader.readAsDataURL($event.target.files[0]);
                                    "
                                    class="block w-full text-xs text-[#7a7189] file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#fbf7f0] file:text-[#f2723b] hover:file:bg-[#ecdfd0] cursor-pointer">
                                <p class="text-[11px] text-[#7a7189]">
                                    Pilih file baru jika ingin mengganti foto yang ada. Jika tidak ingin mengubah foto, biarkan kolom ini kosong.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="h-px bg-[#f2ece2]"></div>

                <!-- Section 3: Pengaturan Publikasi -->
                <div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-[#f2723b] mb-3 flex items-center gap-2">
                        <i data-lucide="settings" class="w-4 h-4"></i> Pengaturan Publikasi
                    </h2>

                    <div class="bg-[#fbf7f0] p-4 rounded-xl border border-[#ecdfd0] space-y-3">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="is_landing_page" value="1" {{ old('is_landing_page', $fasilitas->is_landing_page) ? 'checked' : '' }}
                                class="mt-0.5 rounded text-[#f2723b] focus:ring-[#f2723b] border-[#ecdfd0]">
                            <div>
                                <span class="text-xs font-bold text-[#2a2338] block">Tampilkan di Galeri Landing Page</span>
                                <span class="text-[11px] text-[#7a7189] block mt-0.5">
                                    Aktifkan agar ruangan ini otomatis tampil di viewer galeri <strong>Fasilitas Kampus</strong> di halaman depan.
                                </span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="pt-4 flex items-center justify-end gap-3">
                    <a href="{{ route('rtk.fasilitas-kampus.index') }}" class="px-5 py-2.5 rounded-xl border border-[#ecdfd0] text-xs font-semibold text-[#7a7189] hover:bg-[#fbf7f0] transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl text-white font-bold text-xs shadow-md transition-all hover:-translate-y-0.5 flex items-center gap-2" style="background: linear-gradient(135deg, var(--sunset-500), var(--sunset-400));">
                        <i data-lucide="check" class="w-4 h-4"></i> Simpan Perubahan
                    </button>
                </div>

            </form>
        </div>

    </div>
</div>
@endsection
