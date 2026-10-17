<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Ijin Peminjaman - {{ $nomor_surat }}</title>

    {{-- Tailwind CSS & Font Inter (Hanya untuk UI Preview Browser) --}}
    @if (!isset($isPdf) || !$isPdf)
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
        <style>
            body { font-family: 'Inter', sans-serif; background-color: #fbf7f0; }
            .paper-shadow { box-shadow: 0 10px 25px -5px rgba(25, 19, 51, 0.08), 0 8px 10px -6px rgba(25, 19, 51, 0.04); }
        </style>
    @endif

    {{-- Style Khusus Kertas Dokumen / PDF Engine --}}
    <style>
        @page {
            margin: 25pt 35pt;
        }
        .document-body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #1a1a1a;
            background: #ffffff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table {
            border-bottom: 2px solid #1a1a1a;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        .header-text h1 {
            font-size: 13pt;
            font-weight: bold;
            margin: 0;
            text-align: center;
        }
        .header-text h2 {
            font-size: 13pt;
            font-weight: bold;
            margin: 0;
            text-align: center;
        }
        .header-text .address {
            font-size: 9pt;
            color: #333;
            text-align: center;
            margin-top: 2px;
        }
        .header-text .contact {
            font-size: 8.5pt;
            color: #333;
            text-align: center;
        }
        .title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 15px 0 10px 0;
        }
        .surat-meta p {
            margin: 2px 0;
        }
        .content p {
            margin: 6px 0;
            text-align: justify;
        }
        .field-table {
            margin: 6px 0 6px 20px;
            width: 95%;
        }
        .field-table td {
            vertical-align: top;
            padding: 2px 0;
        }
        .ttd-table {
            margin-top: 15px;
            width: 100%;
        }
        .ttd-table td {
            text-align: center;
            vertical-align: top;
        }
        .line {
            border-top: 1px solid #333;
            width: 75%;
            margin: 10px auto 2px auto;
        }
        .footer {
            margin-top: 25px;
            text-align: center;
            font-size: 8.5pt;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }
        /* Hide UI elements saat diprint langsung via browser */
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .paper-container { padding: 0 !important; margin: 0 !important; }
            .paper-shadow { box-shadow: none !important; }
        }
    </style>
