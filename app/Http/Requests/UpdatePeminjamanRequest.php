<?php

namespace App\Http\Requests;

use App\Models\Fasilitas;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Carbon\Carbon;

class UpdatePeminjamanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'keperluan' => 'required|string|min:10',
            'catatan_peminjam' => 'nullable|string|max:500',
            'waktu_mulai' => 'required|date|after:today',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
            'file_pendukung' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'items' => 'required|array|min:1',
            'items.*.fasilitas_id' => 'required|distinct|exists:fasilitas,id',
            'items.*.jumlah_dipinjam' => 'required|integer|min:1',
        ];
    }

    /**
     * Validasi tambahan: jika ada item ruangan (prasarana),
     * waktu_mulai harus minimal H+2 (after:tomorrow).
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $items = $this->input('items', []);
            $fasilitasIds = collect($items)->pluck('fasilitas_id')->filter()->all();

            if (empty($fasilitasIds)) {
                return;
            }

            $hasRuangan = Fasilitas::whereIn('id', $fasilitasIds)
                ->where('kategori', 'prasarana')
                ->exists();

            if ($hasRuangan) {
                $waktuMulai = $this->input('waktu_mulai');
                if ($waktuMulai && Carbon::parse($waktuMulai)->lte(Carbon::tomorrow()->endOfDay())) {
                    $v->errors()->add(
                        'waktu_mulai',
                        'Peminjaman ruangan harus diajukan minimal 2 hari sebelumnya (H-2). Pilih tanggal mulai mulai ' . Carbon::now()->addDays(2)->format('d/m/Y') . ' atau lebih.'
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'keperluan.required' => 'Keperluan wajib diisi.',
            'keperluan.min' => 'Keperluan minimal 10 karakter.',
            'waktu_mulai.required' => 'Waktu mulai wajib diisi.',
            'waktu_mulai.after' => 'Waktu mulai harus setelah hari ini.',
            'waktu_selesai.required' => 'Waktu selesai wajib diisi.',
            'waktu_selesai.after' => 'Waktu selesai harus setelah waktu mulai.',
            'items.required' => 'Minimal 1 item harus dipilih.',
            'items.*.fasilitas_id.required' => 'Fasilitas wajib dipilih.',
            'items.*.fasilitas_id.distinct' => 'Fasilitas yang sama tidak boleh dipilih lebih dari satu kali.',
            'items.*.fasilitas_id.exists' => 'Fasilitas yang dipilih tidak valid.',
            'items.*.jumlah_dipinjam.required' => 'Jumlah dipinjam wajib diisi.',
            'items.*.jumlah_dipinjam.min' => 'Jumlah dipinjam minimal 1.',
            'file_pendukung.file' => 'File pendukung harus berupa file.',
            'file_pendukung.mimes' => 'File pendukung harus berformat PDF, DOC, DOCX, JPG, JPEG, atau PNG.',
            'file_pendukung.max' => 'Ukuran file pendukung maksimal 5MB.',
        ];
    }
}