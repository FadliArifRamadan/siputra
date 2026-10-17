<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // 1. BERSIHKAN DATA LAMA (Opsional)
        // ==========================================
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ==========================================
        // 2. BUAT PERMISSIONS (Dasar)
        // ==========================================
        $permissions = [
            'manage users',
            'manage fasilitas',
            'manage pengajuan',
            'manage penugasan',
            'manage peminjaman',
            'view dashboard',
            'create laporan',
            'edit laporan',
            'delete laporan',
            'approve laporan',
            'reject laporan',
            'close ticket',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ==========================================
        // 3. BUAT ROLE
        // ==========================================
        $roleRtk = Role::firstOrCreate(['name' => 'rtk']);
        $rolePelapor = Role::firstOrCreate(['name' => 'pelapor']);
        $roleTeknisi = Role::firstOrCreate(['name' => 'teknisi']);

        // ==========================================
        // 4. ASSIGN PERMISSIONS KE ROLE
        // ==========================================

        // RTK (Super Admin) - semua permission
        $roleRtk->givePermissionTo(Permission::all());

        // Pelapor - hanya untuk pengajuan sendiri
        $rolePelapor->givePermissionTo([
            'view dashboard',
            'create laporan',
            'edit laporan',
            'delete laporan',
        ]);

        // Teknisi - hanya untuk penugasan dan close ticket
        $roleTeknisi->givePermissionTo([
            'view dashboard',
            'close ticket',
        ]);

        // ==========================================
        // 5. BUAT AKUN MASTER RTK
        // ==========================================
        $rtkUser = User::firstOrCreate(
            ['email' => 'rtk@ucic.ac.id'],
            [
                'nomor_induk' => 'RTK001',
                'nama' => 'Administrator RTK',
                'email' => 'rtk@ucic.ac.id',
                'password' => Hash::make('password123'),
                'status_akun' => 'active',
            ]
        );

        // Assign role RTK ke user master
        $rtkUser->assignRole('rtk');

        // ==========================================
        // 6. BUAT AKUN DUMMY PELAPOR (Opsional)
        // ==========================================
        $pelaporUser = User::firstOrCreate(
            ['email' => 'pelapor@ucic.ac.id'],
            [
                'nomor_induk' => 'PEL001',
                'nama' => 'Pelapor Dummy',
                'email' => 'pelapor@ucic.ac.id',
                'password' => Hash::make('password123'),
                'status_akun' => 'active',
            ]
        );
        $pelaporUser->assignRole('pelapor');

        // ==========================================
        // 7. BUAT AKUN DUMMY TEKNISI (Opsional)
        // ==========================================
        $teknisiUser = User::firstOrCreate(
            ['email' => 'teknisi@ucic.ac.id'],
            [
                'nomor_induk' => 'TEK001',
                'nama' => 'Teknisi Dummy',
                'email' => 'teknisi@ucic.ac.id',
                'password' => Hash::make('password123'),
                'status_akun' => 'active',
            ]
        );
        $teknisiUser->assignRole('teknisi');

        $this->command->info('Database seeding completed successfully!');
        $this->command->info('RTK Account: rtk@ucic.ac.id / password123');
        $this->command->info('Pelapor Account: pelapor@ucic.ac.id / password123');
        $this->command->info('Teknisi Account: teknisi@ucic.ac.id / password123');
    }
}
