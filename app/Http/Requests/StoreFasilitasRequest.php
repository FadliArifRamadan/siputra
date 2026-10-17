<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFasilitasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_fasilitas' => 'required|string|max:255',
            'kategori' => 'required|in:sarana,prasarana',
            'lokasi_id' => 'nullable|exists:lokasi,id',
            'stok_total' => 'required|integer|min:1',
            'status_kondisi' => 'required|in:baik,rusak_ringan,rusak_berat,maintenance',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_fasilitas.required' => 'Nama fasilitas wajib diisi.',
            'nama_fasilitas.max' => 'Nama fasilitas maksimal 255 karakter.',
            'kategori.required' => 'Kategori wajib dipilih.',
            'kategori.in' => 'Kategori tidak valid.',
            'lokasi_detail.required' => 'Lokasi detail wajib diisi.',
            'lokasi_detail.max' => 'Lokasi detail maksimal 255 karakter.',
            'stok_total.required' => 'Stok total wajib diisi.',
            'stok_total.min' => 'Stok total minimal 1.',
            'status_kondisi.required' => 'Status kondisi wajib dipilih.',
            'status_kondisi.in' => 'Status kondisi tidak valid.',
        ];
    }
}
