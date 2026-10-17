@extends('layouts.rtk')

@section('title', 'Manajemen Role & Permission')

@section('content')
<div class="py-4 sm:py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Header Banner & Top Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold font-heading text-[#2a2338] flex items-center gap-2">
                    <i data-lucide="shield-check" class="w-6 h-6 text-[#f2723b]"></i> Manajemen Role & Permission
                </h1>
                <p class="text-xs sm:text-sm text-[#7a7189] mt-1">
                    Kelola peran akses pengguna dan penugasan hak izin sistem secara terpusat.
                </p>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('rtk.roles.create') }}" 
                   class="bg-[#f2723b] hover:bg-[#d95f2a] text-white px-4 py-2.5 rounded-xl transition-colors text-xs font-semibold shadow-sm flex items-center gap-1.5">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah Role
                </a>
                <a href="{{ route('rtk.roles.assign') }}" 
                   class="bg-[#191333] hover:bg-[#2f2559] text-white px-4 py-2.5 rounded-xl transition-colors text-xs font-semibold shadow-sm flex items-center gap-1.5">
                    <i data-lucide="user-plus" class="w-4 h-4 text-[#f2723b]"></i> Assign Role
                </a>
            </div>
        </div>

        <!-- Feedback Messages -->
        @if (session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-r-xl shadow-sm flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500 shrink-0"></i>
            <span class="text-xs sm:text-sm font-medium">{{ session('success') }}</span>
        </div>
        @endif

        @if (session('error'))
        <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-800 p-4 rounded-r-xl shadow-sm flex items-center gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-500 shrink-0"></i>
            <span class="text-xs sm:text-sm font-medium">{{ session('error') }}</span>
        </div>
        @endif

        <!-- Role Table Card -->
        <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm overflow-hidden">
            <div class="p-5 border-b border-[#ecdfd0] bg-[#fbf7f0]/50 flex items-center justify-between">
                <h2 class="text-sm font-bold text-[#2a2338] uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="list" class="w-4 h-4 text-[#7a7189]"></i> Daftar Role Terdaftar
                </h2>
                <span class="text-xs font-medium text-[#7a7189]">Total: {{ $roles->count() }} Role</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#ecdfd0] text-xs sm:text-sm">
                    <thead class="bg-[#fbf7f0]">
                        <tr>
                            <th class="px-5 py-3.5 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px] w-16">ID</th>
                            <th class="px-5 py-3.5 text-left font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Nama Role</th>
                            <th class="px-5 py-3.5 text-left font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Permissions Assigned</th>
                            <th class="px-5 py-3.5 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Pengguna</th>
                            <th class="px-5 py-3.5 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px] w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#ecdfd0] bg-white text-[#2a2338]">
                        @foreach ($roles as $role)
                        <tr class="hover:bg-[#fbf7f0]/50 transition-colors">
                            <td class="px-5 py-4 text-center font-mono text-[#7a7189] font-bold">#{{ $role->id }}</td>
                            <td class="px-5 py-4 font-bold">
                                <span class="px-3 py-1 inline-flex text-xs font-bold rounded-lg uppercase tracking-wide
                                    {{ $role->name === 'rtk' ? 'bg-rose-100 text-rose-800' : '' }}
                                    {{ $role->name === 'teknisi' ? 'bg-sky-100 text-sky-800' : '' }}
                                    {{ $role->name === 'pelapor' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    {{ !in_array($role->name, ['rtk', 'teknisi', 'pelapor']) ? 'bg-[#fbf7f0] text-[#191333] border border-[#ecdfd0]' : '' }}">
                                    {{ $role->name }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap gap-1.5">
                                    @forelse ($role->permissions as $permission)
                                        <span class="px-2 py-0.5 text-[11px] bg-[#fbf7f0] text-[#2a2338] border border-[#ecdfd0] rounded-md font-medium">
                                            {{ $permission->name }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-[#7a7189] italic">Tidak ada permission</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center gap-1 font-mono font-bold text-xs bg-[#fbf7f0] px-2.5 py-1 rounded-lg text-[#191333]">
                                    <i data-lucide="users" class="w-3.5 h-3.5 text-[#7a7189]"></i>
                                    {{ $role->users()->count() }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if ($role->name !== 'rtk')
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('rtk.roles.edit', $role) }}" 
                                           class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                                           title="Edit Role">
                                            <i data-lucide="pencil" class="w-4 h-4"></i>
                                        </a>
                                        <form action="{{ route('rtk.roles.destroy', $role) }}" 
                                              method="POST" class="inline-block"
                                              onsubmit="return confirm('Yakin ingin menghapus role ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Role">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-[11px] font-semibold text-[#7a7189] bg-[#fbf7f0] px-2 py-1 rounded border border-[#ecdfd0] inline-flex items-center gap-1">
                                        <i data-lucide="lock" class="w-3 h-3 text-[#f2723b]"></i> Protected
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.lucide) { lucide.createIcons(); }
    });
</script>
@endpush