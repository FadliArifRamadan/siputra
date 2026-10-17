@extends('layouts.rtk')

@section('title', 'Manajemen Fasilitas Pinjam')

@push('styles')
<style>
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

    .dataTables_wrapper .dataTables_info {
        float: left !important;
        padding-top: 0.755em !important;
        font-size: 0.75rem !important;
        color: #7a7189 !important;
    }

    .dataTables_wrapper .dataTables_paginate {
        display: flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
        gap: 0.25rem !important;
        padding-top: 0.75rem !important;
    }

    .dataTables_wrapper::after {
        content: "" !important;
        display: table !important;
        clear: both !important;
    }

    /* Memaksa tombol pagination DataTables berjejer horizontal & rapi */
    .dataTables_paginate {
        display: flex !important;
        align-items: center !important;
        gap: 0.25rem !important;
    }

    .dataTables_paginate .paginate_button {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0.375rem 0.75rem !important;
        border-radius: 0.5rem !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        cursor: pointer !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border: none !important;
        /* Hilangkan border jelek bawaan */
        background: transparent !important;
        color: #7a7189 !important;
        /* Muted text */
        font-size: 0.8125rem !important;
        /* 13px */
        font-weight: 600 !important;
        padding: 0.4rem 0.75rem !important;
        border-radius: 0.625rem !important;
        /* Rounded-lg lembut */
        cursor: pointer !important;
        transition: all 0.2s ease-in-out !important;
        box-shadow: none !important;
        outline: none !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #fbf7f0 !important;
        border-color: #f2723b !important;
        color: #f2723b !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        border: none !important;
        background-color: #191333 !important;
        /* Dark Ink */
        color: #ffffff !important;
        font-weight: 700 !important;
        box-shadow: 0 2px 4px rgba(25, 19, 51, 0.12) !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
        opacity: 0.4 !important;
        cursor: not-allowed !important;
        background: transparent !important;
        color: #7a7189 !important;
    }

    table.dataTable.no-footer {
        border-bottom: 1px solid #ecdfd0 !important;
    }

    #fasilitas-table td,
    #fasilitas-table th {
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
                    <i data-lucide="package" class="w-6 h-6 text-[#f2723b]"></i> Manajemen Fasilitas Pinjam
                </h1>
                <p class="text-xs sm:text-sm text-[#7a7189] mt-1">
                    Kelola data inventaris dan sarana/prasarana yang dapat dipinjam oleh civitas akademika, monitor stok, dan kelola QR Code.
                </p>
            </div>
            <a href="{{ route('rtk.fasilitas.create') }}" class="inline-flex items-center justify-center gap-2 bg-[#f2723b] hover:bg-[#e05d25] text-white px-5 py-2.5 rounded-xl font-semibold text-xs transition-all duration-150 shadow-sm shrink-0">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Fasilitas Pinjam
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
                        <label class="block text-xs font-bold text-[#2a2338] mb-1.5">Kategori</label>
                        <select name="kategori" class="w-full rounded-lg border-[#ecdfd0] shadow-sm focus:border-[#f2723b] focus:ring-[#f2723b] text-xs font-medium text-[#2a2338]">
                            <option value="">Semua Kategori</option>
                            <option value="sarana" {{ request('kategori') == 'sarana' ? 'selected' : '' }}>Sarana</option>
                            <option value="prasarana" {{ request('kategori') == 'prasarana' ? 'selected' : '' }}>Prasarana</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#2a2338] mb-1.5">Status Kondisi</label>
                        <select name="status_kondisi" class="w-full rounded-lg border-[#ecdfd0] shadow-sm focus:border-[#f2723b] focus:ring-[#f2723b] text-xs font-medium text-[#2a2338]">
                            <option value="">Semua Status</option>
                            <option value="baik" {{ request('status_kondisi') == 'baik' ? 'selected' : '' }}>Baik</option>
                            <option value="rusak_ringan" {{ request('status_kondisi') == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                            <option value="rusak_berat" {{ request('status_kondisi') == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                            <option value="maintenance" {{ request('status_kondisi') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#2a2338] mb-1.5">Tanggal Dibuat (Dari)</label>
                        <input type="date" name="created_at_start" value="{{ request('created_at_start') }}" class="w-full rounded-lg border-[#ecdfd0] shadow-sm focus:border-[#f2723b] focus:ring-[#f2723b] text-xs font-medium text-[#2a2338]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#2a2338] mb-1.5">Tanggal Dibuat (Sampai)</label>
                        <input type="date" name="created_at_end" value="{{ request('created_at_end') }}" class="w-full rounded-lg border-[#ecdfd0] shadow-sm focus:border-[#f2723b] focus:ring-[#f2723b] text-xs font-medium text-[#2a2338]">
                    </div>
                    <div class="flex items-end gap-2 col-span-1 sm:col-span-2 lg:col-span-4 justify-end pt-2">
                        <button type="submit" class="bg-[#191333] hover:bg-[#2f2559] text-white px-5 py-2 rounded-lg transition-colors text-xs font-semibold shadow-sm inline-flex items-center gap-1.5">
                            <i data-lucide="filter" class="w-3.5 h-3.5"></i> Filter
                        </button>
                        <a href="{{ route('rtk.fasilitas.index') }}" class="bg-white border border-[#ecdfd0] text-[#2a2338] hover:bg-[#fbf7f0] px-5 py-2 rounded-lg transition-colors text-xs font-semibold inline-flex items-center gap-1.5">
                            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            <!-- Datatable -->
            <div class="overflow-x-auto">
                <table id="fasilitas-table" class="min-w-full divide-y divide-[#ecdfd0] text-xs sm:text-sm">
                    <thead class="bg-[#fbf7f0]">
                        <tr>
                            <th class="px-4 py-3 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">ID</th>
                            <th class="px-4 py-3 text-left font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Nama Fasilitas</th>
                            <th class="px-4 py-3 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Kategori</th>
                            <th class="px-4 py-3 text-left font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Lokasi</th>
                            <th class="px-4 py-3 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Stok</th>
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

        $('#fasilitas-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("rtk.fasilitas.index") }}',
                data: filterData
            },
            columns: [{
                    data: 'id',
                    name: 'id',
                    className: 'text-center font-mono text-xs'
                },
                {
                    data: 'nama_fasilitas',
                    name: 'nama_fasilitas',
                    className: 'font-medium text-[#2a2338]'
                },
                {
                    data: 'kategori',
                    name: 'kategori',
                    className: 'text-center'
                },
                {
                    data: 'lokasi_detail',
                    name: 'lokasi_detail'
                },
                {
                    data: 'stok_total',
                    name: 'stok_total',
                    className: 'text-center font-semibold'
                },
                {
                    data: 'status_kondisi',
                    name: 'status_kondisi',
                    className: 'text-center'
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    className: 'text-center text-xs text-[#7a7189]'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                }
            ],
            order: [
                [0, 'desc']
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            },
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, 'Semua']
            ],
            drawCallback: function() {
                if (window.lucide) lucide.createIcons();
            }
        });
    });
</script>
@endpush