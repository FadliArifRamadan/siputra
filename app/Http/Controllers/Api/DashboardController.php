<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\PelaporDashboardService;
use App\Services\Dashboard\RtkDashboardService;
use App\Services\Dashboard\TeknisiDashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function rtk(): JsonResponse
    {
        if (!Auth::check() || !Auth::user()->hasRole('rtk')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $service = new RtkDashboardService();
        $data = $service->getData(Auth::id());

        return response()->json($data);
    }

    public function teknisi(): JsonResponse
    {
        if (!Auth::check() || !Auth::user()->hasRole('teknisi')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $service = new TeknisiDashboardService();
        $data = $service->getData(Auth::id());

        return response()->json($data);
    }

    public function pelapor(): JsonResponse
    {
        if (!Auth::check() || !Auth::user()->hasRole('pelapor')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $service = new PelaporDashboardService();
        $data = $service->getData(Auth::id());

        return response()->json($data);
    }
}