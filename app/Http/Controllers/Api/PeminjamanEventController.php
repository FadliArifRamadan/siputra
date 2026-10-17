<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PeminjamanFasilitas;
use Illuminate\Http\JsonResponse;

class PeminjamanEventController extends Controller
{
    public function index(): JsonResponse
    {
        $peminjaman = PeminjamanFasilitas::with(['items.fasilitas', 'fasilitas', 'peminjam'])
            ->whereIn('status_pinjam', ['disetujui', 'berjalan', 'selesai'])
            ->get();

        $events = $peminjaman->map(function ($item) {
            // Dapatkan daftar nama fasilitas
            $fasilitasNames = $item->items->map(function ($itemFasilitas) {
                $nama = $itemFasilitas->fasilitas->nama_fasilitas ?? null;
                if ($nama && $itemFasilitas->jumlah_dipinjam > 1) {
                    $nama .= ' (' . $itemFasilitas->jumlah_dipinjam . ' unit)';
                }
                return $nama;
            })->filter()->values();

            $fasilitasText = $fasilitasNames->isNotEmpty() 
                ? $fasilitasNames->implode(', ') 
                : ($item->fasilitas->nama_fasilitas ?? 'Fasilitas');

            $firstFasilitas = $fasilitasNames->first() ?? ($item->fasilitas->nama_fasilitas ?? 'Fasilitas');
            $shortFasilitas = $firstFasilitas;
            if ($fasilitasNames->count() > 1) {
                $shortFasilitas .= ' (+' . ($fasilitasNames->count() - 1) . ' lainnya)';
            }

            $peminjamName = $item->peminjam->nama ?? 'Unknown';
            $title = $shortFasilitas . ' - ' . $peminjamName;

            $statusKey = strtolower($item->status_pinjam);
            $eventColor = match ($statusKey) {
                'disetujui' => '#0f8a82', // Hijau Toska (Teal)
                'berjalan'  => '#f2723b', // Oranye (Sunset)
                'selesai'   => '#64748b', // Abu-abu (Slate)
                default     => '#0f8a82',
            };

            return [
                'id' => $item->id,
                'title' => $title,
                // Gunakan format string ISO tanpa timezone offset/Z (misal: 2026-09-11T10:30:00)
                // agar FullCalendar dan browser memprosesnya tepat sesuai jam lokal WIB yang tersimpan
                'start' => $item->waktu_mulai->format('Y-m-d\TH:i:s'),
                'end' => $item->waktu_selesai->format('Y-m-d\TH:i:s'),
                'color' => $eventColor,
                'backgroundColor' => $eventColor,
                'borderColor' => $eventColor,
                'textColor' => '#ffffff',
                'classNames' => ['fc-status-' . $statusKey],
                'fasilitas' => $fasilitasText,
                'peminjam' => $peminjamName,
                'keperluan' => $item->keperluan,
                'status' => ucfirst($item->status_pinjam),
                'waktu_mulai_formatted' => $item->waktu_mulai->format('d/m/Y H:i') . ' WIB',
                'waktu_selesai_formatted' => $item->waktu_selesai->format('d/m/Y H:i') . ' WIB',
            ];
        });

        return response()->json($events);
    }
}