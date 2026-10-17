<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class BikemRoleSeeder extends Seeder
{
    /**
     * Seeder khusus untuk menambahkan Role, Permission, dan Akun BIKEM
     * tanpa mengubah atau mengganggu data pengguna / peran yang sudah ada.
     */
    public function run(): void
    {
        // Reset cache permission Spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Daftarkan permission khusus BIKEM (hanya buat jika belum ada)
        $bikemPermissions = [
            'view bikem dashboard',
            'manage peminjaman bikem',
            'approve bikem',
            'reject bikem',
            'revisi bikem',
        ];

        foreach ($bikemPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // 2. Buat Role BIKEM (hanya buat jika belum ada)
        $roleBikem = Role::firstOrCreate(['name' => 'bikem']);
        $roleBikem->givePermissionTo(array_merge([
            'view dashboard',
            'create laporan',
            'edit laporan',
            'delete laporan'
        ], $bikemPermissions));

        // 3. Pastikan Role RTK juga memiliki akses ke permission baru
        $roleRtk = Role::where('name', 'rtk')->first();
        if ($roleRtk) {
            $roleRtk->givePermissionTo($bikemPermissions);
        }

        // 4. Buat akun default BIKEM (hanya buat jika email belum ada di database)
        $bikemUser = User::firstOrCreate(
            ['email' => 'bikem@ucic.ac.id'],
            [
                'nomor_induk' => 'BIKEM001',
                'nama' => 'Biro Kemahasiswaan (BIKEM)',
                'email' => 'bikem@ucic.ac.id',
                'password' => Hash::make('password123'),
                'status_akun' => 'active',
            ]
        );

        if (!$bikemUser->hasRole('bikem')) {
            $bikemUser->assignRole('bikem');
        }

        $this->command->info('Role BIKEM dan izin terkait berhasil disiapkan dengan aman.');
        $this->command->info('Email: bikem@ucic.ac.id | Password: password123');
    }
}
