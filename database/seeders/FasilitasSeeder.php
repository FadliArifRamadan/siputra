<?php

namespace Database\Seeders;

use App\Models\Fasilitas;
use Illuminate\Database\Seeder;

class FasilitasSeeder extends Seeder
{
    public function run(): void
    {
        // Fasilitas dengan status baik
        Fasilitas::create([
            'nama_fasilitas' => 'Ruang Aula Utama',
            'kategori' => 'prasarana',
            'lokasi_detail' => 'Gedung A Lantai 2',
            'stok_total' => 1,
            'status_kondisi' => 'baik',
        ]);

        Fasilitas::create([
            'nama_fasilitas' => 'Proyektor Epson',
            'kategori' => 'sarana',
            'lokasi_detail' => 'Gedung B Lantai 1 - Ruang 102',
            'stok_total' => 3,
            'status_kondisi' => 'baik',
        ]);

        // Fasilitas dengan status rusak ringan
        Fasilitas::create([
            'nama_fasilitas' => 'AC Ruang Rapat',
            'kategori' => 'sarana',
            'lokasi_detail' => 'Gedung C Lantai 3 - Ruang 301',
            'stok_total' => 2,
            'status_kondisi' => 'rusak_ringan',
        ]);

        // Fasilitas dengan status rusak berat (tidak bisa dipinjam)
        Fasilitas::create([
            'nama_fasilitas' => 'Laptop Lab Komputer',
            'kategori' => 'sarana',
            'lokasi_detail' => 'Gedung D Lantai 1 - Lab Komputer',
            'stok_total' => 5,
            'status_kondisi' => 'rusak_berat',
        ]);

        // Fasilitas dengan status maintenance (tidak bisa dipinjam)
        Fasilitas::create([
            'nama_fasilitas' => 'Ruang Seminar',
            'kategori' => 'prasarana',
            'lokasi_detail' => 'Gedung E Lantai 2',
            'stok_total' => 1,
            'status_kondisi' => 'maintenance',
        ]);
    }
}