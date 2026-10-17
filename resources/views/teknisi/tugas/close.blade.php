@extends('layouts.teknisi')

@section('title', 'Selesaikan Tugas')

@section('content')
    <div class="py-4 sm:py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Top Header Card -->
            <div class="flex items-center justify-between bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-600">
                        <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold font-heading text-[#2a2338]">Selesaikan Tugas</h1>
                        <p class="text-xs sm:text-sm text-[#7a7189]">Tugas #{{ $penugasan->id }} &bull; Unggah bukti
                            perbaikan dan perbarui status akhir.</p>
                    </div>
                </div>
                <a href="{{ route('teknisi.tugas.show', $penugasan) }}"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#7a7189] hover:text-[#191333] transition-colors bg-[#fbf7f0] border border-[#ecdfd0] px-3.5 py-2 rounded-xl">
                    <i data-lucide="x" class="w-4 h-4"></i> Batal
                </a>
            </div>

            <!-- Alert Error Flash -->
            @if (session('error'))
                <div
                    class="flex items-center gap-3 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs font-medium">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            <!-- Summary Info Card -->
            <div class="bg-[#fbf7f0] p-6 rounded-2xl border border-[#ecdfd0] space-y-4">
                <div class="flex items-center gap-2 text-[#2a2338]">
                    <i data-lucide="clipboard-list" class="w-5 h-5 text-[#f2723b]"></i>
                    <h2 class="text-sm font-bold font-heading uppercase tracking-wider">Ringkasan Tugas Penanganan</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="space-y-1">
                        <span class="text-[#7a7189] font-medium">Jenis Kerusakan:</span>
                        <p class="font-bold text-[#191333]">{{ $penugasan->pengajuanKerusakan->jenis_kerusakan }}</p>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[#7a7189] font-medium">Lokasi Detail:</span>
                        <p class="font-bold text-[#191333]">{{ $penugasan->pengajuanKerusakan->lokasi_detail }}</p>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[#7a7189] font-medium">Pelapor:</span>
                        <p class="font-bold text-[#191333]">
                            {{ $penugasan->pengajuanKerusakan->pelapor->nama ?? 'Tidak diketahui' }}</p>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[#7a7189] font-medium">Jadwal Penanganan:</span>
                        <p class="font-bold text-[#191333]">{{ $penugasan->jadwal_penanganan->format('d/m/Y H:i') }} WIB</p>
                    </div>
                </div>

                @if ($penugasan->instruksi_kerja)
                    <div class="pt-3 border-t border-[#ecdfd0] text-xs">
                        <span class="text-[#7a7189] font-medium">Instruksi Kerja Admin:</span>
                        <p class="mt-1 font-semibold text-[#191333] bg-white p-3 rounded-xl border border-[#ecdfd0]">
                            {{ $penugasan->instruksi_kerja }}</p>
                    </div>
                @endif

                <!-- Info Fasilitas Terkait -->
                @if ($fasilitas)
                    <div class="p-4 bg-white rounded-xl border border-[#ecdfd0] space-y-1">
                        <div class="flex items-center gap-2 text-xs font-bold text-[#191333]">
                            <i data-lucide="box" class="w-4 h-4 text-[#f2723b]"></i>
                            <span>Fasilitas: {{ $fasilitas->nama_fasilitas }}</span>
                            <span class="text-[10px] text-[#7a7189] font-normal">(Status awal:
                                {{ ucfirst(str_replace('_', ' ', $fasilitas->status_kondisi)) }})</span>
                        </div>
                        <p class="text-[11px] text-[#7a7189] mt-0.5">
                            ⚠️ Pastikan untuk memilih kondisi akhir fasilitas di bawah ini sesuai hasil perbaikan nyata.
                        </p>
                    </div>
                @endif
            </div>

            <!-- Form Complete Task -->
            <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm p-6 sm:p-8">
                @if ($penugasan->pengajuanKerusakan->status_laporan === 'selesai')
                    <div class="bg-green-50 p-4 rounded-lg border border-green-200 mb-6">
                        <p class="text-sm text-green-700">
                            <i class="fas fa-check-circle mr-1"></i>
                            <strong>Laporan ini sudah selesai.</strong>
                            Tidak perlu upload bukti perbaikan lagi.
                        </p>
                        <a href="{{ route('teknisi.laporan-kerusakan.show', $penugasan->pengajuan_id) }}"
                            class="inline-block mt-2 text-sm text-green-700 hover:underline">
                            Lihat Detail Laporan →
                        </a>
                    </div>
                @else
                    <form action="{{ route('teknisi.tugas.store-close', $penugasan) }}" method="POST"
                        enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <!-- Upload Foto Selesai -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-[#2a2338] uppercase tracking-wider">
                                Foto Bukti Perbaikan <span class="text-rose-500">*</span>
                            </label>
                            <div
                                class="flex justify-center px-6 pt-6 pb-6 border-2 border-dashed border-[#ecdfd0] hover:border-[#f2723b] rounded-2xl bg-[#fbf7f0]/50 transition-colors">
                                <div class="space-y-2 text-center">
                                    <div
                                        class="p-3 bg-white rounded-full w-12 h-12 mx-auto flex items-center justify-center text-[#f2723b] shadow-sm">
                                        <i data-lucide="camera" class="w-6 h-6"></i>
                                    </div>
                                    <div class="flex text-xs text-[#7a7189] justify-center">
                                        <label for="foto_selesai"
                                            class="relative cursor-pointer font-bold text-[#f2723b] hover:underline focus-within:outline-none">
                                            <span>Pilih / Upload Foto</span>
                                            <input id="foto_selesai" name="foto_selesai" type="file"
                                                accept="image/jpeg,image/png,image/jpg" class="sr-only" required>
                                        </label>
                                        <p class="pl-1">atau tarik file ke sini</p>
                                    </div>
                                    <p class="text-[11px] text-[#7a7189]">Format JPEG, PNG, JPG (Maks. 5MB)</p>
                                    <p id="file-name" class="text-xs text-emerald-600 font-bold hidden"></p>
                                </div>
                            </div>
                            @error('foto_selesai')
                                <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kondisi Akhir Fasilitas -->
                        @if ($fasilitas)
                            <div class="space-y-1.5">
                                <label for="kondisi_akhir"
                                    class="block text-xs font-bold text-[#2a2338] uppercase tracking-wider">
                                    Kondisi Akhir Fasilitas <span class="text-rose-500">*</span>
                                </label>
                                <select name="kondisi_akhir" id="kondisi_akhir" required
                                    class="w-full bg-[#fbf7f0] text-[#191333] text-xs sm:text-sm rounded-xl border border-[#ecdfd0] focus:ring-2 focus:ring-[#f2723b] py-2.5 px-3 transition @error('kondisi_akhir') border-rose-500 @enderror">
                                    <option value="">-- Pilih kondisi akhir fasilitas --</option>
                                    <option value="baik" {{ old('kondisi_akhir') === 'baik' ? 'selected' : '' }}>✅ Baik
                                        (100% Siap Digunakan)</option>
                                    <option value="rusak_ringan"
                                        {{ old('kondisi_akhir') === 'rusak_ringan' ? 'selected' : '' }}>⚠️ Rusak Ringan
                                        (Masih Ada Kendala Kecil)</option>
                                    <option value="perlu_tindak_lanjut"
                                        {{ old('kondisi_akhir') === 'perlu_tindak_lanjut' ? 'selected' : '' }}>🔄 Perlu
                                        Tindak Lanjut (Butuh Perbaikan Lanjutan)</option>
                                    <option value="rusak_berat"
                                        {{ old('kondisi_akhir') === 'rusak_berat' ? 'selected' : '' }}>❌ Rusak Berat (Belum
                                        Selesai, Butuh Vendor External)</option>
                                    <option value="maintenance"
                                        {{ old('kondisi_akhir') === 'maintenance' ? 'selected' : '' }}>🛠️ Maintenance
                                        (Masih Dalam Perawatan rutin)</option>
                                </select>
                                <p class="text-[11px] text-[#7a7189]">Status kondisi fasilitas ini akan langsung terbarui
                                    secara otomatis di dalam inventaris.</p>
                                @error('kondisi_akhir')
                                    <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <!-- Keterangan Selesai -->
                        <div class="space-y-1.5">
                            <label for="keterangan_selesai"
                                class="block text-xs font-bold text-[#2a2338] uppercase tracking-wider">
                                Keterangan & Catatan Perbaikan <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="keterangan_selesai" id="keterangan_selesai" rows="4" required
                                placeholder="Uraikan tindakan teknis yang telah dilakukan, penggantian komponen (jika ada), atau rekomendasi lanjutan..."
                                class="w-full bg-[#fbf7f0] text-[#191333] text-xs sm:text-sm rounded-xl border border-[#ecdfd0] focus:ring-2 focus:ring-[#f2723b] p-3 transition @error('keterangan_selesai') border-rose-500 @enderror">{{ old('keterangan_selesai') }}</textarea>
                            @error('keterangan_selesai')
                                <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#ecdfd0]">
                            <a href="{{ route('teknisi.tugas.show', $penugasan) }}"
                                class="px-5 py-2.5 text-xs font-semibold text-[#7a7189] hover:text-[#191333] transition">
                                Batal
                            </a>
                            <button type="submit"
                                class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl text-xs font-semibold shadow-sm transition">
                                <i data-lucide="check" class="w-4 h-4"></i> Selesaikan Tugas
                            </button>
                        </div>
                    </form>
				@endif
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

            const fileInput = document.getElementById('foto_selesai');
            const fileNameDisplay = document.getElementById('file-name');

            if (fileInput && fileNameDisplay) {
                fileInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        fileNameDisplay.textContent = '📎 File terpilih: ' + this.files[0].name;
                        fileNameDisplay.classList.remove('hidden');
                    } else {
                        fileNameDisplay.classList.add('hidden');
                    }
                });
            }
        });
    </script>
@endpush
