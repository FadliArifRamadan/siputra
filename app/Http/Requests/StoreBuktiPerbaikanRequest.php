<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBuktiPerbaikanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'foto_selesai' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'keterangan_selesai' => 'required|string|min:10',
            // ✅ TAMBAHKAN: kondisi akhir fasilitas
            'kondisi_akhir' => [
                'nullable',
                Rule::in(['baik', 'rusak_ringan', 'rusak_berat', 'maintenance', 'perlu_tindak_lanjut'])
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'foto_selesai.required' => 'Foto selesai wajib diupload.',
            'foto_selesai.image' => 'File harus berupa gambar.',
            'foto_selesai.mimes' => 'Format gambar harus jpeg, png, atau jpg.',
            'foto_selesai.max' => 'Ukuran gambar maksimal 5MB.',
            'keterangan_selesai.required' => 'Keterangan selesai wajib diisi.',
            'keterangan_selesai.min' => 'Keterangan selesai minimal 10 karakter.',
            'kondisi_akhir.in' => 'Kondisi akhir yang dipilih tidak valid.',
        ];
    }
}