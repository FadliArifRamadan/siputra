<?php

namespace App\Models;

use App\Traits\HasStatusLogs;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsStatusChanges;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\Traits\HasDashboardCache;  // ✅ TAMBAHKAN


class PengajuanKerusakan extends Model
{
    protected $table = 'pengajuan_kerusakan';
    use HasFactory, HasStatusLogs, LogsStatusChanges, SoftDeletes, HasDashboardCache;

    protected $fillable = [
        'pelapor_id',
        'fasilitas_id',
        'lokasi_id',  // ✅ TAMBAHKAN
        'jenis_kerusakan',
        'lokasi_detail',
        'deskripsi',
        'foto_kerusakan',
        'tingkat_urgensi',
        'status_laporan',
        'catatan_rtk',
        'target_selesai',      // ✅ TAMBAHKAN
        'escalated_at',        // ✅ TAMBAHKAN
        'sla_status',          // ✅ TAMBAHKAN

    ];

    protected $casts = [
        'pelapor_id' => 'integer',
        'fasilitas_id' => 'integer',
        'lokasi_id' => 'integer',
        'target_selesai' => 'datetime',   // ✅ TAMBAHKAN
        'escalated_at' => 'datetime',     // ✅ TAMBAHKAN
    ];

    public function pelapor()
    {
        return $this->belongsTo(User::class, 'pelapor_id');
    }

    // ✅ TAMBAHKAN RELASI INI
    public function fasilitas()
    {
        return $this->belongsTo(Fasilitas::class, 'fasilitas_id');
    }

    public function lokasi()  // ✅ TAMBAHKAN
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id');
    }

    public function penugasanTeknisi()
    {
        return $this->hasOne(PenugasanTeknisi::class, 'pengajuan_id');
    }
    /**
     * Cek apakah laporan masih dalam SLA.
     */
    public function isSlaBreached(): bool
    {
        return $this->sla_status === 'breached';
    }

    /**
     * Cek apakah laporan mendekati batas SLA.
     */
    public function isSlaWarning(): bool
    {
        return $this->sla_status === 'warning';
    }

    /**
     * Dapatkan persentase waktu yang tersisa untuk SLA.
     */
    public function getSlaProgress(): ?float
    {
        if (!$this->target_selesai) {
            return null;
        }

        $now = Carbon::now();
        $created = $this->created_at;
        $target = $this->target_selesai;

        $totalDuration = $created->diffInHours($target);
        $elapsed = $created->diffInHours($now);

        if ($totalDuration <= 0) {
            return null;
        }

        $progress = ($elapsed / $totalDuration) * 100;
        return min(100, round($progress, 1));
    }

    /**
     * Dapatkan status SLA dengan label dan warna.
     */
    public function getSlaDisplay(): array
    {
        $statusMap = [
            'on_track' => ['label' => 'On Track', 'color' => 'green'],
            'warning' => ['label' => '⚠️ Mendekati Deadline', 'color' => 'yellow'],
            'breached' => ['label' => '🔴 Melewati Deadline', 'color' => 'red'],
        ];

        return $statusMap[$this->sla_status] ?? ['label' => 'Unknown', 'color' => 'gray'];
    }
}
