@extends('layouts.rtk')

@section('title', 'Assign Role ke User')

@section('content')
<div class="py-4 sm:py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Header & Top Action -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold font-heading text-[#2a2338] flex items-center gap-2">
                    <i data-lucide="user-plus" class="w-6 h-6 text-[#f2723b]"></i> Assign Role ke User
                </h1>
                <p class="text-xs sm:text-sm text-[#7a7189] mt-1">
                    Atur hak akses pengguna dengan menetapkan role yang sesuai untuk setiap akun.
                </p>
            </div>
            <a href="{{ route('rtk.roles.index') }}" 
               class="inline-flex items-center gap-2 text-xs font-semibold text-[#191333] bg-[#fbf7f0] hover:bg-[#ecdfd0] border border-[#ecdfd0] px-4 py-2.5 rounded-xl transition-colors shrink-0">
                <i data-lucide="arrow-left" class="w-4 h-4 text-[#f2723b]"></i> Kembali ke Manajemen Role
            </a>
        </div>

        <!-- Alert Notifications -->
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

        <!-- Form Quick Assign Role -->
        <div class="bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm space-y-4">
            <div class="flex items-center gap-2">
                <i data-lucide="shield-plus" class="w-5 h-5 text-[#f2723b]"></i>
                <h2 class="text-base font-bold text-[#2a2338]">Tetapkan Role Baru</h2>
            </div>
            <form action="{{ route('rtk.roles.assign.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-12 gap-3">
                @csrf
                <div class="md:col-span-5">
                    <label class="block text-xs font-semibold text-[#7a7189] uppercase tracking-wider mb-1">Pilih Pengguna</label>
                    <select name="user_id" class="w-full rounded-xl border-[#ecdfd0] text-xs font-medium text-[#2a2338] shadow-sm focus:border-[#f2723b] focus:ring-[#f2723b]" required>
                        <option value="">-- Pilih User --</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">
                                {{ $user->nama }} ({{ $user->nomor_induk }}) 
                                @if ($user->roles->isNotEmpty())
                                    - [ {{ $user->roles->pluck('name')->implode(', ') }} ]
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-4">
                    <label class="block text-xs font-semibold text-[#7a7189] uppercase tracking-wider mb-1">Pilih Role</label>
                    <select name="role" class="w-full rounded-xl border-[#ecdfd0] text-xs font-medium text-[#2a2338] shadow-sm focus:border-[#f2723b] focus:ring-[#f2723b]" required>
                        <option value="">-- Pilih Role --</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-3 flex items-end">
                    <button type="submit" class="w-full bg-[#191333] hover:bg-[#2f2559] text-white px-5 py-2.5 rounded-xl transition-colors text-xs font-semibold shadow-sm flex items-center justify-center gap-2 h-[38px]">
                        <i data-lucide="check" class="w-4 h-4 text-[#f2723b]"></i> Assign Role
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabel Daftar User & Role -->
        <div class="bg-white rounded-2xl border border-[#ecdfd0] shadow-sm overflow-hidden p-5 sm:p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold text-[#2a2338] flex items-center gap-2">
                    <i data-lucide="users" class="w-5 h-5 text-[#f2723b]"></i> Daftar Pengguna & Akses Role
                </h2>
                <span class="text-xs font-semibold text-[#7a7189]">Total: {{ $users->count() }} Pengguna</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#ecdfd0] text-xs sm:text-sm">
                    <thead class="bg-[#fbf7f0]">
                        <tr>
                            <th class="px-4 py-3 text-left font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Nama</th>
                            <th class="px-4 py-3 text-left font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Nomor Induk</th>
                            <th class="px-4 py-3 text-left font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Role Aktif</th>
                            <th class="px-4 py-3 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Status Akun</th>
                            <th class="px-4 py-3 text-center font-bold text-[#2a2338] uppercase tracking-wider text-[11px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#ecdfd0] bg-white text-[#2a2338]">
                        @foreach ($users as $user)
                        <tr class="hover:bg-[#fbf7f0]/50 transition-colors">
                            <td class="px-4 py-3 font-semibold text-[#191333]">{{ $user->nama }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-[#7a7189]">{{ $user->nomor_induk }}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    @forelse ($user->roles as $role)
                                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg 
                                            {{ $role->name === 'rtk' ? 'bg-purple-100 text-purple-900' : '' }}
                                            {{ $role->name === 'teknisi' ? 'bg-sky-100 text-sky-900' : '' }}
                                            {{ $role->name === 'pelapor' ? 'bg-emerald-100 text-emerald-900' : '' }}
                                            {{ !in_array($role->name, ['rtk', 'teknisi', 'pelapor']) ? 'bg-[#fbf7f0] text-[#2a2338] border border-[#ecdfd0]' : '' }}">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-[#7a7189] italic">Tidak ada role</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2.5 py-1 inline-flex text-[11px] font-bold rounded-lg
                                    {{ $user->status_akun === 'active' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    {{ $user->status_akun === 'pending' ? 'bg-amber-100 text-amber-800' : '' }}
                                    {{ $user->status_akun === 'suspended' ? 'bg-rose-100 text-rose-800' : '' }}">
                                    {{ ucfirst($user->status_akun) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if (!$user->hasRole('rtk') && $user->roles->isNotEmpty())
                                    <form action="{{ route('rtk.roles.remove', $user) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="role" value="{{ $user->roles->first()->name }}">
                                        <button type="submit" 
                                                class="inline-flex items-center gap-1 text-xs font-semibold text-rose-600 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 px-2.5 py-1 rounded-lg transition-colors"
                                                onclick="return confirm('Hapus role dari user ini?')">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus Role
                                        </button>
                                    </form>
                                @elseif ($user->hasRole('rtk'))
                                    <span class="inline-flex items-center gap-1 text-xs text-[#7a7189] bg-[#fbf7f0] px-2.5 py-1 rounded-lg font-medium border border-[#ecdfd0]">
                                        <i data-lucide="lock" class="w-3 h-3 text-[#f2723b]"></i> Protected
                                    </span>
                                @else
                                    <span class="text-xs text-[#7a7189] italic">-</span>
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
        if (window.lucide) {
            lucide.createIcons();
        }
    });
</script>
@endpush