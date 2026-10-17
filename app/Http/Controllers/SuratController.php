<?php

namespace App\Http\Controllers;

use App\Models\PeminjamanFasilitas;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SuratController extends Controller
{
    /**
     * Generate nomor surat otomatis
     * Format: {no_urut}/PH/BIKEM/{bulan_romawi}/{tahun}
     */
    private function generateNomorSurat(): string
    {
        $now = Carbon::now();
        $bulan = $now->month;
        $tahun = $now->year;
        $bulanRomawi = $this->toRoman($bulan);

        // Hitung jumlah surat yang sudah dibuat di bulan ini
        $count = PeminjamanFasilitas::where('status_pinjam', 'disetujui')
            ->whereMonth('updated_at', $bulan)
            ->whereYear('updated_at', $tahun)
            ->count() + 1;

        return sprintf('%03d/PH/BIKEM/%s/%d', $count, $bulanRomawi, $tahun);
    }

    /**
     * Konversi angka ke romawi
     */
    private function toRoman(int $number): string
    {
        $map = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ];
        return $map[$number] ?? 'I';
    }

    /**
     * Preview surat izin peminjaman
     */
    public function preview(PeminjamanFasilitas $peminjaman)
    {
        // Validasi akses: hanya peminjam yang bisa melihat suratnya
        if ($peminjaman->peminjam_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke surat ini.');
        }

        // Validasi status: hanya yang sudah disetujui
        if (!in_array($peminjaman->status_pinjam, ['disetujui', 'berjalan', 'selesai'])) {
            return redirect()->back()
                ->with('error', 'Surat hanya dapat dicetak untuk peminjaman yang sudah disetujui.');
        }

        $peminjaman->load(['peminjam', 'items.fasilitas', 'verifikasiBikemBy']);

        $data = [
            'peminjaman' => $peminjaman,
            'nomor_surat' => $this->generateNomorSurat(),
            'tanggal_surat' => Carbon::now()->locale('id')->isoFormat('D MMMM YYYY'),
            'tanggal_peminjaman' => Carbon::parse($peminjaman->created_at)->locale('id')->isoFormat('D MMMM YYYY'),
            'hari_tanggal' => Carbon::parse($peminjaman->waktu_mulai)->locale('id')->isoFormat('dddd, D MMMM YYYY'),
            'waktu' => Carbon::parse($peminjaman->waktu_mulai)->format('H:i') . ' - ' . Carbon::parse($peminjaman->waktu_selesai)->format('H:i') . ' WIB',
            'penanggung_jawab' => $peminjaman->peminjam->nama,
            'keperluan' => $peminjaman->keperluan,
            'items' => $peminjaman->items,
            'logo_base64' => $this->getLogoBase64(),
            'ttd_qr' => $this->getQrPlaceholder(),
        ];

        return view('surat.peminjaman', $data);
    }

    /**
     * Download surat izin peminjaman (PDF)
     */
    /**
     * Download surat izin peminjaman (PDF)
     */
    public function download(PeminjamanFasilitas $peminjaman)
    {
        if ($peminjaman->peminjam_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke surat ini.');
        }

        if (!in_array($peminjaman->status_pinjam, ['disetujui', 'berjalan', 'selesai'])) {
            return redirect()->back()
                ->with('error', 'Surat hanya dapat dicetak untuk peminjaman yang sudah disetujui.');
        }

        $peminjaman->load(['peminjam', 'items.fasilitas', 'verifikasiBikemBy']);

        $data = [
            'peminjaman' => $peminjaman,
            'nomor_surat' => $this->generateNomorSurat(),
            'tanggal_surat' => Carbon::now()->locale('id')->isoFormat('D MMMM YYYY'),
            'tanggal_peminjaman' => Carbon::parse($peminjaman->created_at)->locale('id')->isoFormat('D MMMM YYYY'),
            'hari_tanggal' => Carbon::parse($peminjaman->waktu_mulai)->locale('id')->isoFormat('dddd, D MMMM YYYY'),
            'waktu' => Carbon::parse($peminjaman->waktu_mulai)->format('H:i') . ' - ' . Carbon::parse($peminjaman->waktu_selesai)->format('H:i') . ' WIB',
            'penanggung_jawab' => $peminjaman->peminjam->nama,
            'keperluan' => $peminjaman->keperluan,
            'items' => $peminjaman->items,
            'logo_base64' => $this->getLogoBase64(),
            'ttd_qr' => $this->getQrPlaceholder(),
            'isPdf' => true, // ✅ Flag agar tombol action hilang saat jadi PDF
        ];

        $pdf = Pdf::loadView('surat.peminjaman', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'chroot' => public_path(),
        ]);

        return $pdf->download('Surat_Izin_Peminjaman_' . $peminjaman->id . '.pdf');
    }

    /**
     * Get logo UCIC base64
     */
    private function getLogoBase64(): string
    {
        $path = public_path('images/logo.png');
        if (file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
        return '';
    }

    /**
     * Get QR Code Base64 (Format PNG agar cepat di DomPDF)
     */
    /**
     * Get QR Code Base64 (Menggunakan SVG Base64 agar selalu muncul di DomPDF)
     */
    private function getQrPlaceholder(): string
    {
        try {
            // Generate QR Code format SVG
            $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(80)
                ->margin(0)
                ->generate('https://ucic.ac.id');

            // Konversi SVG ke Base64 Data URI yang pasti dibaca DomPDF
            return 'data:image/svg+xml;base64,' . base64_encode($qrSvg);
        } catch (\Exception $e) {
            return '';
        }
    }
}