</head>
<body class="{{ (!isset($isPdf) || !$isPdf) ? 'min-h-screen pb-12' : '' }}">

    <!-- 🌟 TOP BAR NAVIGATION (DISESUAIKAN DENGAN TEMA & PALET WARNA APLIKASI) -->
    @if (!isset($isPdf) || !$isPdf)
    <header class="no-print sticky top-0 z-50 bg-white border-b border-[#ecdfd0] shadow-sm">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            
            {{-- Sisi Kiri: Judul & Badge Status --}}
            <div class="flex items-center space-x-3">
                <button onclick="window.close()" 
                        class="p-2 rounded-xl text-[#7a7189] hover:text-[#191333] hover:bg-[#fbf7f0] border border-transparent hover:border-[#ecdfd0] transition" 
                        title="Tutup">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                <div class="h-5 w-px bg-[#ecdfd0]"></div>
                <div>
                    <h1 class="text-xs sm:text-sm font-bold text-[#2a2338] flex items-center gap-2">
                        <span>Surat Izin Peminjaman</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Disetujui
                        </span>
                    </h1>
                    <p class="text-[11px] text-[#7a7189]">{{ $nomor_surat }}</p>
                </div>
            </div>

            {{-- Sisi Kanan: Action Buttons Berdasar Style Aplikasi --}}
            <div class="flex items-center space-x-2 sm:space-x-3">
                {{-- Tombol Cetak (Secondary Style) --}}
                <button onclick="window.print()" 
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-[#7a7189] hover:text-[#191333] bg-[#fbf7f0] hover:bg-white border border-[#ecdfd0] transition shadow-sm">
                    <svg class="w-4 h-4 text-[#7a7189]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-4-0H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    <span>Cetak</span>
                </button>

                {{-- Tombol Download (Primary Style) --}}
                <a href="{{ route('surat.download', $peminjaman->id) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-[#191333] hover:bg-[#2f2559] transition shadow-sm active:scale-95">
                    <svg class="w-4 h-4 text-[#f2723b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    <span>Download PDF</span>
                </a>
            </div>

        </div>
    </header>
    @endif

    <!-- 📄 KERTAS DOKUMEN -->
    <div class="paper-container flex justify-center {{ (!isset($isPdf) || !$isPdf) ? 'pt-8 px-4' : '' }}">
        <div class="document-body w-full max-w-[210mm] {{ (!isset($isPdf) || !$isPdf) ? 'p-12 rounded-2xl paper-shadow border border-[#ecdfd0]' : '' }}">

            <!-- HEADER (TABLE BASED) -->
            <table class="header-table">
                <tr>
                    {{-- Logo Proporsional Tanpa Gepeng/Tertekan --}}
                    <td style="width: 15%; vertical-align: middle; text-align: left;">
                        @if(!empty($logo_base64))
                            <img src="{{ $logo_base64 }}" style="max-height: 70px; max-width: 100px; object-fit: contain; margin-bottom: 4px;">
                        @else
                            <img src="{{ public_path('images/logo.png') }}" style="max-height: 70px; max-width: 100px; object-fit: contain; margin-bottom: 4px;">
                        @endif
                    </td>
                    <td style="width: 85%; vertical-align: middle;" class="header-text">
                        <h1>BIRO KEMAHASISWAAN</h1>
                        <h2>UNIVERSITAS CATUR INSAN CENDEKIA (UCIC)</h2>
                        <div class="address">
                            Jl. Kesambi No. 202, Drajat, Kesambi, Kota Cirebon, Jawa Barat 45133
                        </div>
                        <div class="contact">
                            Telp. (0231) 200418 &nbsp;Fax. (0231) 242112 &nbsp;email : kemahasiswaan@cic.ac.id
                        </div>
                    </td>
                </tr>
            </table>

            <!-- TITLE -->
            <div class="title">SURAT IJIN PENGGUNAAN RUANGAN/FASILITAS KAMPUS</div>

            <!-- NOMOR & LAMPIRAN -->
            <div class="surat-meta">
                <p>Nomor &nbsp;&nbsp;&nbsp;&nbsp;: {{ $nomor_surat }}</p>
                <p>Lampiran &nbsp;:</p>
            </div>

            <!-- CONTENT -->
            <div class="content">
                <p>Kepada Yth.<br>
                BAAK<br>
                Universitas Catur Insan Cendekia (UCIC)<br>
                di tempat</p>

                <p style="margin-top: 10px;">
                    Dengan hormat, berdasarkan permohonan penggunaan ruangan/fasilitas kampus tertanggal <strong>{{ $tanggal_peminjaman }}</strong>,
                    maka melalui surat ini kami memberikan ijin kepada:
                </p>

                <table class="field-table">
                    <tr>
                        <td style="width: 200px;">Nama Penanggung Jawab</td>
                        <td style="width: 15px;">:</td>
                        <td><strong>{{ $penanggung_jawab }}</strong></td>
                    </tr>
                    <tr>
                        <td>Nama Kegiatan</td>
                        <td>:</td>
                        <td>{{ $keperluan }}</td>
                    </tr>
                </table>

                <p>untuk menggunakan fasilitas kampus berupa:</p>

                <table class="field-table">
                    @foreach ($items as $index => $item)
                        <tr>
                            @if (count($items) > 1)
                                <td style="width: 20px;">{{ $loop->iteration }}.</td>
                            @endif
                            <td style="width: 180px;">{{ ucfirst($item->fasilitas->kategori ?? 'Fasilitas') }}</td>
                            <td style="width: 15px;">:</td>
                            <td>
                                {{ $item->fasilitas->nama_fasilitas ?? 'Tidak diketahui' }}
                                @if ($item->jumlah_dipinjam > 1)
                                    ({{ $item->jumlah_dipinjam }} unit)
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>

                <table class="field-table">
                    <tr>
                        <td style="width: 200px;">Hari/Tanggal</td>
                        <td style="width: 15px;">:</td>
                        <td>{{ $hari_tanggal }}</td>
                    </tr>
                    <tr>
                        <td>Waktu (durasi)</td>
                        <td>:</td>
                        <td>{{ $waktu }}</td>
                    </tr>
                </table>

                <p style="margin-top: 10px;">
                    Demikianlah surat ijin ini dibuat untuk digunakan sebagaimana mestinya, atas perhatian dan kerjasamanya kami ucapkan terima kasih.
                </p>
            </div>

            <!-- TTD (TABLE BASED) -->
            <table class="ttd-table">
                <tr>
                    <td colspan="2" style="text-align: center; padding-bottom: 5px;">
                        Cirebon, {{ $tanggal_surat }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center; font-weight: bold; padding-bottom: 12px;">
                        Menyetujui,
                    </td>
                </tr>
                <tr>
                    <td style="width: 50%; text-align: center; vertical-align: top;">
                        <div style="font-weight: bold;">Kepala Biro Kemahasiswaan UCIC</div>
                        <div style="height: 70px; margin-top: 5px; text-align: center;">
                            @if(!empty($ttd_qr))
                                <img src="{{ $ttd_qr }}" style="max-height: 65px; max-width: 65px; object-fit: contain; display: inline-block;">
                            @endif
                        </div>
                        <div class="line"></div>
                        <div style="font-weight: bold; text-decoration: underline;">{{ $peminjaman->verifikasiBikemBy->nama ?? 'Chiang, Y. Darmawan, M.Psi., Psikolog.' }}</div>
                        <div style="font-size: 9pt;">{{ $peminjaman->verifikasiBikemBy?->nomor_induk ? 'NUPTK/NIDN. ' . $peminjaman->verifikasiBikemBy->nomor_induk : 'NUPTK. 2053765666131133' }}</div>
                    </td>
                    <td style="width: 50%; text-align: center; vertical-align: top;">
                        <div style="font-weight: bold;">Kepala General Affairs UCIC</div>
                        <div style="height: 70px; margin-top: 5px;">
                            &nbsp;
                        </div>
                        <div class="line"></div>
                        <div style="font-weight: bold; text-decoration: underline;">Willy Eka Sepian, M.Kom.</div>
                        <div style="font-size: 9pt;">NIDN. 0401099303</div>
                    </td>
                </tr>
            </table>

            <!-- FOOTER -->
            <div class="footer">
                Dokumen ini adalah bukti resmi peminjaman fasilitas kampus UCIC yang telah disetujui.
                <br>Dicetak pada: {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY HH:mm') }}
            </div>

        </div>
    </div>

</body>
</html>