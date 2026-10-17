<?php

namespace App\Mail;

use App\Models\PenugasanTeknisi;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public PenugasanTeknisi $penugasan;

    public function __construct(PenugasanTeknisi $penugasan)
    {
        $this->penugasan = $penugasan;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tugas Baru Diterima - UCIC',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.task-assigned',
            with: [
                'nama' => $this->penugasan->teknisi->nama,
                'jenis_kerusakan' => $this->penugasan->pengajuanKerusakan->jenis_kerusakan,
                'lokasi' => $this->penugasan->pengajuanKerusakan->lokasi_detail,
                'jadwal' => $this->penugasan->jadwal_penanganan->format('d/m/Y H:i'),
                'instruksi' => $this->penugasan->instruksi_kerja,
                'link' => route('teknisi.tugas.show', $this->penugasan),
            ]
        );
    }
}