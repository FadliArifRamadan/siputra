<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fasilitas;
use App\Services\BookingValidationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    protected BookingValidationService $bookingService;

    public function __construct(BookingValidationService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function cekStok(Request $request): JsonResponse
    {

        // ✅ Pastikan user login (via session)
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $request->validate([
            'fasilitas_id' => 'required|exists:fasilitas,id',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
        ]);

        $fasilitas = Fasilitas::find($request->fasilitas_id);

        // Hitung stok tersedia
        $totalUsed = $this->bookingService->getTotalPeminjamanPadaWaktu(
            $request->fasilitas_id,
            $request->waktu_mulai,
            $request->waktu_selesai
        );

        $available = max(0, $fasilitas->stok_total - $totalUsed);

        return response()->json([
            'success' => true,
            'available' => $available,
            'stok_total' => $fasilitas->stok_total,
            'total_used' => $totalUsed,
        ]);
    }
}
