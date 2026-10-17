<?php

namespace App\DataTables;

use App\Models\PengajuanKerusakan;
use App\Traits\DataTableFilterTrait;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class LaporanKerusakanDataTable extends DataTable
{
    use DataTableFilterTrait;

    protected Request $httprequest;

    public function __construct(Request $request)
    {
        $this->httprequest = $request;
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $this->applyFilters($query, $this->httprequest, [
            'status_laporan' => [
                'column' => 'status_laporan',
                'operator' => '=',
                'label' => 'Status Laporan',
                'type' => 'select',
                'options' => [
                    'pending' => 'Pending',
                    'diproses' => 'Diproses',
                    'selesai' => 'Selesai',
                    'ditolak' => 'Ditolak',
                ],
            ],
            'tingkat_urgensi' => [
                'column' => 'tingkat_urgensi',
                'operator' => '=',
                'label' => 'Urgensi',
                'type' => 'select',
                'options' => [
                    'rendah' => 'Rendah',
                    'sedang' => 'Sedang',
                    'tinggi' => 'Tinggi',
                ],
            ],
            'created_at' => [
                'column' => 'created_at',
                'operator' => 'between',
                'label' => 'Tanggal Laporan',
                'type' => 'date_range',
            ],
        ]);

        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->filterColumn('pelapor', function($query, $keyword) {
                $query->whereHas('pelapor', function($q) use ($keyword) {
                    $q->where('nama', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('fasilitas', function($query, $keyword) {
                $query->whereHas('fasilitas', function($q) use ($keyword) {
                    $q->where('nama_fasilitas', 'like', "%{$keyword}%");
                });
            })
            ->addColumn('action', function ($laporan) {
                $buttons = '<div class="flex items-center justify-center gap-1.5 whitespace-nowrap">';
                
                // 📄 Tombol Detail
                $buttons .= '<a href="' . route('rtk.laporan-kerusakan.show', $laporan->id) . '" 
                               class="inline-flex items-center justify-center gap-1 bg-[#2a2338] hover:bg-[#191333] text-white text-[11px] font-semibold px-2.5 py-1.5 rounded-lg transition-all shadow-sm">
                                <i data-lucide="eye" class="w-3.5 h-3.5 text-[#ecdfd0]"></i> Detail
                            </a>';

                // 🔧 Tombol Tugaskan Teknisi (Hanya jika pending)
                if ($laporan->status_laporan === 'pending') {
                    $buttons .= '<a href="' . route('rtk.penugasan.create', ['pengajuan_id' => $laporan->id]) . '" 
                                   class="inline-flex items-center justify-center gap-1 bg-[#f2723b] hover:bg-[#e05d26] text-white text-[11px] font-semibold px-2.5 py-1.5 rounded-lg transition-all shadow-sm">
                                    <i data-lucide="wrench" class="w-3.5 h-3.5"></i> Tugaskan
                                </a>';
                }

                $buttons .= '</div>';
                return $buttons;
            })
            ->addColumn('pelapor', function ($laporan) {
                return '<div class="font-semibold text-[#191333] text-xs leading-tight whitespace-nowrap">' . e($laporan->pelapor->nama ?? 'Tidak diketahui') . '</div>';
            })
            ->addColumn('fasilitas', function ($laporan) {
                return '<span class="text-xs text-[#2a2338] font-medium">' . e($laporan->fasilitas->nama_fasilitas ?? '-') . '</span>';
            })
            ->editColumn('status_laporan', function ($laporan) {
                $badge = [
                    'pending'  => 'bg-amber-50 text-amber-700 border-amber-200',
                    'diproses' => 'bg-sky-50 text-sky-700 border-sky-200',
                    'selesai'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'ditolak'  => 'bg-rose-50 text-rose-700 border-rose-200',
                ];

                $class = $badge[$laporan->status_laporan] ?? 'bg-gray-50 text-gray-700 border-gray-200';

                return '<div class="flex justify-center items-center w-full">
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border whitespace-nowrap ' . $class . '">
                                ' . ucfirst($laporan->status_laporan) . '
                            </span>
                        </div>';
            })
            ->editColumn('tingkat_urgensi', function ($laporan) {
                $badge = [
                    'tinggi' => 'bg-rose-50 text-rose-700 border-rose-200',
                    'sedang' => 'bg-amber-50 text-amber-700 border-amber-200',
                    'rendah' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                ];

                $class = $badge[$laporan->tingkat_urgensi] ?? 'bg-gray-50 text-gray-700 border-gray-200';

                return '<div class="flex justify-center items-center w-full">
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border whitespace-nowrap ' . $class . '">
                                ' . ucfirst($laporan->tingkat_urgensi) . '
                            </span>
                        </div>';
            })
            ->editColumn('created_at', function ($laporan) {
                return '<span class="text-xs text-[#7a7189] font-medium whitespace-nowrap">' . $laporan->created_at->format('d/m/Y H:i') . '</span>';
            })
            ->editColumn('sla_status', function ($laporan) {
                $badge = [
                    'on_track' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'warning'  => 'bg-amber-50 text-amber-700 border-amber-200',
                    'breached' => 'bg-rose-50 text-rose-700 border-rose-200',
                ];

                $label = [
                    'on_track' => 'On Track',
                    'warning'  => 'Warning',
                    'breached' => 'Breached',
                ];

                $class = $badge[$laporan->sla_status] ?? 'bg-gray-50 text-gray-700 border-gray-200';
                $labelText = $label[$laporan->sla_status] ?? $laporan->sla_status;

                return '<div class="flex justify-center items-center w-full">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-tight border whitespace-nowrap shrink-0 max-w-max ' . $class . '">
                                <i data-lucide="clock" class="w-3 h-3 shrink-0"></i> <span>' . $labelText . '</span>
                            </span>
                        </div>';
            })
            ->rawColumns(['action', 'pelapor', 'fasilitas', 'status_laporan', 'tingkat_urgensi', 'created_at', 'sla_status'])
            ->setRowId('id');
    }

    public function query(PengajuanKerusakan $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['pelapor', 'fasilitas', 'penugasanTeknisi.teknisi'])
            ->select('pengajuan_kerusakan.*')
            ->orderBy('pengajuan_kerusakan.created_at', 'desc');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('laporan-kerusakan-table')
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
                        'targets' => '_all',
                        'className' => 'align-middle py-3 px-3',
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
            Column::make('pelapor')
                ->title('Pelapor')
                ->addClass('text-left')
                ->width('130px'),
            Column::make('jenis_kerusakan')
                ->title('Jenis Kerusakan')
                ->addClass('text-left')
                ->width('140px'),
            Column::make('fasilitas')
                ->title('Fasilitas')
                ->addClass('text-left')
                ->width('130px'),
            Column::make('lokasi_detail')
                ->title('Lokasi')
                ->addClass('text-left')
                ->width('130px'),
            Column::make('tingkat_urgensi')
                ->title('Urgensi')
                ->addClass('text-center')
                ->width('95px'),
            Column::make('status_laporan')
                ->title('Status')
                ->addClass('text-center')
                ->width('105px'),
            Column::make('sla_status')
                ->title('SLA')
                ->addClass('text-center')
                ->width('110px'),
            Column::make('created_at')
                ->title('Tanggal')
                ->addClass('text-center')
                ->width('125px'),
            Column::computed('action')
                ->title('Aksi')
                ->exportable(false)
                ->printable(false)
                ->addClass('text-center')
                ->width('150px'),
        ];
    }

    protected function filename(): string
    {
        return 'Laporan_Kerusakan_' . date('YmdHis');
    }
}