<?php

namespace App\DataTables;

use App\Models\PeminjamanFasilitas;
use App\Traits\DataTableFilterTrait;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PeminjamanDataTable extends DataTable
{
    use DataTableFilterTrait;

    protected Request $httpRequest;

    public function __construct(Request $request)
    {
        $this->httpRequest = $request;
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $this->applyFilters($query, $this->httpRequest, [
            'status_pinjam' => [
                'column' => 'peminjaman_fasilitas.status_pinjam',
                'operator' => '=',
                'label' => 'Status Peminjaman',
                'type' => 'select',
                'options' => [
                    'menunggu_bikem' => 'Menunggu BIKEM',
                    'perlu_revisi' => 'Perlu Revisi',
                    'menunggu_rtk' => 'Menunggu RTK',
                    'disetujui' => 'Disetujui RTK',
                    'ditolak' => 'Ditolak',
                    'berjalan' => 'Berjalan',
                    'selesai' => 'Selesai',
                ],
            ],
            'fasilitas_id' => [
                'column' => 'peminjaman_fasilitas_item.fasilitas_id',
                'operator' => '=',
                'label' => 'Fasilitas',
                'type' => 'select',
                'options' => $this->getFasilitasOptions(),
            ],
            'waktu_mulai' => [
                'column' => 'peminjaman_fasilitas.waktu_mulai',
                'operator' => 'between',
                'label' => 'Tanggal Peminjaman',
                'type' => 'date_range',
            ],
        ]);

        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->filterColumn('peminjam', function($query, $keyword) {
                $query->whereHas('peminjam', function($q) use ($keyword) {
                    $q->where('nama', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('fasilitas', function($query, $keyword) {
                $query->whereHas('items.fasilitas', function($q) use ($keyword) {
                    $q->where('nama_fasilitas', 'like', "%{$keyword}%");
                });
            })
            ->addColumn('action', function ($peminjaman) {
                $buttons = '<div style="display:flex; gap:4px; flex-wrap:wrap; justify-content:center; align-items:center;">';

                // ✅ Tombol Detail
                $buttons .= '<a href="' . route('rtk.peminjaman.show', $peminjaman->id) . '" 
                               class="btn-detail" style="background:#6366f1; color:white; border:none; padding:3px 10px; border-radius:4px; font-size:11px; cursor:pointer; text-decoration:none; display:inline-block; white-space:nowrap;">
                                📄 Detail
                            </a>';

                if (in_array($peminjaman->status_pinjam, ['menunggu_rtk', 'pending'])) {
                    // ✅ Tombol Approve
                    $buttons .= '<form action="' . route('rtk.peminjaman.approve', $peminjaman->id) . '" method="POST" style="display:inline-block;">
                                    ' . csrf_field() . '
                                    ' . method_field('PATCH') . '
                                    <input type="hidden" name="status_pinjam" value="disetujui">
                                    <button type="submit" class="btn-approve" style="background:#22c55e; color:white; border:none; padding:3px 10px; border-radius:4px; font-size:11px; cursor:pointer; white-space:nowrap;" onclick="return confirm(\'Setujui peminjaman fasilitas ini?\')">
                                        ✅ Setujui
                                    </button>
                                </form>';

                    // ✅ Tombol Reject (panggil modal)
                    $buttons .= '<button onclick="showRejectModal(' . $peminjaman->id . ')" 
                                        class="btn-reject" style="background:#ef4444; color:white; border:none; padding:3px 10px; border-radius:4px; font-size:11px; cursor:pointer; white-space:nowrap;">
                                    ❌ Tolak
                                </button>';
                } elseif (in_array($peminjaman->status_pinjam, ['menunggu_bikem'])) {
                    $buttons .= '<span style="color:#d97706; font-size:11px; font-weight:600; background:#fef3c7; padding:2px 8px; border-radius:4px;">⏳ Tunggu BIKEM</span>';
                } elseif ($peminjaman->status_pinjam === 'perlu_revisi') {
                    $buttons .= '<span style="color:#c2410c; font-size:11px; font-style:italic; background:#ffedd5; padding:2px 8px; border-radius:4px;">⚠️ Perlu Revisi</span>';
                } elseif ($peminjaman->status_pinjam === 'disetujui' && $peminjaman->waktu_mulai <= now()) {
                    // ✅ Tombol Mulai (jika sudah waktunya)
                    $buttons .= '<form action="' . route('rtk.peminjaman.start', $peminjaman->id) . '" method="POST" style="display:inline-block;">
                                    ' . csrf_field() . '
                                    ' . method_field('PATCH') . '
                                    <button type="submit" class="btn-start" style="background:#3b82f6; color:white; border:none; padding:3px 10px; border-radius:4px; font-size:11px; cursor:pointer; white-space:nowrap;">
                                        ▶️ Mulai
                                    </button>
                                </form>';
                } elseif ($peminjaman->status_pinjam === 'berjalan') {
                    // ✅ Tombol Selesai
                    $buttons .= '<form action="' . route('rtk.peminjaman.complete', $peminjaman->id) . '" method="POST" style="display:inline-block;">
                                    ' . csrf_field() . '
                                    ' . method_field('PATCH') . '
                                    <button type="submit" class="btn-complete" style="background:#6b7280; color:white; border:none; padding:3px 10px; border-radius:4px; font-size:11px; cursor:pointer; white-space:nowrap;" onclick="return confirm(\'Yakin ingin menandai peminjaman ini selesai?\')">
                                        ✅ Selesai
                                    </button>
                                </form>';
                } else {
                    $buttons .= '<span style="color:#9ca3af; font-size:11px; font-style:italic;">Tidak ada aksi</span>';
                }

                $buttons .= '</div>';
                return $buttons;
            })
            ->addColumn('peminjam', function ($peminjaman) {
                return $peminjaman->peminjam->nama ?? 'Tidak diketahui';
            })
            ->addColumn('fasilitas', function ($peminjaman) {
                $items = $peminjaman->items;
                if ($items->isEmpty()) {
                    return '-';
                }

                $names = $items->map(function ($item) {
                    $nama = $item->fasilitas->nama_fasilitas ?? 'Tidak diketahui';
                    $status = $item->fasilitas->status_kondisi ?? 'baik';
                    $suffix = '';

                    if (in_array($status, ['rusak_berat', 'maintenance'])) {
                        $suffix = ' <span style="color:#ef4444; font-size:10px; font-weight:600;">(' . str_replace('_', ' ', $status) . ')</span>';
                    } elseif ($status === 'rusak_ringan') {
                        $suffix = ' <span style="color:#eab308; font-size:10px; font-weight:600;">(Rusak Ringan)</span>';
                    }
                    return $nama . $suffix . ' (' . $item->jumlah_dipinjam . 'x)';
                })->implode('<br>');

                return $names;
            })
            ->addColumn('total_items', function ($peminjaman) {
                return $peminjaman->items->sum('jumlah_dipinjam');
            })
            ->editColumn('status_pinjam', function ($peminjaman) {
                $badge = [
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'menunggu_bikem' => 'bg-amber-100 text-amber-800',
                    'perlu_revisi' => 'bg-orange-100 text-orange-800',
                    'menunggu_rtk' => 'bg-blue-100 text-blue-800',
                    'disetujui' => 'bg-green-100 text-green-800',
                    'ditolak' => 'bg-red-100 text-red-800',
                    'berjalan' => 'bg-sky-100 text-sky-800',
                    'selesai' => 'bg-gray-100 text-gray-800',
                ];

                $label = match($peminjaman->status_pinjam) {
                    'pending', 'menunggu_bikem' => 'Menunggu BIKEM',
                    'perlu_revisi' => 'Perlu Revisi',
                    'menunggu_rtk' => 'Menunggu RTK',
                    'disetujui' => 'Disetujui RTK',
                    'ditolak' => 'Ditolak',
                    'berjalan' => 'Berjalan',
                    'selesai' => 'Selesai',
                    default => ucfirst($peminjaman->status_pinjam),
                };

                return '<span class="px-2.5 py-1 inline-flex text-xs leading-4 font-semibold rounded-full ' . ($badge[$peminjaman->status_pinjam] ?? 'bg-gray-100 text-gray-800') . '">
                            ' . $label . '
                        </span>';
            })
            ->editColumn('waktu_mulai', function ($peminjaman) {
                return $peminjaman->waktu_mulai->format('d/m/Y H:i');
            })
            ->editColumn('waktu_selesai', function ($peminjaman) {
                return $peminjaman->waktu_selesai->format('d/m/Y H:i');
            })
            ->editColumn('created_at', function ($peminjaman) {
                return $peminjaman->created_at->format('d/m/Y H:i');
            })
            ->rawColumns(['action', 'status_pinjam', 'fasilitas'])
            ->setRowId('id');
    }

    public function query(PeminjamanFasilitas $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['peminjam', 'items.fasilitas'])
            ->leftJoin('peminjaman_fasilitas_item', 'peminjaman_fasilitas.id', '=', 'peminjaman_fasilitas_item.peminjaman_id')
            ->select('peminjaman_fasilitas.*')
            ->distinct()
            ->orderBy('peminjaman_fasilitas.created_at', 'desc');
    }

    private function getFasilitasOptions(): array
    {
        return \App\Models\Fasilitas::pluck('nama_fasilitas', 'id')->toArray();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('peminjaman-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->buttons([
                Button::make('reload'),
            ])
            ->parameters([
                'language' => [
                    'url' => '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json',
                ],
                'pageLength' => 10,
                'lengthMenu' => [[10, 25, 50, -1], [10, 25, 50, 'All']],
                'columnDefs' => [
                    [
                        'targets' => -1,
                        'className' => 'text-center',
                        'width' => '180px',
                    ],
                ],
                'responsive' => true,
                'autoWidth' => false,
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')
                ->title('NO')
                ->addClass('text-center font-bold text-[#191333]')
                ->width('50px')
                ->orderable(false)
                ->searchable(false),
            Column::make('peminjam')->title('Peminjam')->addClass('text-center')->width('120px'),
            Column::make('fasilitas')->title('Fasilitas (Jumlah)')->addClass('text-center')->width('200px'),
            Column::make('total_items')->title('Total Unit')->addClass('text-center')->width('80px'),
            Column::make('waktu_mulai')->title('Mulai')->addClass('text-center')->width('130px'),
            Column::make('waktu_selesai')->title('Selesai')->addClass('text-center')->width('130px'),
            Column::make('status_pinjam')->title('Status')->addClass('text-center')->width('100px'),
            Column::make('created_at')->title('Tanggal')->addClass('text-center')->width('130px'),
            Column::make('file_pendukung')
                ->title('Berkas')
                ->addClass('text-center')
                ->width('80px')
                ->render("function(data, type, row) {
                    if (data) {
                        return '<a href=\"/storage/' + data + '\" target=\"_blank\" class=\"text-blue-600 hover:text-blue-800\">📎</a>';
                    }
                    return '<span class=\"text-gray-400\">-</span>';
                }"),
            Column::computed('action')
                ->title('Aksi')
                ->exportable(false)
                ->printable(false)
                ->addClass('text-center')
                ->width('180px'),
        ];
    }

    protected function filename(): string
    {
        return 'Peminjaman_' . date('YmdHis');
    }
}