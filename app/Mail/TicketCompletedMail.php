<?php

namespace App\Mail;

use App\Models\PengajuanKerusakan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public PengajuanKerusakan $pengajuan;

    public function __construct(PengajuanKerusakan $pengajuan)
    {
        $this->pengajuan = $pengajuan;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Laporan Kerusakan Selesai - UCIC',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket-completed',
            with: [
                'nama' => $this->pengajuan->pelapor->nama,
                'jenis_kerusakan' => $this->pengajuan->jenis_kerusakan,
                'lokasi' => $this->pengajuan->lokasi_detail,
                'link' => route('pelapor.pengajuan.show', $this->pengajuan),
            ]
        );
    }
}