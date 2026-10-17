<?php

namespace App\Exports;

use App\Models\PengajuanKerusakan;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanKerusakanExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
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
        return PengajuanKerusakan::with(['pelapor', 'fasilitas', 'penugasanTeknisi.teknisi'])
            ->whereMonth('created_at', $this->bulan)
            ->whereYear('created_at', $this->tahun);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Pelapor',
            'Nomor Induk Pelapor',
            'Fasilitas',
            'Jenis Kerusakan',
            'Lokasi',
            'Deskripsi',
            'Tingkat Urgensi',
            'Status Laporan',
            'Teknisi Ditugaskan',
            'Jadwal Penanganan',
            'Tanggal Laporan',
            'Catatan RTK',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->pelapor->nama ?? '-',
            $row->pelapor->nomor_induk ?? '-',
            $row->fasilitas->nama_fasilitas ?? '-',
            $row->jenis_kerusakan,
            $row->lokasi_detail,
            $row->deskripsi,
            ucfirst($row->tingkat_urgensi),
            ucfirst($row->status_laporan),
            $row->penugasanTeknisi->teknisi->nama ?? '-',
            $row->penugasanTeknisi ? $row->penugasanTeknisi->jadwal_penanganan->format('d/m/Y H:i') : '-',
            $row->created_at->format('d/m/Y H:i'),
            $row->catatan_rtk ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11]],
            'A1:M1' => ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => 'E5E7EB']]],
        ];
    }

    public function title(): string
    {
        return 'Laporan Kerusakan';
    }
}