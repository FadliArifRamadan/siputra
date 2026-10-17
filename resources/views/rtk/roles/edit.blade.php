@extends('layouts.rtk')

@section('title', 'Edit Role')

@section('content')
<div class="py-4 sm:py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold font-heading text-[#2a2338] flex items-center gap-2">
                    <i data-lucide="edit-3" class="w-6 h-6 text-[#f2723b]"></i> Edit Role: <span class="text-[#f2723b]">{{ $role->name }}</span>
                </h1>
                <p class="text-xs sm:text-sm text-[#7a7189] mt-1">
                    Ubah nama role atau kelola kembali batasan hak akses permission yang diberikan.
                </p>
            </div>
            <a href="{{ route('rtk.roles.index') }}" 
               class="inline-flex items-center gap-2 text-xs font-semibold text-[#191333] bg-[#fbf7f0] hover:bg-[#ecdfd0] border border-[#ecdfd0] px-4 py-2.5 rounded-xl transition-colors shrink-0">
                <i data-lucide="arrow-left" class="w-4 h-4 text-[#f2723b]"></i> Kembali
            </a>
        </div>

        <!-- Form Edit Role -->
        <div class="bg-white p-6 rounded-2xl border border-[#ecdfd0] shadow-sm">
            <form action="{{ route('rtk.roles.update', $role) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Nama Role -->
                <div>
                    <label for="name" class="block text-xs font-bold text-[#2a2338] uppercase tracking-wider mb-2">
                        Nama Role <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name"
                           value="{{ old('name', $role->name) }}"
                           class="w-full rounded-xl border-[#ecdfd0] text-sm text-[#2a2338] shadow-sm focus:border-[#f2723b] focus:ring-[#f2723b] @error('name') border-rose-500 @enderror"
                           required>
                    @error('name')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1">
                            <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Section Permission Checklist -->
                <div class="pt-4 border-t border-[#ecdfd0]">
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-xs font-bold text-[#2a2338] uppercase tracking-wider">
                            Hak Akses / Permission
                        </label>
                        <span class="text-xs text-[#7a7189]">Atur izin untuk role ini</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 bg-[#fbf7f0] p-4 rounded-xl border border-[#ecdfd0]">
                        @foreach ($permissions as $permission)
                            <label class="flex items-center space-x-2.5 text-xs font-semibold text-[#2a2338] cursor-pointer hover:text-[#f2723b] transition-colors p-1.5 rounded-lg hover:bg-white">
                                <input type="checkbox" 
                                       name="permissions[]" 
                                       value="{{ $permission->id }}"
                                       {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}
                                       class="rounded border-[#ecdfd0] text-[#f2723b] focus:ring-[#f2723b] w-4 h-4">
                                <span>{{ $permission->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('permissions')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center gap-1">
                            <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Submit Action Buttons -->
                <div class="pt-4 border-t border-[#ecdfd0] flex items-center justify-between gap-3">
                    <a href="{{ route('rtk.roles.index') }}" 
                       class="text-xs font-semibold text-[#7a7189] hover:text-[#2a2338] transition-colors">
                        Batal
                    </a>
                    <button type="submit" 
                            class="bg-[#f2723b] hover:bg-[#d95f2a] text-white px-6 py-2.5 rounded-xl transition-colors text-xs font-semibold shadow-sm flex items-center gap-2">
                        <i data-lucide="check" class="w-4 h-4"></i> Update Role
                    </button>
                </div>
            </form>
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