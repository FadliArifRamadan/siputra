<?php

namespace App\Exports;

use App\Models\PeminjamanFasilitas;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PeminjamanExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    protected $bulan;
    protected $tahun;

    public function __construct($bulan, $tahun)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function query()
    {
        return PeminjamanFasilitas::with(['peminjam', 'fasilitas'])
            ->whereMonth('created_at', $this->bulan)
            ->whereYear('created_at', $this->tahun);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Peminjam',
            'Nomor Induk Peminjam',
            'Fasilitas',
            'Kategori Fasilitas',
            'Jumlah Dipinjam',
            'Waktu Mulai',
            'Waktu Selesai',
            'Keperluan',
            'Status Peminjaman',
            'Tanggal Pengajuan',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->peminjam->nama ?? '-',
            $row->peminjam->nomor_induk ?? '-',
            $row->fasilitas->nama_fasilitas ?? '-',
            ucfirst($row->fasilitas->kategori ?? '-'),
            $row->jumlah_dipinjam,
            $row->waktu_mulai->format('d/m/Y H:i'),
            $row->waktu_selesai->format('d/m/Y H:i'),
            $row->keperluan,
            ucfirst($row->status_pinjam),
            $row->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11]],
            'A1:K1' => ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => 'E5E7EB']]],
        ];
    }

    public function title(): string
    {
        return 'Peminjaman';
    }
}