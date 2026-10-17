@extends(auth()->check() && auth()->user()->hasRole('bikem') ? 'layouts.bikem' : 'layouts.pelapor')

@section('title', 'Daftar Laporan Kerusakan')

@section('content')
<div class="py-4 sm:py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Top Bar Header & Action -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold font-heading text-[#2a2338] flex items-center gap-2">
                    <i data-lucide="file-text" class="w-6 h-6 text-[#f2723b]"></i> Daftar Laporan Kerusakan
                </h1>
                <p class="text-xs sm:text-sm text-[#7a7189] mt-1">
                    Kelola dan pantau seluruh histori pengajuan laporan kerusakan fasilitas Anda.
                </p>
            </div>
            <a href="{{ route('pelapor.pengajuan.create') }}" 
               class="inline-flex items-center justify-center gap-2 text-xs font-semibold text-white bg-[#191333] hover:bg-[#2f2559] px-4 py-2.5 rounded-xl transition-colors shrink-0 shadow-sm">
                <i data-lucide="plus-circle" class="w-4 h-4 text-[#f2723b]"></i> Lapor Kerusakan Baru
            </a>
        </div>

        <!-- Alert Notifications -->
        @if (session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-r-xl shadow-sm flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500 shrink-0"></i>
            <span class="text-xs sm:text-sm font-medium">{{ session('success') }}</span>
        </div>
        @endif

        @if (session('error'))
        <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-800 p-4 rounded-r-xl shadow-sm flex items-center gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-500 shrink-0"></i>
            <span class="text-xs sm:text-sm font-medium">{{ session('error') }}</span>
        </div>
        @endif

        <!-- Main Content Table / Empty State -->
        <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm overflow-hidden p-5 sm:p-6 space-y-4">
            
            @if ($pengajuan->isEmpty())
                <div class="text-center py-12 space-y-3">
                    <div class="w-12 h-12 rounded-full bg-[#fbf7f0] border border-[#ecdfd0] flex items-center justify-center mx-auto text-[#7a7189]">
                        <i data-lucide="clipboard-x" class="w-6 h-6 text-[#f2723b]"></i>
                    </div>
                    <h3 class="text-sm font-bold text-[#2a2338]">Belum Ada Laporan</h3>
                    <p class="text-xs text-[#7a7189]">Anda belum membuat laporan kerusakan fasilitas saat ini.</p>
                    <a href="{{ route('pelapor.pengajuan.create') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#f2723b] hover:underline mt-2">
                        <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i> Buat laporan pertama Anda
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-[#ecdfd0] text-xs sm:text-sm">
                        <thead class="bg-[#fbf7f0]">
                            <tr>
                                <th class="px-4 py-3 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px] w-12">No</th>
                                <th class="px-4 py-3 text-left font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Jenis Kerusakan</th>
                                <th class="px-4 py-3 text-left font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Lokasi</th>
                                <th class="px-4 py-3 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Urgensi</th>
                                <th class="px-4 py-3 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Status</th>
                                <th class="px-4 py-3 text-left font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Tanggal</th>
                                <th class="px-4 py-3 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#ecdfd0] bg-white text-[#2a2338]">
                            @foreach ($pengajuan as $index => $item)
                                <tr class="hover:bg-[#fbf7f0]/50 transition-colors">
                                    <td class="px-4 py-3 text-center font-medium text-[#7a7189]">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="px-4 py-3 font-bold text-[#191333]">
                                        {{ $item->jenis_kerusakan }}
                                    </td>
                                    <td class="px-4 py-3 text-[#7a7189]">
                                        {{ $item->lokasi_detail }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2.5 py-1 inline-flex text-[11px] font-bold rounded-lg
                                            {{ $item->tingkat_urgensi === 'tinggi' ? 'bg-rose-100 text-rose-800' : '' }}
                                            {{ $item->tingkat_urgensi === 'sedang' ? 'bg-amber-100 text-amber-800' : '' }}
                                            {{ $item->tingkat_urgensi === 'rendah' ? 'bg-emerald-100 text-emerald-800' : '' }}">
                                            {{ ucfirst($item->tingkat_urgensi) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2.5 py-1 inline-flex text-[11px] font-bold rounded-lg
                                            {{ $item->status_laporan === 'pending' ? 'bg-amber-100 text-amber-800' : '' }}
                                            {{ $item->status_laporan === 'ditolak' ? 'bg-rose-100 text-rose-800' : '' }}
                                            {{ $item->status_laporan === 'diproses' ? 'bg-sky-100 text-sky-800' : '' }}
                                            {{ $item->status_laporan === 'selesai' ? 'bg-emerald-100 text-emerald-800' : '' }}">
                                            {{ ucfirst($item->status_laporan) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 font-mono text-xs text-[#7a7189]">
                                        {{ $item->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <a href="{{ route('pelapor.pengajuan.show', $item) }}" 
                                               class="inline-flex items-center gap-1 text-xs font-semibold text-sky-700 hover:text-sky-900 bg-sky-50 hover:bg-sky-100 px-2.5 py-1 rounded-lg transition-colors">
                                                <i data-lucide="eye" class="w-3.5 h-3.5"></i> Detail
                                            </a>
                                            
                                            @if (!in_array($item->status_laporan, ['diproses', 'selesai']))
                                                <a href="{{ route('pelapor.pengajuan.edit', $item) }}" 
                                                   class="inline-flex items-center gap-1 text-xs font-semibold text-amber-700 hover:text-amber-900 bg-amber-50 hover:bg-amber-100 px-2.5 py-1 rounded-lg transition-colors">
                                                    <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Edit
                                                </a>
                                                <form action="{{ route('pelapor.pengajuan.destroy', $item) }}" 
                                                      method="POST" class="inline-block"
                                                      onsubmit="return confirm('Yakin ingin menghapus laporan ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center gap-1 text-xs font-semibold text-rose-700 hover:text-rose-900 bg-rose-50 hover:bg-rose-100 px-2.5 py-1 rounded-lg transition-colors">
                                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                                                    </button>
                                                </form>
                                            @else
                                                <span class="inline-flex items-center gap-1 text-xs text-[#7a7189] bg-[#fbf7f0] px-2.5 py-1 rounded-lg font-medium border border-[#ecdfd0]">
                                                    <i data-lucide="lock" class="w-3 h-3 text-[#f2723b]"></i> Dikunci
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 pt-4 border-t border-[#ecdfd0]">
                    {{ $pengajuan->links() }}
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