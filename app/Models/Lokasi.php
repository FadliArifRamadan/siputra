<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lokasi extends Model
{
    protected $table = 'lokasi';
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kampus',
        'lantai',
        'kode_ruangan',
        'nama_ruangan',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function fasilitas()
    {
        return $this->hasMany(Fasilitas::class, 'lokasi_id');
    }

    public function pengajuanKerusakan()
    {
        return $this->hasMany(PengajuanKerusakan::class, 'lokasi_id');
    }

    public function getFullNameAttribute(): string
    {
        $parts = [];
        if ($this->kampus) $parts[] = $this->kampus;
        if ($this->lantai) $parts[] = $this->lantai;
        if ($this->kode_ruangan) $parts[] = $this->kode_ruangan;
        if ($this->nama_ruangan) $parts[] = $this->nama_ruangan;
        return implode(' - ', $parts);
    }
}