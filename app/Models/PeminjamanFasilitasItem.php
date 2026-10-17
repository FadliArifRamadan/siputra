<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PeminjamanFasilitasItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'peminjaman_fasilitas_item';

    protected $fillable = [
        'peminjaman_id',
        'fasilitas_id',
        'jumlah_dipinjam',
        'jumlah_dikembalikan',
        'status_item',
        'catatan_item',
    ];

    protected function casts(): array
    {
        return [
            'peminjaman_id' => 'integer',
            'fasilitas_id' => 'integer',
            'jumlah_dipinjam' => 'integer',
            'jumlah_dikembalikan' => 'integer',
        ];
    }

    public function peminjaman()
    {
        return $this->belongsTo(PeminjamanFasilitas::class, 'peminjaman_id');
    }

    public function fasilitas()
    {
        return $this->belongsTo(Fasilitas::class, 'fasilitas_id');
    }

    // ✅ Cek stok tersedia untuk item ini pada rentang waktu tertentu
    public function getAvailableStock(string $waktuMulai, string $waktuSelesai): int
    {
        return $this->fasilitas->getAvailableStock($waktuMulai, $waktuSelesai);
    }

    // ✅ Cek apakah stok mencukupi
    public function hasSufficientStock(string $waktuMulai, string $waktuSelesai): bool
    {
        return $this->getAvailableStock($waktuMulai, $waktuSelesai) >= $this->jumlah_dipinjam;
    }
}