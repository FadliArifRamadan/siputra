@extends('layouts.rtk')

@section('title', 'Manajemen User')

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

    #users-table td,
    #users-table th {
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
                    <i data-lucide="users" class="w-6 h-6 text-[#f2723b]"></i> Manajemen User
                </h1>
                <p class="text-xs sm:text-sm text-[#7a7189] mt-1">
                    Kelola data pengguna, verifikasi pendaftaran akun, dan sesuaikan peran hak akses.
                </p>
            </div>
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

        <!-- Statistik User -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white p-4 rounded-xl border border-[#ecdfd0] shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-[#7a7189]">Total User</span>
                    <i data-lucide="users" class="w-4 h-4 text-[#191333]"></i>
                </div>
                <div class="text-2xl font-bold font-heading text-[#2a2338] mt-2">{{ $stats['total'] }}</div>
            </div>
            <div class="bg-amber-50/70 p-4 rounded-xl border border-amber-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-amber-800">Menunggu Verifikasi</span>
                    <i data-lucide="clock" class="w-4 h-4 text-amber-600"></i>
                </div>
                <div class="text-2xl font-bold font-heading text-amber-700 mt-2">{{ $stats['pending'] }}</div>
            </div>
            <div class="bg-emerald-50/70 p-4 rounded-xl border border-emerald-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-emerald-800">Aktif</span>
                    <i data-lucide="user-check" class="w-4 h-4 text-emerald-600"></i>
                </div>
                <div class="text-2xl font-bold font-heading text-emerald-700 mt-2">{{ $stats['active'] }}</div>
            </div>
            <div class="bg-rose-50/70 p-4 rounded-xl border border-rose-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-rose-800">Ditangguhkan</span>
                    <i data-lucide="user-x" class="w-4 h-4 text-rose-600"></i>
                </div>
                <div class="text-2xl font-bold font-heading text-rose-700 mt-2">{{ $stats['suspended'] }}</div>
            </div>
        </div>
        <!-- Action Bar / Tombol Aksi Utama -->
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-bold font-heading text-[#2a2338]">Daftar Pengguna</h2>
                <p class="text-xs text-[#7a7189]">Kelola seluruh akun pengguna dan hak akses di bawah ini.</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('rtk.users.import') }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white hover:bg-[#fbf7f0] text-[#2a2338] border border-[#ecdfd0] text-xs font-semibold transition-all shadow-sm">
                    <i data-lucide="file-up" class="w-4 h-4 text-[#7a7189]"></i>
                    <span>Import</span>
                </a>

                <a href="{{ route('rtk.users.create') }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-[#191333] hover:bg-[#2f2559] text-white text-xs font-semibold transition-all shadow-sm">
                    <i data-lucide="plus" class="w-4 h-4 text-[#f2723b]"></i>
                    <span>Tambah User</span>
                </a>
            </div>
        </div>
        <!-- Card Table Utama -->
        <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm overflow-hidden p-5 sm:p-6">

            <!-- Filter Bar -->
            <form id="filter-form" method="GET" class="mb-6 p-4 rounded-xl bg-[#fbf7f0] border border-[#ecdfd0]/80">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-[#2a2338] mb-1.5">Status Akun</label>
                        <select name="status_akun" class="w-full rounded-lg border-[#ecdfd0] shadow-sm focus:border-[#f2723b] focus:ring-[#f2723b] text-xs font-medium text-[#2a2338]">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status_akun') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="active" {{ request('status_akun') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="suspended" {{ request('status_akun') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#2a2338] mb-1.5">Role</label>
                        <select name="role" class="w-full rounded-lg border-[#ecdfd0] shadow-sm focus:border-[#f2723b] focus:ring-[#f2723b] text-xs font-medium text-[#2a2338]">
                            <option value="">Semua Role</option>
                            <option value="pelapor" {{ request('role') == 'pelapor' ? 'selected' : '' }}>Pelapor</option>
                            <option value="teknisi" {{ request('role') == 'teknisi' ? 'selected' : '' }}>Teknisi</option>
                        </select>
                    </div>
                    <div class="flex items-center pt-2 sm:pt-6">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox"
                                id="withTrashed"
                                name="with_trashed"
                                value="1"
                                {{ request('with_trashed') ? 'checked' : '' }}
                                class="rounded border-[#ecdfd0] text-[#f2723b] shadow-sm focus:border-[#f2723b] focus:ring-[#f2723b]"
                                onchange="this.form.submit()">
                            <span class="ml-2 text-xs font-medium text-[#2a2338]">Tampilkan data terhapus</span>
                        </label>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 bg-[#191333] hover:bg-[#2f2559] text-white px-4 py-2 rounded-lg transition-colors text-xs font-semibold shadow-sm flex items-center justify-center gap-1.5">
                            <i data-lucide="filter" class="w-3.5 h-3.5"></i> Filter
                        </button>
                        <a href="{{ route('rtk.users.index') }}" class="bg-white border border-[#ecdfd0] text-[#2a2338] hover:bg-[#fbf7f0] px-4 py-2 rounded-lg transition-colors text-xs font-semibold flex items-center justify-center gap-1.5">
                            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            <!-- Datatable -->
            <div class="overflow-x-auto">
                <table id="users-table" class="min-w-full divide-y divide-[#ecdfd0] text-xs sm:text-sm">
                    <thead class="bg-[#fbf7f0]">
                        <tr>
                            <th class="px-4 py-3 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">No</th>
                            <th class="px-4 py-3 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Nomor Induk</th>
                            <th class="px-4 py-3 text-left font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Nama</th>
                            <th class="px-4 py-3 text-left font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Email</th>
                            <th class="px-4 py-3 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Role</th>
                            <th class="px-4 py-3 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Status Akun</th>
                            <th class="px-4 py-3 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Tanggal Daftar</th>
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

        const table = $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("rtk.users.data") }}',
                data: filterData
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                },
                {
                    data: 'nomor_induk',
                    name: 'nomor_induk',
                    className: 'text-center font-mono'
                },
                {
                    data: 'nama',
                    name: 'nama'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'roles',
                    name: 'roles',
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                },
                {
                    data: 'status_akun',
                    name: 'status_akun',
                    className: 'text-center'
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    className: 'text-center'
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
                [6, 'asc']
            ],
            language: {
                url: '{{ asset("js/datatables-id.json") }}'
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

    function closeAllActionMenus() {
        document.querySelectorAll('.action-menu').forEach((menu) => menu.classList.add('hidden'));
    }

    function toggleActionMenu(event, menuId) {
        event.stopPropagation();
        const menu = document.getElementById(menuId);
        if (!menu) return;

        const wasOpen = !menu.classList.contains('hidden');
        closeAllActionMenus();
        if (wasOpen) return; // klik ulang pada tombol yang sama -> cukup ditutup

        const btnRect = event.currentTarget.getBoundingClientRect();

        // Ukur dulu (tanpa kelihatan) supaya tahu lebar/tinggi menu
        menu.style.visibility = 'hidden';
        menu.classList.remove('hidden');
        const menuRect = menu.getBoundingClientRect();

        let left = btnRect.right - menuRect.width;
        let top = btnRect.bottom + 4;

        // Jangan sampai keluar sisi kiri layar
        if (left < 8) left = 8;

        // Kalau ruang di bawah tidak cukup, tampilkan di atas tombol
        if (top + menuRect.height > window.innerHeight - 8) {
            top = btnRect.top - menuRect.height - 4;
        }

        menu.style.left = left + 'px';
        menu.style.top = top + 'px';
        menu.style.visibility = 'visible';
    }

    // Tutup menu saat klik di luar, saat scroll, atau saat DataTables re-render halaman
    document.addEventListener('click', closeAllActionMenus);
    document.addEventListener('scroll', closeAllActionMenus, true);
    document.addEventListener('DOMContentLoaded', function() {
        if (window.$ && $.fn.dataTable) {
            $('#users-table').on('draw.dt', closeAllActionMenus);
        }
    });
</script>
@endpush