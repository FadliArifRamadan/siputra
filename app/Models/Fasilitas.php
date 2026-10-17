<?php

namespace App\Models;

use App\Traits\HasDashboardCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasStatusLogs;
use App\Traits\LogsStatusChanges;
use App\Services\QrCodeService;

class Fasilitas extends Model
{
    protected $table = 'fasilitas';
    use HasFactory, SoftDeletes, HasStatusLogs, LogsStatusChanges, HasDashboardCache;

    protected $fillable = [
        'nama_fasilitas',
        'tipe_fasilitas',
        'kategori',
        'lokasi_id',
        'lokasi_detail',
        'stok_total',
        'status_kondisi',
        'foto',
        'fasilitas_ruangan',
        'deskripsi',
        'is_landing_page',
    ];

    protected $casts = [
        'stok_total' => 'integer',
        'lokasi_id' => 'integer',
        'is_landing_page' => 'boolean',
    ];

    // Scopes
    public function scopePinjam($query)
    {
        return $query->where('tipe_fasilitas', 'pinjam');
    }

    public function scopeKampus($query)
    {
        return $query->where('tipe_fasilitas', 'kampus');
    }

    public function scopeLandingPage($query)
    {
        return $query->where('tipe_fasilitas', 'kampus')->where('is_landing_page', true);
    }

    // Accessors
    public function getFotoUrlAttribute(): string
    {
        if ($this->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->foto)) {
            return \Illuminate\Support\Facades\Storage::url($this->foto);
        }
        return asset('images/hero-1.jpg');
    }

    public function getFasilitasListAttribute(): array
    {
        if (empty($this->fasilitas_ruangan)) {
            return [];
        }
        return array_values(array_filter(array_map('trim', explode(',', $this->fasilitas_ruangan))));
    }

    // ✅ FIX: Relasi ke Item Peminjaman (bukan langsung ke Header)
    public function peminjamanItems()
    {
        return $this->hasMany(PeminjamanFasilitasItem::class, 'fasilitas_id');
    }

    // ✅ Relasi Many-to-Many ke Header Peminjaman via Pivot Table
    public function peminjamanFasilitas()
    {
        return $this->belongsToMany(PeminjamanFasilitas::class, 'peminjaman_fasilitas_item', 'fasilitas_id', 'peminjaman_id')
            ->withPivot(['jumlah_dipinjam', 'jumlah_dikembalikan', 'status_item', 'catatan_item'])
            ->withTimestamps();
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id');
    }

    public function pengajuanKerusakan()
    {
        return $this->hasMany(PengajuanKerusakan::class, 'fasilitas_id');
    }

    public function getLokasiDisplayAttribute(): string
    {
        if ($this->lokasi_id && $this->lokasi) {
            return $this->lokasi->full_name;
        }
        return $this->lokasi_detail ?? 'Tidak diketahui';
    }

    public function getQrCodeAttribute(): ?string
    {
        $service = app(QrCodeService::class);
        return $service->generateBase64($this);
    }

    public function getQrCodeSvgAttribute(): ?string
    {
        $service = app(QrCodeService::class);
        return $service->generateWithLabel($this);
    }

    public function getQrCodePathAttribute(): ?string
    {
        $path = 'qrcodes/fasilitas_' . $this->id . '.svg';
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            return \Illuminate\Support\Facades\Storage::url($path);
        }
        return null;
    }

    public function generateQrCode(): string
    {
        $service = app(QrCodeService::class);
        return $service->generateAndSave($this);
    }

    public function regenerateQrCode(): string
    {
        $service = app(QrCodeService::class);
        return $service->regenerate($this);
    }

    public function deleteQrCode(): bool
    {
        $service = app(QrCodeService::class);
        return $service->delete($this);
    }

    /**
     * ✅ Hitung stok yang tersedia (Real-time & Akurat).
     * Jika tanpa parameter waktu, menghitung seluruh peminjaman aktif/disetujui yang belum selesai.
     */
    public function getAvailableStock(?string $waktuMulai = null, ?string $waktuSelesai = null, ?int $excludePeminjamanId = null): int
    {
        $query = PeminjamanFasilitasItem::where('fasilitas_id', $this->id)
            ->where('status_item', '!=', 'ditolak') // Abaikan item ditolak
            ->whereHas('peminjaman', function ($q) use ($waktuMulai, $waktuSelesai, $excludePeminjamanId) {
                // Perhitungkan transaksi yang sedang dalam proses review atau disetujui
                $q->whereIn('status_pinjam', ['pending', 'menunggu_bikem', 'perlu_revisi', 'menunggu_rtk', 'disetujui', 'berjalan']);

                if ($waktuMulai && $waktuSelesai) {
                    // Cek konflik pada rentang waktu peminjaman spesifik
                    $q->where('waktu_mulai', '<', $waktuSelesai)
                        ->where('waktu_selesai', '>', $waktuMulai);
                } else {
                    // Default saat tanpa rentang waktu spesifik (stok riil saat ini juga):
                    // Hanya potong stok untuk transaksi yang sedang berlangsung pada saat ini
                    $q->where(function ($sub) {
                        $sub->where('status_pinjam', 'berjalan')
                            ->orWhere(function ($s) {
                                $s->where('waktu_mulai', '<=', now())
                                  ->where('waktu_selesai', '>=', now());
                            });
                    });
                }

                if ($excludePeminjamanId) {
                    $q->where('id', '!=', $excludePeminjamanId);
                }
            });

        $used = $query->sum('jumlah_dipinjam');

        return max(0, $this->stok_total - $used);
    }

    public function getStokTersediaAttribute(): int
    {
        return $this->getAvailableStock();
    }

    public function hasSufficientStock(int $jumlah, string $waktuMulai, string $waktuSelesai, ?int $excludePeminjamanId = null): bool
    {
        return $this->getAvailableStock($waktuMulai, $waktuSelesai, $excludePeminjamanId) >= $jumlah;
    }
}
