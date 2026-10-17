@extends('layouts.rtk')

@section('title', 'Laporan Kerusakan')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-[#ecdfd0]">
            <div class="p-6 text-[#191333]">
                <!-- Header Card & Title -->
                <div class="flex justify-between items-center mb-6 pb-4 border-b border-[#ecdfd0]">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-[#fbf7f0] border border-[#ecdfd0] rounded-xl text-[#f2723b]">
                            <i data-lucide="wrench" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold font-heading text-[#191333]">🛠️ Laporan Kerusakan</h2>
                            <p class="text-xs text-[#7a7189]">Kelola dan tindak lanjuti laporan kerusakan fasilitas.</p>
                        </div>
                    </div>
                    <span class="text-xs font-semibold px-3 py-1.5 bg-[#fbf7f0] border border-[#ecdfd0] rounded-xl text-[#7a7189]">
                        Total: <span class="text-[#f2723b] font-bold">{{ \App\Models\PengajuanKerusakan::count() }}</span>
                    </span>
                </div>

                <!-- Alert Messages -->
                @if (session('success'))
                    <div class="mb-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-r-xl text-xs font-medium flex items-center gap-2">
                        <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 p-4 rounded-r-xl text-xs font-medium flex items-center gap-2">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 shrink-0"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <!-- Filter Form -->
                <form id="filter-form" method="GET" class="mb-6 p-4 bg-[#fbf7f0]/60 rounded-2xl border border-[#ecdfd0]">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-[#7a7189] uppercase tracking-wider mb-1">Status Laporan</label>
                            <select name="status_laporan" class="w-full rounded-xl border-[#ecdfd0] bg-white text-[#191333] shadow-sm focus:border-[#f2723b] focus:ring-[#f2723b] text-sm">
                                <option value="">Semua</option>
                                <option value="pending" {{ request('status_laporan') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="diproses" {{ request('status_laporan') == 'diproses' ? 'selected' : '' }}>Diproses</option>
                                <option value="selesai" {{ request('status_laporan') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="ditolak" {{ request('status_laporan') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#7a7189] uppercase tracking-wider mb-1">Tingkat Urgensi</label>
                            <select name="tingkat_urgensi" class="w-full rounded-xl border-[#ecdfd0] bg-white text-[#191333] shadow-sm focus:border-[#f2723b] focus:ring-[#f2723b] text-sm">
                                <option value="">Semua</option>
                                <option value="rendah" {{ request('tingkat_urgensi') == 'rendah' ? 'selected' : '' }}>Rendah</option>
                                <option value="sedang" {{ request('tingkat_urgensi') == 'sedang' ? 'selected' : '' }}>Sedang</option>
                                <option value="tinggi" {{ request('tingkat_urgensi') == 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#7a7189] uppercase tracking-wider mb-1">Tanggal Laporan (Dari)</label>
                            <input type="date" name="created_at_start" value="{{ request('created_at_start') }}" class="w-full rounded-xl border-[#ecdfd0] bg-white text-[#191333] shadow-sm focus:border-[#f2723b] focus:ring-[#f2723b] text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#7a7189] uppercase tracking-wider mb-1">Tanggal Laporan (Sampai)</label>
                            <input type="date" name="created_at_end" value="{{ request('created_at_end') }}" class="w-full rounded-xl border-[#ecdfd0] bg-white text-[#191333] shadow-sm focus:border-[#f2723b] focus:ring-[#f2723b] text-sm">
                        </div>
                        <div class="flex items-end gap-2 col-span-1 md:col-span-4 pt-1">
                            <button type="submit" class="bg-[#f2723b] hover:bg-[#e05d26] text-white font-semibold px-4 py-2 rounded-xl transition text-sm shadow-sm inline-flex items-center gap-1.5">
                                <i data-lucide="filter" class="w-4 h-4"></i> Filter
                            </button>
                            <a href="{{ route('rtk.laporan-kerusakan.index') }}" class="bg-gray-100 border border-gray-200 text-[#7a7189] hover:text-[#191333] px-4 py-2 rounded-xl transition text-sm font-semibold inline-flex items-center gap-1.5">
                                <i data-lucide="rotate-ccw" class="w-4 h-4"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Datatable -->
                <div class="overflow-x-auto">
                    <table id="laporan-kerusakan-table" class="min-w-full divide-y divide-[#ecdfd0] text-sm">
                        <thead class="bg-[#fbf7f0]">
                            <tr>
                                <th class="px-6 py-3.5 text-center text-xs font-bold text-[#191333] uppercase tracking-wider w-12">NO</th>
                                <th class="px-6 py-3.5 text-left text-xs font-bold text-[#191333] uppercase tracking-wider">Pelapor</th>
                                <th class="px-6 py-3.5 text-left text-xs font-bold text-[#191333] uppercase tracking-wider">Jenis Kerusakan</th>
                                <th class="px-6 py-3.5 text-left text-xs font-bold text-[#191333] uppercase tracking-wider">Fasilitas</th>
                                <th class="px-6 py-3.5 text-left text-xs font-bold text-[#191333] uppercase tracking-wider">Lokasi</th>
                                <th class="px-6 py-3.5 text-left text-xs font-bold text-[#191333] uppercase tracking-wider">Urgensi</th>
                                <th class="px-6 py-3.5 text-left text-xs font-bold text-[#191333] uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3.5 text-left text-xs font-bold text-[#191333] uppercase tracking-wider">SLA</th>
                                <th class="px-6 py-3.5 text-left text-xs font-bold text-[#191333] uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3.5 text-left text-xs font-bold text-[#191333] uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(function() {
        if (window.lucide) { lucide.createIcons(); }

        const params = new URLSearchParams(window.location.search);
        const filterData = {};
        params.forEach((value, key) => {
            if (key !== 'page' && key !== 'length' && key !== 'draw' && key !== 'columns') {
                filterData[key] = value;
            }
        });

        $('#laporan-kerusakan-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("rtk.laporan-kerusakan.index") }}',
                data: filterData
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center font-semibold text-[#7a7189]' },
                { data: 'pelapor', name: 'pelapor' },
                { data: 'jenis_kerusakan', name: 'jenis_kerusakan' },
                { data: 'fasilitas', name: 'fasilitas' },
                { data: 'lokasi_detail', name: 'lokasi_detail' },
                { data: 'tingkat_urgensi', name: 'tingkat_urgensi' },
                { data: 'status_laporan', name: 'status_laporan' },
                { data: 'sla_status', name: 'sla_status' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            },
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Semua']],
            drawCallback: function() {
                if (window.lucide) { lucide.createIcons(); }
            }
        });
    });
</script>
@endpush