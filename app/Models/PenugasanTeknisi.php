<?php

namespace App\Models;

use App\Traits\HasDashboardCache;
use App\Traits\HasStatusLogs;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PenugasanTeknisi extends Model
{
    protected $table = 'penugasan_teknisi';
    use HasFactory, HasStatusLogs, SoftDeletes, HasDashboardCache;  

    protected $fillable = [
        'pengajuan_id',
        'teknisi_id',
        'jadwal_penanganan',
        'instruksi_kerja',
    ];

    protected $casts = [
        'pengajuan_id' => 'integer',
        'teknisi_id' => 'integer',
        'jadwal_penanganan' => 'datetime',
    ];

    public function pengajuanKerusakan()
    {
        return $this->belongsTo(PengajuanKerusakan::class, 'pengajuan_id');
    }

    public function teknisi()
    {
        return $this->belongsTo(User::class, 'teknisi_id');
    }

    public function buktiPerbaikan()
    {
        return $this->hasOne(BuktiPerbaikan::class, 'penugasan_id');
    }
}