<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePeminjamanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Header
            'keperluan' => 'required|string|min:10',
            'catatan_peminjam' => 'nullable|string|max:500',
            'waktu_mulai' => 'required|date|after:tomorrow',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
            'file_pendukung' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',

            // Items (multi-item support dengan pencegahan duplikat)
            'items' => 'required|array|min:1',
            'items.*.fasilitas_id' => 'required|distinct|exists:fasilitas,id', // ✅ Tambah distinct
            'items.*.jumlah_dipinjam' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'keperluan.required' => 'Keperluan wajib diisi.',
            'keperluan.min' => 'Keperluan minimal 10 karakter.',
            'waktu_mulai.required' => 'Waktu mulai wajib diisi.',
            'waktu_mulai.after' => 'Waktu mulai harus minimal 1 hari setelah sekarang.',
            'waktu_selesai.required' => 'Waktu selesai wajib diisi.',
            'waktu_selesai.after' => 'Waktu selesai harus setelah waktu mulai.',
            'items.required' => 'Minimal 1 item harus dipilih.',
            'items.*.fasilitas_id.required' => 'Fasilitas wajib dipilih.',
            'items.*.fasilitas_id.distinct' => 'Fasilitas yang sama tidak boleh dipilih lebih dari satu kali.', // ✅ Pesan error duplikat
            'items.*.fasilitas_id.exists' => 'Fasilitas yang dipilih tidak valid.',
            'items.*.jumlah_dipinjam.required' => 'Jumlah dipinjam wajib diisi.',
            'items.*.jumlah_dipinjam.min' => 'Jumlah dipinjam minimal 1.',
            'file_pendukung.required' => 'Berkas pendukung wajib diunggah.',
            'file_pendukung.file' => 'Berkas pendukung harus berupa file.',
            'file_pendukung.mimes' => 'Berkas pendukung harus berformat PDF, DOC, DOCX, JPG, JPEG, atau PNG.',
            'file_pendukung.max' => 'Ukuran berkas pendukung maksimal 5MB.',
        ];
    }
}