@extends('layouts.rtk')

@section('title', 'Edit Lokasi')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">✏️ Edit Lokasi</h2>
                    <a href="{{ route('rtk.lokasi.index') }}" class="text-gray-600 hover:text-gray-800">
                        ← Kembali
                    </a>
                </div>

                <form action="{{ route('rtk.lokasi.update', $lokasi) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="kampus" class="block text-sm font-medium text-gray-700">
                            Kampus <span class="text-red-500">*</span>
                        </label>
                        <select name="kampus" 
                                id="kampus"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('kampus') border-red-500 @enderror"
                                required>
                            <option value="Kampus 1" {{ old('kampus', $lokasi->kampus) == 'Kampus 1' ? 'selected' : '' }}>Kampus 1</option>
                            <option value="Kampus 2" {{ old('kampus', $lokasi->kampus) == 'Kampus 2' ? 'selected' : '' }}>Kampus 2</option>
                        </select>
                        @error('kampus')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="lantai" class="block text-sm font-medium text-gray-700">
                            Lantai <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="lantai" 
                               id="lantai"
                               value="{{ old('lantai', $lokasi->lantai) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('lantai') border-red-500 @enderror"
                               required>
                        @error('lantai')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="kode_ruangan" class="block text-sm font-medium text-gray-700">
                            Kode Ruangan
                        </label>
                        <input type="text" 
                               name="kode_ruangan" 
                               id="kode_ruangan"
                               value="{{ old('kode_ruangan', $lokasi->kode_ruangan) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('kode_ruangan') border-red-500 @enderror"
                               placeholder="Contoh: A.1">
                        @error('kode_ruangan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="nama_ruangan" class="block text-sm font-medium text-gray-700">
                            Nama Ruangan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="nama_ruangan" 
                               id="nama_ruangan"
                               value="{{ old('nama_ruangan', $lokasi->nama_ruangan) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('nama_ruangan') border-red-500 @enderror"
                               required>
                        @error('nama_ruangan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="keterangan" class="block text-sm font-medium text-gray-700">
                            Keterangan
                        </label>
                        <textarea name="keterangan" 
                                  id="keterangan"
                                  rows="2"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('keterangan') border-red-500 @enderror">{{ old('keterangan', $lokasi->keterangan) }}</textarea>
                        @error('keterangan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Status
                        </label>
                        <label class="inline-flex items-center">
                            <input type="checkbox" 
                                   name="is_active" 
                                   value="1"
                                   {{ old('is_active', $lokasi->is_active) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">Aktif</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('rtk.lokasi.index') }}" class="text-gray-600 hover:text-gray-800">
                            ← Kembali
                        </a>
                        <button type="submit" class="bg-yellow-600 text-white px-6 py-2 rounded-md hover:bg-yellow-700 transition">
                            Update Lokasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection