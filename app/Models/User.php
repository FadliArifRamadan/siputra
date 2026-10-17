<?php

namespace App\Models;

use App\Traits\HasDashboardCache;
use App\Traits\HasStatusLogs;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles, HasStatusLogs, HasDashboardCache;

    protected $fillable = [
        'nomor_induk',
        'nama',
        'email',
        'password',
        'status_akun',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function pengajuanKerusakan()
    {
        return $this->hasMany(PengajuanKerusakan::class, 'pelapor_id');
    }

    public function penugasanTeknisi()
    {
        return $this->hasMany(PenugasanTeknisi::class, 'teknisi_id');
    }

    public function peminjamanFasilitas()
    {
        return $this->hasMany(PeminjamanFasilitas::class, 'peminjam_id');
    }
}
