<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            [
                'nomor_induk' => 'NIM123456',
                'nama' => 'Contoh User',
                'email' => 'user@example.com',
                'password' => 'password123',
                'role' => 'pelapor',
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'nomor_induk',
            'nama',
            'email',
            'password',
            'role',
        ];
    }
}