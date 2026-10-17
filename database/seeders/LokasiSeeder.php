<?php

namespace Database\Seeders;

use App\Models\Lokasi;
use Illuminate\Database\Seeder;

class LokasiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // ===================== KAMPUS 1 =====================
            // Lantai 1
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'A.1', 'nama_ruangan' => 'Security'],
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'B.1', 'nama_ruangan' => 'Student Admission'],
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'B.2', 'nama_ruangan' => 'Rectorate Office'],
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'B.3', 'nama_ruangan' => 'Podcast Studio'],
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'B.4', 'nama_ruangan' => 'Great Meeting Room 1'],
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'B.5', 'nama_ruangan' => 'Rectorate Officer'],
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'B.6', 'nama_ruangan' => 'Vice Rector III Office'],
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'B.7', 'nama_ruangan' => 'HR&GA Office'],
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'B.8', 'nama_ruangan' => 'Research and Community Service Office'],
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'B.9', 'nama_ruangan' => 'Quality Assurance Office'],
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'B.10', 'nama_ruangan' => 'Finance Office'],
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'C.1', 'nama_ruangan' => 'Academic & Student Administration Office'],
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'C.2', 'nama_ruangan' => 'Classroom 106'],
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'C.3', 'nama_ruangan' => 'Classroom 107'],
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'C.4', 'nama_ruangan' => 'Classroom 103'],
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'C.5', 'nama_ruangan' => 'Classroom 102'],
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'C.6', 'nama_ruangan' => 'Classroom 104'],
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'C.7', 'nama_ruangan' => 'Warehouse'],
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'D.1', 'nama_ruangan' => 'Lecture Room 2'],
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'D.2', 'nama_ruangan' => 'Faculty Office (FTI)'],
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'F.1', 'nama_ruangan' => 'Faculty Office (FTI)'],
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'D.3', 'nama_ruangan' => 'Lecture Room 1'],
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'G.1', 'nama_ruangan' => 'Health and Counseling Center'],
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'G.2', 'nama_ruangan' => 'Student Organization Office'],
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'G.3', 'nama_ruangan' => 'Faculty Office (FEB)'],
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'F.2', 'nama_ruangan' => 'Restroom'],
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'F.3', 'nama_ruangan' => 'Prayer Room'],

            // Lantai 2
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 2', 'kode_ruangan' => 'A.1', 'nama_ruangan' => 'Great Meeting 2'],
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 2', 'kode_ruangan' => 'B.1', 'nama_ruangan' => 'Library'],
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 2', 'kode_ruangan' => 'B.2', 'nama_ruangan' => 'CIC Hub'],
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 2', 'kode_ruangan' => 'B.3', 'nama_ruangan' => 'CIC Hub Labs'],
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 2', 'kode_ruangan' => 'C.1', 'nama_ruangan' => 'Computer Laboratory 1'],
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 2', 'kode_ruangan' => 'C.2', 'nama_ruangan' => 'Classroom 201'],
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 2', 'kode_ruangan' => 'C.3', 'nama_ruangan' => 'Data and Information Center'],
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 2', 'kode_ruangan' => 'C.4', 'nama_ruangan' => 'Computer Laboratory 1'],
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 2', 'kode_ruangan' => 'C.5', 'nama_ruangan' => 'Self-access Laboratory'],

            // Lantai 3
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 3', 'kode_ruangan' => 'A.1', 'nama_ruangan' => 'Auditorium'],
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 3', 'kode_ruangan' => 'B.1', 'nama_ruangan' => 'IoT and Computer Network Labs'],
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 3', 'kode_ruangan' => 'B.2', 'nama_ruangan' => 'Studio'],
            ['kampus' => 'Kampus 1', 'lantai' => 'Lantai 3', 'kode_ruangan' => 'B.3', 'nama_ruangan' => 'Classroom 301'],

            // ===================== KAMPUS 2 =====================
            // Lantai 1
            ['kampus' => 'Kampus 2', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'A.1', 'nama_ruangan' => 'Student Admission'],
            ['kampus' => 'Kampus 2', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'A.2', 'nama_ruangan' => 'Main Lobby'],
            ['kampus' => 'Kampus 2', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'A.3', 'nama_ruangan' => 'Restroom & Service Area'],
            ['kampus' => 'Kampus 2', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'B.2', 'nama_ruangan' => 'Lecture Room 3'],
            ['kampus' => 'Kampus 2', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'B.3', 'nama_ruangan' => "Rector's Office"],
            ['kampus' => 'Kampus 2', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'B.4', 'nama_ruangan' => 'Office of the Vice Rector I'],
            ['kampus' => 'Kampus 2', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'B.5', 'nama_ruangan' => 'Office of the Vice Rector II'],
            ['kampus' => 'Kampus 2', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'B.6', 'nama_ruangan' => "Rector's Secretary Office"],
            ['kampus' => 'Kampus 2', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'B.7', 'nama_ruangan' => 'Financial Audit Office'],
            ['kampus' => 'Kampus 2', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'B.8', 'nama_ruangan' => 'Head of Finance Office / Head of Academic and Student Affairs Bureau Office'],
            ['kampus' => 'Kampus 2', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'D.3', 'nama_ruangan' => 'Prayer Room'],
            ['kampus' => 'Kampus 2', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'D.4', 'nama_ruangan' => "Women's Restroom"],
            ['kampus' => 'Kampus 2', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'D.5', 'nama_ruangan' => "Men's Restroom"],
            ['kampus' => 'Kampus 2', 'lantai' => 'Lantai 1', 'kode_ruangan' => 'F.1', 'nama_ruangan' => 'CIC Mart'],

            // Lantai 2
            ['kampus' => 'Kampus 2', 'lantai' => 'Lantai 2', 'kode_ruangan' => 'G.1', 'nama_ruangan' => 'Classroom 201'],
            ['kampus' => 'Kampus 2', 'lantai' => 'Lantai 2', 'kode_ruangan' => 'G.2', 'nama_ruangan' => 'Classroom 202'],
            ['kampus' => 'Kampus 2', 'lantai' => 'Lantai 2', 'kode_ruangan' => 'G.3', 'nama_ruangan' => 'Classroom 205'],
            ['kampus' => 'Kampus 2', 'lantai' => 'Lantai 2', 'kode_ruangan' => 'G.4', 'nama_ruangan' => 'Computer Laboratory'],
            ['kampus' => 'Kampus 2', 'lantai' => 'Lantai 2', 'kode_ruangan' => 'G.5', 'nama_ruangan' => 'Classroom 203'],
            ['kampus' => 'Kampus 2', 'lantai' => 'Lantai 2', 'kode_ruangan' => 'G.6', 'nama_ruangan' => 'Room Control'],
            ['kampus' => 'Kampus 2', 'lantai' => 'Lantai 2', 'kode_ruangan' => 'G.6A', 'nama_ruangan' => 'Great Meeting Room 3'],
            ['kampus' => 'Kampus 2', 'lantai' => 'Lantai 2', 'kode_ruangan' => 'G.7', 'nama_ruangan' => 'Restroom'],

            // Lantai 3
            ['kampus' => 'Kampus 2', 'lantai' => 'Lantai 3', 'kode_ruangan' => 'H.1', 'nama_ruangan' => 'Convention Hall'],
            ['kampus' => 'Kampus 2', 'lantai' => 'Lantai 3', 'kode_ruangan' => 'H.2', 'nama_ruangan' => 'VVIP Room'],
            ['kampus' => 'Kampus 2', 'lantai' => 'Lantai 3', 'kode_ruangan' => 'H.3', 'nama_ruangan' => 'Operator Room'],
            ['kampus' => 'Kampus 2', 'lantai' => 'Lantai 3', 'kode_ruangan' => 'H.4', 'nama_ruangan' => 'Restroom'],
        ];

        foreach ($data as $item) {
            Lokasi::create($item);
        }
    }
}