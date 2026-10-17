<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Informasi Akun</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #dc2626; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 8px 8px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <h1>❌ Informasi Akun</h1>
    </div>
    <div class="content">
        <p>Halo <strong>{{ $nama }}</strong>,</p>
        <p>Akun Anda di <strong>Sistem Informasi Manajemen Fasilitas UCIC</strong> belum dapat disetujui oleh RTK.</p>
        <p>Untuk informasi lebih lanjut, silakan hubungi RTK kampus secara langsung.</p>
        <p style="margin-top: 20px; font-size: 14px; color: #6b7280;">
            Terima kasih atas pengertiannya.
        </p>
    </div>
    <div class="footer">
        <p>&copy; {{ date('Y') }} UCIC Facility Management. All rights reserved.</p>
    </div>
</body>
</html>