<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Rekapitulasi Bulanan - UCIC SIM-Fasilitas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #2a2338;
            background: #ffffff;
            padding: 24px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #191333;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .header h1 {
            font-size: 16px;
            color: #191333;
            font-weight: bold;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }
        .header h3 {
            font-size: 12px;
            font-weight: bold;
            color: #f2723b;
            text-transform: uppercase;
        }
        .header .periode {
            font-size: 11px;
            color: #7a7189;
            margin-top: 4px;
            font-weight: bold;
        }
        
        /* Layout Grid Stat Box (Support Dompdf table format) */
        .stats-table {
            width: 100%;
            margin-bottom: 18px;
            border-spacing: 6px;
            border-collapse: separate;
        }
        .stat-card {
            background: #fbf7f0;
            border: 1px solid #ecdfd0;
            border-radius: 6px;
            padding: 8px;
            text-align: center;
        }
        .stat-card .number {
            font-size: 15px;
            font-weight: bold;
            color: #191333;
        }
        .stat-card .number.highlight {
            color: #f2723b;
        }
        .stat-card .label {
            font-size: 8.5px;
            color: #7a7189;
            text-transform: uppercase;
            font-weight: bold;
            margin-top: 2px;
        }

        .section {
            margin-top: 14px;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #191333;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #ecdfd0;
            text-transform: uppercase;
        }
        
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table.data-table th {
            background: #fbf7f0;
            color: #191333;
            padding: 6px 5px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            border: 1px solid #ecdfd0;
            text-transform: uppercase;
        }
        table.data-table td {
            padding: 5px;
            border: 1px solid #ecdfd0;
            font-size: 9px;
            color: #2a2338;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        .footer {
            margin-top: 24px;
            padding-top: 10px;
            border-top: 1px solid #ecdfd0;
            font-size: 8.5px;
            color: #7a7189;
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            text-transform: capitalize;
        }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-ditolak { background: #fee2e2; color: #991b1b; }
        .badge-diproses { background: #e0f2fe; color: #0369a1; }
        .badge-selesai { background: #d1fae5; color: #065f46; }
        .badge-disetujui { background: #d1fae5; color: #065f46; }
        .badge-berjalan { background: #e0e7ff; color: #3730a3; }

        .empty-row td {
            text-align: center;
            color: #7a7189;
            font-style: italic;
            padding: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>UCIC - SISTEM INFORMASI MANAJEMEN FASILITAS</h1>
        <h3>REKAPITULASI BULANAN</h3>
        <div class="periode">Periode: {{ $namaBulan }}</div>
    </div>

    <!-- Ringkasan Statistik -->
    <table class="stats-table">
        <tr>
            <td class="stat-card" width="16%">
                <div class="number">{{ $statistik['total_laporan'] }}</div>
                <div class="label">Total Laporan</div>
            </td>
            <td class="stat-card" width="16%">
                <div class="number">{{ $statistik['total_penugasan'] }}</div>
                <div class="label">Total Penugasan</div>
            </td>
            <td class="stat-card" width="16%">
                <div class="number">{{ $statistik['total_peminjaman'] }}</div>
                <div class="label">Total Peminjaman</div>
            </td>
            <td class="stat-card" width="16%">
                <div class="number highlight">{{ $statistik['laporan_selesai'] }}</div>
                <div class="label">Selesai</div>
            </td>
            <td class="stat-card" width="16%">
                <div class="number">{{ $statistik['laporan_diproses'] }}</div>
                <div class="label">Diproses</div>
            </td>
            <td class="stat-card" width="16%">
                <div class="number">{{ $statistik['laporan_pending'] }}</div>
                <div class="label">Pending</div>
            </td>
        </tr>
    </table>

    <!-- Laporan Kerusakan -->
    <div class="section">
        <div class="section-title">Laporan Kerusakan</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th width="20%">Pelapor</th>
                    <th width="25%">Jenis Kerusakan</th>
                    <th width="25%">Lokasi</th>
                    <th width="10%" class="text-center">Status</th>
                    <th width="15%" class="text-center">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($laporanKerusakan as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $item->pelapor->nama ?? '-' }}</td>
                        <td>{{ $item->jenis_kerusakan }}</td>
                        <td>{{ $item->lokasi_detail }}</td>
                        <td class="text-center">
                            <span class="badge badge-{{ $item->status_laporan }}">
                                {{ ucfirst($item->status_laporan) }}
                            </span>
                        </td>
                        <td class="text-center">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr class="empty-row">
                        <td colspan="6">Tidak ada data laporan kerusakan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Penugasan Teknisi -->
    <div class="section">
        <div class="section-title"> Penugasan Teknisi</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th width="35%">Jenis Kerusakan</th>
                    <th width="25%">Teknisi</th>
                    <th width="20%" class="text-center">Jadwal Penanganan</th>
                    <th width="15%" class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($penugasan as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $item->pengajuanKerusakan->jenis_kerusakan ?? '-' }}</td>
                        <td>{{ $item->teknisi->nama ?? '-' }}</td>
                        <td class="text-center">{{ $item->jadwal_penanganan->format('d/m/Y H:i') }}</td>
                        <td class="text-center">
                            <span class="badge badge-{{ $item->pengajuanKerusakan->status_laporan ?? 'pending' }}">
                                {{ ucfirst($item->pengajuanKerusakan->status_laporan ?? '-') }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row">
                        <td colspan="5">Tidak ada data penugasan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Peminjaman Fasilitas -->
    <div class="section">
        <div class="section-title">Peminjaman Fasilitas</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th width="22%">Fasilitas</th>
                    <th width="18%">Peminjam</th>
                    <th width="8%" class="text-center">Jumlah</th>
                    <th width="18%" class="text-center">Waktu Mulai</th>
                    <th width="18%" class="text-center">Waktu Selesai</th>
                    <th width="11%" class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($peminjaman as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>
                            @if($item->items && $item->items->count() > 0)
                                @foreach($item->items as $detailItem)
                                    <div>• {{ $detailItem->fasilitas->nama_fasilitas ?? 'Fasilitas' }} ({{ $detailItem->jumlah_dipinjam }} unit)</div>
                                @endforeach
                            @else
                                {{ $item->fasilitas->nama_fasilitas ?? '-' }}
                            @endif
                        </td>
                        <td>{{ $item->peminjam->nama ?? '-' }}</td>
                        <td class="text-center">{{ $item->items && $item->items->isNotEmpty() ? $item->items->sum('jumlah_dipinjam') : ($item->jumlah_dipinjam ?? 1) }}</td>
                        <td class="text-center">{{ $item->waktu_mulai->format('d/m/Y H:i') }}</td>
                        <td class="text-center">{{ $item->waktu_selesai->format('d/m/Y H:i') }}</td>
                        <td class="text-center">
                            <span class="badge badge-{{ $item->status_pinjam }}">
                                {{ ucfirst($item->status_pinjam) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row">
                        <td colspan="7">Tidak ada data peminjaman</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Dicetak pada: {{ Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY HH:mm') }} WIB</p>
        <p>Dokumen ini adalah output resmi dari Sistem Informasi Manajemen Fasilitas UCIC</p>
    </div>
</body>
</html>