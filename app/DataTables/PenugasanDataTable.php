<?php

namespace App\DataTables;

use App\Models\PenugasanTeknisi;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\DataTableFilterTrait;

class PenugasanDataTable extends DataTable
{
    use DataTableFilterTrait;

    protected Request $httpRequest;

    public function __construct(Request $request)
    {
        $this->httpRequest = $request;
    }


    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        // ✅ Apply filters
        $this->applyFilters($query, $this->httpRequest, [
            'status_laporan' => [
                'column' => 'pengajuan_kerusakan.status_laporan',
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
                'column' => 'pengajuan_kerusakan.tingkat_urgensi',
                'operator' => '=',
                'label' => 'Urgensi',
                'type' => 'select',
                'options' => [
                    'rendah' => 'Rendah',
                    'sedang' => 'Sedang',
                    'tinggi' => 'Tinggi',
                ],
            ],
            'teknisi_id' => [
                'column' => 'teknisi_id',
                'operator' => '=',
                'label' => 'Teknisi',
                'type' => 'select',
                'options' => $this->getTeknisiOptions(),
            ],
        ]);


        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('action', function ($penugasan) {
                $buttons = '';

                if ($penugasan->pengajuanKerusakan->status_laporan !== 'selesai') {
                    $buttons .= '<a href="' . route('rtk.penugasan.edit', $penugasan->id) . '" 
                                   class="text-yellow-600 hover:text-yellow-800 mr-2">
                                    <i class="fas fa-edit"></i>
                                </a>';
                    $buttons .= '<form action="' . route('rtk.penugasan.destroy', $penugasan->id) . '" 
                                  method="POST" class="inline-block"
                                  onsubmit="return confirm(\'Yakin ingin membatalkan penugasan ini?\')">
                                    ' . csrf_field() . '
                                    ' . method_field('DELETE') . '
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>';
                } else {
                    $buttons .= '<span class="text-gray-400 text-xs">Selesai</span>';
                }

                return $buttons;
            })
            ->addColumn('pelapor', function ($penugasan) {
                return $penugasan->pengajuanKerusakan->pelapor->nama ?? 'Tidak diketahui';
            })
            ->addColumn('jenis_kerusakan', function ($penugasan) {
                return $penugasan->pengajuanKerusakan->jenis_kerusakan;
            })
            ->addColumn('teknisi', function ($penugasan) {
                return $penugasan->teknisi->nama ?? 'Tidak diketahui';
            })
            ->addColumn('status_laporan', function ($penugasan) {
                $badge = [
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'ditolak' => 'bg-red-100 text-red-800',
                    'diproses' => 'bg-blue-100 text-blue-800',
                    'selesai' => 'bg-green-100 text-green-800',
                ];

                $status = $penugasan->pengajuanKerusakan->status_laporan;

                return '<span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ' . ($badge[$status] ?? 'bg-gray-100 text-gray-800') . '">
                            ' . ucfirst($status) . '
                        </span>';
            })
            ->addColumn('conflict', function ($penugasan) {
                // Cek apakah ada penugasan lain yang bentrok dengan teknisi ini
                $conflict = \App\Models\PenugasanTeknisi::where('teknisi_id', $penugasan->teknisi_id)
                    ->where('id', '!=', $penugasan->id)
                    ->whereHas('pengajuanKerusakan', function ($q) {
                        $q->whereIn('status_laporan', ['diproses', 'pending']);
                    })
                    ->where('jadwal_penanganan', $penugasan->jadwal_penanganan)
                    ->exists();

                if ($conflict) {
                    return '<span class="text-red-600 text-xs font-semibold"><i class="fas fa-exclamation-triangle"></i> Bentrok</span>';
                }
                return '<span class="text-green-600 text-xs"><i class="fas fa-check"></i> OK</span>';
            })

            ->editColumn('jadwal_penanganan', function ($penugasan) {
                return $penugasan->jadwal_penanganan
                    ? Carbon::parse($penugasan->jadwal_penanganan)->format('d/m/Y H:i')
                    : '-';
            })
            ->editColumn('created_at', function ($penugasan) {
                return $penugasan->created_at->format('d/m/Y H:i');
            })
            ->rawColumns(['action', 'status_laporan', 'conflict'])
            ->setRowId('id');
    }

    public function query(PenugasanTeknisi $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['pengajuanKerusakan.pelapor', 'teknisi'])
            ->join('pengajuan_kerusakan', 'penugasan_teknisi.pengajuan_id', '=', 'pengajuan_kerusakan.id')
            ->select('penugasan_teknisi.*')  // ✅ TAMBAHKAN
            ->orderBy('penugasan_teknisi.created_at', 'desc');  // ✅ TAMBAHKAN PREFIX
    }

    private function getTeknisiOptions(): array
    {
        return \App\Models\User::role('teknisi')
            ->pluck('nama', 'id')
            ->toArray();
    }


    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('penugasan-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0, 'desc')
            ->buttons([
                Button::make('reload'),
            ])
            ->parameters([
                'language' => [
                    'url' => '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json',
                ],
                'pageLength' => 10,
                'lengthMenu' => [[10, 25, 50, -1], [10, 25, 50, 'All']],
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')
                ->title('NO')
                ->addClass('text-center font-bold')
                ->width('50px')
                ->orderable(false)
                ->searchable(false),
            Column::make('pelapor')
                ->title('Pelapor')
                ->addClass('text-center'),
            Column::make('jenis_kerusakan')
                ->title('Jenis Kerusakan')
                ->addClass('text-center'),
            Column::make('teknisi')
                ->title('Teknisi')
                ->addClass('text-center'),
            Column::make('jadwal_penanganan')
                ->title('Jadwal')
                ->addClass('text-center'),
            Column::make('status_laporan')
                ->title('Status')
                ->addClass('text-center'),
            Column::make('created_at')
                ->title('Tanggal')
                ->addClass('text-center'),
            Column::computed('action')
                ->title('Aksi')
                ->exportable(false)
                ->printable(false)
                ->width(100)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Penugasan_' . date('YmdHis');
    }
}
