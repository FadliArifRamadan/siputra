<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\LaporanKerusakanDataTable;
use App\Http\Controllers\Controller;
use App\Models\PengajuanKerusakan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LaporanKerusakanController extends Controller
{
    public function index(LaporanKerusakanDataTable $dataTable)
    {
        return $dataTable->render('rtk.laporan-kerusakan.index');
    }

    public function show(PengajuanKerusakan $laporanKerusakan): View
    {
        $laporanKerusakan->load(['pelapor', 'fasilitas', 'penugasanTeknisi.teknisi', 'statusLogs.actor']);
        return view('rtk.laporan-kerusakan.show', compact('laporanKerusakan'));
    }

    public function destroy(PengajuanKerusakan $laporanKerusakan): RedirectResponse
    {
        DB::transaction(function () use ($laporanKerusakan) {
            // Hapus penugasan teknisi terkait (jika ada)
            if ($laporanKerusakan->penugasanTeknisi) {
                $laporanKerusakan->penugasanTeknisi->delete();
            }

            // Hapus laporan kerusakan (soft delete)
            $laporanKerusakan->delete();
        });

        return redirect()->route('rtk.laporan-kerusakan.index')
            ->with('success', 'Laporan kerusakan berhasil dihapus.');
    }
}