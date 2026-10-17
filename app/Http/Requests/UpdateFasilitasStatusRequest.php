<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFasilitasStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status_kondisi' => [
                'required',
                Rule::in(['baik', 'rusak_ringan', 'rusak_berat', 'maintenance'])
            ],
            'catatan' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'status_kondisi.required' => 'Status kondisi wajib dipilih.',
            'status_kondisi.in' => 'Status kondisi yang dipilih tidak valid.',
            'catatan.max' => 'Catatan maksimal 500 karakter.',
        ];
    }
}