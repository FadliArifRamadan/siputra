@extends(auth()->check() && auth()->user()->hasRole('bikem') ? 'layouts.bikem' : 'layouts.pelapor')

@section('title', 'Edit Peminjaman Fasilitas')

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
                    <h1 class="text-xl sm:text-2xl font-bold font-heading text-[#2a2338]">Edit Pengajuan Peminjaman</h1>
                    <p class="text-xs sm:text-sm text-[#7a7189]">Perbarui rincian pengajuan izin peminjaman fasilitas Anda.</p>
                </div>
            </div>
            <a href="{{ route('pelapor.peminjaman.index') }}"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#7a7189] hover:text-[#191333] transition-colors bg-[#fbf7f0] border border-[#ecdfd0] px-3.5 py-2 rounded-xl">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Batal
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
            <form action="{{ route('pelapor.peminjaman.update', $peminjaman) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Keperluan -->
                <div class="space-y-1.5">
                    <label for="keperluan" class="block text-xs font-bold text-[#2a2338] uppercase tracking-wider">
                        Keperluan Peminjaman <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="keperluan" id="keperluan" rows="3" required
                        placeholder="Jelaskan keperluan peminjaman secara rinci..."
                        class="w-full bg-[#fbf7f0] text-[#191333] text-xs sm:text-sm rounded-xl border border-[#ecdfd0] focus:ring-2 focus:ring-[#f2723b] focus:border-[#f2723b] p-3 transition @error('keperluan') border-rose-500 @enderror">{{ old('keperluan', $peminjaman->keperluan) }}</textarea>
                    @error('keperluan')
                    <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Catatan Peminjam -->
                <div class="space-y-1.5">
                    <label for="catatan_peminjam" class="block text-xs font-bold text-[#2a2338] uppercase tracking-wider">
                        Catatan Tambahan <span class="text-xs text-gray-400 lowercase">(opsional)</span>
                    </label>
                    <textarea name="catatan_peminjam" id="catatan_peminjam" rows="2"
                        placeholder="Catatan tambahan..."
                        class="w-full bg-[#fbf7f0] text-[#191333] text-xs sm:text-sm rounded-xl border border-[#ecdfd0] focus:ring-2 focus:ring-[#f2723b] focus:border-[#f2723b] p-3 transition @error('catatan_peminjam') border-rose-500 @enderror">{{ old('catatan_peminjam', $peminjaman->catatan_peminjam) }}</textarea>
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
                        <input type="datetime-local" name="waktu_mulai" id="waktu_mulai"
                            value="{{ old('waktu_mulai', $peminjaman->waktu_mulai ? $peminjaman->waktu_mulai->format('Y-m-d\TH:i') : '') }}" required
                            class="w-full bg-[#fbf7f0] text-[#191333] text-xs sm:text-sm rounded-xl border border-[#ecdfd0] focus:ring-2 focus:ring-[#f2723b] py-2.5 px-3 transition @error('waktu_mulai') border-rose-500 @enderror">
                        @error('waktu_mulai')
                        <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                        {{-- Hint ruangan: muncul dinamis via JS --}}
                        <div id="hint-ruangan" class="hidden flex items-center gap-1.5 text-xs text-amber-700 font-medium bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                            <i data-lucide="info" class="w-3.5 h-3.5 shrink-0"></i>
                            Peminjaman <strong>ruangan</strong> harus diajukan minimal <strong>2 hari sebelumnya (H-2)</strong>.
                        </div>
                    </div>

                    <!-- Waktu Selesai -->
                    <div class="space-y-1.5">
                        <label for="waktu_selesai" class="block text-xs font-bold text-[#2a2338] uppercase tracking-wider">
                            Waktu Selesai <span class="text-rose-500">*</span>
                        </label>
                        <input type="datetime-local" name="waktu_selesai" id="waktu_selesai"
                            value="{{ old('waktu_selesai', $peminjaman->waktu_selesai ? $peminjaman->waktu_selesai->format('Y-m-d\TH:i') : '') }}" required
                            class="w-full bg-[#fbf7f0] text-[#191333] text-xs sm:text-sm rounded-xl border border-[#ecdfd0] focus:ring-2 focus:ring-[#f2723b] py-2.5 px-3 transition @error('waktu_selesai') border-rose-500 @enderror">
                        @error('waktu_selesai')
                        <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Multi-Item Section -->
                <div class="space-y-3">
                    <label class="block text-xs font-bold text-[#2a2338] uppercase tracking-wider">
                        Daftar Fasilitas yang Dipinjam <span class="text-rose-500">*</span>
                    </label>
                    
                    <div id="items-container" class="space-y-3">
                        @foreach ($peminjaman->items as $index => $item)
                        <div class="item-row bg-[#fbf7f0] p-3.5 rounded-xl border border-[#ecdfd0] space-y-3">
                            <div class="flex flex-wrap sm:flex-nowrap items-center gap-3">
                                <!-- Select Fasilitas -->
                                <div class="flex-1 min-w-[200px]">
                                    <select name="items[{{ $index }}][fasilitas_id]" 
                                        class="item-select w-full bg-white text-[#191333] text-xs sm:text-sm rounded-lg border border-[#ecdfd0] focus:ring-2 focus:ring-[#f2723b] p-2.5 transition"
                                        required>
                                        <option value="">-- Pilih Fasilitas --</option>
                                        @foreach ($fasilitas as $fasItem)
                                        <option value="{{ $fasItem->id }}"
                                            data-stok-total="{{ $fasItem->stok_total }}"
                                            data-kategori="{{ $fasItem->kategori }}"
                                            data-nama="{{ $fasItem->nama_fasilitas }}"
                                            {{ old("items.{$index}.fasilitas_id", $item->fasilitas_id) == $fasItem->id ? 'selected' : '' }}>
                                            {{ $fasItem->nama_fasilitas }} (Kapasitas: {{ $fasItem->stok_total }} unit)
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Jumlah Input -->
                                <div class="w-28">
                                    <input type="number" 
                                        name="items[{{ $index }}][jumlah_dipinjam]" 
                                        value="{{ old('items.' . $index . '.jumlah_dipinjam', $item->jumlah_dipinjam) }}"
                                        min="1"
                                        placeholder="Jumlah"
                                        class="item-jumlah w-full bg-white text-[#191333] text-xs sm:text-sm rounded-lg border border-[#ecdfd0] focus:ring-2 focus:ring-[#f2723b] p-2.5 transition"
                                        required>
                                </div>

                                <!-- Info Stok -->
                                <div class="text-xs text-[#7a7189] w-28 shrink-0" id="stok-info-{{ $index }}">
                                    Status: <span class="stok-tersedia font-semibold text-[#2a2338]">-</span>
                                </div>

                                <!-- Hapus Button -->
                                <button type="button" class="remove-item inline-flex items-center gap-1 text-xs text-rose-600 hover:text-rose-800 font-medium px-2 py-1 rounded-md hover:bg-rose-50 transition {{ $loop->first && count($peminjaman->items) == 1 ? 'hidden' : '' }}">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i> Hapus
                                </button>
                            </div>

                            <!-- Detail Jadwal & Ketersediaan Jam -->
                            <div class="schedule-box hidden text-xs rounded-xl p-3 bg-white border border-[#ecdfd0] space-y-2"></div>
                        </div>
                        @endforeach
                    </div>

                    <button type="button" id="add-item" class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-700 hover:text-amber-800 bg-amber-50 hover:bg-amber-100 border border-amber-200 px-3.5 py-2 rounded-xl transition">
                        <i data-lucide="plus" class="w-4 h-4"></i> Tambah Item
                    </button>
                    <p class="text-[11px] text-[#7a7189]">Klik "Tambah Item" untuk menambahkan lebih dari satu fasilitas dalam satu pengajuan.</p>
                </div>

                <!-- Upload Berkas Pendukung -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-[#2a2338] uppercase tracking-wider">
                        Upload Berkas Pendukung <span class="text-xs text-gray-400 lowercase">(opsional)</span>
                    </label>

                    @if ($peminjaman->file_pendukung)
                    <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between text-xs text-emerald-800">
                        <span class="flex items-center gap-2">
                            <i data-lucide="paperclip" class="w-4 h-4 text-emerald-600"></i>
                            File saat ini: <strong>{{ basename($peminjaman->file_pendukung) }}</strong>
                        </span>
                        <span class="text-[11px] text-emerald-600">(Upload baru untuk mengganti)</span>
                    </div>
                    @endif

                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed border-[#ecdfd0] rounded-2xl hover:border-amber-500 bg-[#fbf7f0]/50 transition">
                        <div class="space-y-1 text-center">
                            <i data-lucide="upload-cloud" class="mx-auto h-10 w-10 text-[#7a7189]"></i>
                            <div class="flex text-xs text-[#7a7189] justify-center">
                                <label for="file_pendukung" class="relative cursor-pointer font-semibold text-amber-700 hover:text-amber-800 focus-within:outline-none">
                                    <span>Upload file baru</span>
                                    <input id="file_pendukung" name="file_pendukung" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="sr-only">
                                </label>
                                <p class="pl-1">atau drag and drop</p>
                            </div>
                            <p class="text-[11px] text-[#7a7189]">PDF, DOC, DOCX, JPG, JPEG, PNG (Maks. 5MB)</p>
                            <p id="file-name-pendukung" class="text-xs text-emerald-600 font-medium hidden mt-2"></p>
                        </div>
                    </div>
                    @error('file_pendukung')
                    <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Info Status Warning -->
                <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 flex items-start gap-3 text-amber-800">
                    <i data-lucide="info" class="w-5 h-5 text-amber-600 shrink-0 mt-0.5"></i>
                    <div class="space-y-0.5">
                        <p class="text-xs font-bold">Status Pengajuan Saat Ini: <span class="uppercase">{{ $peminjaman->status_pinjam }}</span></p>
                        <p class="text-[11px] text-amber-700">Perubahan hanya diizinkan selama status pengajuan masih dalam antrean (pending). Pengajuan yang sudah disetujui, berjalan, atau ditolak tidak dapat diubah kembali.</p>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#ecdfd0]">
                    <a href="{{ route('pelapor.peminjaman.index') }}" class="px-5 py-2.5 text-xs font-semibold text-[#7a7189] hover:text-[#191333] transition">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white px-6 py-2.5 rounded-xl text-xs font-semibold shadow-sm transition">
                        <i data-lucide="check-circle-2" class="w-4 h-4"></i> Perbarui Peminjaman
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

    let itemCount = {{ count($peminjaman->items) }};
    const excludeId = {{ $peminjaman->id }};

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

        fetch(`{{ route('pelapor.peminjaman.cekSemuaStok') }}?waktu_mulai=${encodeURIComponent(waktuMulai)}&waktu_selesai=${encodeURIComponent(waktuSelesai)}&exclude_peminjaman_id=${excludeId}`)
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

    // ✅ 2. AJAX Check Stok Realtime & Tampilkan Jadwal Pemakaian
    function checkRealtimeStock(row) {
        const select = row.querySelector('.item-select');
        const stokDisplay = row.querySelector('.stok-tersedia');
        const scheduleBox = row.querySelector('.schedule-box');
        const fasilitasId = select.value;
        const waktuMulai = document.getElementById('waktu_mulai').value;
        const waktuSelesai = document.getElementById('waktu_selesai').value;

        if (!fasilitasId) {
            stokDisplay.textContent = '-';
            stokDisplay.className = 'stok-tersedia font-semibold text-[#2a2338]';
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
                        <span>Silakan tentukan <strong>Waktu Mulai</strong> dan <strong>Waktu Selesai</strong> di atas untuk memeriksa ketersediaan jam fasilitas ini.</span>
                    </div>
                `;
                if (window.lucide) lucide.createIcons();
            }
            return;
        }

        stokDisplay.textContent = 'Memeriksa...';
        stokDisplay.className = 'stok-tersedia font-semibold text-gray-500';

        fetch(`{{ route('pelapor.peminjaman.cekStok') }}?fasilitas_id=${fasilitasId}&waktu_mulai=${encodeURIComponent(waktuMulai)}&waktu_selesai=${encodeURIComponent(waktuSelesai)}&exclude_peminjaman_id=${excludeId}`)
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    stokDisplay.textContent = 'Gagal memuat';
                    stokDisplay.className = 'stok-tersedia font-semibold text-rose-600';
                    return;
                }

                const isConflict = data.is_conflict;
                const available = data.available;
                const schedules = data.schedules || [];
                const tglMulai = formatDateOnly(waktuMulai);
                const jamMulai = formatTimeOnly(waktuMulai);
                const jamSelesai = formatTimeOnly(waktuSelesai);

                if (isConflict || available <= 0) {
                    stokDisplay.textContent = 'Bentrok / Habis';
                    stokDisplay.className = 'stok-tersedia font-semibold text-rose-600';
                } else {
                    stokDisplay.textContent = `${available} unit (Tersedia)`;
                    stokDisplay.className = 'stok-tersedia font-semibold text-emerald-600';
                }

                if (scheduleBox) {
                    scheduleBox.classList.remove('hidden');

                    let statusBanner = '';
                    if (isConflict || available <= 0) {
                        statusBanner = `
                            <div class="flex items-start gap-2.5 p-2.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-lg">
                                <i data-lucide="alert-octagon" class="w-4 h-4 mt-0.5 text-rose-600 shrink-0"></i>
                                <div>
                                    <p class="font-bold text-rose-900">Jadwal Bentrok pada Jam Tersebut!</p>
                                    <p class="text-[11px] text-rose-700">${namaFasilitas} sudah dipesan pada rentang jam pilihan Anda (${jamMulai} - ${jamSelesai}). Silakan pilih slot jam lain.</p>
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

    // Run ketersediaan awal untuk semua row saat halaman dibuka
    checkAllAvailability();
    document.querySelectorAll('.item-row').forEach(row => {
        checkRealtimeStock(row);
    });

    // Preview File Input
    const fileInput = document.getElementById('file_pendukung');
    const fileNameDisplay = document.getElementById('file-name-pendukung');
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                fileNameDisplay.textContent = 'File terpilih: ' + e.target.files[0].name;
                fileNameDisplay.classList.remove('hidden');
            } else {
                fileNameDisplay.classList.add('hidden');
            }
        });
    }

    // Tambah Row Item Baru
    document.getElementById('add-item').addEventListener('click', function() {
        const container = document.getElementById('items-container');
        const firstRow = container.querySelector('.item-row');
        const newRow = firstRow.cloneNode(true);
        
        const select = newRow.querySelector('select');
        select.value = '';
        Array.from(select.options).forEach(opt => opt.removeAttribute('selected'));

        newRow.querySelectorAll('input[type="number"]').forEach(input => input.value = 1);
        
        newRow.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace(/\[\d+\]/, '[' + itemCount + ']');
        });
        
        const stokInfo = newRow.querySelector('[id^="stok-info-"]');
        if (stokInfo) {
            stokInfo.id = 'stok-info-' + itemCount;
            const stokText = stokInfo.querySelector('.stok-tersedia');
            if (stokText) {
                stokText.textContent = '-';
                stokText.className = 'stok-tersedia font-semibold text-[#2a2338]';
            }
        }

        const scheduleBox = newRow.querySelector('.schedule-box');
        if (scheduleBox) {
            scheduleBox.classList.add('hidden');
            scheduleBox.innerHTML = '';
        }
        
        const removeBtn = newRow.querySelector('.remove-item');
        if (removeBtn) {
            removeBtn.classList.remove('hidden');
        }
        
        container.appendChild(newRow);
        itemCount++;

        if (window.lucide) lucide.createIcons();
    });

    // Hapus Row
    document.getElementById('items-container').addEventListener('click', function(e) {
        const btn = e.target.closest('.remove-item');
        if (btn) {
            const rows = document.querySelectorAll('.item-row');
            if (rows.length > 1) {
                btn.closest('.item-row').remove();
            } else {
                alert('Minimal 1 item harus dipilih.');
            }
        }
    });

    // Event Listener Change Select
    document.getElementById('items-container').addEventListener('change', function(e) {
        if (e.target.classList.contains('item-select')) {
            checkRealtimeStock(e.target.closest('.item-row'));
        }
    });

    // Event Listener Change Jam
    ['waktu_mulai', 'waktu_selesai'].forEach(id => {
        document.getElementById(id).addEventListener('change', function() {
            checkAllAvailability();
            document.querySelectorAll('.item-row').forEach(row => {
                checkRealtimeStock(row);
            });
        });
    });

    // ✅ Tampilkan hint H-2 jika ada ruangan yang dipilih
    function updateHintRuangan() {
        const hintEl = document.getElementById('hint-ruangan');
        if (!hintEl) return;
        const hasRuangan = Array.from(document.querySelectorAll('.item-select')).some(sel => {
            const opt = sel.options[sel.selectedIndex];
            return opt && opt.getAttribute('data-kategori') === 'prasarana';
        });
        if (hasRuangan) {
            hintEl.classList.remove('hidden');
            if (window.lucide) lucide.createIcons();
        } else {
            hintEl.classList.add('hidden');
        }
    }

    document.getElementById('items-container').addEventListener('change', function(e) {
        if (e.target.classList.contains('item-select')) {
            updateHintRuangan();
        }
    });

    // Jalankan saat load (untuk edit form yang sudah ada item terpilih)
    updateHintRuangan();

    // Validasi form saat submit jika ada jadwal bentrok
    const editForm = document.querySelector('form');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            let conflictRow = null;
            document.querySelectorAll('.item-row').forEach(row => {
                const stokDisplay = row.querySelector('.stok-tersedia');
                if (stokDisplay && stokDisplay.textContent.includes('Bentrok')) {
                    conflictRow = row;
                }
            });

            if (conflictRow) {
                e.preventDefault();
                alert('Pengajuan tidak dapat disimpan karena terdapat fasilitas yang jadwalnya bentrok. Silakan sesuaikan jam atau ganti fasilitas.');
                conflictRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return false;
            }
        });
    }
});
</script>
@endpush