<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanKerusakan;
use App\Models\PenugasanTeknisi;
use App\Models\PeminjamanFasilitas;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanKerusakanExport;
use App\Exports\PeminjamanExport;
use App\Exports\PenugasanExport;
use App\Exports\RekapitulasiMultiSheetExport;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LaporanController extends Controller
{
    public function rekapitulasiBulanan(Request $request)
    {
        $bulan = $request->input('bulan', Carbon::now()->month);
        $tahun = $request->input('tahun', Carbon::now()->year);

        // Data laporan kerusakan
        $laporanKerusakan = PengajuanKerusakan::with(['pelapor', 'penugasanTeknisi.teknisi'])
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->get();

        // Data penugasan teknisi
        $penugasan = PenugasanTeknisi::with(['pengajuanKerusakan.pelapor', 'teknisi'])
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->get();

        // Data peminjaman fasilitas
        $peminjaman = PeminjamanFasilitas::with(['fasilitas', 'peminjam'])
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->get();

        // Statistik
        $statistik = [
            'total_laporan' => $laporanKerusakan->count(),
            'total_penugasan' => $penugasan->count(),
            'total_peminjaman' => $peminjaman->count(),
            'laporan_selesai' => $laporanKerusakan->where('status_laporan', 'selesai')->count(),
            'laporan_diproses' => $laporanKerusakan->where('status_laporan', 'diproses')->count(),
            'laporan_pending' => $laporanKerusakan->where('status_laporan', 'pending')->count(),
            'laporan_ditolak' => $laporanKerusakan->where('status_laporan', 'ditolak')->count(),
            'peminjaman_disetujui' => $peminjaman->where('status_pinjam', 'disetujui')->count(),
            'peminjaman_berjalan' => $peminjaman->where('status_pinjam', 'berjalan')->count(),
            'peminjaman_selesai' => $peminjaman->where('status_pinjam', 'selesai')->count(),
        ];

        return view('rtk.laporan.rekapitulasi', compact(
            'laporanKerusakan',
            'penugasan',
            'peminjaman',
            'statistik',
            'bulan',
            'tahun'
        ));
    }

    public function eksporPdf(Request $request): Response
    {
        $bulan = $request->input('bulan', Carbon::now()->month);
        $tahun = $request->input('tahun', Carbon::now()->year);

        // Data laporan kerusakan
        $laporanKerusakan = PengajuanKerusakan::with(['pelapor', 'penugasanTeknisi.teknisi'])
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->get();

        // Data penugasan teknisi
        $penugasan = PenugasanTeknisi::with(['pengajuanKerusakan.pelapor', 'teknisi'])
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->get();

        // Data peminjaman fasilitas
        $peminjaman = PeminjamanFasilitas::with(['fasilitas', 'peminjam'])
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->get();

        // Statistik
        $statistik = [
            'total_laporan' => $laporanKerusakan->count(),
            'total_penugasan' => $penugasan->count(),
            'total_peminjaman' => $peminjaman->count(),
            'laporan_selesai' => $laporanKerusakan->where('status_laporan', 'selesai')->count(),
            'laporan_diproses' => $laporanKerusakan->where('status_laporan', 'diproses')->count(),
            'laporan_pending' => $laporanKerusakan->where('status_laporan', 'pending')->count(),
            'laporan_ditolak' => $laporanKerusakan->where('status_laporan', 'ditolak')->count(),
            'peminjaman_disetujui' => $peminjaman->where('status_pinjam', 'disetujui')->count(),
            'peminjaman_berjalan' => $peminjaman->where('status_pinjam', 'berjalan')->count(),
            'peminjaman_selesai' => $peminjaman->where('status_pinjam', 'selesai')->count(),
        ];

        $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->locale('id')->isoFormat('MMMM YYYY');

        $pdf = Pdf::loadView('rtk.laporan.pdf-rekapitulasi', compact(
            'laporanKerusakan',
            'penugasan',
            'peminjaman',
            'statistik',
            'bulan',
            'tahun',
            'namaBulan'
        ));

        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('Rekapitulasi_Bulanan_' . $namaBulan . '.pdf');
    }

    // ✅ TAMBAHKAN METODE INI
    public function eksporExcel(Request $request)
    {
        $bulan = $request->input('bulan', Carbon::now()->month);
        $tahun = $request->input('tahun', Carbon::now()->year);
        $format = $request->input('format', 'xlsx'); // xlsx atau csv

        $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->locale('id')->isoFormat('MMMM_YYYY');
        $fileName = 'Rekapitulasi_' . $namaBulan;

        if ($format === 'csv') {
            return Excel::download(new LaporanKerusakanExport($bulan, $tahun), $fileName . '_kerusakan.csv', \Maatwebsite\Excel\Excel::CSV);
        }

        // Default: xlsx dengan multiple sheets
        return Excel::download(new \App\Exports\RekapitulasiMultiSheetExport($bulan, $tahun), $fileName . '.xlsx');
    }

    // ✅ TAMBAHKAN METODE INI UNTUK EKSPOR PER JENIS
    public function eksporKerusakan(Request $request)
    {
        $bulan = $request->input('bulan', Carbon::now()->month);
        $tahun = $request->input('tahun', Carbon::now()->year);
        $format = $request->input('format', 'xlsx');

        $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->locale('id')->isoFormat('MMMM_YYYY');
        $fileName = 'Laporan_Kerusakan_' . $namaBulan;

        if ($format === 'csv') {
            return Excel::download(new LaporanKerusakanExport($bulan, $tahun), $fileName . '.csv', \Maatwebsite\Excel\Excel::CSV);
        }

        return Excel::download(new LaporanKerusakanExport($bulan, $tahun), $fileName . '.xlsx');
    }

    public function eksporPeminjaman(Request $request)
    {
        $bulan = $request->input('bulan', Carbon::now()->month);
        $tahun = $request->input('tahun', Carbon::now()->year);
        $format = $request->input('format', 'xlsx');

        $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->locale('id')->isoFormat('MMMM_YYYY');
        $fileName = 'Peminjaman_' . $namaBulan;

        if ($format === 'csv') {
            return Excel::download(new PeminjamanExport($bulan, $tahun), $fileName . '.csv', \Maatwebsite\Excel\Excel::CSV);
        }

        return Excel::download(new PeminjamanExport($bulan, $tahun), $fileName . '.xlsx');
    }

    public function eksporPenugasan(Request $request)
    {
        $bulan = $request->input('bulan', Carbon::now()->month);
        $tahun = $request->input('tahun', Carbon::now()->year);
        $format = $request->input('format', 'xlsx');

        $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->locale('id')->isoFormat('MMMM_YYYY');
        $fileName = 'Penugasan_' . $namaBulan;

        if ($format === 'csv') {
            return Excel::download(new PenugasanExport($bulan, $tahun), $fileName . '.csv', \Maatwebsite\Excel\Excel::CSV);
        }

        return Excel::download(new PenugasanExport($bulan, $tahun), $fileName . '.xlsx');
    }
}
