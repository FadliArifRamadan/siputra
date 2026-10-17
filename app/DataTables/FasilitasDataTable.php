<?php

namespace App\DataTables;

use App\Models\Fasilitas;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Http\Request;
use App\Traits\DataTableFilterTrait;

class FasilitasDataTable extends DataTable
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
            'kategori' => [
                'column' => 'kategori',
                'operator' => '=',
                'label' => 'Kategori',
                'type' => 'select',
                'options' => [
                    'sarana' => 'Sarana',
                    'prasarana' => 'Prasarana',
                ],
            ],
            'status_kondisi' => [
                'column' => 'status_kondisi',
                'operator' => '=',
                'label' => 'Status Kondisi',
                'type' => 'select',
                'options' => [
                    'baik' => 'Baik',
                    'rusak_ringan' => 'Rusak Ringan',
                    'rusak_berat' => 'Rusak Berat',
                    'maintenance' => 'Maintenance',
                ],
            ],
            'created_at' => [
                'column' => 'created_at',
                'operator' => 'between',
                'label' => 'Tanggal Dibuat',
                'type' => 'date_range',
            ],
        ]);

        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('action', function ($fasilitas) {
                return '
                    <div class="flex items-center justify-center gap-1.5">
                        <a href="' . route('rtk.fasilitas.show', $fasilitas->id) . '" 
                           title="Detail" class="p-1.5 text-[#191333] hover:text-[#f2723b] hover:bg-[#fbf7f0] rounded-lg transition-colors">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </a>
                        <a href="' . route('rtk.fasilitas.edit', $fasilitas->id) . '" 
                           title="Edit" class="p-1.5 text-amber-600 hover:text-amber-700 hover:bg-amber-50 rounded-lg transition-colors">
                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                        </a>
                        <a href="' . route('rtk.fasilitas.history', $fasilitas->id) . '" 
                           title="Riwayat" class="p-1.5 text-purple-600 hover:text-purple-700 hover:bg-purple-50 rounded-lg transition-colors">
                            <i data-lucide="history" class="w-4 h-4"></i>
                        </a>
                        <form action="' . route('rtk.fasilitas.destroy', $fasilitas->id) . '" 
                              method="POST" class="inline-block"
                              onsubmit="return confirm(\'Yakin ingin menghapus fasilitas ini?\')">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="submit" title="Hapus" class="p-1.5 text-rose-600 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition-colors">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                ';
            })
            ->editColumn('kategori', function ($fasilitas) {
                $badge = [
                    'sarana' => 'bg-blue-50 text-blue-700 border-blue-200',
                    'prasarana' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                ];

                return '<span class="px-2.5 py-1 inline-flex text-xs font-bold rounded-full border ' . ($badge[$fasilitas->kategori] ?? 'bg-gray-100 text-gray-800') . '">
                            ' . ucfirst($fasilitas->kategori) . '
                        </span>';
            })
            ->editColumn('stok_total', function ($fasilitas) {
                if ($fasilitas->kategori === 'prasarana') {
                    $available = $fasilitas->getAvailableStock();
                    return $available > 0
                        ? '<span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold">Tersedia</span>'
                        : '<span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 text-xs font-bold">Dipakai</span>';
                }

                $available = $fasilitas->getAvailableStock();
                $total = $fasilitas->stok_total;

                if ($available < $total) {
                    $textClass = $available === 0 ? 'text-rose-600 font-bold' : 'text-amber-600 font-bold';
                    return '<span class="' . $textClass . '" title="' . $available . ' tersedia dari ' . $total . ' total">
                                ' . $available . ' / ' . $total . '
                            </span>';
                }

                return '<span class="text-emerald-600 font-semibold">' . $total . ' / ' . $total . '</span>';
            })
            ->editColumn('status_kondisi', function ($fasilitas) {
                $badge = [
                    'baik' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'rusak_ringan' => 'bg-amber-50 text-amber-700 border-amber-200',
                    'rusak_berat' => 'bg-rose-50 text-rose-700 border-rose-200',
                    'maintenance' => 'bg-blue-50 text-blue-700 border-blue-200',
                ];

                return '<span class="px-2.5 py-1 inline-flex text-xs font-bold rounded-full border ' . ($badge[$fasilitas->status_kondisi] ?? 'bg-gray-100 text-gray-800') . '">
                            ' . ucfirst(str_replace('_', ' ', $fasilitas->status_kondisi)) . '
                        </span>';
            })
            ->editColumn('created_at', function ($fasilitas) {
                return $fasilitas->created_at->format('d/m/Y H:i');
            })
            ->rawColumns(['action', 'kategori', 'status_kondisi', 'stok_total'])
            ->setRowId('id');
    }

    public function query(Fasilitas $model): QueryBuilder
    {
        return $model->newQuery()
            ->select('fasilitas.*')
            ->where('fasilitas.tipe_fasilitas', 'pinjam')
            ->orderBy('fasilitas.created_at', 'desc');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('fasilitas-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->parameters([
                'language' => [
                    'url' => '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json',
                ],
                'pageLength' => 10,
                'lengthMenu' => [[10, 25, 50, -1], [10, 25, 50, 'Semua']],
                'dom' => '<"flex flex-col sm:flex-row justify-between items-center mb-4 gap-4"l>rt<"flex flex-col sm:flex-row justify-between items-center mt-4 gap-4"ip>',
                'drawCallback' => 'function() { if (window.lucide) { lucide.createIcons(); } }',
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')
                ->title('NO')
                ->addClass('text-center font-bold text-[#2a2338]')
                ->width('50px')
                ->orderable(false)
                ->searchable(false),
            Column::make('nama_fasilitas')->title('Nama Fasilitas')->addClass('text-left font-medium'),
            Column::make('kategori')->title('Kategori')->addClass('text-center'),
            Column::make('lokasi_detail')->title('Lokasi Detail')->addClass('text-left text-gray-600'),
            Column::make('stok_total')
                ->title('Stok (Tersedia/Total)')
                ->addClass('text-center')
                ->width('120px'),
            Column::make('status_kondisi')->title('Status Kondisi')->addClass('text-center'),
            Column::make('created_at')->title('Tanggal Dibuat')->addClass('text-center text-xs text-gray-500'),
            Column::computed('action')
                ->title('Aksi')
                ->exportable(false)
                ->printable(false)
                ->width(140)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Fasilitas_' . date('YmdHis');
    }
}
