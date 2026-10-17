<?php

namespace App\Services;

use App\Models\PeminjamanFasilitas;
use App\Models\PenugasanTeknisi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ReminderService
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Kirim pengingat untuk peminjaman yang akan datang dalam 24 jam.
     */
    public function sendBookingReminders(): void
    {
        $now = Carbon::now();
        $startWindow = $now->copy()->addHours(24);
        $endWindow = $now->copy()->addHours(26);

        $peminjaman = PeminjamanFasilitas::with(['peminjam', 'fasilitas'])
            ->where('status_pinjam', 'disetujui')
            ->whereBetween('waktu_mulai', [$startWindow, $endWindow])
            ->get();

        foreach ($peminjaman as $item) {
            $this->notificationService->create(
                $item->peminjam,
                '📅 Pengingat Peminjaman',
                'Peminjaman "' . $item->fasilitas->nama_fasilitas . '" akan dimulai besok pukul ' . $item->waktu_mulai->format('H:i') . '.',
                'reminder_booking',
                route('pelapor.peminjaman.show', $item),
                $item
            );

            Log::info('Reminder booking sent to user ' . $item->peminjam->id . ' for booking #' . $item->id);
        }
    }

    /**
     * Kirim pengingat untuk penugasan teknisi yang akan datang dalam 24 jam.
     */
    public function sendTaskReminders(): void
    {
        $now = Carbon::now();
        $startWindow = $now->copy()->addHours(24);
        $endWindow = $now->copy()->addHours(26);

        $penugasan = PenugasanTeknisi::with(['teknisi', 'pengajuanKerusakan'])
            ->whereHas('pengajuanKerusakan', function ($query) {
                $query->where('status_laporan', 'diproses');
            })
            ->whereBetween('jadwal_penanganan', [$startWindow, $endWindow])
            ->get();

        foreach ($penugasan as $item) {
            $this->notificationService->create(
                $item->teknisi,
                '🔧 Pengingat Tugas',
                'Tugas "' . $item->pengajuanKerusakan->jenis_kerusakan . '" harus ditangani besok pukul ' . $item->jadwal_penanganan->format('H:i') . ' di ' . $item->pengajuanKerusakan->lokasi_detail . '.',
                'reminder_task',
                route('teknisi.tugas.show', $item),
                $item
            );

            Log::info('Reminder task sent to teknisi ' . $item->teknisi->id . ' for task #' . $item->id);
        }
    }

    /**
     * Kirim pengingat untuk semua event yang akan datang.
     */
    public function sendAllReminders(): void
    {
        $this->sendBookingReminders();
        $this->sendTaskReminders();
    }
}