@extends('layouts.rtk')

@section('title', 'Edit User')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">✏️ Edit User: {{ $user->nama }}</h2>
                    <a href="{{ route('rtk.users.index') }}" class="text-gray-600 hover:text-gray-800">
                        ← Kembali
                    </a>
                </div>

                <form action="{{ route('rtk.users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="nomor_induk" class="block text-sm font-medium text-gray-700">
                            Nomor Induk <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="nomor_induk" 
                               id="nomor_induk"
                               value="{{ old('nomor_induk', $user->nomor_induk) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('nomor_induk') border-red-500 @enderror"
                               required>
                        @error('nomor_induk')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="nama" class="block text-sm font-medium text-gray-700">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="nama" 
                               id="nama"
                               value="{{ old('nama', $user->nama) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('nama') border-red-500 @enderror"
                               required>
                        @error('nama')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               name="email" 
                               id="email"
                               value="{{ old('email', $user->email) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('email') border-red-500 @enderror"
                               required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Password
                        </label>
                        <input type="text" 
                               name="password" 
                               id="password"
                               value=""
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('password') border-red-500 @enderror"
                               placeholder="Kosongkan jika tidak ingin mengubah password">
                        <p class="mt-1 text-xs text-gray-500">Minimal 8 karakter. Kosongkan jika tidak ingin mengubah password.</p>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="role" class="block text-sm font-medium text-gray-700">
                            Role <span class="text-red-500">*</span>
                        </label>
                        <select name="role" 
                                id="role"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('role') border-red-500 @enderror"
                                required>
                            <option value="">Pilih Role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}" {{ old('role', $userRole) == $role->name ? 'selected' : '' }}>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('rtk.users.index') }}" class="text-gray-600 hover:text-gray-800">
                            ← Kembali
                        </a>
                        <button type="submit" class="bg-yellow-600 text-white px-6 py-2 rounded-md hover:bg-yellow-700 transition">
                            Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection