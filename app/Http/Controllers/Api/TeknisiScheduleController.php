<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BookingValidationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TeknisiScheduleController extends Controller
{
    protected BookingValidationService $bookingService;

    public function __construct(BookingValidationService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function index(Request $request): JsonResponse
    {
        // ✅ Pastikan user login dan memiliki role yang sesuai
        if (!Auth::check() || !Auth::user()->hasRole('rtk')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'teknisi_id' => 'required|exists:users,id',
            'start' => 'required|date',
            'end' => 'required|date|after:start',
        ]);

        $cacheKey = 'teknisi_schedule_' . $request->teknisi_id . '_' . $request->start . '_' . $request->end;
        
        $events = Cache::remember($cacheKey, 300, function () use ($request) {
            return $this->bookingService->getTeknisiSchedule(
                $request->teknisi_id,
                $request->start,
                $request->end
            );
        });

        return response()->json($events);
    }

    public function checkAvailability(Request $request): JsonResponse
    {
        if (!Auth::check() || !Auth::user()->hasRole('rtk')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'teknisi_id' => 'required|exists:users,id',
            'jadwal' => 'required|date',
        ]);

        $waktuMulai = $request->jadwal;
        $waktuSelesai = Carbon::parse($waktuMulai)->addHour()->toDateTimeString();

        $validation = $this->bookingService->validateTeknisiAssignment(
            $request->teknisi_id,
            $waktuMulai,
            $waktuSelesai,
            $request->exclude_penugasan_id ?? null
        );

        return response()->json([
            'available' => $validation['valid'],
            'message' => $validation['message'],
        ]);
    }
}