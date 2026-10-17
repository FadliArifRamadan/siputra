@extends('layouts.rtk')

@section('title', 'Import User')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">📥 Import User dari Excel/CSV</h2>
                    <a href="{{ route('rtk.users.index') }}" class="text-gray-600 hover:text-gray-800">
                        ← Kembali
                    </a>
                </div>

                @if (session('error'))
                    <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="bg-blue-50 p-4 rounded-lg mb-6 border-l-4 border-blue-500">
                    <h4 class="text-sm font-semibold text-blue-700">📋 Petunjuk Import</h4>
                    <ul class="text-sm text-blue-600 mt-2 space-y-1 list-disc list-inside">
                        <li>File harus berformat <strong>.xlsx</strong> atau <strong>.csv</strong></li>
                        <li>Kolom yang wajib: <strong>nomor_induk, nama, email, role</strong></li>
                        <li>Kolom password opsional (default: password123)</li>
                        <li>Role yang valid: <strong>pelapor</strong> atau <strong>teknisi</strong></li>
                        <li>Download template di bawah untuk panduan</li>
                    </ul>
                    <a href="{{ route('rtk.users.export-template') }}" class="inline-block mt-3 text-sm text-blue-700 hover:underline">
                        📄 Download Template Import
                    </a>
                </div>

                <form action="{{ route('rtk.users.import.process') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <label for="file" class="block text-sm font-medium text-gray-700">
                            Pilih File <span class="text-red-500">*</span>
                        </label>
                        <input type="file" 
                               name="file" 
                               id="file"
                               accept=".xlsx,.csv"
                               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('file') border-red-500 @enderror"
                               required>
                        @error('file')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('rtk.users.index') }}" class="text-gray-600 hover:text-gray-800">
                            ← Kembali
                        </a>
                        <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-md hover:bg-purple-700 transition">
                            📥 Import User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection