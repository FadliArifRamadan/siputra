@extends('layouts.rtk')

@section('title', 'Manajemen Penugasan Teknisi')

@push('styles')
<style>
    /* Styling Filter & Length Menu DataTables */
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #ecdfd0 !important;
        border-radius: 0.5rem !important;
        padding: 0.375rem 0.75rem !important;
        font-size: 0.875rem !important;
        background-color: #ffffff !important;
    }

    .dataTables_wrapper .dataTables_filter input:focus {
        outline: none !important;
        border-color: #f2723b !important;
        box-shadow: 0 0 0 2px rgba(242, 114, 59, 0.2) !important;
    }

    /* Penataan Layout Footer Datatables (Info Kiri, Pagination Kanan) */
    .dataTables_wrapper .dataTables_info {
        float: left !important;
        padding-top: 0.755em !important;
        font-size: 0.75rem !important;
        color: #7a7189 !important;
    }

    .dataTables_wrapper .dataTables_paginate {
        float: right !important;
        display: flex !important;
        align-items: center !important;
        gap: 0.25rem !important;
        padding-top: 0.5em !important;
    }

    /* Clearfix agar container bawah menampung float dengan sempurna */
    .dataTables_wrapper::after {
        content: "" !important;
        display: table !important;
        clear: both !important;
    }

    /* Styling Tombol Pagination */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 0.5rem !important;
        border: 1px solid #ecdfd0 !important;
        background: #ffffff !important;
        color: #2a2338 !important;
        padding: 0.35rem 0.75rem !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #fbf7f0 !important;
        border-color: #f2723b !important;
        color: #f2723b !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #191333 !important;
        color: #ffffff !important;
        border-color: #191333 !important;
        box-shadow: 0 4px 10px -2px rgba(25, 19, 51, 0.3) !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
        opacity: 0.4 !important;
        cursor: not-allowed !important;
        background: #ffffff !important;
        border-color: #ecdfd0 !important;
        color: #7a7189 !important;
    }

    table.dataTable.no-footer {
        border-bottom: 1px solid #ecdfd0 !important;
    }

    #penugasan-table td, #penugasan-table th {
        padding: 0.85rem 1rem !important;
        vertical-align: middle !important;
    }

    @media (max-width: 768px) {
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            float: none !important;
            justify-content: center !important;
            text-align: center !important;
            margin-top: 0.5rem !important;
        }
    }
</style>
@endpush

@section('content')
<div class="py-4 sm:py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Header Banner -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold font-heading text-[#2a2338] flex items-center gap-2">
                    <i data-lucide="wrench" class="w-6 h-6 text-[#f2723b]"></i> Manajemen Penugasan Teknisi
                </h1>
                <p class="text-xs sm:text-sm text-[#7a7189] mt-1">
                    Kelola pendelegasian perbaikan, pantau ketersediaan teknisi, dan atur jadwal penanganan.
                </p>
            </div>
            <a href="{{ route('rtk.penugasan.create') }}" class="bg-[#191333] hover:bg-[#2f2559] text-white px-4 py-2.5 rounded-xl transition-colors text-xs font-semibold shadow-sm flex items-center justify-center gap-1.5 self-start sm:self-auto">
                <i data-lucide="plus" class="w-4 h-4 text-[#f2723b]"></i> Tugas Teknisi
            </a>
        </div>

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

        <!-- Card Table Utama -->
        <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm overflow-hidden p-5 sm:p-6">

            <!-- Filter Bar -->
            <form id="filter-form" method="GET" class="mb-6 p-4 rounded-xl bg-[#fbf7f0] border border-[#ecdfd0]/80">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-[#2a2338] mb-1.5">Status Laporan</label>
                        <select name="status_laporan" class="w-full rounded-lg border-[#ecdfd0] shadow-sm focus:border-[#f2723b] focus:ring-[#f2723b] text-xs font-medium text-[#2a2338]">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status_laporan') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="diproses" {{ request('status_laporan') == 'diproses' ? 'selected' : '' }}>Diproses</option>
                            <option value="selesai" {{ request('status_laporan') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="ditolak" {{ request('status_laporan') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#2a2338] mb-1.5">Tingkat Urgensi</label>
                        <select name="tingkat_urgensi" class="w-full rounded-lg border-[#ecdfd0] shadow-sm focus:border-[#f2723b] focus:ring-[#f2723b] text-xs font-medium text-[#2a2338]">
                            <option value="">Semua Urgensi</option>
                            <option value="rendah" {{ request('tingkat_urgensi') == 'rendah' ? 'selected' : '' }}>Rendah</option>
                            <option value="sedang" {{ request('tingkat_urgensi') == 'sedang' ? 'selected' : '' }}>Sedang</option>
                            <option value="tinggi" {{ request('tingkat_urgensi') == 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#2a2338] mb-1.5">Teknisi</label>
                        <select name="teknisi_id" class="w-full rounded-lg border-[#ecdfd0] shadow-sm focus:border-[#f2723b] focus:ring-[#f2723b] text-xs font-medium text-[#2a2338]">
                            <option value="">Semua Teknisi</option>
                            @foreach (\App\Models\User::role('teknisi')->get() as $teknisi)
                                <option value="{{ $teknisi->id }}" {{ request('teknisi_id') == $teknisi->id ? 'selected' : '' }}>
                                    {{ $teknisi->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 bg-[#191333] hover:bg-[#2f2559] text-white px-4 py-2 rounded-lg transition-colors text-xs font-semibold shadow-sm flex items-center justify-center gap-1.5">
                            <i data-lucide="filter" class="w-3.5 h-3.5"></i> Filter
                        </button>
                        <a href="{{ route('rtk.penugasan.index') }}" class="bg-white border border-[#ecdfd0] text-[#2a2338] hover:bg-[#fbf7f0] px-4 py-2 rounded-lg transition-colors text-xs font-semibold flex items-center justify-center gap-1.5">
                            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            <!-- Datatable -->
            <div class="overflow-x-auto">
                <table id="penugasan-table" class="min-w-full divide-y divide-[#ecdfd0] text-xs sm:text-sm">
                    <thead class="bg-[#fbf7f0]">
                        <tr>
                            <th class="px-4 py-3 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px] w-12">NO</th>
                            <th class="px-4 py-3 text-left font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Pelapor</th>
                            <th class="px-4 py-3 text-left font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Jenis Kerusakan</th>
                            <th class="px-4 py-3 text-left font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Teknisi</th>
                            <th class="px-4 py-3 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Jadwal</th>
                            <th class="px-4 py-3 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Status</th>
                            <th class="px-4 py-3 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Tanggal</th>
                            <th class="px-4 py-3 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#ecdfd0] bg-white text-[#2a2338]">
                        <!-- Populated by DataTables AJAX -->
                    </tbody>
                </table>
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
        const params = new URLSearchParams(window.location.search);
        const filterData = {};
        params.forEach((value, key) => {
            if (key !== 'page' && key !== 'length' && key !== 'draw' && key !== 'columns') {
                filterData[key] = value;
            }
        });

        $('#penugasan-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("rtk.penugasan.index") }}',
                data: filterData
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center font-semibold text-[#7a7189]' },
                { data: 'pelapor', name: 'pelapor' },
                { data: 'jenis_kerusakan', name: 'jenis_kerusakan' },
                { data: 'teknisi', name: 'teknisi' },
                { data: 'jadwal_penanganan', name: 'jadwal_penanganan', className: 'text-center' },
                { data: 'status_laporan', name: 'status_laporan', className: 'text-center' },
                { data: 'created_at', name: 'created_at', className: 'text-center' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ],
            order: [],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            },
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Semua']],
            drawCallback: function() {
                if (window.lucide) lucide.createIcons();
            }
        });
    });
</script>
@endpush