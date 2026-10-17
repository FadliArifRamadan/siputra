<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePengajuanKerusakanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fasilitas_id' => 'nullable|exists:fasilitas,id',
            'lokasi_id' => 'nullable|exists:lokasi,id',
            'jenis_kerusakan' => 'required|string|max:255',
            'lokasi_detail' => 'nullable|string|max:255',
            'deskripsi' => 'required|string',
            'foto_kerusakan' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'tingkat_urgensi' => 'required|in:rendah,sedang,tinggi',
        ];
    }

    public function messages(): array
    {
        return [
            'fasilitas_id.exists' => 'Fasilitas yang dipilih tidak valid.',
            'jenis_kerusakan.required' => 'Jenis kerusakan wajib diisi.',
            'lokasi_detail.required' => 'Lokasi detail wajib diisi.',
            'deskripsi.required' => 'Deskripsi wajib diisi.',
            'foto_kerusakan.image' => 'File harus berupa gambar.',
            'foto_kerusakan.mimes' => 'Format gambar harus jpeg, png, atau jpg.',
            'foto_kerusakan.max' => 'Ukuran gambar maksimal 5MB.',
            'tingkat_urgensi.required' => 'Tingkat urgensi wajib dipilih.',
        ];
    }
}
