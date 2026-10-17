@extends(auth()->check() && auth()->user()->hasRole('bikem') ? 'layouts.bikem' : 'layouts.pelapor')

@section('title', 'Edit Laporan Kerusakan')

@section('content')
<div class="py-4 sm:py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Header Card -->
        <div class="flex items-center justify-between bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-amber-700">
                    <i data-lucide="edit-3" class="w-6 h-6"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold font-heading text-[#2a2338]">Edit Laporan Kerusakan</h1>
                    <p class="text-xs sm:text-sm text-[#7a7189]">Perbarui rincian laporan perbaikan yang telah dibuat.</p>
                </div>
            </div>
            <a href="{{ route('pelapor.pengajuan.index') }}" 
               class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#7a7189] hover:text-[#191333] transition-colors bg-[#fbf7f0] border border-[#ecdfd0] px-3.5 py-2 rounded-xl">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Batal
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm p-6 sm:p-8">
            <form action="{{ route('pelapor.pengajuan.update', $pengajuan) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Grid Options: Fasilitas & Lokasi -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Pilih Fasilitas -->
                    <div class="space-y-1.5">
                        <label for="fasilitas_id" class="block text-xs font-bold text-[#2a2338] uppercase tracking-wider">
                            Pilih Fasilitas <span class="text-rose-500">*</span>
                        </label>
                        <select name="fasilitas_id" id="fasilitas_id" required
                            class="w-full bg-[#fbf7f0] text-[#191333] text-xs sm:text-sm rounded-xl border border-[#ecdfd0] focus:ring-2 focus:ring-[#f2723b] py-2.5 px-3 transition @error('fasilitas_id') border-rose-500 @enderror">
                            <option value="">-- Pilih Fasilitas --</option>
                            @foreach ($fasilitas as $item)
                                <option value="{{ $item->id }}"
                                    data-lokasi="{{ $item->lokasi_detail }}"
                                    {{ old('fasilitas_id', $pengajuan->fasilitas_id) == $item->id ? 'selected' : '' }}>
                                    {{ $item->nama_fasilitas }} - {{ ucfirst($item->kategori) }}
                                </option>
                            @endforeach
                        </select>
                        @error('fasilitas_id')
                            <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Pilih Lokasi -->
                    <div class="space-y-1.5">
                        <label for="lokasi_id" class="block text-xs font-bold text-[#2a2338] uppercase tracking-wider">
                            Pilih Lokasi <span class="text-rose-500">*</span>
                        </label>
                        <select name="lokasi_id" id="lokasi_id" required
                            class="w-full bg-[#fbf7f0] text-[#191333] text-xs sm:text-sm rounded-xl border border-[#ecdfd0] focus:ring-2 focus:ring-[#f2723b] py-2.5 px-3 transition @error('lokasi_id') border-rose-500 @enderror">
                            <option value="">-- Pilih Lokasi --</option>
                            @foreach ($lokasi as $kampus => $items)
                                <optgroup label="{{ $kampus }}">
                                    @foreach ($items as $item)
                                        <option value="{{ $item->id }}"
                                            data-nama="{{ $item->nama_ruangan }}"
                                            {{ old('lokasi_id', $pengajuan->lokasi_id) == $item->id ? 'selected' : '' }}>
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

                <!-- Grid Options: Jenis Kerusakan & Lokasi Detail -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Jenis Kerusakan -->
                    <div class="space-y-1.5">
                        <label for="jenis_kerusakan" class="block text-xs font-bold text-[#2a2338] uppercase tracking-wider">
                            Jenis Kerusakan <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="jenis_kerusakan" id="jenis_kerusakan" value="{{ old('jenis_kerusakan', $pengajuan->jenis_kerusakan) }}" required
                            class="w-full bg-[#fbf7f0] text-[#191333] text-xs sm:text-sm rounded-xl border border-[#ecdfd0] focus:ring-2 focus:ring-[#f2723b] py-2.5 px-3 transition @error('jenis_kerusakan') border-rose-500 @enderror">
                        @error('jenis_kerusakan')
                            <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Lokasi Detail -->
                    <div class="space-y-1.5">
                        <label for="lokasi_detail" class="block text-xs font-bold text-[#2a2338] uppercase tracking-wider">
                            Lokasi Detail <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="lokasi_detail" id="lokasi_detail" value="{{ old('lokasi_detail', $pengajuan->lokasi_detail) }}" required
                            class="w-full bg-[#fbf7f0] text-[#191333] text-xs sm:text-sm rounded-xl border border-[#ecdfd0] focus:ring-2 focus:ring-[#f2723b] py-2.5 px-3 transition @error('lokasi_detail') border-rose-500 @enderror">
                        @error('lokasi_detail')
                            <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <!-- Deskripsi -->
                <div class="space-y-1.5">
                    <label for="deskripsi" class="block text-xs font-bold text-[#2a2338] uppercase tracking-wider">
                        Deskripsi Kerusakan <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="deskripsi" id="deskripsi" rows="4" required
                        class="w-full bg-[#fbf7f0] text-[#191333] text-xs sm:text-sm rounded-xl border border-[#ecdfd0] focus:ring-2 focus:ring-[#f2723b] p-3 transition @error('deskripsi') border-rose-500 @enderror">{{ old('deskripsi', $pengajuan->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Foto Kerusakan Upload & Preview -->
                <div class="space-y-2">
                    <label for="foto_kerusakan" class="block text-xs font-bold text-[#2a2338] uppercase tracking-wider">
                        Foto Kerusakan
                    </label>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 bg-[#fbf7f0] p-4 rounded-xl border border-[#ecdfd0]">
                        @if ($pengajuan->foto_kerusakan)
                            <div class="shrink-0">
                                <img src="{{ Storage::url($pengajuan->foto_kerusakan) }}" alt="Foto Kerusakan Saat Ini" class="h-20 w-20 object-cover rounded-xl border border-[#ecdfd0]">
                            </div>
                        @endif
                        <div class="w-full space-y-1">
                            <input type="file" name="foto_kerusakan" id="foto_kerusakan" accept="image/jpeg,image/png,image/jpg"
                                class="block w-full text-xs text-[#7a7189] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#191333] file:text-white hover:file:bg-[#2f2559]">
                            <p class="text-[11px] text-[#7a7189]">Format: JPEG, PNG, JPG. Maksimal 5MB. Kosongkan jika tidak ingin mengganti.</p>
                        </div>
                    </div>
                    @error('foto_kerusakan')
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
                        <option value="rendah" {{ old('tingkat_urgensi', $pengajuan->tingkat_urgensi) === 'rendah' ? 'selected' : '' }}>Rendah</option>
                        <option value="sedang" {{ old('tingkat_urgensi', $pengajuan->tingkat_urgensi) === 'sedang' ? 'selected' : '' }}>Sedang</option>
                        <option value="tinggi" {{ old('tingkat_urgensi', $pengajuan->tingkat_urgensi) === 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                    </select>
                    @error('tingkat_urgensi')
                        <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#ecdfd0]">
                    <a href="{{ route('pelapor.pengajuan.index') }}" class="px-5 py-2.5 text-xs font-semibold text-[#7a7189] hover:text-[#191333] transition">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white px-6 py-2.5 rounded-xl text-xs font-semibold shadow-sm transition">
                        <i data-lucide="check-circle" class="w-4 h-4"></i> Perbarui Laporan
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

        if (!lokasiDetailInput) return;
        let isInitialLoad = true;

        if (lokasiSelect && lokasiSelect.value) {
            lokasiDetailInput.readOnly = true;
        }

        if (fasilitasSelect) {
            fasilitasSelect.addEventListener('change', function() {
                if (isInitialLoad) return;
                const selectedOption = this.options[this.selectedIndex];
                const lokasiFasilitas = selectedOption.getAttribute('data-lokasi');

                if (lokasiFasilitas) {
                    lokasiDetailInput.value = lokasiFasilitas;
                    lokasiDetailInput.readOnly = true;
                } else {
                    lokasiDetailInput.readOnly = false;
                }
            });
        }

        if (lokasiSelect) {
            lokasiSelect.addEventListener('change', function() {
                if (isInitialLoad) return;
                const selectedOption = this.options[this.selectedIndex];
                const namaRuangan = selectedOption.getAttribute('data-nama');

                if (namaRuangan) {
                    const kampus = selectedOption.parentElement?.label || '';
                    const optionText = selectedOption.text || '';
                    const lantai = optionText.includes(' - ') ? optionText.split(' - ')[0] : '';
                    lokasiDetailInput.value = [kampus, lantai, namaRuangan].filter(Boolean).join(' - ');
                    lokasiDetailInput.readOnly = true;
                } else {
                    lokasiDetailInput.readOnly = false;
                }
            });
        }

        isInitialLoad = false;
    });
</script>
@endpush