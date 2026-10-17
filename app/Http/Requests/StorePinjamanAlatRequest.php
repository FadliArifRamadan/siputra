<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePinjamanAlatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
{
    return [
        'items' => 'required|array|min:1',
        'items.*.fasilitas_id' => 'required|exists:fasilitas,id',
        'items.*.jumlah_dipinjam' => 'required|integer|min:1',
        'waktu_mulai' => 'required|date|after_or_equal:now',
        'waktu_selesai' => 'required|date|after:waktu_mulai',
        'keperluan' => 'required|string|min:10',
    ];
}

    public function messages(): array
    {
        return [
            'fasilitas_id.required' => 'Alat kerja wajib dipilih.',
            'fasilitas_id.exists' => 'Alat kerja yang dipilih tidak valid.',
            'jumlah_dipinjam.required' => 'Jumlah dipinjam wajib diisi.',
            'jumlah_dipinjam.min' => 'Jumlah dipinjam minimal 1.',
            'waktu_mulai.required' => 'Waktu mulai wajib diisi.',
            'waktu_mulai.after' => 'Waktu mulai harus minimal 1 hari setelah sekarang.',
            'waktu_selesai.required' => 'Waktu selesai wajib diisi.',
            'waktu_selesai.after' => 'Waktu selesai harus setelah waktu mulai.',
            'keperluan.required' => 'Keperluan wajib diisi.',
            'keperluan.min' => 'Keperluan minimal 10 karakter.',
        ];
    }
}