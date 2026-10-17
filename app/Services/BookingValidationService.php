<?php

namespace App\Services;

use App\Models\Fasilitas;
use App\Models\PeminjamanFasilitas;
use App\Models\PeminjamanFasilitasItem;
use App\Models\PenugasanTeknisi;
use Carbon\Carbon;

class BookingValidationService
{
    /**
     * Memeriksa apakah terdapat jadwal yang bentrok (untuk prasarana/ruangan).
     * Mendukung exclude ID transaksi saat update.
     */
    public function isConflict(int $fasilitasId, string $waktuMulai, string $waktuSelesai, ?int $excludePeminjamanId = null): bool
    {
        return PeminjamanFasilitasItem::where('fasilitas_id', $fasilitasId)
            ->where('status_item', '!=', 'ditolak') // ✅ Abaikan item ditolak
            ->whereHas('peminjaman', function ($query) use ($waktuMulai, $waktuSelesai, $excludePeminjamanId) {
                $query->whereIn('status_pinjam', ['pending', 'menunggu_bikem', 'perlu_revisi', 'menunggu_rtk', 'disetujui', 'berjalan'])
                    ->where('waktu_mulai', '<', $waktuSelesai)
                    ->where('waktu_selesai', '>', $waktuMulai);

                if ($excludePeminjamanId) {
                    $query->where('id', '!=', $excludePeminjamanId);
                }
            })
            ->exists();
    }

