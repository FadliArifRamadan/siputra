<?php

namespace App\Models;

use App\Traits\HasDashboardCache;
use App\Traits\HasStatusLogs;
use App\Traits\LogsStatusChanges;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PeminjamanFasilitas extends Model
{
    protected $table = 'peminjaman_fasilitas';
    use HasFactory, HasStatusLogs, LogsStatusChanges, SoftDeletes, HasDashboardCache;

    protected $fillable = [
        'peminjam_id',
        'waktu_mulai',
        'waktu_selesai',
        'keperluan',
        'catatan_peminjam', // ✅ Ditambahkan sesuai migration
        'file_pendukung',
        'status_pinjam',
        'verifikasi_bikem_by',
        'verifikasi_bikem_at',
        'catatan_bikem',
        'verifikasi_serah_terima_by',
        'verifikasi_serah_terima_at',
        'kondisi_serah_terima',
        'catatan_serah_terima',
        'verifikasi_pengembalian_by',
        'verifikasi_pengembalian_at',
        'kondisi_pengembalian',
        'catatan_pengembalian',
    ];

    protected $casts = [
        'peminjam_id' => 'integer',
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'verifikasi_bikem_at' => 'datetime',
        'verifikasi_bikem_by' => 'integer',
        'verifikasi_serah_terima_at' => 'datetime',
        'verifikasi_pengembalian_at' => 'datetime',
        'verifikasi_serah_terima_by' => 'integer',
        'verifikasi_pengembalian_by' => 'integer',
    ];

    public function peminjam()
    {
        return $this->belongsTo(User::class, 'peminjam_id');
    }

    public function verifikasiBikemBy()
    {
        return $this->belongsTo(User::class, 'verifikasi_bikem_by');
    }

    // ✅ Relasi ke item (Header-Detail)
    public function items()
    {
        return $this->hasMany(PeminjamanFasilitasItem::class, 'peminjaman_id');
    }

    // ✅ Helper untuk mendapatkan total item unik
    public function getTotalItemsAttribute(): int
    {
        // Jika relasi sudah diload, gunakan collection count agar tidak query berulang ke DB
        return $this->relationLoaded('items') ? $this->items->count() : $this->items()->count();
    }

    // ✅ Helper untuk mendapatkan total unit yang dipinjam
    public function getTotalJumlahDipinjamAttribute(): int
    {
        return $this->relationLoaded('items') ? $this->items->sum('jumlah_dipinjam') : $this->items()->sum('jumlah_dipinjam');
    }

    // ✅ Helper untuk cek apakah semua item sudah disetujui
    public function isAllItemsApproved(): bool
    {
        $items = $this->relationLoaded('items') ? $this->items : $this->items();
        return $items->where('status_item', '!=', 'disetujui')->count() === 0;
    }
    public function fasilitas()
    {
        return $this->hasOneThrough(
            Fasilitas::class,
            PeminjamanFasilitasItem::class,
            'peminjaman_id',
            'id',
            'id',
            'fasilitas_id'
        );
    }

    public function getNamaFasilitasAttribute(): string
    {
        $items = $this->relationLoaded('items') ? $this->items : $this->items()->with('fasilitas')->get();
        if ($items->isEmpty()) {
            return 'Fasilitas #' . $this->id;
        }

        $names = $items->map(function ($item) {
            return $item->fasilitas ? $item->fasilitas->nama_fasilitas : null;
        })->filter()->values();

        if ($names->isEmpty()) {
            return 'Fasilitas #' . $this->id;
        }

        return $names->implode(', ');
    }

    public function getDaftarFasilitasTextAttribute(): string
    {
        $items = $this->relationLoaded('items') ? $this->items : $this->items()->with('fasilitas')->get();
        if ($items->isEmpty()) {
            return 'Fasilitas #' . $this->id;
        }

        $names = $items->map(function ($item) {
            if (!$item->fasilitas) return null;
            return $item->jumlah_dipinjam > 1
                ? "{$item->fasilitas->nama_fasilitas} ({$item->jumlah_dipinjam} unit)"
                : $item->fasilitas->nama_fasilitas;
        })->filter()->values();

        return $names->isNotEmpty() ? $names->implode(', ') : 'Fasilitas #' . $this->id;
    }

    // ✅ Helper untuk cek apakah ada item yang ditolak
    public function hasRejectedItems(): bool
    {
        if ($this->relationLoaded('items')) {
            return $this->items->contains('status_item', 'ditolak');
        }
        return $this->items()->where('status_item', 'ditolak')->exists();
    }

    public function verifikasiSerahTerimaBy()
    {
        return $this->belongsTo(User::class, 'verifikasi_serah_terima_by');
    }

    public function verifikasiPengembalianBy()
    {
        return $this->belongsTo(User::class, 'verifikasi_pengembalian_by');
    }

    public function isSudahDiverifikasiSerahTerima(): bool
    {
        return !is_null($this->verifikasi_serah_terima_at);
    }

    public function isSudahDiverifikasiPengembalian(): bool
    {
        return !is_null($this->verifikasi_pengembalian_at);
    }

    public function isMenungguBikem(): bool
    {
        return in_array($this->status_pinjam, ['pending', 'menunggu_bikem']);
    }

    public function isPerluRevisi(): bool
    {
        return $this->status_pinjam === 'perlu_revisi';
    }

    public function isMenungguRtk(): bool
    {
        return $this->status_pinjam === 'menunggu_rtk';
    }

    public function isApprovedByBikem(): bool
    {
        return !is_null($this->verifikasi_bikem_at);
    }
}