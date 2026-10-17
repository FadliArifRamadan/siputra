<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePeminjamanStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status_pinjam' => [
                'required',
                Rule::in(['disetujui', 'ditolak', 'berjalan', 'selesai'])
            ],
            'catatan' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'status_pinjam.required' => 'Status wajib dipilih.',
            'status_pinjam.in' => 'Status yang dipilih tidak valid.',
            'catatan.max' => 'Catatan maksimal 500 karakter.',
        ];
    }
}