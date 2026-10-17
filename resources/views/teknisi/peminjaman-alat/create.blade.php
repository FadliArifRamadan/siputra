@extends('layouts.teknisi')

@section('title', 'Ajukan Pinjaman Alat')

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
                    <h1 class="text-xl sm:text-2xl font-bold font-heading text-[#2a2338]">Ajukan Pinjaman Alat</h1>
                    <p class="text-xs sm:text-sm text-[#7a7189]">Isi formulir di bawah untuk mengajukan pinjaman alat kerja teknisi.</p>
                </div>
            </div>
            <a href="{{ route('teknisi.peminjaman-alat.index') }}"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#7a7189] hover:text-[#191333] transition-colors bg-[#fbf7f0] border border-[#ecdfd0] px-3.5 py-2 rounded-xl">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
            </a>
        </div>

        <!-- Alert Info RTK Card -->
        <div class="flex items-start gap-3 p-4 bg-[#fbf7f0] border border-[#ecdfd0] text-[#191333] rounded-xl text-xs font-medium">
            <div class="p-1 bg-white rounded-lg border border-[#ecdfd0] text-[#f2723b] shrink-0">
                <i data-lucide="info" class="w-4 h-4"></i>
            </div>
            <div class="leading-relaxed">
                Form ini khusus untuk meminjam <strong class="text-[#f2723b]">alat kerja (sarana)</strong> seperti bor, tangga, multitester, dan sejenisnya. Anda dapat menambahkan beberapa alat sekaligus. Seluruh pengajuan akan ditinjau dan disetujui oleh <strong class="text-[#2a2338]">RTK</strong>.
            </div>
        </div>

        <!-- Alert Error Flash -->
        @if (session('error'))
        <div class="flex items-center gap-3 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs font-medium">
            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0"></i>
            <div>{{ session('error') }}</div>
        </div>
        @endif

        <!-- Form Card -->
        <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm p-6 sm:p-8">
            <form action="{{ route('teknisi.peminjaman-alat.store') }}" method="POST" id="formPinjamanAlat" class="space-y-6">
                @csrf

                <!-- Section Multi-Item Alat -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-[#2a2338] uppercase tracking-wider">
                            Daftar Alat Dipinjam <span class="text-rose-500">*</span>
                        </label>
                        <button type="button" id="btnAddItem" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-[#f2723b]/10 text-[#f2723b] hover:bg-[#f2723b]/20 transition-all">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span>Tambah Alat</span>
                        </button>
                    </div>

                    <!-- Container Dynamic Item Rows -->
                    <div id="itemsContainer" class="space-y-3">
                        <!-- Baris Alat Awal -->
                        <div class="item-row grid grid-cols-1 md:grid-cols-12 gap-3 p-4 rounded-xl bg-[#fbf7f0]/60 border border-[#ecdfd0] items-end">
                            <div class="md:col-span-7 space-y-1">
                                <label class="block text-[11px] font-semibold text-[#7a7189]">Pilih Alat</label>
                                <select name="items[0][fasilitas_id]" class="select-fasilitas w-full bg-white text-[#191333] text-xs rounded-xl border border-[#ecdfd0] focus:ring-2 focus:ring-[#f2723b] focus:border-[#f2723b] py-2 px-3 transition" required>
                                    <option value="">-- Pilih Alat --</option>
                                    @foreach ($fasilitas as $item)
                                        <option value="{{ $item->id }}"
                                            data-stok-total="{{ $item->stok_total }}"
                                            data-available="{{ method_exists($item, 'getAvailableStock') ? $item->getAvailableStock() : $item->stok_total }}"
                                            data-status="{{ $item->status_kondisi ?? $item->status ?? 'baik' }}">
                                            {{ $item->nama_fasilitas }} 
                                            (Stok: {{ method_exists($item, 'getAvailableStock') ? $item->getAvailableStock() : $item->stok_total }}/{{ $item->stok_total }})
                                            @if(($item->status_kondisi ?? '') === 'rusak_ringan') - ⚠️ Rusak Ringan @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-4 space-y-1">
                                <label class="block text-[11px] font-semibold text-[#7a7189]">Jumlah Unit</label>
                                <input type="number" name="items[0][jumlah_dipinjam]" class="input-jumlah w-full bg-white text-[#191333] text-xs rounded-xl border border-[#ecdfd0] focus:ring-2 focus:ring-[#f2723b] py-2 px-3 transition" min="1" value="1" required>
                            </div>

                            <div class="md:col-span-1 flex justify-end">
                                <button type="button" class="btn-remove-item p-2 rounded-xl text-rose-500 hover:bg-rose-50 transition hidden">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>

                            <!-- Dynamic Info Label for Stock & Status -->
                            <div class="item-info md:col-span-12 text-[11px] text-[#7a7189] hidden pt-1 border-t border-[#ecdfd0]/60"></div>
                        </div>
                    </div>
                </div>

                <!-- Grid: Waktu Mulai & Selesai -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">

                    <!-- Waktu Mulai -->
                    <div class="space-y-1.5">
                        <label for="waktu_mulai" class="block text-xs font-bold text-[#2a2338] uppercase tracking-wider">
                            Waktu Mulai <span class="text-rose-500">*</span>
                        </label>
                        <input type="datetime-local" name="waktu_mulai" id="waktu_mulai" value="{{ old('waktu_mulai') }}" required
                            class="w-full bg-[#fbf7f0] text-[#191333] text-xs sm:text-sm rounded-xl border border-[#ecdfd0] focus:ring-2 focus:ring-[#f2723b] py-2.5 px-3 transition @error('waktu_mulai') border-rose-500 @enderror">
                        <p class="text-[11px] text-[#7a7189] font-medium flex items-center gap-1">
                            <i data-lucide="clock" class="w-3 h-3 text-[#f2723b]"></i> Minimal 1 hari dari sekarang.
                        </p>
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

                <!-- Keperluan -->
                <div class="space-y-1.5">
                    <label for="keperluan" class="block text-xs font-bold text-[#2a2338] uppercase tracking-wider">
                        Keperluan Pinjaman Alat <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="keperluan" id="keperluan" rows="4" required
                        placeholder="Jelaskan keperluan peminjaman alat kerja secara rinci (misal: perbaikan AC Gedung B, instalasi jaringan, dll)..."
                        class="w-full bg-[#fbf7f0] text-[#191333] text-xs sm:text-sm rounded-xl border border-[#ecdfd0] focus:ring-2 focus:ring-[#f2723b] p-3 transition @error('keperluan') border-rose-500 @enderror">{{ old('keperluan') }}</textarea>
                    @error('keperluan')
                    <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#ecdfd0]">
                    <a href="{{ route('teknisi.peminjaman-alat.index') }}" class="px-5 py-2.5 text-xs font-semibold text-[#7a7189] hover:text-[#191333] transition">
                        Batal
                    </a>
                    <button type="submit" id="submitBtn" class="inline-flex items-center gap-2 bg-[#191333] hover:bg-[#2f2559] text-white px-6 py-2.5 rounded-xl text-xs font-semibold shadow-sm transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <i data-lucide="send" class="w-4 h-4 text-[#f2723b]"></i> Ajukan Pinjaman
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

        const itemsContainer = document.getElementById('itemsContainer');
        const btnAddItem = document.getElementById('btnAddItem');
        const submitBtn = document.getElementById('submitBtn');
        let itemIndex = 1;

        // Dynamic item row template options HTML
        const fasilitasOptionsHtml = `
            <option value="">-- Pilih Alat --</option>
            @foreach ($fasilitas as $item)
                <option value="{{ $item->id }}"
                    data-stok-total="{{ $item->stok_total }}"
                    data-available="{{ method_exists($item, 'getAvailableStock') ? $item->getAvailableStock() : $item->stok_total }}"
                    data-status="{{ $item->status_kondisi ?? $item->status ?? 'baik' }}">
                    {{ $item->nama_fasilitas }} 
                    (Stok: {{ method_exists($item, 'getAvailableStock') ? $item->getAvailableStock() : $item->stok_total }}/{{ $item->stok_total }})
                    @if(($item->status_kondisi ?? '') === 'rusak_ringan') - ⚠️ Rusak Ringan @endif
                </option>
            @endforeach
        `;

        // Function: Tambah Baris Alat Baru
        btnAddItem.addEventListener('click', function() {
            const rowDiv = document.createElement('div');
            rowDiv.className = 'item-row grid grid-cols-1 md:grid-cols-12 gap-3 p-4 rounded-xl bg-[#fbf7f0]/60 border border-[#ecdfd0] items-end';
            rowDiv.innerHTML = `
                <div class="md:col-span-7 space-y-1">
                    <label class="block text-[11px] font-semibold text-[#7a7189]">Pilih Alat</label>
                    <select name="items[${itemIndex}][fasilitas_id]" class="select-fasilitas w-full bg-white text-[#191333] text-xs rounded-xl border border-[#ecdfd0] focus:ring-2 focus:ring-[#f2723b] focus:border-[#f2723b] py-2 px-3 transition" required>
                        ${fasilitasOptionsHtml}
                    </select>
                </div>
                <div class="md:col-span-4 space-y-1">
                    <label class="block text-[11px] font-semibold text-[#7a7189]">Jumlah Unit</label>
                    <input type="number" name="items[${itemIndex}][jumlah_dipinjam]" class="input-jumlah w-full bg-white text-[#191333] text-xs rounded-xl border border-[#ecdfd0] focus:ring-2 focus:ring-[#f2723b] py-2 px-3 transition" min="1" value="1" required>
                </div>
                <div class="md:col-span-1 flex justify-end">
                    <button type="button" class="btn-remove-item p-2 rounded-xl text-rose-500 hover:bg-rose-50 transition">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </div>
                <div class="item-info md:col-span-12 text-[11px] text-[#7a7189] hidden pt-1 border-t border-[#ecdfd0]/60"></div>
            `;
            itemsContainer.appendChild(rowDiv);
            itemIndex++;

            if (window.lucide) lucide.createIcons();
            updateRemoveButtons();
            validateAllRows();
        });

        // Function: Hapus Baris
        itemsContainer.addEventListener('click', function(e) {
            const removeBtn = e.target.closest('.btn-remove-item');
            if (removeBtn) {
                const row = removeBtn.closest('.item-row');
                if (itemsContainer.querySelectorAll('.item-row').length > 1) {
                    row.remove();
                    updateRemoveButtons();
                    validateAllRows();
                }
            }
        });

        // Toggle visibilitas tombol hapus
        function updateRemoveButtons() {
            const rows = itemsContainer.querySelectorAll('.item-row');
            rows.forEach(row => {
                const btn = row.querySelector('.btn-remove-item');
                if (rows.length > 1) {
                    btn.classList.remove('hidden');
                } else {
                    btn.classList.add('hidden');
                }
            });
        }

        // Validate Row & Update Info
        function validateRow(row) {
            const select = row.querySelector('.select-fasilitas');
            const inputJumlah = row.querySelector('.input-jumlah');
            const infoDiv = row.querySelector('.item-info');

            const option = select.options[select.selectedIndex];
            if (!option || !select.value) {
                infoDiv.classList.add('hidden');
                return true;
            }

            infoDiv.classList.remove('hidden');

            const available = parseInt(option.getAttribute('data-available')) || 0;
            const total = parseInt(option.getAttribute('data-stok-total')) || 0;
            const status = option.getAttribute('data-status');
            const jumlah = parseInt(inputJumlah.value) || 0;

            let isRowValid = true;
            let infoText = `Stok Tersedia: <strong class="text-emerald-600">${available}/${total}</strong> unit.`;

            if (status === 'rusak_berat' || status === 'maintenance') {
                isRowValid = false;
                infoText = `<span class="text-rose-600 font-bold"><i data-lucide="alert-circle" class="w-3.5 h-3.5 inline"></i> Alat tidak layak dipinjam (Kondisi: ${status.replace('_', ' ')})</span>`;
            } else if (jumlah > available) {
                isRowValid = false;
                infoText += ` <span class="text-rose-600 font-bold ml-2"><i data-lucide="alert-triangle" class="w-3.5 h-3.5 inline"></i> Jumlah melebih stok (${jumlah} diminta)</span>`;
            }

            infoDiv.innerHTML = infoText;
            if (window.lucide) lucide.createIcons();

            return isRowValid;
        }

        function validateAllRows() {
            const rows = itemsContainer.querySelectorAll('.item-row');
            let allValid = true;

            // Memastikan tidak ada alat ganda yang dipilih
            const selectedIds = [];

            rows.forEach(row => {
                const select = row.querySelector('.select-fasilitas');
                if (select.value) {
                    if (selectedIds.includes(select.value)) {
                        allValid = false;
                        const infoDiv = row.querySelector('.item-info');
                        infoDiv.classList.remove('hidden');
                        infoDiv.innerHTML = `<span class="text-rose-600 font-bold"><i data-lucide="alert-circle" class="w-3.5 h-3.5 inline"></i> Alat ini sudah dipilih di baris lain.</span>`;
                    } else {
                        selectedIds.push(select.value);
                        if (!validateRow(row)) {
                            allValid = false;
                        }
                    }
                } else {
                    allValid = false;
                }
            });

            submitBtn.disabled = !allValid;
        }

        // Listener input & change
        itemsContainer.addEventListener('change', function(e) {
            if (e.target.classList.contains('select-fasilitas') || e.target.classList.contains('input-jumlah')) {
                validateAllRows();
            }
        });

        itemsContainer.addEventListener('input', function(e) {
            if (e.target.classList.contains('input-jumlah')) {
                validateAllRows();
            }
        });

        updateRemoveButtons();
    });
</script>
@endpush