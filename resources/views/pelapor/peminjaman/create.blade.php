@extends(auth()->check() && auth()->user()->hasRole('bikem') ? 'layouts.bikem' : 'layouts.pelapor')

@section('title', 'Ajukan Peminjaman Fasilitas')

@section('content')
<div class="py-4 sm:py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Header Card -->
        <div class="flex items-center justify-between bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-[#fbf7f0] border border-[#ecdfd0] rounded-xl text-[#f2723b]">
                    <i data-lucide="calendar-plus" class="w-6 h-6"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold font-heading text-[#2a2338]">Ajukan Peminjaman Fasilitas</h1>
                    <p class="text-xs sm:text-sm text-[#7a7189]">Isi formulir di bawah untuk mengajukan izin penggunaan fasilitas.</p>
                </div>
            </div>
            <a href="{{ route('pelapor.peminjaman.index') }}"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#7a7189] hover:text-[#191333] transition-colors bg-[#fbf7f0] border border-[#ecdfd0] px-3.5 py-2 rounded-xl">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
            </a>
        </div>

        <!-- Alert Error Flash -->
        @if (session('error'))
        <div class="flex items-center gap-3 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs font-medium">
            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0"></i>
            <div>{!! session('error') !!}</div>
        </div>
        @endif

        <!-- Form Card -->
        <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm p-6 sm:p-8">
            <form action="{{ route('pelapor.peminjaman.store') }}" method="POST" enctype="multipart/form-data" id="formPeminjaman" class="space-y-6">
                @csrf

                <!-- Keperluan -->
                <div class="space-y-1.5">
                    <label for="keperluan" class="block text-xs font-bold text-[#2a2338] uppercase tracking-wider">
                        Keperluan Peminjaman <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="keperluan" id="keperluan" rows="3" required
                        placeholder="Jelaskan keperluan peminjaman secara rinci (nama kegiatan, organisasi, dll)..."
                        class="w-full bg-[#fbf7f0] text-[#191333] text-xs sm:text-sm rounded-xl border border-[#ecdfd0] focus:ring-2 focus:ring-[#f2723b] p-3 transition @error('keperluan') border-rose-500 @enderror">{{ old('keperluan') }}</textarea>
                    @error('keperluan')
                    <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Catatan Tambahan -->
                <div class="space-y-1.5">
                    <label for="catatan_peminjam" class="block text-xs font-bold text-[#2a2338] uppercase tracking-wider">
                        Catatan Tambahan <span class="text-xs text-[#7a7189] font-normal">(Opsional)</span>
                    </label>
                    <textarea name="catatan_peminjam" id="catatan_peminjam" rows="2"
                        placeholder="Catatan tambahan (opsional)"
                        class="w-full bg-[#fbf7f0] text-[#191333] text-xs sm:text-sm rounded-xl border border-[#ecdfd0] focus:ring-2 focus:ring-[#f2723b] p-3 transition @error('catatan_peminjam') border-rose-500 @enderror">{{ old('catatan_peminjam') }}</textarea>
                    @error('catatan_peminjam')
                    <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Grid: Waktu Mulai & Selesai -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Waktu Mulai -->
                    <div class="space-y-1.5">
                        <label for="waktu_mulai" class="block text-xs font-bold text-[#2a2338] uppercase tracking-wider">
                            Waktu Mulai <span class="text-rose-500">*</span>
                        </label>
                        <input type="datetime-local" name="waktu_mulai" id="waktu_mulai" value="{{ old('waktu_mulai') }}" required
                            class="w-full bg-[#fbf7f0] text-[#191333] text-xs sm:text-sm rounded-xl border border-[#ecdfd0] focus:ring-2 focus:ring-[#f2723b] py-2.5 px-3 transition @error('waktu_mulai') border-rose-500 @enderror">
                        @error('waktu_mulai')
                        <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Waktu Selesai -->
                    <div class="space-y-1.5">
                        <label for="waktu_selesai" class="block text-xs font-bold text-[#2a2338] uppercase tracking-wider">
                            Waktu Selesai <span class="text-rose-500">*</span>
                        </label>
                        <input type="datetime-local" name="waktu_selesai" id="waktu_selesai" value="{{ old('waktu_selesai') }}" required
                            class="w-full bg-[#fbf7f0] text-[#191333] text-xs sm:text-sm rounded-xl border border-[#ecdfd0] focus:ring-2 focus:ring-[#f2723b] py-2.5 px-3 transition @error('waktu_selesai') border-rose-500 @enderror">
                        @error('waktu_selesai')
                        <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- SECTION: ITEMS (Multi-item Peminjaman) -->
                <div class="space-y-3">
                    <label class="block text-xs font-bold text-[#2a2338] uppercase tracking-wider">
                        Daftar Fasilitas yang Dipinjam <span class="text-rose-500">*</span>
                    </label>

                    <div id="items-container" class="space-y-3">
                        <!-- Row Item 0 (Default) -->
                        <div class="item-row p-4 bg-[#fbf7f0] border border-[#ecdfd0] rounded-xl space-y-3">
                            <div class="flex flex-wrap items-center gap-3">
                                <!-- Select Fasilitas -->
                                <div class="flex-1 min-w-[220px]">
                                    {{-- Ganti bagian <select name="items[0][fasilitas_id]"> --}}
                                    <select name="items[0][fasilitas_id]" required
                                        class="item-select w-full bg-white text-[#191333] text-xs sm:text-sm rounded-xl border border-[#ecdfd0] focus:ring-2 focus:ring-[#f2723b] py-2.5 px-3 transition">
                                        <option value="">-- Pilih Fasilitas --</option>
                                        @foreach ($fasilitas as $item)
                                        <option value="{{ $item->id }}"
                                            data-stok-total="{{ $item->stok_total }}"
                                            data-kategori="{{ $item->kategori }}"
                                            data-nama="{{ $item->nama_fasilitas }}">
                                            {{ $item->nama_fasilitas }} (Kapasitas: {{ $item->stok_total }} unit)
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Input Jumlah -->
                                <div class="w-28">
                                    <input type="number" name="items[0][jumlah_dipinjam]" value="1" min="1" required
                                        placeholder="Jumlah"
                                        class="item-jumlah w-full bg-white text-[#191333] text-xs sm:text-sm rounded-xl border border-[#ecdfd0] focus:ring-2 focus:ring-[#f2723b] py-2.5 px-3 transition">
                                </div>

                                <!-- Info Stok Singkat -->
                                <div class="text-xs text-[#7a7189] min-w-[120px]" id="stok-info-0">
                                    Status: <span class="stok-tersedia font-semibold text-[#191333]">-</span>
                                </div>

                                <!-- Tombol Hapus Row -->
                                <button type="button" class="remove-item hidden inline-flex items-center gap-1 text-xs font-semibold text-rose-600 hover:text-rose-800 transition">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i> Hapus
                                </button>
                            </div>

                            <!-- Detail Jadwal & Ketersediaan Jam -->
                            <div class="schedule-box hidden text-xs rounded-xl p-3 bg-white border border-[#ecdfd0] space-y-2"></div>
                        </div>
                    </div>

                    <!-- Tombol Tambah Item -->
                    <button type="button" id="add-item"
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#f2723b] hover:text-[#d95d28] transition bg-[#fbf7f0] border border-[#ecdfd0] px-4 py-2 rounded-xl">
                        <i data-lucide="plus" class="w-4 h-4"></i> Tambah Item
                    </button>
                    <p class="text-[11px] text-[#7a7189]">Klik "Tambah Item" jika ingin meminjam lebih dari satu fasilitas dalam satu pengajuan.</p>
                </div>

                <!-- File Pendukung -->
                <div class="space-y-1.5">
                    <label for="file_pendukung" class="block text-xs font-bold text-[#2a2338] uppercase tracking-wider">
                        📎 Upload Berkas Pendukung <span class="text-rose-500">*</span>
                    </label>
                    <div id="dropzone_pendukung" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed border-[#ecdfd0] rounded-xl hover:border-[#f2723b] bg-[#fbf7f0] transition">
                        <div id="dropzone_pendukung_placeholder" class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-[#7a7189]" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-[#7a7189]">
                                <label for="file_pendukung" class="relative cursor-pointer rounded-md font-semibold text-[#f2723b] hover:underline focus-within:outline-none">
                                    <span>Upload file</span>
                                    <input id="file_pendukung" name="file_pendukung" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required class="sr-only">
                                </label>
                                <p class="pl-1">atau drag and drop</p>
                            </div>
                            <p class="text-xs text-[#7a7189]">PDF, DOC, DOCX, JPG, JPEG, PNG (Maks. 5MB)</p>
                        </div>

                        <!-- Preview berkas -->
                        <div id="dropzone_pendukung_preview" class="hidden w-full text-center space-y-2">
                            <img id="preview_pendukung_image" src="" alt="Preview berkas" class="hidden mx-auto max-h-32 rounded-md border border-[#ecdfd0] object-cover">
                            <svg id="preview_pendukung_icon" class="hidden mx-auto h-10 w-10 text-[#f2723b]" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <p id="file-name-pendukung" class="text-xs text-[#2a2338] font-semibold break-all"></p>
                            <button type="button" id="btn_ganti_pendukung" class="text-xs font-medium text-[#f2723b] hover:underline transition">
                                Ganti Berkas
                            </button>
                        </div>
                    </div>
                    <p id="file_pendukung_error" class="text-xs text-rose-600 font-medium hidden">Berkas pendukung wajib diunggah.</p>
                    @error('file_pendukung')
                    <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#ecdfd0]">
                    <a href="{{ route('pelapor.peminjaman.index') }}" class="px-5 py-2.5 text-xs font-semibold text-[#7a7189] hover:text-[#191333] transition">
                        Batal
                    </a>
                    <button type="submit" id="submitBtn" class="inline-flex items-center gap-2 bg-[#191333] hover:bg-[#2f2559] text-white px-6 py-2.5 rounded-xl text-xs font-semibold shadow-sm transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <i data-lucide="send" class="w-4 h-4 text-[#f2723b]"></i> Ajukan Peminjaman
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
        if (window.lucide) {
            lucide.createIcons();
        }

        let itemCount = 1;

        // ✅ Format tanggal & jam rapi untuk UI
        function formatTimeOnly(dtString) {
            if (!dtString) return '';
            const dt = new Date(dtString);
            if (isNaN(dt.getTime())) return dtString;
            const hr = String(dt.getHours()).padStart(2, '0');
            const mn = String(dt.getMinutes()).padStart(2, '0');
            return `${hr}:${mn}`;
        }

        function formatDateOnly(dtString) {
            if (!dtString) return '';
            const dt = new Date(dtString);
            if (isNaN(dt.getTime())) return dtString;
            const d = String(dt.getDate()).padStart(2, '0');
            const m = String(dt.getMonth() + 1).padStart(2, '0');
            const y = dt.getFullYear();
            return `${d}/${m}/${y}`;
        }

        // ✅ 1. Cek Ketersediaan Semua Fasilitas di Dropdown Berdasarkan Jam Dipilih
        function checkAllAvailability() {
            const waktuMulai = document.getElementById('waktu_mulai').value;
            const waktuSelesai = document.getElementById('waktu_selesai').value;

            if (!waktuMulai || !waktuSelesai) {
                document.querySelectorAll('.item-select').forEach(select => {
                    Array.from(select.options).forEach(opt => {
                        if (!opt.value) return;
                        const nama = opt.getAttribute('data-nama') || '';
                        const total = opt.getAttribute('data-stok-total') || '';
                        opt.textContent = `${nama} (Kapasitas: ${total} unit)`;
                    });
                });
                return;
            }

            fetch(`{{ route('pelapor.peminjaman.cekSemuaStok') }}?waktu_mulai=${encodeURIComponent(waktuMulai)}&waktu_selesai=${encodeURIComponent(waktuSelesai)}`)
                .then(res => res.json())
                .then(res => {
                    if (res.success && Array.isArray(res.data)) {
                        const availMap = {};
                        res.data.forEach(item => {
                            availMap[item.id] = item;
                        });

                        document.querySelectorAll('.item-select').forEach(select => {
                            Array.from(select.options).forEach(opt => {
                                if (!opt.value) return;
                                const itemData = availMap[opt.value];
                                if (itemData) {
                                    if (itemData.available <= 0) {
                                        opt.textContent = `${itemData.nama} (Stok: 0 / ${itemData.stok_total}) - [Terpakai di jam ini]`;
                                    } else {
                                        opt.textContent = `${itemData.nama} (Tersedia: ${itemData.available} / ${itemData.stok_total})`;
                                    }
                                }
                            });
                        });
                    }
                })
                .catch(err => console.error('Gagal memuat status fasilitas:', err));
        }

        // ✅ 2. Cek Realtime Status & Tampilkan Jadwal Terpakai untuk Baris Item
        function checkRealtimeStock(row) {
            const select = row.querySelector('.item-select');
            const stokDisplay = row.querySelector('.stok-tersedia');
            const scheduleBox = row.querySelector('.schedule-box');
            const fasilitasId = select.value;
            const waktuMulai = document.getElementById('waktu_mulai').value;
            const waktuSelesai = document.getElementById('waktu_selesai').value;

            if (!fasilitasId) {
                stokDisplay.textContent = '-';
                stokDisplay.className = 'stok-tersedia font-semibold text-[#191333]';
                if (scheduleBox) {
                    scheduleBox.classList.add('hidden');
                    scheduleBox.innerHTML = '';
                }
                return;
            }

            const selectedOption = select.options[select.selectedIndex];
            const namaFasilitas = selectedOption.getAttribute('data-nama') || 'Fasilitas';

            if (!waktuMulai || !waktuSelesai) {
                stokDisplay.textContent = 'Pilih waktu dulu';
                stokDisplay.className = 'stok-tersedia font-semibold text-amber-600';
                if (scheduleBox) {
                    scheduleBox.classList.remove('hidden');
                    scheduleBox.innerHTML = `
                        <div class="flex items-center gap-2 text-amber-800 bg-amber-50/70 p-2.5 rounded-lg border border-amber-200/70">
                            <i data-lucide="info" class="w-4 h-4 text-amber-600 shrink-0"></i>
                            <span>Silakan tentukan <strong>Waktu Mulai</strong> dan <strong>Waktu Selesai</strong> di atas untuk melihat ketersediaan jam fasilitas ini.</span>
                        </div>
                    `;
                    if (window.lucide) lucide.createIcons();
                }
                return;
            }

            stokDisplay.textContent = 'Memeriksa...';
            stokDisplay.className = 'stok-tersedia font-semibold text-gray-500';

            fetch(`{{ route('pelapor.peminjaman.cekStok') }}?fasilitas_id=${fasilitasId}&waktu_mulai=${encodeURIComponent(waktuMulai)}&waktu_selesai=${encodeURIComponent(waktuSelesai)}`)
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        stokDisplay.textContent = 'Gagal memuat';
                        stokDisplay.className = 'stok-tersedia font-semibold text-rose-600';
                        return;
                    }

                    const isConflict = data.is_conflict;
                    const available = data.available;
                    const stokTotal = data.stok_total;
                    const schedules = data.schedules || [];
                    const tglMulai = formatDateOnly(waktuMulai);
                    const jamMulai = formatTimeOnly(waktuMulai);
                    const jamSelesai = formatTimeOnly(waktuSelesai);

                    // Update ringkasan status di baris
                    if (isConflict || available <= 0) {
                        stokDisplay.textContent = 'Bentrok / Habis';
                        stokDisplay.className = 'stok-tersedia font-semibold text-rose-600';
                    } else {
                        stokDisplay.textContent = `${available} unit (Tersedia)`;
                        stokDisplay.className = 'stok-tersedia font-semibold text-emerald-600';
                    }

                    // Render Schedule & Info Box
                    if (scheduleBox) {
                        scheduleBox.classList.remove('hidden');

                        let statusBanner = '';
                        if (isConflict || available <= 0) {
                            statusBanner = `
                                <div class="flex items-start gap-2.5 p-2.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-lg">
                                    <i data-lucide="alert-octagon" class="w-4 h-4 mt-0.5 text-rose-600 shrink-0"></i>
                                    <div>
                                        <p class="font-bold text-rose-900">Jadwal Bentrok pada Jam Tersebut!</p>
                                        <p class="text-[11px] text-rose-700">${namaFasilitas} sudah dipesan pada rentang jam pilihan Anda (${jamMulai} - ${jamSelesai}). Silakan pilih jam lain.</p>
                                    </div>
                                </div>
                            `;
                        } else {
                            statusBanner = `
                                <div class="flex items-start gap-2.5 p-2.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg">
                                    <i data-lucide="check-circle-2" class="w-4 h-4 mt-0.5 text-emerald-600 shrink-0"></i>
                                    <div>
                                        <p class="font-bold text-emerald-900">Tersedia pada Jam Pilihan Anda (${jamMulai} - ${jamSelesai})</p>
                                        <p class="text-[11px] text-emerald-700">Tersedia ${available} unit siap digunakan untuk waktu ini.</p>
                                    </div>
                                </div>
                            `;
                        }

                        // Daftar Pemakaian di Hari Tersebut
                        let scheduleListHtml = '';
                        if (schedules.length > 0) {
                            scheduleListHtml = `
                                <div class="pt-2 border-t border-[#ecdfd0] space-y-2">
                                    <div class="flex items-center justify-between text-xs font-bold text-[#2a2338]">
                                        <span class="flex items-center gap-1.5">
                                            <i data-lucide="calendar" class="w-3.5 h-3.5 text-[#f2723b]"></i>
                                            Jadwal Pemakaian pada ${tglMulai}:
                                        </span>
                                        <span class="text-[11px] font-normal text-[#7a7189]">${schedules.length} peminjaman</span>
                                    </div>
                                    <div class="space-y-1.5">
                                        ${schedules.map(sc => `
                                            <div class="flex items-center justify-between p-2 bg-[#fbf7f0] border border-[#ecdfd0] rounded-lg text-[11px]">
                                                <div class="flex items-center gap-2">
                                                    <span class="inline-block w-2 h-2 rounded-full bg-rose-500"></span>
                                                    <span class="font-bold text-[#191333]">${sc.jam_range}</span>
                                                    <span class="text-[#7a7189]">• Peminjam: <strong class="text-[#2a2338]">${sc.peminjam}</strong></span>
                                                    ${sc.keperluan ? `<span class="italic text-gray-500 hidden sm:inline">(${sc.keperluan})</span>` : ''}
                                                </div>
                                                <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded bg-rose-100 text-rose-700">Terisi</span>
                                            </div>
                                        `).join('')}
                                    </div>
                                    <p class="text-[11px] text-[#7a7189] font-medium">
                                        💡 <em>Fasilitas ini dapat dipinjam lebih dari 1x dalam satu hari, asalkan memilih jam yang tidak bertabrakan dengan jadwal di atas.</em>
                                    </p>
                                </div>
                            `;
                        } else {
                            scheduleListHtml = `
                                <div class="pt-2 border-t border-[#ecdfd0] flex items-center gap-2 text-emerald-800 text-xs">
                                    <i data-lucide="sparkles" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                                    <span>Belum ada peminjaman lain pada tanggal <strong>${tglMulai}</strong>. Fasilitas ini bebas dipinjam sepanjang hari.</span>
                                </div>
                            `;
                        }

                        scheduleBox.innerHTML = statusBanner + scheduleListHtml;
                        if (window.lucide) lucide.createIcons();
                    }
                })
                .catch(() => {
                    stokDisplay.textContent = '-';
                });
        }

        // ✅ 3. Fitur Tambah Row Item Baru
        document.getElementById('add-item').addEventListener('click', function() {
            const container = document.getElementById('items-container');
            const newRow = container.children[0].cloneNode(true);

            // Reset select dan hapus atribut selected
            const select = newRow.querySelector('select');
            select.value = '';
            Array.from(select.options).forEach(opt => opt.removeAttribute('selected'));

            // Reset input number
            const input = newRow.querySelector('input[type="number"]');
            if (input) input.value = 1;

            // Reset tampilan stok
            const stokText = newRow.querySelector('.stok-tersedia');
            if (stokText) {
                stokText.textContent = '-';
                stokText.className = 'stok-tersedia font-semibold text-[#191333]';
            }

            // Reset schedule box
            const scheduleBox = newRow.querySelector('.schedule-box');
            if (scheduleBox) {
                scheduleBox.classList.add('hidden');
                scheduleBox.innerHTML = '';
            }

            // Re-index atribut name
            const index = itemCount;
            newRow.querySelectorAll('[name]').forEach(el => {
                el.name = el.name.replace(/\[\d+\]/, '[' + index + ']');
            });

            // Re-index ID pendukung
            const stokInfo = newRow.querySelector('[id^="stok-info-"]');
            if (stokInfo) stokInfo.id = 'stok-info-' + index;

            // Tampilkan tombol Hapus
            const removeBtn = newRow.querySelector('.remove-item');
            removeBtn.classList.remove('hidden');

            container.appendChild(newRow);
            itemCount++;

            if (window.lucide) lucide.createIcons();
        });

        // ✅ 4. Hapus Row Item
        document.getElementById('items-container').addEventListener('click', function(e) {
            const btn = e.target.closest('.remove-item');
            if (btn) {
                const row = btn.closest('.item-row');
                if (document.querySelectorAll('.item-row').length > 1) {
                    row.remove();
                } else {
                    alert('Minimal 1 item harus dipilih.');
                }
            }
        });

        // ✅ 5. Update Informasi Stok ketika Select Fasilitas Berubah
        document.getElementById('items-container').addEventListener('change', function(e) {
            if (e.target.classList.contains('item-select')) {
                checkRealtimeStock(e.target.closest('.item-row'));
            }
        });

        // ✅ 6. Re-check Stok ketika Jam Mulai / Selesai Berubah
        ['waktu_mulai', 'waktu_selesai'].forEach(id => {
            document.getElementById(id).addEventListener('change', function() {
                checkAllAvailability();
                document.querySelectorAll('.item-row').forEach(row => {
                    checkRealtimeStock(row);
                });
            });
        });

        // ✅ 5. Drag & Drop File Pendukung
        const fileInput = document.getElementById('file_pendukung');
        const fileNameDisplay = document.getElementById('file-name-pendukung');
        const dropzonePendukung = document.getElementById('dropzone_pendukung');
        const dropzonePendukungPlaceholder = document.getElementById('dropzone_pendukung_placeholder');
        const dropzonePendukungPreview = document.getElementById('dropzone_pendukung_preview');
        const previewPendukungImage = document.getElementById('preview_pendukung_image');
        const previewPendukungIcon = document.getElementById('preview_pendukung_icon');
        const btnGantiPendukung = document.getElementById('btn_ganti_pendukung');
        const filePendukungError = document.getElementById('file_pendukung_error');
        const formPeminjaman = document.getElementById('formPeminjaman');

        function resetDropzonePendukung() {
            fileInput.value = '';
            dropzonePendukungPreview.classList.add('hidden');
            dropzonePendukungPlaceholder.classList.remove('hidden');
            previewPendukungImage.classList.add('hidden');
            previewPendukungImage.src = '';
            previewPendukungIcon.classList.add('hidden');
            fileNameDisplay.textContent = '';
            dropzonePendukung.classList.remove('border-[#f2723b]', 'bg-[#fbf7f0]', 'border-rose-500', 'bg-rose-50/50');
            dropzonePendukung.classList.add('border-[#ecdfd0]');
        }

        if (fileInput && fileNameDisplay) {
            fileInput.addEventListener('change', function() {
                const file = this.files && this.files[0];
                if (!file) {
                    resetDropzonePendukung();
                    return;
                }

                const maxSizeBytes = 5 * 1024 * 1024;
                if (file.size > maxSizeBytes) {
                    alert('Ukuran file terlalu besar. Maksimal 5MB.');
                    resetDropzonePendukung();
                    return;
                }

                // Sembunyikan pesan error wajib upload jika sudah memilih file
                if (filePendukungError) {
                    filePendukungError.classList.add('hidden');
                }

                fileNameDisplay.textContent = '📎 ' + file.name;
                dropzonePendukungPlaceholder.classList.add('hidden');
                dropzonePendukungPreview.classList.remove('hidden');
                dropzonePendukung.classList.remove('border-[#ecdfd0]', 'border-rose-500', 'bg-rose-50/50');
                dropzonePendukung.classList.add('border-[#f2723b]', 'bg-[#fbf7f0]');

                if (file.type && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewPendukungImage.src = e.target.result;
                        previewPendukungImage.classList.remove('hidden');
                        previewPendukungIcon.classList.add('hidden');
                    };
                    reader.readAsDataURL(file);
                } else {
                    previewPendukungImage.classList.add('hidden');
                    previewPendukungIcon.classList.remove('hidden');
                }
            });
        }

        if (dropzonePendukung && fileInput) {
            ['dragenter', 'dragover'].forEach(eventName => {
                dropzonePendukung.addEventListener(eventName, function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzonePendukung.classList.add('border-[#f2723b]', 'bg-[#fbf7f0]');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropzonePendukung.addEventListener(eventName, function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (!fileInput.files || fileInput.files.length === 0) {
                        dropzonePendukung.classList.remove('border-[#f2723b]');
                    }
                }, false);
            });

            dropzonePendukung.addEventListener('drop', function(e) {
                const dt = e.dataTransfer;
                const files = dt && dt.files;
                if (files && files.length > 0) {
                    fileInput.files = files;
                    fileInput.dispatchEvent(new Event('change'));
                }
            }, false);
        }

        if (btnGantiPendukung) {
            btnGantiPendukung.addEventListener('click', function() {
                resetDropzonePendukung();
                fileInput.click();
            });
        }

        // ✅ Validasi submit form jika berkas belum diunggah atau ada jadwal bentrok
        if (formPeminjaman && fileInput) {
            formPeminjaman.addEventListener('submit', function(e) {
                if (!fileInput.files || fileInput.files.length === 0) {
                    e.preventDefault();
                    if (filePendukungError) {
                        filePendukungError.classList.remove('hidden');
                    }
                    dropzonePendukung.classList.remove('border-[#ecdfd0]', 'border-[#f2723b]');
                    dropzonePendukung.classList.add('border-rose-500', 'bg-rose-50/50');
                    dropzonePendukung.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return false;
                }

                let conflictRow = null;
                document.querySelectorAll('.item-row').forEach(row => {
                    const stokDisplay = row.querySelector('.stok-tersedia');
                    if (stokDisplay && stokDisplay.textContent.includes('Bentrok')) {
                        conflictRow = row;
                    }
                });

                if (conflictRow) {
                    e.preventDefault();
                    alert('Pengajuan tidak dapat dikirim karena terdapat fasilitas yang jadwalnya bentrok. Silakan sesuaikan jam atau ganti fasilitas.');
                    conflictRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return false;
                }
            });
        }
    });
</script>
@endpush