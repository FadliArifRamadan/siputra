<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(): View
    {
        $user = Auth::user();
        $notifications = $this->notificationService->getLatest($user, 50);
        $unreadCount = $this->notificationService->getUnreadCount($user);

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markAllAsRead(): RedirectResponse
    {
        $user = Auth::user();
        $count = $this->notificationService->markAllAsRead($user);

        return redirect()->back()
            ->with('success', $count . ' notifikasi telah ditandai sudah dibaca.');
    }

    public function markAsRead(int $id): RedirectResponse
    {
        $user = Auth::user();
        $notification = $this->notificationService->markAsRead($user, $id);

        if ($notification && $notification->link) {
            return redirect($notification->link);
        }

        return redirect()->route('notifications.index');
    }

    public function unreadCount(): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $count = $this->notificationService->getUnreadCount($user);

        return response()->json(['count' => $count]);
    }

    public function destroy(int $id): RedirectResponse
    {
        $user = Auth::user();
        $notification = \App\Models\Notification::forUser($user->id)->find($id);

        if ($notification) {
            $notification->delete();
            return redirect()->back()->with('success', 'Notifikasi berhasil dihapus.');
        }

        return redirect()->back()->with('error', 'Notifikasi tidak ditemukan atau Anda tidak memiliki akses.');
    }

    public function destroyAll(): RedirectResponse
    {
        $user = Auth::user();
        \App\Models\Notification::forUser($user->id)->delete();

        return redirect()->back()->with('success', 'Semua notifikasi berhasil dihapus.');
    }
}