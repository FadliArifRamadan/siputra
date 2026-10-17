<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BuktiPerbaikan extends Model
{
    protected $table = 'bukti_perbaikan';
    use HasFactory, SoftDeletes;

    public $timestamps = false;

    protected $fillable = [
        'penugasan_id',
        'foto_selesai',
        'keterangan_selesai',
        'tanggal_selesai',
    ];

    protected $casts = [
        'penugasan_id' => 'integer',
        'tanggal_selesai' => 'datetime',
    ];

    public function penugasanTeknisi()
    {
        return $this->belongsTo(PenugasanTeknisi::class, 'penugasan_id');
    }
}
