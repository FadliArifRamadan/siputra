<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Selesai</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #10b981; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 8px 8px; }
        .button { display: inline-block; background: #10b981; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <h1>✅ Laporan Selesai</h1>
    </div>
    <div class="content">
        <p>Halo <strong>{{ $nama }}</strong>,</p>
        <p>Laporan kerusakan Anda telah selesai diperbaiki oleh tim teknisi.</p>
        <p><strong>Jenis Kerusakan:</strong> {{ $jenis_kerusakan }}</p>
        <p><strong>Lokasi:</strong> {{ $lokasi }}</p>
        <p style="text-align: center; margin-top: 20px;">
            <a href="{{ $link }}" class="button">📄 Lihat Detail Laporan</a>
        </p>
        <p style="margin-top: 20px; font-size: 14px; color: #6b7280;">
            Terima kasih atas laporan Anda. Kami senang bisa membantu!
        </p>
    </div>
    <div class="footer">
        <p>&copy; {{ date('Y') }} UCIC Facility Management. All rights reserved.</p>
    </div>
</body>
</html>