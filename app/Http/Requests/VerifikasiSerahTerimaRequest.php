<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifikasiSerahTerimaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kondisi' => [
                'required',
                Rule::in(['baik', 'rusak'])
            ],
            'catatan' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'kondisi.required' => 'Kondisi barang wajib dipilih.',
            'kondisi.in' => 'Kondisi barang yang dipilih tidak valid.',
            'catatan.max' => 'Catatan maksimal 500 karakter.',
        ];
    }
}