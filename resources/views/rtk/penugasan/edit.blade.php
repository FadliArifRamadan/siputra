@extends('layouts.rtk')

@section('title', 'Edit Penugasan Teknisi')

@push('styles')
<style>
    .availability-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.2rem 0.6rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .availability-badge.available { background: #d1fae5; color: #065f46; }
    .availability-badge.busy { background: #fee2e2; color: #991b1b; }
    .availability-badge.checking { background: #fef3c7; color: #92400e; }
</style>
@endpush

@section('content')
<div class="py-4 sm:py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Header Banner -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold font-heading text-[#2a2338] flex items-center gap-2">
                    <i data-lucide="edit-3" class="w-6 h-6 text-[#f2723b]"></i> Edit Penugasan Teknisi
                </h1>
                <p class="text-xs sm:text-sm text-[#7a7189] mt-1">
                    Perbarui teknisi bertugas, penyesuaian jadwal penanganan, atau instruksi kerja.
                </p>
            </div>
            <a href="{{ route('rtk.penugasan.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#7a7189] hover:text-[#2a2338] transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
            </a>
        </div>

        @if (session('error'))
        <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-800 p-4 rounded-r-xl shadow-sm flex items-center gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-500 shrink-0"></i>
            <span class="text-xs sm:text-sm font-medium">{{ session('error') }}</span>
        </div>
        @endif

        <!-- Card Form -->
        <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm overflow-hidden p-6 sm:p-8">
            <form action="{{ route('rtk.penugasan.update', $penugasan) }}" method="POST" id="penugasanForm" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Info Laporan (Read Only) -->
                <div class="bg-[#fbf7f0] p-4 sm:p-5 rounded-xl border border-[#ecdfd0] space-y-2">
                    <h3 class="text-xs font-bold text-[#2a2338] uppercase tracking-wider mb-3 flex items-center gap-1.5">
                        <i data-lucide="file-text" class="w-4 h-4 text-[#f2723b]"></i> Informasi Laporan
                    </h3>
                    <p class="text-xs sm:text-sm text-[#2a2338] flex items-center gap-2">
                        <span class="font-semibold text-[#7a7189] min-w-[120px]">Pelapor:</span>
                        <span class="font-medium">{{ $penugasan->pengajuanKerusakan->pelapor->nama ?? 'Tidak diketahui' }}</span>
                    </p>
                    <p class="text-xs sm:text-sm text-[#2a2338] flex items-center gap-2">
                        <span class="font-semibold text-[#7a7189] min-w-[120px]">Jenis Kerusakan:</span>
                        <span class="font-medium">{{ $penugasan->pengajuanKerusakan->jenis_kerusakan }}</span>
                    </p>
                    <p class="text-xs sm:text-sm text-[#2a2338] flex items-center gap-2">
                        <span class="font-semibold text-[#7a7189] min-w-[120px]">Lokasi:</span>
                        <span class="font-medium">{{ $penugasan->pengajuanKerusakan->lokasi_detail }}</span>
                    </p>
                    <p class="text-xs sm:text-sm text-[#2a2338] flex items-center gap-2">
                        <span class="font-semibold text-[#7a7189] min-w-[120px]">Status:</span>
                        <span class="font-semibold text-[#191333]">{{ ucfirst($penugasan->pengajuanKerusakan->status_laporan) }}</span>
                    </p>
                </div>

                <!-- Pilih Teknisi -->
                <div>
                    <label for="teknisi_id" class="block text-xs font-bold text-[#2a2338] mb-1.5 flex items-center justify-between">
                        <span>Pilih Teknisi <span class="text-rose-500">*</span></span>
                        <span id="availability-status"></span>
                    </label>
                    <select name="teknisi_id" 
                            id="teknisi_id"
                            class="w-full rounded-lg border-[#ecdfd0] shadow-sm focus:border-[#f2723b] focus:ring-[#f2723b] text-xs sm:text-sm font-medium text-[#2a2338] @error('teknisi_id') border-rose-500 @enderror"
                            required>
                        <option value="">Pilih teknisi</option>
                        @foreach ($teknisi as $item)
                            <option value="{{ $item->id }}" {{ old('teknisi_id', $penugasan->teknisi_id) == $item->id ? 'selected' : '' }}>
                                {{ $item->nama }} ({{ $item->nomor_induk }})
                            </option>
                        @endforeach
                    </select>
                    @error('teknisi_id')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jadwal Penanganan -->
                <div>
                    <label for="jadwal_penanganan" class="block text-xs font-bold text-[#2a2338] mb-1.5 flex items-center justify-between">
                        <span>Jadwal Penanganan <span class="text-rose-500">*</span></span>
                        <span id="schedule-status"></span>
                    </label>
                    <input type="datetime-local" 
                           name="jadwal_penanganan" 
                           id="jadwal_penanganan"
                           value="{{ old('jadwal_penanganan', $penugasan->jadwal_penanganan ? \Carbon\Carbon::parse($penugasan->jadwal_penanganan)->format('Y-m-d\TH:i') : '') }}"
                           class="w-full rounded-lg border-[#ecdfd0] shadow-sm focus:border-[#f2723b] focus:ring-[#f2723b] text-xs sm:text-sm font-medium text-[#2a2338] @error('jadwal_penanganan') border-rose-500 @enderror"
                           required>
                    @error('jadwal_penanganan')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Instruksi Kerja -->
                <div>
                    <label for="instruksi_kerja" class="block text-xs font-bold text-[#2a2338] mb-1.5">
                        Instruksi Kerja
                    </label>
                    <textarea name="instruksi_kerja" 
                              id="instruksi_kerja"
                              rows="4"
                              class="w-full rounded-lg border-[#ecdfd0] shadow-sm focus:border-[#f2723b] focus:ring-[#f2723b] text-xs sm:text-sm font-medium text-[#2a2338] @error('instruksi_kerja') border-rose-500 @enderror"
                              placeholder="Berikan instruksi kerja untuk teknisi (opsional)">{{ old('instruksi_kerja', $penugasan->instruksi_kerja) }}</textarea>
                    @error('instruksi_kerja')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="pt-4 border-t border-[#ecdfd0] flex items-center justify-between gap-4">
                    <a href="{{ route('rtk.penugasan.index') }}" 
                       class="bg-white border border-[#ecdfd0] text-[#2a2338] hover:bg-[#fbf7f0] px-5 py-2.5 rounded-xl transition-colors text-xs font-semibold flex items-center gap-2">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
                    </a>
                    <button type="submit" 
                            id="submitBtn"
                            class="bg-[#191333] hover:bg-[#2f2559] text-white px-6 py-2.5 rounded-xl transition-colors text-xs font-semibold shadow-sm flex items-center gap-2">
                        <i data-lucide="save" class="w-4 h-4 text-[#f2723b]"></i> Update Penugasan
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
        const teknisiSelect = document.getElementById('teknisi_id');
        const jadwalInput = document.getElementById('jadwal_penanganan');
        const availabilityStatus = document.getElementById('availability-status');
        const scheduleStatus = document.getElementById('schedule-status');
        const submitBtn = document.getElementById('submitBtn');
        const penugasanId = '{{ $penugasan->id }}';
        let checkTimeout = null;

        function checkAvailability() {
            const teknisiId = teknisiSelect.value;
            const jadwal = jadwalInput.value;

            if (!teknisiId || !jadwal) {
                availabilityStatus.innerHTML = '';
                scheduleStatus.innerHTML = '';
                submitBtn.disabled = false;
                return;
            }

            // Tampilkan loading
            availabilityStatus.innerHTML = '<span class="availability-badge checking"><i data-lucide="loader-2" class="w-3 h-3 animate-spin"></i> Mengecek...</span>';
            if (window.lucide) lucide.createIcons();

            // Debounce untuk menghindari terlalu banyak request
            clearTimeout(checkTimeout);
            checkTimeout = setTimeout(() => {
                let url = '{{ route("api.teknisi.availability") }}?teknisi_id=' + teknisiId + '&jadwal=' + encodeURIComponent(jadwal) + '&ignore_id=' + penugasanId;
                
                fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.available) {
                        availabilityStatus.innerHTML = '<span class="availability-badge available"><i data-lucide="check-circle" class="w-3 h-3"></i> Tersedia</span>';
                        submitBtn.disabled = false;
                    } else {
                        availabilityStatus.innerHTML = '<span class="availability-badge busy"><i data-lucide="x-circle" class="w-3 h-3"></i> ' + data.message + '</span>';
                        submitBtn.disabled = true;
                    }
                    if (window.lucide) lucide.createIcons();
                })
                .catch(error => {
                    console.error('Error checking availability:', error);
                    availabilityStatus.innerHTML = '<span class="availability-badge busy"><i data-lucide="alert-triangle" class="w-3 h-3"></i> Gagal mengecek</span>';
                    if (window.lucide) lucide.createIcons();
                });
            }, 500);
        }

        // Panggil langsung saat halaman pertama kali dibuka untuk mengecek status data yang ada
        checkAvailability();

        teknisiSelect.addEventListener('change', checkAvailability);
        jadwalInput.addEventListener('change', checkAvailability);
        jadwalInput.addEventListener('input', checkAvailability);

        if (window.lucide) lucide.createIcons();
    });
</script>
@endpush