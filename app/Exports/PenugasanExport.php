<?php

namespace App\Exports;

use App\Models\PenugasanTeknisi;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PenugasanExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
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
        return PenugasanTeknisi::with(['pengajuanKerusakan.pelapor', 'teknisi', 'pengajuanKerusakan.fasilitas'])
            ->whereMonth('created_at', $this->bulan)
            ->whereYear('created_at', $this->tahun);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Laporan ID',
            'Jenis Kerusakan',
            'Pelapor',
            'Lokasi',
            'Teknisi',
            'Jadwal Penanganan',
            'Instruksi Kerja',
            'Status Laporan',
            'Tanggal Penugasan',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->pengajuan_id,
            $row->pengajuanKerusakan->jenis_kerusakan ?? '-',
            $row->pengajuanKerusakan->pelapor->nama ?? '-',
            $row->pengajuanKerusakan->lokasi_detail ?? '-',
            $row->teknisi->nama ?? '-',
            $row->jadwal_penanganan->format('d/m/Y H:i'),
            $row->instruksi_kerja ?? '-',
            ucfirst($row->pengajuanKerusakan->status_laporan ?? '-'),
            $row->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11]],
            'A1:J1' => ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => 'E5E7EB']]],
        ];
    }

    public function title(): string
    {
        return 'Penugasan';
    }
}