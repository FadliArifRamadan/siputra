@extends('layouts.rtk')

@section('title', 'Manajemen Peminjaman')

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

    /* Penataan Layout Footer Datatables */
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

    #peminjaman-table td, #peminjaman-table th {
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
                    <i data-lucide="calendar-check" class="w-6 h-6 text-[#f2723b]"></i> Manajemen Peminjaman
                </h1>
                <p class="text-xs sm:text-sm text-[#7a7189] mt-1">
                    Kelola permohonan peminjaman fasilitas, verifikasi jadwal, dan lacak status peminjaman kampus.
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

        <!-- Card Table Utama -->
        <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm overflow-hidden p-5 sm:p-6">

            <!-- Filter Bar -->
            <form id="filter-form" method="GET" class="mb-6 p-4 rounded-xl bg-[#fbf7f0] border border-[#ecdfd0]/80">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-[#2a2338] mb-1.5">Status Peminjaman</label>
                        <select name="status_pinjam" class="w-full rounded-lg border-[#ecdfd0] shadow-sm focus:border-[#f2723b] focus:ring-[#f2723b] text-xs font-medium text-[#2a2338]">
                            <option value="">Semua Status</option>
                            <option value="menunggu_rtk" {{ request('status_pinjam') == 'menunggu_rtk' ? 'selected' : '' }}>Menunggu RTK</option>
                            <option value="menunggu_bikem" {{ request('status_pinjam') == 'menunggu_bikem' ? 'selected' : '' }}>Menunggu BIKEM</option>
                            <option value="perlu_revisi" {{ request('status_pinjam') == 'perlu_revisi' ? 'selected' : '' }}>Perlu Revisi</option>
                            <option value="pending" {{ request('status_pinjam') == 'pending' ? 'selected' : '' }}>Pending (Legacy)</option>
                            <option value="disetujui" {{ request('status_pinjam') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                            <option value="ditolak" {{ request('status_pinjam') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            <option value="berjalan" {{ request('status_pinjam') == 'berjalan' ? 'selected' : '' }}>Berjalan</option>
                            <option value="selesai" {{ request('status_pinjam') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#2a2338] mb-1.5">Fasilitas</label>
                        <select name="fasilitas_id" class="w-full rounded-lg border-[#ecdfd0] shadow-sm focus:border-[#f2723b] focus:ring-[#f2723b] text-xs font-medium text-[#2a2338]">
                            <option value="">Semua Fasilitas</option>
                            @foreach (\App\Models\Fasilitas::orderBy('nama_fasilitas')->get() as $fasilitas)
                                <option value="{{ $fasilitas->id }}" {{ request('fasilitas_id') == $fasilitas->id ? 'selected' : '' }}>
                                    {{ $fasilitas->nama_fasilitas }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#2a2338] mb-1.5">Tanggal (Dari)</label>
                        <input type="date" name="waktu_mulai_start" value="{{ request('waktu_mulai_start') }}" class="w-full rounded-lg border-[#ecdfd0] shadow-sm focus:border-[#f2723b] focus:ring-[#f2723b] text-xs font-medium text-[#2a2338]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#2a2338] mb-1.5">Tanggal (Sampai)</label>
                        <input type="date" name="waktu_mulai_end" value="{{ request('waktu_mulai_end') }}" class="w-full rounded-lg border-[#ecdfd0] shadow-sm focus:border-[#f2723b] focus:ring-[#f2723b] text-xs font-medium text-[#2a2338]">
                    </div>
                    <div class="flex items-end gap-2 col-span-1 sm:col-span-2 lg:col-span-4 pt-2">
                        <button type="submit" class="bg-[#191333] hover:bg-[#2f2559] text-white px-5 py-2.5 rounded-lg transition-colors text-xs font-semibold shadow-sm flex items-center justify-center gap-1.5">
                            <i data-lucide="filter" class="w-3.5 h-3.5"></i> Filter
                        </button>
                        <a href="{{ route('rtk.peminjaman.index') }}" class="bg-white border border-[#ecdfd0] text-[#2a2338] hover:bg-[#fbf7f0] px-5 py-2.5 rounded-lg transition-colors text-xs font-semibold flex items-center justify-center gap-1.5">
                            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            <!-- Datatable -->
            <div class="overflow-x-auto">
                <table id="peminjaman-table" class="min-w-full divide-y divide-[#ecdfd0] text-xs sm:text-sm">
                    <thead class="bg-[#fbf7f0]">
                        <tr>
                            <th class="px-4 py-3 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px] w-12">NO</th>
                            <th class="px-4 py-3 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Peminjam</th>
                            <th class="px-4 py-3 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Fasilitas</th>
                            <th class="px-4 py-3 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Total Unit</th>
                            <th class="px-4 py-3 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Mulai</th>
                            <th class="px-4 py-3 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Selesai</th>
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

<!-- ✅ MODAL REJECT -->
<div id="rejectModal" class="fixed inset-0 bg-[#191333]/60 backdrop-blur-sm hidden overflow-y-auto h-full w-full z-50 transition-all duration-300">
    <div class="relative top-20 mx-auto p-6 border border-[#ecdfd0] w-full max-w-md shadow-2xl rounded-2xl bg-white animate-in fade-in zoom-in duration-200">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-rose-100 text-rose-600">
                <i data-lucide="x-circle" class="w-6 h-6"></i>
            </div>
            <h3 class="text-lg font-bold font-heading text-[#2a2338] mt-4">Tolak Peminjaman</h3>
            <p class="text-xs text-[#7a7189] mt-1">Berikan alasan penolakan (opsional)</p>
            
            <form id="rejectForm" method="POST" class="mt-4 text-left">
                @csrf
                @method('PATCH')
                <textarea name="catatan" rows="3" class="w-full rounded-xl border-[#ecdfd0] shadow-sm focus:border-rose-500 focus:ring-rose-500 text-xs text-[#2a2338] placeholder-[#7a7189]" placeholder="Tuliskan alasan penolakan..."></textarea>
                
                <div class="flex justify-end gap-2 mt-5">
                    <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-white border border-[#ecdfd0] text-[#2a2338] rounded-lg hover:bg-[#fbf7f0] text-xs font-semibold transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-semibold shadow-sm transition-colors flex items-center gap-1.5">
                        <i data-lucide="x-octagon" class="w-3.5 h-3.5"></i> Tolak Peminjaman
                    </button>
                </div>
            </form>
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

        $('#peminjaman-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("rtk.peminjaman.index") }}',
                data: filterData
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center font-semibold text-[#7a7189]' },
                { data: 'peminjam', name: 'peminjam', className: 'text-center font-medium' },
                { data: 'fasilitas', name: 'fasilitas', className: 'text-center font-medium' },
                { data: 'total_items', name: 'total_items', className: 'text-center', searchable: false },
                { data: 'waktu_mulai', name: 'waktu_mulai', className: 'text-center' },
                { data: 'waktu_selesai', name: 'waktu_selesai', className: 'text-center' },
                { data: 'status_pinjam', name: 'status_pinjam', className: 'text-center' },
                { data: 'created_at', name: 'created_at', className: 'text-center' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ],
            order: [],
            language: {
                url: '{{ asset("js/datatables-id.json") }}'
            },
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Semua']],
            drawCallback: function() {
                if (window.lucide) lucide.createIcons();
            }
        });
    });

    function showRejectModal(id) {
        document.getElementById('rejectModal').classList.remove('hidden');
        document.getElementById('rejectForm').action = '{{ url("rtk/peminjaman") }}/' + id + '/reject';
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }

    document.addEventListener('click', function(event) {
        const modal = document.getElementById('rejectModal');
        if (event.target === modal) {
            closeRejectModal();
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeRejectModal();
        }
    });
</script>
@endpush