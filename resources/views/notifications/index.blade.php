{{-- ✅ Gunakan layout dinamis berdasarkan role --}}
@php
    $user = Auth::user();
    $layout = 'layouts.pelapor';
    
    if ($user->hasRole('rtk')) {
        $layout = 'layouts.rtk';
    } elseif ($user->hasRole('teknisi')) {
        $layout = 'layouts.teknisi';
    } elseif ($user->hasRole('bikem')) {
        $layout = 'layouts.bikem';
    } elseif ($user->hasRole(['unit', 'UNIT'])) {
        $layout = 'layouts.unit';
    }
@endphp

@extends($layout)

@section('title', 'Notifikasi')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold flex items-center">
                        <svg class="h-6 w-6 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        Notifikasi
                        @if ($unreadCount > 0)
                            <span class="ml-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                                {{ $unreadCount }} belum dibaca
                            </span>
                        @endif
                    </h2>
                    <div class="flex items-center gap-4" style="gap: 16px;">
                        @if ($unreadCount > 0)
                            <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                    Tandai semua sudah dibaca
                                </button>
                            </form>
                        @endif
                        @if (!$notifications->isEmpty())
                            <form action="{{ route('notifications.clear') }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua notifikasi?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-medium flex items-center gap-1">
                                    <i class="fas fa-trash-alt"></i> Hapus Semua
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                @if (session('success'))
                    <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($notifications->isEmpty())
                    <div class="text-center py-12 text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <p class="text-lg">Belum ada notifikasi</p>
                        <p class="text-sm">Notifikasi akan muncul di sini saat ada aktivitas penting.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($notifications as $notification)
                            @php
                                $iconMap = [
                                    'account_approved' => 'fa-check-circle text-green-600',
                                    'account_rejected' => 'fa-times-circle text-red-600',
                                    'task_assigned' => 'fa-tasks text-purple-600',
                                    'ticket_processing' => 'fa-hourglass-half text-yellow-600',
                                    'ticket_completed' => 'fa-check-circle text-green-600',
                                    'booking_approved' => 'fa-calendar-check text-green-600',
                                    'booking_rejected' => 'fa-calendar-times text-red-600',
                                    'sla_breached' => 'fa-exclamation-triangle text-red-600',
                                    'reminder_booking' => 'fa-bell text-blue-600',
                                    'reminder_task' => 'fa-bell text-orange-600',
                                ];
                                $icon = $iconMap[$notification->type] ?? 'fa-bell text-gray-600';
                            @endphp
                            <div class="flex items-start p-4 rounded-lg border {{ $notification->isRead() ? 'bg-gray-50 border-gray-200' : 'bg-blue-50 border-blue-200' }}">
                                <div class="flex-shrink-0 mr-3 mt-1">
                                    <i class="fas {{ $icon }} text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-semibold text-gray-800 {{ $notification->isRead() ? '' : 'text-blue-800' }}">
                                            {{ $notification->title }}
                                            @if (!$notification->isRead())
                                                <span class="ml-2 inline-block w-2 h-2 bg-blue-600 rounded-full"></span>
                                            @endif
                                        </h4>
                                        <span class="text-xs text-gray-400">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">{{ $notification->message }}</p>
                                    <div class="mt-2 flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            @if ($notification->link)
                                                <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-xs text-blue-600 hover:text-blue-800">
                                                        Lihat Detail →
                                                    </button>
                                                </form>
                                            @endif
                                            @if (!$notification->isRead())
                                                <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-xs text-gray-400 hover:text-gray-600">
                                                        Tandai sudah dibaca
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                        <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus notifikasi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-semibold flex items-center gap-1">
                                                <i class="fas fa-trash-alt"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection