<?php

namespace App\Http\Controllers\Unit;

use App\Http\Controllers\Controller;
use App\Models\PengajuanKerusakan;
use App\Models\PeminjamanFasilitas;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UnitController extends Controller
{
    public function index(): View
    {
        $userId = Auth::id();
        // 1. Data Riwayat Laporan Kerusakan Unit Ini
        $laporanTerbaru = PengajuanKerusakan::with(['pelapor', 'penugasanTeknisi.teknisi'])
            ->where('pelapor_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // 2. Data Riwayat Peminjaman Fasilitas Unit Ini
        $peminjamanTerbaru = PeminjamanFasilitas::with(['items.fasilitas', 'peminjam'])
            ->where('peminjam_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // 3. Ringkasan Statistik
        $statistik = [
            'total_laporan' => PengajuanKerusakan::where('pelapor_id', $userId)->count(),
            'laporan_pending' => PengajuanKerusakan::where('pelapor_id', $userId)->where('status_laporan', 'pending')->count(),
            'laporan_selesai' => PengajuanKerusakan::where('pelapor_id', $userId)->where('status_laporan', 'selesai')->count(),
            'total_peminjaman' => PeminjamanFasilitas::where('peminjam_id', $userId)->count(),
            'peminjaman_aktif' => PeminjamanFasilitas::where('peminjam_id', $userId)->whereIn('status_pinjam', ['disetujui', 'berjalan'])->count(),
        ];

        return view('unit.dashboard', compact(
            'laporanTerbaru',
            'peminjamanTerbaru',
            'statistik'
        ));
    }
}
