<?php

namespace App\DataTables;

use App\Models\User;
use App\Traits\DataTableFilterTrait;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UserDataTable extends DataTable
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
            'status_akun' => [
                'column' => 'users.status_akun',
                'operator' => '=',
                'label' => 'Status Akun',
                'type' => 'select',
                'options' => [
                    'pending' => 'Pending',
                    'active' => 'Active',
                    'suspended' => 'Suspended',
                ],
            ],
            'role' => [
                'column' => 'roles.name',
                'operator' => '=',
                'label' => 'Role',
                'type' => 'select',
                'options' => [
                    'pelapor' => 'Pelapor',
                    'teknisi' => 'Teknisi',
                ],
            ],
        ]);

        return (new EloquentDataTable($query))
            ->addIndexColumn()

            // 1. Kolom Aksi: Pending -> tombol Setujui/Tolak langsung; lainnya -> dropdown menu
            ->addColumn('action', function ($user) {
                $isPending = $user->status_akun === 'pending';

                // === Akun PENDING: tampilkan Setujui & Tolak langsung (tanpa dropdown) ===
                if ($isPending) {
                    $html = '<div class="flex items-center justify-center gap-2">';

                    $html .= '<form action="' . route('rtk.users.approve', $user->id) . '" method="POST" class="m-0">'
                            . csrf_field() . method_field('PATCH')
                            . '<button type="submit" title="Setujui" class="inline-flex items-center gap-1.5 rounded-md border border-emerald-200 bg-emerald-50 px-2.5 py-1.5 text-xs font-semibold text-emerald-700 transition-colors hover:bg-emerald-100">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                                    Setujui
                                </button></form>';

                    $html .= '<form action="' . route('rtk.users.reject', $user->id) . '" method="POST" class="m-0">'
                            . csrf_field() . method_field('PATCH')
                            . '<button type="submit" title="Tolak" onclick="return confirm(\'Yakin ingin menolak akun ' . addslashes($user->nama) . '?\')" class="inline-flex items-center gap-1.5 rounded-md border border-rose-200 bg-rose-50 px-2.5 py-1.5 text-xs font-semibold text-rose-700 transition-colors hover:bg-rose-100">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16ZM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22Z" clip-rule="evenodd" /></svg>
                                    Tolak
                                </button></form>';

                    $html .= '</div>';
                    return $html;
                }

                // === Akun ACTIVE / SUSPENDED: dropdown menu (Edit, status, Hapus) ===
                $menuId = 'action-menu-' . $user->id;
                $itemClass = 'flex w-full items-center gap-2.5 px-3 py-2 text-left text-xs font-medium transition-colors';

                $html = '<div class="flex items-center justify-center">';
                $html .= '<div class="relative inline-block text-left">';

                $html .= '<button type="button" onclick="toggleActionMenu(event, \'' . $menuId . '\')"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700"
                            aria-haspopup="true" aria-expanded="false" title="Aksi">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 8.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM11.5 15.5a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0z" /></svg>
                        </button>';

                $html .= '<div id="' . $menuId . '" class="action-menu hidden fixed z-50 w-44 rounded-lg border border-gray-200 bg-white py-1 shadow-lg" role="menu">';

                // Edit
                $html .= '<a href="' . route('rtk.users.edit', $user->id) . '" class="' . $itemClass . ' text-gray-700 hover:bg-gray-50" role="menuitem">
                                <svg class="h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path d="M5.433 13.917l1.262-3.155A4 4 0 016.58 9.42l6.92-6.918a2.121 2.121 0 013 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 01-.65-.65z" /><path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H9A.75.75 0 009 3H4.75A2.75 2.75 0 002 5.75v9.5A2.75 2.75 0 004.75 18h9.5A2.75 2.75 0 0017 15.25V11a.75.75 0 00-1.5 0v4.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5z" /></svg>
                                Edit
                            </a>';

                if ($user->status_akun === 'active') {
                    $html .= '<form action="' . route('rtk.users.suspend', $user->id) . '" method="POST" class="m-0">'
                            . csrf_field() . method_field('PATCH')
                            . '<button type="submit" class="' . $itemClass . ' text-amber-700 hover:bg-amber-50" role="menuitem" onclick="return confirm(\'Yakin ingin menangguhkan akun ' . addslashes($user->nama) . '?\')">
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.75 7.75a.75.75 0 00-1.5 0v4.5a.75.75 0 001.5 0v-4.5zm3.75 0a.75.75 0 00-1.5 0v4.5a.75.75 0 001.5 0v-4.5z" clip-rule="evenodd" /></svg>
                                    Tangguhkan
                                </button></form>';
                } elseif ($user->status_akun === 'suspended') {
                    $html .= '<form action="' . route('rtk.users.activate', $user->id) . '" method="POST" class="m-0">'
                            . csrf_field() . method_field('PATCH')
                            . '<button type="submit" class="' . $itemClass . ' text-blue-700 hover:bg-blue-50" role="menuitem" onclick="return confirm(\'Yakin ingin mengaktifkan kembali akun ' . addslashes($user->nama) . '?\')">
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.75 7.4a.75.75 0 011.14-.638l3.5 2.6a.75.75 0 010 1.276l-3.5 2.6a.75.75 0 01-1.14-.638V7.4z" clip-rule="evenodd" /></svg>
                                    Aktifkan
                                </button></form>';
                }

                $html .= '<div class="my-1 border-t border-gray-100"></div>';
                $html .= '<form action="' . route('rtk.users.destroy', $user->id) . '" method="POST" class="m-0">'
                        . csrf_field() . method_field('DELETE')
                        . '<button type="submit" class="' . $itemClass . ' text-red-600 hover:bg-red-50" role="menuitem" onclick="return confirm(\'Yakin ingin menghapus user ' . addslashes($user->nama) . '?\')">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" /></svg>
                                Hapus
                            </button></form>';

                $html .= '</div>'; // .action-menu
                $html .= '</div>'; // relative wrapper
                $html .= '</div>'; // center wrapper

                return $html;
            })

            ->addColumn('roles', function ($user) {
                $roles = $user->roles->pluck('name')->toArray();

                if (empty($roles)) {
                    return '<span class="text-xs italic text-gray-400">Tidak ada role</span>';
                }

                $badges = [];
                foreach ($roles as $role) {
                    $color = match ($role) {
                        'rtk' => 'bg-red-50 text-red-700 border border-red-200',
                        'teknisi' => 'bg-blue-50 text-blue-700 border border-blue-200',
                        'pelapor' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                        default => 'bg-gray-50 text-gray-600 border border-gray-200',
                    };
                    $badges[] = '<span class="inline-flex items-center justify-center min-w-[75px] px-2.5 py-1 rounded-md text-xs font-medium tracking-wide whitespace-nowrap ' . $color . '">' . ucfirst($role) . '</span>';
                }
                return '<div class="flex items-center justify-center gap-1.5 overflow-hidden">' . implode(' ', $badges) . '</div>';
            })

            ->editColumn('status_akun', function ($user) {
                $badge = [
                    'pending' => 'bg-amber-50 text-amber-700 border border-amber-200',
                    'active' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                    'suspended' => 'bg-rose-50 text-rose-700 border border-rose-200',
                ];

                $statusClass = $badge[$user->status_akun] ?? 'bg-gray-50 text-gray-600 border border-gray-200';

                return '<span class="inline-flex items-center justify-center min-w-[85px] px-2.5 py-1 rounded-md text-xs font-medium tracking-wide whitespace-nowrap ' . $statusClass . '">
                            ' . ucfirst($user->status_akun) . '
                        </span>';
            })
            ->editColumn('created_at', function ($user) {
                return '<span class="whitespace-nowrap text-xs text-gray-600">' . $user->created_at->format('d/m/Y H:i') . '</span>';
            })
            ->rawColumns(['action', 'status_akun', 'roles', 'created_at'])
            ->setRowId('id');
    }

    public function query(User $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->with('roles')
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'rtk');
            })
            ->select('users.*')
            ->orderBy('users.created_at', 'asc');

        if ($this->request->query('with_trashed', false)) {
            $query->withTrashed();
        }

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('users-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0, 'asc')
            ->buttons([
                Button::make('reload'),
            ])
            ->parameters([
                'language' => [
                    'url' => '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json',
                ],
                'pageLength' => 10,
                'lengthMenu' => [[10, 25, 50, -1], [10, 25, 50, 'Semua']],
                'responsive' => true,
                'autoWidth' => false,
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')
                ->title('No')
                ->addClass('text-center align-middle py-3 px-3')
                ->width('50px')
                ->orderable(false)
                ->searchable(false),

            Column::make('nomor_induk')
                ->title('Nomor Induk')
                ->addClass('text-center align-middle py-3 px-3 font-mono text-xs'),

            Column::make('nama')
                ->title('Nama')
                ->addClass('text-left align-middle py-3 px-4 font-medium text-gray-900'),

            Column::make('email')
                ->title('Email')
                ->addClass('text-left align-middle py-3 px-4 text-gray-600'),

            Column::make('roles')
                ->title('Role')
                ->addClass('text-center align-middle py-3 px-3'),

            Column::make('status_akun')
                ->title('Status Akun')
                ->addClass('text-center align-middle py-3 px-3'),

            Column::make('created_at')
                ->title('Tanggal Daftar')
                ->addClass('text-center align-middle py-3 px-3'),

            Column::computed('action')
                ->title('Aksi')
                ->exportable(false)
                ->printable(false)
                ->addClass('text-center align-middle py-3 px-3'),
        ];
    }

    protected function filename(): string
    {
        return 'Users_' . date('YmdHis');
    }
}