<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Akun Disetujui</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2563eb; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 8px 8px; }
        .button { display: inline-block; background: #2563eb; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <h1>✅ Akun Disetujui</h1>
    </div>
    <div class="content">
        <p>Halo <strong>{{ $nama }}</strong>,</p>
        <p>Selamat! Akun Anda di <strong>Sistem Informasi Manajemen Fasilitas UCIC</strong> telah disetujui oleh RTK.</p>
        <p>Anda sekarang dapat mengakses semua fitur, termasuk:</p>
        <ul>
            <li>Melaporkan kerusakan fasilitas</li>
            <li>Mengajukan peminjaman fasilitas</li>
            <li>Melacak status laporan dan peminjaman</li>
        </ul>
        <p style="text-align: center; margin-top: 30px;">
            <a href="{{ $dashboardUrl }}" class="button">🚀 Akses Dashboard</a>
        </p>
        <p style="margin-top: 20px; font-size: 14px; color: #6b7280;">
            Jika Anda memiliki pertanyaan, silakan hubungi RTK kampus.
        </p>
    </div>
    <div class="footer">
        <p>&copy; {{ date('Y') }} UCIC Facility Management. All rights reserved.</p>
    </div>
</body>
</html>