@extends(auth()->check() && auth()->user()->hasRole('bikem') ? 'layouts.bikem' : 'layouts.pelapor')

@section('title', 'Daftar Peminjaman Fasilitas')

@section('content')
<div class="py-4 sm:py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Top Header Card -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-[#fbf7f0] border border-[#ecdfd0] rounded-xl text-[#f2723b]">
                    <i data-lucide="calendar" class="w-6 h-6"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold font-heading text-[#2a2338]">Daftar Peminjaman Fasilitas</h1>
                    <p class="text-xs sm:text-sm text-[#7a7189]">Pantau status pengajuan izin dan peminjaman fasilitas Anda.</p>
                </div>
            </div>
            <a href="{{ route('pelapor.peminjaman.create') }}"
                class="inline-flex items-center gap-2 bg-[#191333] hover:bg-[#2f2559] text-white px-4 py-2.5 rounded-xl text-xs font-semibold shadow-sm transition shrink-0">
                <i data-lucide="plus" class="w-4 h-4 text-[#f2723b]"></i> Ajukan Peminjaman
            </a>
        </div>

        <!-- Alert Flashes -->
        @if (session('success'))
        <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-medium">
            <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0"></i>
            <div>{{ session('success') }}</div>
        </div>
        @endif

        @if (session('error'))
        <div class="flex items-center gap-3 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs font-medium">
            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0"></i>
            <div>{{ session('error') }}</div>
        </div>
        @endif

        <!-- Content Card / Table -->
        <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm overflow-hidden">
            @if ($peminjaman->isEmpty())
            <div class="text-center py-12 px-4 space-y-3">
                <div class="p-4 bg-[#fbf7f0] rounded-full w-16 h-16 mx-auto flex items-center justify-center text-[#7a7189]">
                    <i data-lucide="calendar-x" class="w-8 h-8"></i>
                </div>
                <p class="text-sm font-medium text-[#2a2338]">Belum ada peminjaman fasilitas.</p>
                <a href="{{ route('pelapor.peminjaman.create') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#f2723b] hover:underline">
                    Ajukan peminjaman pertama Anda <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#ecdfd0]">
                    <thead class="bg-[#fbf7f0]">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 text-left text-[11px] font-bold text-[#7a7189] uppercase tracking-wider">No</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-[11px] font-bold text-[#7a7189] uppercase tracking-wider">Fasilitas Dipinjam</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-[11px] font-bold text-[#7a7189] uppercase tracking-wider">Total Item</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-[11px] font-bold text-[#7a7189] uppercase tracking-wider">Waktu Peminjaman</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-[11px] font-bold text-[#7a7189] uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3.5 text-right text-[11px] font-bold text-[#7a7189] uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#ecdfd0] bg-white text-xs">
                        @foreach ($peminjaman as $index => $item)
                        <tr class="hover:bg-[#fbf7f0]/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-[#7a7189]">
                                {{ $loop->iteration }}
                            </td>
                            <!-- Kolom Fasilitas (Multi-item Ringkasan) -->
                            <td class="px-6 py-4 font-semibold text-[#2a2338]">
                                @if($item->items && $item->items->count() > 0)
                                    <div class="space-y-0.5">
                                        @foreach($item->items as $detailItem)
                                            <div>{{ $detailItem->fasilitas->nama_fasilitas ?? 'Fasilitas' }} <span class="text-xs text-[#7a7189] font-normal">({{ $detailItem->jumlah_dipinjam }} unit)</span></div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400 italic">Tidak ada item</span>
                                @endif
                            </td>
                            <!-- Ringkasan Jumlah Unit -->
                            <td class="px-6 py-4 whitespace-nowrap text-[#191333] font-medium">
                                {{ $item->items ? $item->items->count() : 0 }} Jenis 
                                <br>
                                <span class="text-xs text-gray-500 font-normal">({{ $item->items ? $item->items->sum('jumlah_dipinjam') : 0 }} unit)</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-[#7a7189] leading-tight">
                                <span class="font-medium text-[#191333]">{{ \Carbon\Carbon::parse($item->waktu_mulai)->format('d/m/Y H:i') }}</span>
                                <span class="block text-[11px] text-[#7a7189]">s/d {{ \Carbon\Carbon::parse($item->waktu_selesai)->format('d/m/Y H:i') }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $badgeConfig = match($item->status_pinjam) {
                                        'pending', 'menunggu_bikem' => ['class' => 'bg-amber-100 text-amber-800 border-amber-200', 'label' => 'Menunggu BIKEM'],
                                        'perlu_revisi' => ['class' => 'bg-orange-100 text-orange-800 border-orange-200 animate-pulse', 'label' => 'Perlu Revisi'],
                                        'menunggu_rtk' => ['class' => 'bg-blue-100 text-blue-800 border-blue-200', 'label' => 'Menunggu RTK'],
                                        'disetujui' => ['class' => 'bg-emerald-100 text-emerald-800 border-emerald-200', 'label' => 'Disetujui'],
                                        'ditolak' => ['class' => 'bg-rose-100 text-rose-800 border-rose-200', 'label' => 'Ditolak'],
                                        'berjalan' => ['class' => 'bg-sky-100 text-sky-800 border-sky-200', 'label' => 'Berjalan'],
                                        'selesai' => ['class' => 'bg-gray-100 text-gray-700 border-gray-200', 'label' => 'Selesai'],
                                        default => ['class' => 'bg-gray-100 text-gray-700 border-gray-200', 'label' => ucfirst($item->status_pinjam)],
                                    };
                                @endphp
                                <span class="px-2.5 py-1 inline-flex text-[11px] font-bold rounded-xl border {{ $badgeConfig['class'] }}">
                                    {{ $badgeConfig['label'] }}
                                </span>
                            </td>
                            @php
                                $isHariH = \Carbon\Carbon::parse($item->waktu_mulai)->isPast();
                                $canEdit = in_array($item->status_pinjam, ['pending', 'menunggu_bikem', 'perlu_revisi']) && !$isHariH;
                            @endphp

                            <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Tombol Detail -->
                                    <a href="{{ route('pelapor.peminjaman.show', $item) }}"
                                        class="inline-flex items-center gap-1 text-sky-700 hover:text-sky-900 bg-sky-50 px-2.5 py-1 rounded-lg border border-sky-100 transition">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i> Detail
                                    </a>

                                    <!-- Tombol Surat (Tampil jika status disetujui, berjalan, atau selesai) -->
                                    @if (in_array($item->status_pinjam, ['disetujui', 'berjalan', 'selesai']))
                                    <a href="{{ route('surat.preview', $item->id) }}"
                                        target="_blank"
                                        class="inline-flex items-center gap-1 text-emerald-700 hover:text-emerald-900 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-100 transition">
                                        <i data-lucide="printer" class="w-3.5 h-3.5"></i> Surat
                                    </a>
                                    @endif

                                    <!-- Tombol Edit & Hapus / Kondisi Kunci -->
                                    @if ($canEdit)
                                    <a href="{{ route('pelapor.peminjaman.edit', $item) }}"
                                        class="inline-flex items-center gap-1 {{ $item->status_pinjam === 'perlu_revisi' ? 'text-orange-700 hover:text-orange-900 bg-orange-50 border-orange-200 font-bold' : 'text-amber-700 hover:text-amber-900 bg-amber-50 border-amber-100' }} px-2.5 py-1 rounded-lg border transition">
                                        <i data-lucide="{{ $item->status_pinjam === 'perlu_revisi' ? 'refresh-cw' : 'edit-3' }}" class="w-3.5 h-3.5"></i>
                                        {{ $item->status_pinjam === 'perlu_revisi' ? 'Revisi' : 'Edit' }}
                                    </a>

                                    <form action="{{ route('pelapor.peminjaman.destroy', $item) }}"
                                        method="POST" class="inline-block"
                                        onsubmit="return confirm('Yakin ingin menghapus pengajuan peminjaman ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 text-rose-700 hover:text-rose-900 bg-rose-50 px-2.5 py-1 rounded-lg border border-rose-100 transition">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                                        </button>
                                    </form>
                                    @elseif ($isHariH && in_array($item->status_pinjam, ['pending', 'menunggu_bikem']))
                                    <span class="inline-flex items-center gap-1 text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg text-[11px] italic">
                                        <i data-lucide="lock" class="w-3 h-3"></i> Read-only (hari H)
                                    </span>
                                    @elseif (!in_array($item->status_pinjam, ['disetujui', 'berjalan', 'selesai']))
                                    <span class="inline-flex items-center gap-1 text-slate-400 bg-slate-50 px-2.5 py-1 rounded-lg text-[11px] italic">
                                        <i data-lucide="lock" class="w-3 h-3"></i> Tidak dapat diubah
                                    </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-[#ecdfd0] bg-[#fbf7f0]/30">
                {{ $peminjaman->links() }}
            </div>
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
    });
</script>
@endpush