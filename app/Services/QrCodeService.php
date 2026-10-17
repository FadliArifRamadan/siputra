<?php

namespace App\Services;

use App\Models\Fasilitas;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    /**
     * Generate QR Code untuk fasilitas.
     */
    public function generate(Fasilitas $fasilitas): string
    {
        // ✅ Gunakan APP_URL dari config, bukan hardcode localhost
        $appUrl = config('app.url');

        // Buat URL yang mengarah ke halaman lapor kerusakan dengan fasilitas_id terisi otomatis
        $url = $appUrl . '/pelapor/pengajuan/create?fasilitas_id=' . $fasilitas->id;

        // Generate QR Code sebagai SVG
        return QrCode::size(300)
            ->format('svg')
            ->generate($url);
    }

    /**
     * Generate QR Code dan simpan ke storage.
     */
    public function generateAndSave(Fasilitas $fasilitas): string
    {
        $filename = 'qrcodes/fasilitas_' . $fasilitas->id . '.svg';
        $content = $this->generate($fasilitas);

        Storage::disk('public')->put($filename, $content);

        return $filename;
    }

    /**
     * Generate QR Code dalam format base64 (untuk inline display).
     */
    public function generateBase64(Fasilitas $fasilitas): string
    {
        $appUrl = config('app.url');
        $url = $appUrl . '/pelapor/pengajuan/create?fasilitas_id=' . $fasilitas->id;

        $qrCode = QrCode::size(200)
            ->format('png')
            ->generate($url);

        return 'data:image/png;base64,' . base64_encode($qrCode);
    }


    /**
     * Generate QR Code dengan label nama fasilitas.
     */
    public function generateWithLabel(Fasilitas $fasilitas): string
    {
        $appUrl = config('app.url');
        $url = $appUrl . '/pelapor/pengajuan/create?fasilitas_id=' . $fasilitas->id;
        $label = $fasilitas->nama_fasilitas;

        $qrSvg = QrCode::size(250)
            ->format('svg')
            ->generate($url);

        $labelHtml = '<div style="text-align:center;font-family:Arial;font-size:14px;margin-top:8px;color:#333;">' . e($label) . '</div>';

        return $qrSvg . $labelHtml;
    }

    /**
     * Generate QR Code untuk multiple fasilitas (batch).
     */
    public function generateBatch(array $fasilitasIds): array
    {
        $results = [];

        foreach ($fasilitasIds as $id) {
            $fasilitas = Fasilitas::find($id);
            if ($fasilitas) {
                $results[$id] = $this->generateAndSave($fasilitas);
            }
        }

        return $results;
    }

    /**
     * Hapus QR Code dari storage.
     */
    public function delete(Fasilitas $fasilitas): bool
    {
        $filename = 'qrcodes/fasilitas_' . $fasilitas->id . '.svg';

        if (Storage::disk('public')->exists($filename)) {
            return Storage::disk('public')->delete($filename);
        }

        return false;
    }

    /**
     * Regenerate QR Code untuk fasilitas.
     */
    public function regenerate(Fasilitas $fasilitas): string
    {
        $this->delete($fasilitas);
        return $this->generateAndSave($fasilitas);
    }
}
