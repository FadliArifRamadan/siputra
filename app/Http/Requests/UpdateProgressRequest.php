<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProgressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status_laporan' => [
                'required',
                Rule::in(['diterima', 'dikerjakan', 'menunggu_parts', 'selesai'])
            ],
            'catatan' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'status_laporan.required' => 'Status progress wajib dipilih.',
            'status_laporan.in' => 'Status progress yang dipilih tidak valid.',
            'catatan.max' => 'Catatan maksimal 500 karakter.',
        ];
    }
}