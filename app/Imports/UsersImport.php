<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Spatie\Permission\Models\Role;

class UsersImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $user = User::create([
            'nomor_induk' => $row['nomor_induk'],
            'nama' => $row['nama'],
            'email' => $row['email'],
            'password' => Hash::make($row['password'] ?? 'password123'),
            'status_akun' => 'active',
        ]);

        // Assign role
        $role = Role::where('name', $row['role'])->first();
        if ($role) {
            $user->assignRole($role);
        }

        return $user;
    }

    public function rules(): array
    {
        return [
            'nomor_induk' => 'required|unique:users,nomor_induk',
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:pelapor,teknisi',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nomor_induk.required' => 'Nomor induk wajib diisi.',
            'nomor_induk.unique' => 'Nomor induk sudah terdaftar.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar.',
            'role.required' => 'Role wajib diisi.',
            'role.in' => 'Role hanya boleh pelapor atau teknisi.',
        ];
    }
}