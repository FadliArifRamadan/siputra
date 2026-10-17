<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lokasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LokasiController extends Controller
{
    public function index(): View
    {
        $lokasi = Lokasi::orderBy('kampus')
            ->orderBy('lantai')
            ->orderBy('kode_ruangan')
            ->get()
            ->groupBy('kampus');

        return view('rtk.lokasi.index', compact('lokasi'));
    }

    public function create(): View
    {
        return view('rtk.lokasi.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'kampus' => 'required|string|max:50',
            'lantai' => 'required|string|max:20',
            'kode_ruangan' => 'nullable|string|max:50',
            'nama_ruangan' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        Lokasi::create($request->all());

        return redirect()->route('rtk.lokasi.index')
            ->with('success', 'Lokasi berhasil ditambahkan.');
    }

    public function edit(Lokasi $lokasi): View
    {
        return view('rtk.lokasi.edit', compact('lokasi'));
    }

    public function update(Request $request, Lokasi $lokasi): RedirectResponse
    {
        $request->validate([
            'kampus' => 'required|string|max:50',
            'lantai' => 'required|string|max:20',
            'kode_ruangan' => 'nullable|string|max:50',
            'nama_ruangan' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        $lokasi->update($data);

        return redirect()->route('rtk.lokasi.index')
            ->with('success', 'Lokasi berhasil diperbarui.');
    }

    public function destroy(Lokasi $lokasi): RedirectResponse
    {
        // Cek apakah lokasi digunakan di fasilitas
        $used = $lokasi->fasilitas()->exists();
        if ($used) {
            return redirect()->back()
                ->with('error', 'Lokasi tidak dapat dihapus karena masih digunakan oleh fasilitas.');
        }

        $lokasi->delete();

        return redirect()->route('rtk.lokasi.index')
            ->with('success', 'Lokasi berhasil dihapus.');
    }
}