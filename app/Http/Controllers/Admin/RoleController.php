<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::with('permissions')->get();
        return view('rtk.roles.index', compact('roles'));
    }

    public function create(): View
    {
        $permissions = Permission::all();
        return view('rtk.roles.create', compact('permissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create(['name' => $request->name]);

        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            $role->syncPermissions($permissions);
        }

        return redirect()->route('rtk.roles.index')
            ->with('success', 'Role "' . $role->name . '" berhasil dibuat.');
    }

    public function edit(Role $role): View
    {
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('rtk.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        // Jangan izinkan mengubah role 'rtk' (super admin)
        if ($role->name === 'rtk') {
            return redirect()->back()
                ->with('error', 'Role RTK tidak dapat diubah.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update(['name' => $request->name]);

        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            $role->syncPermissions($permissions);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('rtk.roles.index')
            ->with('success', 'Role "' . $role->name . '" berhasil diperbarui.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->name === 'rtk') {
            return redirect()->back()
                ->with('error', 'Role RTK tidak dapat dihapus.');
        }

        if ($role->users()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Role ini masih memiliki ' . $role->users()->count() . ' pengguna. Pindahkan atau hapus pengguna terlebih dahulu.');
        }

        $roleName = $role->name;
        $role->delete();

        return redirect()->route('rtk.roles.index')
            ->with('success', 'Role "' . $roleName . '" berhasil dihapus.');
    }

    public function assignForm(): View
    {
        $users = \App\Models\User::with('roles')->orderBy('nama')->get();
        $roles = Role::all();

        return view('rtk.roles.assign', compact('users', 'roles'));
    }

    public function assign(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|string|exists:roles,name',
        ]);

        $user = \App\Models\User::findOrFail($request->user_id);

        // Jangan izinkan mengubah role RTK melalui UI
        if ($user->hasRole('rtk')) {
            return redirect()->back()
                ->with('error', 'Role pengguna RTK tidak dapat diubah melalui UI.');
        }

        // Sync role (hapus semua role sebelumnya, assign role baru)
        $user->syncRoles([$request->role]);

        return redirect()->route('rtk.roles.assign')
            ->with('success', 'Role "' . $request->role . '" berhasil diberikan kepada ' . $user->nama . '.');
    }

    public function removeRole(Request $request, \App\Models\User $user): RedirectResponse
    {
        $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        if ($user->hasRole('rtk')) {
            return redirect()->back()
                ->with('error', 'Role RTK tidak dapat dihapus dari pengguna.');
        }

        if ($user->hasRole($request->role)) {
            $user->removeRole($request->role);
            return redirect()->route('rtk.roles.assign')
                ->with('success', 'Role "' . $request->role . '" berhasil dihapus dari ' . $user->nama . '.');
        }

        return redirect()->back()
            ->with('error', 'Pengguna tidak memiliki role tersebut.');
    }
}