<?php

namespace App\Http\Controllers;

use App\Models\PengajuanKerusakan;
use App\Models\PeminjamanFasilitas;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PelaporController extends Controller
{
    public function index(): View
    {
        $userId = Auth::id();

        // ✅ Tambahkan with() untuk eager loading relasi
        $laporanTerbaru = PengajuanKerusakan::with(['pelapor', 'penugasanTeknisi.teknisi'])
            ->where('pelapor_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // ✅ PERBAIKAN: Gunakan 'items.fasilitas' alih-alih 'fasilitas'
        $peminjamanTerbaru = PeminjamanFasilitas::with(['items.fasilitas', 'peminjam'])
            ->where('peminjam_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $statistik = [
            'total_laporan' => PengajuanKerusakan::where('pelapor_id', $userId)->count(),
            'laporan_selesai' => PengajuanKerusakan::where('pelapor_id', $userId)
                ->where('status_laporan', 'selesai')
                ->count(),
            'total_peminjaman' => PeminjamanFasilitas::where('peminjam_id', $userId)->count(),
            'peminjaman_disetujui' => PeminjamanFasilitas::where('peminjam_id', $userId)
                ->whereIn('status_pinjam', ['disetujui', 'berjalan'])
                ->count(),
        ];

        return view('pelapor.dashboard', compact(
            'laporanTerbaru',
            'peminjamanTerbaru',
            'statistik'
        ));
    }
}