<?php

namespace App\Http\Controllers\Bikem;

use App\Http\Controllers\Controller;
use App\Models\Fasilitas;
use App\Models\PeminjamanFasilitas;
use App\Models\PeminjamanFasilitasItem;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BikemController extends Controller
{
    public function index(): View
    {
        // 1. Metrik Utama
        $stats = [
            'menunggu_bikem' => PeminjamanFasilitas::whereIn('status_pinjam', ['pending', 'menunggu_bikem'])->count(),
            'perlu_revisi' => PeminjamanFasilitas::where('status_pinjam', 'perlu_revisi')->count(),
            'menunggu_rtk' => PeminjamanFasilitas::where('status_pinjam', 'menunggu_rtk')->count(),
            'disetujui' => PeminjamanFasilitas::where('status_pinjam', 'disetujui')->count(),
            'selesai' => PeminjamanFasilitas::where('status_pinjam', 'selesai')->count(),
            'total' => PeminjamanFasilitas::count(),
        ];

        // 2. Daftar Pengajuan yang Memerlukan Tindakan BIKEM (Prioritas)
        $antreanPeminjaman = PeminjamanFasilitas::with(['peminjam', 'items.fasilitas'])
            ->whereIn('status_pinjam', ['pending', 'menunggu_bikem'])
            ->orderBy('created_at', 'asc')
            ->limit(10)
            ->get();

        // 3. Pengajuan Terakhir yang Sedang Perlu Revisi
        $antreanRevisi = PeminjamanFasilitas::with(['peminjam', 'items.fasilitas'])
            ->where('status_pinjam', 'perlu_revisi')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        // 4. Data Tren Bulanan (6 Bulan Terakhir)
        $months = [];
        $trendData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->locale('id')->isoFormat('MMM YYYY');
            $months[] = $monthName;

            $count = PeminjamanFasilitas::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $trendData[] = $count;
        }

        // 5. Top 5 Fasilitas / Ruangan Terpopuler
        $topFasilitas = PeminjamanFasilitasItem::select('fasilitas_id', DB::raw('count(*) as total_dipinjam'))
            ->with('fasilitas')
            ->groupBy('fasilitas_id')
            ->orderByDesc('total_dipinjam')
            ->limit(5)
            ->get();

        return view('bikem.dashboard', compact(
            'stats',
            'antreanPeminjaman',
            'antreanRevisi',
            'months',
            'trendData',
            'topFasilitas'
        ));
    }
}
