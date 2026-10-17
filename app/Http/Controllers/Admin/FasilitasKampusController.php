<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fasilitas;
use App\Models\Lokasi;
use App\Services\QrCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FasilitasKampusController extends Controller
{
    protected QrCodeService $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    public function index(Request $request): View
    {
        $query = Fasilitas::kampus()->with('lokasi');

        // Filter Pencarian
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama_fasilitas', 'like', "%{$search}%")
                    ->orWhere('lokasi_detail', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%")
                    ->orWhereHas('lokasi', function ($lq) use ($search) {
                        $lq->where('kode_ruangan', 'like', "%{$search}%")
                            ->orWhere('nama_ruangan', 'like', "%{$search}%");
                    });
            });
        }

        // Filter Kampus
        if ($request->filled('kampus')) {
            $kampus = $request->input('kampus');
            $query->whereHas('lokasi', function ($q) use ($kampus) {
                $q->where('kampus', $kampus);
            });
        }

        // Filter Status Kondisi
        if ($request->filled('status_kondisi')) {
            $query->where('status_kondisi', $request->input('status_kondisi'));
        }

        // Filter Landing Page
        if ($request->filled('landing_page')) {
            $query->where('is_landing_page', $request->boolean('landing_page'));
        }

        $fasilitas = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();

        // Statistik ringkas
        $totalKampus = Fasilitas::kampus()->count();
        $totalKampus1 = Fasilitas::kampus()->whereHas('lokasi', fn($q) => $q->where('kampus', 'Kampus 1'))->count();
        $totalKampus2 = Fasilitas::kampus()->whereHas('lokasi', fn($q) => $q->where('kampus', 'Kampus 2'))->count();
        $totalLandingPage = Fasilitas::kampus()->where('is_landing_page', true)->count();

        return view('rtk.fasilitas-kampus.index', compact(
            'fasilitas',
            'totalKampus',
            'totalKampus1',
            'totalKampus2',
            'totalLandingPage'
        ));
    }

    public function create(): View
    {
        $lokasi = Lokasi::where('is_active', true)
            ->orderBy('kampus')
            ->orderBy('lantai')
            ->orderBy('kode_ruangan')
            ->get()
            ->groupBy('kampus');

        return view('rtk.fasilitas-kampus.create', compact('lokasi'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_fasilitas' => 'required|string|max:255',
            'lokasi_id' => 'nullable|exists:lokasi,id',
            'status_kondisi' => 'required|in:baik,rusak_ringan,rusak_berat,maintenance',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'fasilitas_ruangan' => 'nullable|string',
            'deskripsi' => 'nullable|string',
            'is_landing_page' => 'nullable|boolean',
        ]);

        $data = $request->only([
            'nama_fasilitas',
            'lokasi_id',
            'status_kondisi',
            'fasilitas_ruangan',
            'deskripsi',
        ]);

        $data['tipe_fasilitas'] = 'kampus';
        $data['kategori'] = 'prasarana';
        $data['stok_total'] = 1;
        $data['is_landing_page'] = $request->boolean('is_landing_page', true);

        if (!empty($data['lokasi_id'])) {
            $lokasi = Lokasi::find($data['lokasi_id']);
            if ($lokasi) {
                $data['lokasi_detail'] = $lokasi->full_name;
            }
        }

        // Upload Foto
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('foto_fasilitas', 'public');
            $data['foto'] = $path;
        }

        $fasilitas = Fasilitas::create($data);

        // Otomatis generate QR Code untuk ditempel di ruangan
        try {
            $this->qrCodeService->generateAndSave($fasilitas);
        } catch (\Throwable $e) {
            // Log error silently if QR fail
        }

        return redirect()->route('rtk.fasilitas-kampus.index')
            ->with('success', 'Fasilitas Kampus berhasil ditambahkan! QR Code ruangan telah siap.');
    }

    public function show(Fasilitas $fasilitas): View
    {
        $fasilitas->load(['lokasi', 'pengajuanKerusakan' => function ($q) {
            $q->with(['pelapor', 'penugasanTeknisi.teknisi'])->latest()->take(10);
        }]);

        // Pastikan QR Code sudah tersedia di storage
        if (!$fasilitas->qr_code_path) {
            try {
                $this->qrCodeService->generateAndSave($fasilitas);
            } catch (\Throwable $e) {
                // Ignore
            }
        }

        return view('rtk.fasilitas-kampus.show', compact('fasilitas'));
    }

    public function edit(Fasilitas $fasilitas): View
    {
        $lokasi = Lokasi::where('is_active', true)
            ->orderBy('kampus')
            ->orderBy('lantai')
            ->orderBy('kode_ruangan')
            ->get()
            ->groupBy('kampus');

        return view('rtk.fasilitas-kampus.edit', compact('fasilitas', 'lokasi'));
    }

    public function update(Request $request, Fasilitas $fasilitas): RedirectResponse
    {
        $request->validate([
            'nama_fasilitas' => 'required|string|max:255',
            'lokasi_id' => 'nullable|exists:lokasi,id',
            'status_kondisi' => 'required|in:baik,rusak_ringan,rusak_berat,maintenance',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'fasilitas_ruangan' => 'nullable|string',
            'deskripsi' => 'nullable|string',
            'is_landing_page' => 'nullable|boolean',
        ]);

        $data = $request->only([
            'nama_fasilitas',
            'lokasi_id',
            'status_kondisi',
            'fasilitas_ruangan',
            'deskripsi',
        ]);

        $data['is_landing_page'] = $request->boolean('is_landing_page');

        if (!empty($data['lokasi_id'])) {
            $lokasi = Lokasi::find($data['lokasi_id']);
            if ($lokasi) {
                $data['lokasi_detail'] = $lokasi->full_name;
            }
        }

        // Update Foto jika ada upload baru
        if ($request->hasFile('foto')) {
            if ($fasilitas->foto && Storage::disk('public')->exists($fasilitas->foto)) {
                Storage::disk('public')->delete($fasilitas->foto);
            }
            $path = $request->file('foto')->store('foto_fasilitas', 'public');
            $data['foto'] = $path;
        }

        $fasilitas->update($data);

        return redirect()->route('rtk.fasilitas-kampus.index')
            ->with('success', 'Fasilitas Kampus berhasil diperbarui!');
    }

    public function destroy(Fasilitas $fasilitas): RedirectResponse
    {
        $fasilitas->delete();

        return redirect()->route('rtk.fasilitas-kampus.index')
            ->with('success', 'Fasilitas Kampus berhasil dihapus.');
    }

    public function generateQrCode(Fasilitas $fasilitas): RedirectResponse
    {
        try {
            $this->qrCodeService->regenerate($fasilitas);
            return back()->with('success', 'QR Code ruangan berhasil digenerate ulang!');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal generate QR Code: ' . $e->getMessage());
        }
    }

    public function downloadQrCode(Fasilitas $fasilitas): BinaryFileResponse|RedirectResponse
    {
        $filename = 'qrcodes/fasilitas_' . $fasilitas->id . '.svg';

        if (!Storage::disk('public')->exists($filename)) {
            $this->qrCodeService->generateAndSave($fasilitas);
        }

        $path = Storage::disk('public')->path($filename);

        if (file_exists($path)) {
            $downloadName = 'QR_' . str_replace(' ', '_', $fasilitas->nama_fasilitas) . '.svg';
            return response()->download($path, $downloadName);
        }

        return back()->with('error', 'File QR Code tidak ditemukan.');
    }
}