    /**
     * Mengambil daftar jadwal peminjaman yang sudah terisi untuk fasilitas tertentu pada tanggal tertentu.
     * Digunakan agar pemohon dapat melihat jam berapa saja fasilitas/ruangan sedang dipakai.
     */
    public function getOccupiedSchedules(int $fasilitasId, string $dateOrDateTime, ?int $excludePeminjamanId = null): array
    {
        try {
            $date = Carbon::parse($dateOrDateTime);
            $startOfDay = $date->copy()->startOfDay();
            $endOfDay = $date->copy()->endOfDay();

            $items = PeminjamanFasilitasItem::with(['peminjaman.peminjam'])
                ->where('fasilitas_id', $fasilitasId)
                ->where('status_item', '!=', 'ditolak')
                ->whereHas('peminjaman', function ($q) use ($startOfDay, $endOfDay, $excludePeminjamanId) {
                    $q->whereIn('status_pinjam', ['pending', 'menunggu_bikem', 'perlu_revisi', 'menunggu_rtk', 'disetujui', 'berjalan'])
                        ->where('waktu_mulai', '<=', $endOfDay)
                        ->where('waktu_selesai', '>=', $startOfDay);

                    if ($excludePeminjamanId) {
                        $q->where('id', '!=', $excludePeminjamanId);
                    }
                })
                ->get();

            return $items->map(function ($item) {
                $p = $item->peminjaman;
                return [
                    'id' => $p->id,
                    'peminjam' => $p->peminjam->nama ?? 'Peminjam',
                    'keperluan' => $p->keperluan,
                    'waktu_mulai' => $p->waktu_mulai ? $p->waktu_mulai->format('H:i') : '-',
                    'waktu_selesai' => $p->waktu_selesai ? $p->waktu_selesai->format('H:i') : '-',
                    'tanggal_mulai' => $p->waktu_mulai ? $p->waktu_mulai->format('d/m/Y') : '-',
                    'tanggal_selesai' => $p->waktu_selesai ? $p->waktu_selesai->format('d/m/Y') : '-',
                    'jam_range' => ($p->waktu_mulai ? $p->waktu_mulai->format('H:i') : '-') . ' - ' . ($p->waktu_selesai ? $p->waktu_selesai->format('H:i') : '-'),
                    'jumlah' => $item->jumlah_dipinjam,
                    'status' => $p->status_pinjam,
                ];
            })->values()->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Menghitung total jumlah dipinjam pada rentang waktu tertentu.
     */
    public function getTotalPeminjamanPadaWaktu(?int $fasilitasId, string $waktuMulai, string $waktuSelesai, ?int $excludePeminjamanId = null): int
    {
        if (is_null($fasilitasId)) {
            return 0;
        }

        return PeminjamanFasilitasItem::where('fasilitas_id', $fasilitasId)
            ->where('status_item', '!=', 'ditolak') // ✅ Abaikan item ditolak
            ->whereHas('peminjaman', function ($q) use ($waktuMulai, $waktuSelesai, $excludePeminjamanId) {
                $q->whereIn('status_pinjam', ['pending', 'menunggu_bikem', 'perlu_revisi', 'menunggu_rtk', 'disetujui', 'berjalan'])
                    ->where('waktu_mulai', '<', $waktuSelesai)
                    ->where('waktu_selesai', '>', $waktuMulai);

                if ($excludePeminjamanId) {
                    $q->where('id', '!=', $excludePeminjamanId);
                }
            })
            ->sum('jumlah_dipinjam');
    }

    public function checkStokAvailability(?int $fasilitasId, int $jumlahDiminta, string $waktuMulai, string $waktuSelesai, ?int $excludePeminjamanId = null): array
    {
        if (is_null($fasilitasId)) {
            return [
                'available' => false,
                'total_used' => 0,
                'stok_total' => 0,
                'message' => 'Fasilitas tidak valid.'
            ];
        }

        if ($jumlahDiminta <= 0) {
            return [
                'available' => false,
                'total_used' => 0,
                'stok_total' => Fasilitas::where('id', $fasilitasId)->value('stok_total') ?? 0,
                'message' => 'Jumlah barang yang dipinjam harus lebih dari 0.'
            ];
        }

        $fasilitas = Fasilitas::find($fasilitasId);
        if (!$fasilitas) {
            return [
                'available' => false,
                'total_used' => 0,
                'stok_total' => 0,
                'message' => 'Fasilitas tidak ditemukan.'
            ];
        }

        // Jika prasarana (ruangan)
        if ($fasilitas->kategori === 'prasarana') {
            $isConflict = $this->isConflict($fasilitasId, $waktuMulai, $waktuSelesai, $excludePeminjamanId);
            return [
                'available' => !$isConflict,
                'total_used' => $isConflict ? 1 : 0,
                'stok_total' => 1,
                'message' => $isConflict ? 'Ruangan (' . $fasilitas->nama_fasilitas . ') sudah dipesan pada waktu tersebut.' : 'Ruangan tersedia.'
            ];
        }

        // Kategori Sarana (perhitungan stok unit)
        $stokTersedia = $fasilitas->getAvailableStock($waktuMulai, $waktuSelesai, $excludePeminjamanId);
        $totalUsed = $fasilitas->stok_total - $stokTersedia;

        $available = ($stokTersedia >= $jumlahDiminta);

        $message = $available
            ? 'Stok tersedia. (' . $stokTersedia . ' unit tersisa dari ' . $fasilitas->stok_total . ')'
            : ($stokTersedia <= 0
                ? 'Stok ' . $fasilitas->nama_fasilitas . ' sedang habis pada rentang waktu ini.'
                : 'Stok ' . $fasilitas->nama_fasilitas . ' tidak mencukupi. Tersedia ' . $stokTersedia . ' unit, diminta ' . $jumlahDiminta . ' unit.');

        return [
            'available' => $available,
            'total_used' => $totalUsed,
            'stok_total' => $fasilitas->stok_total,
            'message' => $message
        ];
    }

    public function validateBooking(int $fasilitasId, int $jumlahDiminta, string $waktuMulai, string $waktuSelesai, ?int $excludePeminjamanId = null): array
    {
        $errors = [];

        $facilityCheck = $this->isFacilityAvailableForBooking($fasilitasId);
        if (!$facilityCheck['available']) {
            $errors[] = $facilityCheck['message'];
        }

        $stokCheck = $this->checkStokAvailability($fasilitasId, $jumlahDiminta, $waktuMulai, $waktuSelesai, $excludePeminjamanId);
        if (!$stokCheck['available']) {
            $errors[] = $stokCheck['message'];
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'details' => [
                'stok' => $stokCheck
            ]
        ];
    }

    public function isFacilityAvailableForBooking(int $fasilitasId): array
    {
        $fasilitas = Fasilitas::find($fasilitasId);

        if (!$fasilitas) {
            return [
                'available' => false,
                'message' => 'Fasilitas tidak ditemukan.',
                'status' => null
            ];
        }

        $blockedStatuses = ['rusak_berat', 'maintenance'];

        if (in_array($fasilitas->status_kondisi, $blockedStatuses)) {
            $statusLabel = [
                'rusak_berat' => 'Rusak Berat',
                'maintenance' => 'Dalam Perawatan'
            ];

            return [
                'available' => false,
                'message' => 'Fasilitas (' . $fasilitas->nama_fasilitas . ') sedang dalam kondisi ' . ($statusLabel[$fasilitas->status_kondisi] ?? $fasilitas->status_kondisi) . ' dan tidak dapat dipinjam.',
                'status' => $fasilitas->status_kondisi
            ];
        }

        return [
            'available' => true,
            'message' => 'Fasilitas tersedia untuk dipinjam.',
            'status' => $fasilitas->status_kondisi
        ];
    }

    public function getStatusByUrgensi(string $tingkatUrgensi): string
    {
        return match ($tingkatUrgensi) {
            'tinggi' => 'rusak_berat',
            'sedang' => 'rusak_ringan',
            'rendah' => 'rusak_ringan',
            default => 'rusak_ringan',
        };
    }

    public function updateFasilitasStatusFromLaporan(int $fasilitasId, string $tingkatUrgensi, ?string $catatan = null): array
    {
        $fasilitas = Fasilitas::find($fasilitasId);

        if (!$fasilitas) {
            return [
                'success' => false,
                'message' => 'Fasilitas tidak ditemukan.'
            ];
        }

        $newStatus = $this->getStatusByUrgensi($tingkatUrgensi);
        $oldStatus = $fasilitas->status_kondisi;

        if ($oldStatus !== $newStatus) {
            $fasilitas->update(['status_kondisi' => $newStatus]);

            $fasilitas->logStatusChange(
                $newStatus,
                $oldStatus,
                $catatan ?? 'Status diperbarui otomatis dari laporan kerusakan dengan urgensi ' . $tingkatUrgensi
            );

            return [
                'success' => true,
                'message' => 'Status fasilitas diperbarui dari ' . $oldStatus . ' menjadi ' . $newStatus,
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ];
        }

        return [
            'success' => true,
            'message' => 'Status fasilitas sudah sesuai (' . $oldStatus . '), tidak ada perubahan.',
            'old_status' => $oldStatus,
            'new_status' => $newStatus
        ];
    }

    public function isTeknisiConflict(int $teknisiId, string $waktuMulai, string $waktuSelesai, ?int $excludePenugasanId = null): bool
    {
        $query = PenugasanTeknisi::where('teknisi_id', $teknisiId)
            ->whereHas('pengajuanKerusakan', function ($q) {
                $q->whereIn('status_laporan', ['diproses', 'pending']);
            })
            ->where(function ($query) use ($waktuMulai, $waktuSelesai) {
                $query->where('jadwal_penanganan', '<', $waktuSelesai)
                    ->where('jadwal_penanganan', '>', $waktuMulai);
            });

        if ($excludePenugasanId) {
            $query->where('id', '!=', $excludePenugasanId);
        }

        return $query->exists();
    }

    public function getTeknisiSchedule(int $teknisiId, string $startDate, string $endDate): array
    {
        $penugasan = PenugasanTeknisi::with(['pengajuanKerusakan'])
            ->where('teknisi_id', $teknisiId)
            ->whereHas('pengajuanKerusakan', function ($q) {
                $q->whereIn('status_laporan', ['diproses', 'pending']);
            })
            ->whereBetween('jadwal_penanganan', [$startDate, $endDate])
            ->orderBy('jadwal_penanganan', 'asc')
            ->get();

        return $penugasan->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->pengajuanKerusakan->jenis_kerusakan,
                'start' => $item->jadwal_penanganan->toIso8601String(),
                'end' => $item->jadwal_penanganan->copy()->addHour()->toIso8601String(),
                'lokasi' => $item->pengajuanKerusakan->lokasi_detail,
                'status' => $item->pengajuanKerusakan->status_laporan,
            ];
        })->toArray();
    }

    public function validateTeknisiAssignment(int $teknisiId, string $waktuMulai, string $waktuSelesai, ?int $excludePenugasanId = null): array
    {
        $isConflict = $this->isTeknisiConflict($teknisiId, $waktuMulai, $waktuSelesai, $excludePenugasanId);

        return [
            'valid' => !$isConflict,
            'message' => $isConflict
                ? 'Teknisi sudah memiliki penugasan lain pada waktu tersebut.'
                : 'Teknisi tersedia pada waktu tersebut.',
        ];
    }
}