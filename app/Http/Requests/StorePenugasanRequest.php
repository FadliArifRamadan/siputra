<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePenugasanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'teknisi_id' => 'required|exists:users,id',
            'jadwal_penanganan' => 'required|date|after:now',
            'instruksi_kerja' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'teknisi_id.required' => 'Teknisi wajib dipilih.',
            'teknisi_id.exists' => 'Teknisi yang dipilih tidak valid.',
            'jadwal_penanganan.required' => 'Jadwal penanganan wajib diisi.',
            'jadwal_penanganan.after' => 'Jadwal penanganan harus setelah waktu sekarang.',
            'instruksi_kerja.string' => 'Instruksi kerja harus berupa teks.',
        ];
    }
}