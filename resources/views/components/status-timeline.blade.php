@props(['logs'])

@if ($logs && $logs->isNotEmpty())
    <div class="flow-root">
        <ul class="-mb-8">
            @foreach ($logs as $log)
                <li>
                    <div class="relative pb-8">
                        @if (!$loop->last)
                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                        @endif
                        <div class="relative flex space-x-3">
                            <div>
                                <span class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center ring-8 ring-white">
                                    @if ($log->new_status === 'selesai' || $log->new_status === 'disetujui')
                                        <svg class="h-4 w-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    @elseif ($log->new_status === 'ditolak' || $log->new_status === 'suspended')
                                        <svg class="h-4 w-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    @else
                                        <svg class="h-4 w-4 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div>
                                    <p class="text-sm text-gray-800">
                                        <span class="font-medium">Status berubah:</span>
                                        @if ($log->old_status)
                                            <span class="text-gray-500">{{ ucfirst($log->old_status) }}</span>
                                            <span class="text-gray-400">→</span>
                                        @endif
                                        <span class="font-semibold 
                                            {{ $log->new_status === 'selesai' || $log->new_status === 'disetujui' ? 'text-green-600' : '' }}
                                            {{ $log->new_status === 'ditolak' || $log->new_status === 'suspended' ? 'text-red-600' : '' }}
                                            {{ in_array($log->new_status, ['pending', 'diproses', 'berjalan']) ? 'text-yellow-600' : '' }}
                                        ">
                                            {{ ucfirst($log->new_status) }}
                                        </span>
                                    </p>
                                    @if ($log->keterangan)
                                        <p class="mt-0.5 text-sm text-gray-500">{{ $log->keterangan }}</p>
                                    @endif
                                    <div class="mt-0.5 flex items-center text-xs text-gray-400">
                                        <span>{{ $log->created_at->format('d/m/Y H:i') }}</span>
                                        @if ($log->actor)
                                            <span class="ml-2">oleh {{ $log->actor->nama }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
@else
    <p class="text-sm text-gray-500">Belum ada perubahan status.</p>
@endif