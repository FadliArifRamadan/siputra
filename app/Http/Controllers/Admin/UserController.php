<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\UserDataTable;
use App\Exports\UsersTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\UsersImport;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\NotificationService;


class UserController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(UserDataTable $dataTable): View
    {
        // ✅ Tambahkan opsi untuk melihat data yang di-soft delete
        $withTrashed = request()->query('with_trashed', false);
        $stats = [
            'total' => User::whereDoesntHave('roles', function ($query) {
                $query->where('name', 'rtk');
            })->count(),
            'pending' => User::where('status_akun', 'pending')
                ->whereDoesntHave('roles', function ($query) {
                    $query->where('name', 'rtk');
                })->count(),
            'active' => User::where('status_akun', 'active')
                ->whereDoesntHave('roles', function ($query) {
                    $query->where('name', 'rtk');
                })->count(),
            'suspended' => User::where('status_akun', 'suspended')
                ->whereDoesntHave('roles', function ($query) {
                    $query->where('name', 'rtk');
                })->count(),
            'trashed' => User::onlyTrashed()
                ->whereDoesntHave('roles', function ($query) {
                    $query->where('name', 'rtk');
                })->count(),

        ];

        return view('rtk.users.index', compact('stats', 'withTrashed'));
    }

    public function getData(UserDataTable $dataTable)
    {
        return $dataTable->render('rtk.users.index');
    }

    // ✅ CREATE USER (MANUAL)
    public function create(): View
    {
        $roles = \Spatie\Permission\Models\Role::where('name', '!=', 'rtk')->get();
        return view('rtk.users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nomor_induk' => 'required|string|max:50|unique:users,nomor_induk',
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'nomor_induk' => $request->nomor_induk,
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status_akun' => 'active',
        ]);

        $user->assignRole($request->role);

        // Notifikasi ke user baru
        $this->notificationService->create(
            $user,
            'Akun Anda Telah Dibuat',
            'Akun Anda telah dibuat oleh RTK. Silakan login dengan password yang diberikan.',
            'account_created',
            route('login'),
            $user
        );

        return redirect()->route('rtk.users.index')
            ->with('success', 'User ' . $user->nama . ' berhasil ditambahkan.');
    }

    // ✅ EDIT USER
    public function edit(User $user): View|RedirectResponse
    {
        // Tidak boleh mengedit user RTK
        if ($user->hasRole('rtk')) {
            return redirect()->route('rtk.users.index')
                ->with('error', 'Tidak dapat mengedit akun RTK.');
        }

        $roles = \Spatie\Permission\Models\Role::where('name', '!=', 'rtk')->get();
        $userRole = $user->roles->first()?->name;

        return view('rtk.users.edit', compact('user', 'roles', 'userRole'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        if ($user->hasRole('rtk')) {
            return redirect()->route('rtk.users.index')
                ->with('error', 'Tidak dapat mengedit akun RTK.');
        }

        $request->validate([
            'nomor_induk' => 'required|string|max:50|unique:users,nomor_induk,' . $user->id,
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|exists:roles,name',
        ]);

        $data = [
            'nomor_induk' => $request->nomor_induk,
            'nama' => $request->nama,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // Sync role (hapus semua role lama, assign role baru)
        $user->syncRoles([$request->role]);

        return redirect()->route('rtk.users.index')
            ->with('success', 'User ' . $user->nama . ' berhasil diperbarui.');
    }

    // ✅ DELETE USER
    public function destroy(User $user): RedirectResponse
    {
        if ($user->hasRole('rtk')) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus akun RTK.');
        }

        $nama = $user->nama;
        $user->delete();

        return redirect()->route('rtk.users.index')
            ->with('success', 'User ' . $nama . ' berhasil dihapus.');
    }

    // ✅ IMPORT EXCEL/CSV
    public function importForm(): View
    {
        return view('rtk.users.import');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv|max:5120',
        ]);

        try {
            Excel::import(new UsersImport, $request->file('file'));
            return redirect()->route('rtk.users.index')
                ->with('success', 'Data user berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    // ✅ EXPORT TEMPLATE
    public function exportTemplate()
    {
        return Excel::download(new UsersTemplateExport, 'template_import_user.xlsx');
    }


    public function approve(User $user): RedirectResponse
    {
        if ($user->hasRole('rtk')) {
            return redirect()->back()->with('error', 'Tidak dapat mengubah status akun RTK.');
        }

        $oldStatus = $user->status_akun;
        $user->update(['status_akun' => 'active']);

        // ✅ Log status change
        $user->logStatusChange('active', $oldStatus, 'Akun disetujui oleh RTK');
        // ✅ Kirim notifikasi + email
        $this->notificationService->notify(
            $user,
            'Akun Anda Disetujui',
            'Selamat! Akun Anda telah disetujui oleh RTK. Anda sekarang dapat mengakses semua fitur.',
            'account_approved',
            route('pelapor.dashboard'),
            $user,
            true // send email
        );

        return redirect()->back()->with('success', 'Akun ' . $user->nama . ' berhasil diaktifkan.');
    }

    public function reject(User $user): RedirectResponse
    {
        if ($user->hasRole('rtk')) {
            return redirect()->back()->with('error', 'Tidak dapat mengubah status akun RTK.');
        }

        $oldStatus = $user->status_akun;
        $user->update(['status_akun' => 'suspended']);

        // ✅ Log status change
        $user->logStatusChange('suspended', $oldStatus, 'Akun ditolak oleh RTK');
        // ✅ Kirim notifikasi + email
        $this->notificationService->notify(
            $user,
            'Akun Anda Ditolak',
            'Akun Anda ditolak oleh RTK. Silakan hubungi RTK untuk informasi lebih lanjut.',
            'account_rejected',
            null,
            $user,
            true // send email
        );

        return redirect()->back()->with('success', 'Akun ' . $user->nama . ' berhasil ditolak.');
    }

    public function suspend(User $user): RedirectResponse
    {
        if ($user->hasRole('rtk')) {
            return redirect()->back()->with('error', 'Tidak dapat menangguhkan akun RTK.');
        }

        $user->update(['status_akun' => 'suspended']);

        return redirect()->back()->with('success', 'Akun ' . $user->nama . ' berhasil ditangguhkan.');
    }

    public function activate(User $user): RedirectResponse
    {
        if ($user->hasRole('rtk')) {
            return redirect()->back()->with('error', 'Tidak dapat mengubah status akun RTK.');
        }

        $user->update(['status_akun' => 'active']);

        return redirect()->back()->with('success', 'Akun ' . $user->nama . ' berhasil diaktifkan kembali.');
    }
}
