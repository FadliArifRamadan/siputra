<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Peminjaman Disetujui</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #10b981; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 8px 8px; }
        .detail { background: white; padding: 15px; border-radius: 6px; border: 1px solid #e5e7eb; margin: 15px 0; }
        .button { display: inline-block; background: #10b981; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <h1>✅ Peminjaman Disetujui</h1>
    </div>
    <div class="content">
        <p>Halo <strong>{{ $nama }}</strong>,</p>
        <p>Pengajuan peminjaman Anda telah <strong>disetujui</strong> oleh RTK.</p>
        <div class="detail">
            <p><strong>Fasilitas:</strong> {{ $fasilitas }}</p>
            <p><strong>Waktu Mulai:</strong> {{ $waktu_mulai }}</p>
            <p><strong>Waktu Selesai:</strong> {{ $waktu_selesai }}</p>
        </div>
        <p style="text-align: center; margin-top: 20px;">
            <a href="{{ $link }}" class="button">📋 Lihat Detail Peminjaman</a>
        </p>
        <p style="margin-top: 20px; font-size: 14px; color: #6b7280;">
            Selamat menggunakan fasilitas kampus! 🎉
        </p>
    </div>
    <div class="footer">
        <p>&copy; {{ date('Y') }} UCIC Facility Management. All rights reserved.</p>
    </div>
</body>
</html>