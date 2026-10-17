<?php

namespace App\Mail;

use App\Models\PeminjamanFasilitas;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public PeminjamanFasilitas $peminjaman;

    public function __construct(PeminjamanFasilitas $peminjaman)
    {
        $this->peminjaman = $peminjaman;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Peminjaman Disetujui - UCIC',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-approved',
            with: [
                'nama' => $this->peminjaman->peminjam->nama,
                'fasilitas' => $this->peminjaman->fasilitas->nama_fasilitas,
                'waktu_mulai' => $this->peminjaman->waktu_mulai->format('d/m/Y H:i'),
                'waktu_selesai' => $this->peminjaman->waktu_selesai->format('d/m/Y H:i'),
                'link' => route('pelapor.peminjaman.show', $this->peminjaman),
            ]
        );
    }
}