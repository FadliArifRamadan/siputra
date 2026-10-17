<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RekapitulasiMultiSheetExport implements WithMultipleSheets
{
    protected $bulan;
    protected $tahun;

    public function __construct($bulan, $tahun)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function sheets(): array
    {
        return [
            new LaporanKerusakanExport($this->bulan, $this->tahun),
            new PeminjamanExport($this->bulan, $this->tahun),
            new PenugasanExport($this->bulan, $this->tahun),
        ];
    }
}