@extends(auth()->check() && auth()->user()->hasRole('bikem') ? 'layouts.bikem' : 'layouts.pelapor')

@section('title', 'Lapor Kerusakan Baru')

@section('content')
<div class="py-4 sm:py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Header Card -->
        <div class="flex items-center justify-between bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-[#fbf7f0] border border-[#ecdfd0] rounded-xl text-[#f2723b]">
                    <i data-lucide="wrench" class="w-6 h-6"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold font-heading text-[#2a2338]">Form Lapor Kerusakan</h1>
                    <p class="text-xs sm:text-sm text-[#7a7189]">Isi formulir di bawah untuk mengajukan perbaikan fasilitas.</p>
                </div>
            </div>
            <a href="{{ route('pelapor.pengajuan.index') }}" 
               class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#7a7189] hover:text-[#191333] transition-colors bg-[#fbf7f0] border border-[#ecdfd0] px-3.5 py-2 rounded-xl">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm p-6 sm:p-8">
            <form action="{{ route('pelapor.pengajuan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @php
                    $fasilitasId = request()->query('fasilitas_id');
                    $selectedFasilitas = $fasilitasId ? \App\Models\Fasilitas::find($fasilitasId) : null;
                @endphp

                <!-- Grid Options: Fasilitas & Lokasi -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Pilih Fasilitas -->
                    <div class="space-y-1.5">
                        <label for="fasilitas_id" class="block text-xs font-bold text-[#2a2338] uppercase tracking-wider">
                            Pilih Fasilitas <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <select name="fasilitas_id" id="fasilitas_id" required
                                class="w-full bg-[#fbf7f0] text-[#191333] text-xs sm:text-sm rounded-xl border border-[#ecdfd0] focus:ring-2 focus:ring-[#f2723b] focus:border-[#f2723b] py-2.5 px-3 pr-8 transition @error('fasilitas_id') border-rose-500 @enderror">
                                <option value="">-- Pilih Fasilitas --</option>
                                @foreach ($fasilitas as $item)
                                    <option value="{{ $item->id }}"
                                        data-lokasi="{{ $item->lokasi_detail }}"
                                        data-stok="{{ $item->stok_total }}"
                                        data-kategori="{{ $item->kategori }}"
                                        data-status="{{ $item->status_kondisi }}"
                                        {{ old('fasilitas_id', $fasilitasId) == $item->id ? 'selected' : '' }}>
                                        {{ $item->nama_fasilitas }} - {{ ucfirst($item->kategori) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('fasilitas_id')
                            <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Pilih Lokasi -->
                    <div class="space-y-1.5">
                        <label for="lokasi_id" class="block text-xs font-bold text-[#2a2338] uppercase tracking-wider">
                            Pilih Lokasi Ruangan <span class="text-rose-500">*</span>
                        </label>
                        <select name="lokasi_id" id="lokasi_id" required
                            class="w-full bg-[#fbf7f0] text-[#191333] text-xs sm:text-sm rounded-xl border border-[#ecdfd0] focus:ring-2 focus:ring-[#f2723b] focus:border-[#f2723b] py-2.5 px-3 transition @error('lokasi_id') border-rose-500 @enderror">
                            <option value="">-- Pilih Lokasi --</option>
                            @foreach ($lokasi as $kampus => $items)
                                <optgroup label="{{ $kampus }}">
                                    @foreach ($items as $item)
                                        <option value="{{ $item->id }}"
                                            data-nama="{{ $item->nama_ruangan }}"
                                            {{ old('lokasi_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->lantai }} - {{ $item->kode_ruangan }}: {{ $item->nama_ruangan }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('lokasi_id')
                            <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <!-- Lokasi Detail Auto-filled -->
                <div class="space-y-1.5">
                    <label for="lokasi_detail" class="block text-xs font-bold text-[#2a2338] uppercase tracking-wider">
                        Lokasi Detail Terdeteksi <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" name="lokasi_detail" id="lokasi_detail" value="{{ old('lokasi_detail') }}" readonly required
                            placeholder="Lokasi akan terisi otomatis berdasarkan opsi di atas"
                            class="w-full bg-[#fbf7f0]/60 text-[#191333] font-medium text-xs sm:text-sm rounded-xl border border-[#ecdfd0] focus:ring-2 focus:ring-[#f2723b] py-2.5 px-3 transition cursor-not-allowed @error('lokasi_detail') border-rose-500 @enderror">
                        <i data-lucide="map-pin" class="w-4 h-4 text-[#7a7189] absolute right-3 top-3"></i>
                    </div>
                    @error('lokasi_detail')
                        <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Grid Options: Jenis Kerusakan & Urgensi -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Jenis Kerusakan -->
                    <div class="space-y-1.5">
                        <label for="jenis_kerusakan" class="block text-xs font-bold text-[#2a2338] uppercase tracking-wider">
                            Jenis Kerusakan <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="jenis_kerusakan" id="jenis_kerusakan" value="{{ old('jenis_kerusakan') }}" required
                            placeholder="Contoh: AC Bocor, Lampu Mati, Saklar Rusak"
                            class="w-full bg-[#fbf7f0] text-[#191333] text-xs sm:text-sm rounded-xl border border-[#ecdfd0] focus:ring-2 focus:ring-[#f2723b] py-2.5 px-3 transition @error('jenis_kerusakan') border-rose-500 @enderror">
                        @error('jenis_kerusakan')
                            <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tingkat Urgensi -->
                    <div class="space-y-1.5">
                        <label for="tingkat_urgensi" class="block text-xs font-bold text-[#2a2338] uppercase tracking-wider">
                            Tingkat Urgensi <span class="text-rose-500">*</span>
                        </label>
                        <select name="tingkat_urgensi" id="tingkat_urgensi" required
                            class="w-full bg-[#fbf7f0] text-[#191333] text-xs sm:text-sm rounded-xl border border-[#ecdfd0] focus:ring-2 focus:ring-[#f2723b] py-2.5 px-3 transition @error('tingkat_urgensi') border-rose-500 @enderror">
                            <option value="">Pilih Tingkat Urgensi</option>
                            <option value="rendah" {{ old('tingkat_urgensi') === 'rendah' ? 'selected' : '' }}>Rendah (Dapat menunggu)</option>
                            <option value="sedang" {{ old('tingkat_urgensi') === 'sedang' ? 'selected' : '' }}>Sedang (Perlu perhatian)</option>
                            <option value="tinggi" {{ old('tingkat_urgensi') === 'tinggi' ? 'selected' : '' }}>Tinggi (Penanganan segera)</option>
                        </select>
                        @error('tingkat_urgensi')
                            <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <!-- Deskripsi -->
                <div class="space-y-1.5">
                    <label for="deskripsi" class="block text-xs font-bold text-[#2a2338] uppercase tracking-wider">
                        Deskripsi Detail Kerusakan <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="deskripsi" id="deskripsi" rows="4" required
                        placeholder="Jelaskan kronologi, posisi akurat, atau detail masalah kerusakan secara lengkap..."
                        class="w-full bg-[#fbf7f0] text-[#191333] text-xs sm:text-sm rounded-xl border border-[#ecdfd0] focus:ring-2 focus:ring-[#f2723b] p-3 transition @error('deskripsi') border-rose-500 @enderror">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Foto Kerusakan Dropzone Area -->
                <div class="space-y-1.5">
                    <label for="foto_kerusakan" class="block text-xs font-bold text-[#2a2338] uppercase tracking-wider">
                        Foto / Bukti Kerusakan <span class="text-rose-500">*</span>
                    </label>
                    <div id="dropzone_foto" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-[#ecdfd0] border-dashed rounded-2xl bg-[#fbf7f0]/40 hover:bg-[#fbf7f0] transition-colors relative">
                        <div id="dropzone_placeholder" class="space-y-2 text-center">
                            <i data-lucide="camera" class="mx-auto h-8 w-8 text-[#f2723b]"></i>
                            <div class="flex text-xs text-[#7a7189] justify-center">
                                <label for="foto_kerusakan" class="relative cursor-pointer rounded-md font-bold text-[#191333] hover:text-[#f2723b] focus-within:outline-none">
                                    <span>Unggah file foto</span>
                                    <input id="foto_kerusakan" name="foto_kerusakan" type="file" accept="image/jpeg,image/png,image/jpg" required class="sr-only">
                                </label>
                                <p class="pl-1">atau tarik berkas ke sini</p>
                            </div>
                            <p class="text-[11px] text-[#7a7189]">PNG, JPG, JPEG hingga 5MB</p>
                        </div>

                        <!-- Preview foto (muncul setelah file dipilih) -->
                        <div id="dropzone_preview" class="hidden w-full text-center">
                            <img id="preview_image" src="" alt="Preview foto kerusakan" class="mx-auto max-h-40 rounded-xl border border-[#ecdfd0] object-cover shadow-sm">
                            <div class="flex items-center justify-center gap-2 mt-3">
                                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-500"></i>
                                <p id="preview_filename" class="text-xs font-semibold text-[#2a2338] truncate max-w-[220px]"></p>
                            </div>
                            <button type="button" id="btn_ganti_foto" class="mt-2 text-xs font-semibold text-[#f2723b] hover:text-[#191333] underline transition">
                                Ganti Foto
                            </button>
                        </div>
                    </div>
                    @error('foto_kerusakan')
                        <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#ecdfd0]">
                    <a href="{{ route('pelapor.pengajuan.index') }}" class="px-5 py-2.5 text-xs font-semibold text-[#7a7189] hover:text-[#191333] transition">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 bg-[#191333] hover:bg-[#2f2559] text-white px-6 py-2.5 rounded-xl text-xs font-semibold shadow-sm transition">
                        <i data-lucide="send" class="w-4 h-4 text-[#f2723b]"></i> Kirim Laporan
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.lucide) { lucide.createIcons(); }

        const fasilitasSelect = document.getElementById('fasilitas_id');
        const lokasiSelect = document.getElementById('lokasi_id');
        const lokasiDetailInput = document.getElementById('lokasi_detail');

        function updateLokasiDetail() {
            if (lokasiSelect && lokasiSelect.value) {
                const selectedOption = lokasiSelect.options[lokasiSelect.selectedIndex];
                const namaRuangan = selectedOption.getAttribute('data-nama') || '';
                const kampus = selectedOption.parentElement.label || '';
                const lantai = selectedOption.text.split(' - ')[0] || '';
                lokasiDetailInput.value = [kampus, lantai, namaRuangan].filter(Boolean).join(' - ');
            } else if (fasilitasSelect && fasilitasSelect.value) {
                const selectedOption = fasilitasSelect.options[fasilitasSelect.selectedIndex];
                const lokasi = selectedOption.getAttribute('data-lokasi');
                lokasiDetailInput.value = lokasi || '';
            } else {
                lokasiDetailInput.value = '';
            }
        }

        if (fasilitasSelect) fasilitasSelect.addEventListener('change', updateLokasiDetail);
        if (lokasiSelect) lokasiSelect.addEventListener('change', updateLokasiDetail);

        if (fasilitasSelect.value || lokasiSelect.value) {
            updateLokasiDetail();
        }

        // --- Preview Foto Kerusakan ---
        const fotoInput = document.getElementById('foto_kerusakan');
        const dropzonePlaceholder = document.getElementById('dropzone_placeholder');
        const dropzonePreview = document.getElementById('dropzone_preview');
        const previewImage = document.getElementById('preview_image');
        const previewFilename = document.getElementById('preview_filename');
        const btnGantiFoto = document.getElementById('btn_ganti_foto');

        function resetDropzone() {
            fotoInput.value = '';
            dropzonePreview.classList.add('hidden');
            dropzonePlaceholder.classList.remove('hidden');
            previewImage.src = '';
            previewFilename.textContent = '';
        }

        if (fotoInput) {
            fotoInput.addEventListener('change', function () {
                const file = this.files && this.files[0];
                if (!file) {
                    resetDropzone();
                    return;
                }

                // Validasi ringan di sisi client (tetap divalidasi juga di server)
                const maxSizeBytes = 5 * 1024 * 1024; // 5MB
                if (file.size > maxSizeBytes) {
                    alert('Ukuran file terlalu besar. Maksimal 5MB.');
                    resetDropzone();
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImage.src = e.target.result;
                    previewFilename.textContent = file.name;
                    dropzonePlaceholder.classList.add('hidden');
                    dropzonePreview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            });
        }

        if (btnGantiFoto) {
            btnGantiFoto.addEventListener('click', function () {
                resetDropzone();
                fotoInput.click();
            });
        }
    });
</script>
@endpush