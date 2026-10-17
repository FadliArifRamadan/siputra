# 📋 PROGRESS.md — Sistem Informasi Manajemen Fasilitas Kampus Terpadu (UCIC)

---

## 1. Overview

Sistem Informasi Manajemen Fasilitas Kampus Terpadu (UCIC) adalah aplikasi berbasis web untuk mengelola peminjaman fasilitas, pelaporan kerusakan, penugasan teknisi, dan rekapitulasi laporan secara terintegrasi. Sistem ini melayani tiga peran utama: **Pelapor** (mengajukan peminjaman dan laporan kerusakan), **Teknisi** (menerima penugasan dan menutup tiket perbaikan), serta **RTK** (verifikasi akun, manajemen fasilitas, disposisi teknisi, dan ekspor laporan PDF).

---
---

## 📌 Update Terakhir (21 Juli 2026)

**Status:** Production Ready dengan fitur tambahan

**Fitur Baru yang Ditambahkan:**
- ✅ **Manajemen Stok Real-Time** — validasi kuantitas terpakai vs `stok_total` pada rentang waktu
- ✅ **Riwayat Status (Status History)** — tabel `status_logs` polymorphic untuk semua model
- ✅ **Notifikasi In-App** — tersimpan di database dengan read/unread status + badge counter
- ✅ **Email Notifications** — untuk event penting (approve/reject, task assigned, ticket completed, booking approved)
- ✅ **SLA & Eskalasi Otomatis** — target penyelesaian berdasarkan urgensi, cron job `sla:check` setiap jam
- ✅ **Manajemen Role & Permission via UI** — CRUD role, assign role ke user
- ✅ **Dashboard Analytics (Chart.js)** — per role (RTK, Teknisi, Pelapor) dengan data real-time
- ✅ **Pencarian & Filter Lanjutan** — di semua DataTables (status, tanggal, kategori, urgensi)
- ✅ **Pengingat Otomatis (Reminder)** — notifikasi H-1 sebelum jadwal peminjaman/penanganan
- ✅ **Manajemen Referensi Lokasi** — tabel master lokasi dengan data 2 kampus (Kampus 1 & Kampus 2)
- ✅ **Kalender Ketersediaan Teknisi** — cek bentrok jadwal saat disposisi + real-time availability
- ✅ **Ekspor Laporan Multi-Format** — Excel (.xlsx) dan CSV
- ✅ **QR Code per Aset** — generate, download, scan untuk lapor kerusakan otomatis
- ✅ **Middleware & Route Fixes** — perbaikan redirect loop, flash messages, dan role-based layout

**Perbaikan Bug:**
- 🔧 Redirect loop pada login/registrasi (middleware `account.active` dan route `/dashboard`)
- 🔧 Flash message tidak muncul pada registrasi dan login
- 🔧 Error `Column 'created_at' ambiguous` di DataTables
- 🔧 Error `Too few arguments` di trait `LogsStatusChanges`
- 🔧 Halaman notifikasi menggunakan layout sesuai role user
- 🔧 Validasi form pengajuan dan peminjaman (hapus opsi "Lainnya")
## 2. Tech Stack

| Komponen          | Teknologi / Library             | Versi          |
| ----------------- | ------------------------------- | -------------- |
| Backend Framework | Laravel                         | 10.x           |
| PHP               | PHP                             | 8.1+           |
| Database          | MySQL                           | 8.0+           |
| Frontend Engine   | Blade + Tailwind CSS            | 3.x            |
| Build Tool        | Vite                            | -              |
| Authentication    | Laravel Breeze (Blade)          | -              |
| RBAC              | Spatie Laravel Permission       | ^5.0           |
| DataTables        | Yajra Laravel-Datatables-Oracle | ^10.0          |
| PDF Generator     | Barryvdh Laravel-DomPDF         | ^2.0           |
| Image Processing  | Intervention Image              | ^3.0           |
| Calendar          | FullCalendar.js                 | ^6.0 (NPM)     |
| Charts            | Chart.js                        | ^4.0 (NPM)     |
| Testing           | PHPUnit                         | Bawaan Laravel |

---

## 3. Arsitektur

Sistem mengikuti arsitektur **Model-View-Controller (MVC)** monolitik dengan pola sebagai berikut:

```
┌─────────────────────────────────────────────────────────────────┐
│                        Client (Browser)                        │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                      Routes (web.php / api.php)                │
│          Middleware: auth, account.active, role, permission    │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                        Controllers                             │
│  ┌──────────────┐ ┌──────────────┐ ┌────────────────────────┐ │
│  │   Pelapor    │ │     RTK      │ │       Teknisi          │ │
│  │  Controller  │ │  Controller  │ │      Controller        │ │
│  └──────────────┘ └──────────────┘ └────────────────────────┘ │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                   Service Layer / Form Requests                │
│  ┌──────────────────────────────────────────────────────────┐ │
│  │   BookingValidationService · RegisterPelaporRequest     │ │
│  │   StorePengajuanRequest · StorePeminjamanRequest        │ │
│  └──────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                       Eloquent Models                          │
│  ┌──────────┐ ┌──────────┐ ┌──────────────┐ ┌──────────────┐ │
│  │  User    │ │Fasilitas │ │PengajuanKerus│ │PenugasanTekn │ │
│  └──────────┘ └──────────┘ └──────────────┘ └──────────────┘ │
│  ┌──────────┐ ┌──────────────┐                               │
│  │BuktiPerb │ │PeminjamanFas │                               │
│  └──────────┘ └──────────────┘                               │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                         MySQL 8.0+                             │
└─────────────────────────────────────────────────────────────────┘
```

**Alur Data Utama:**
1. Pelapor mendaftar → status `pending` → RTK approve → aktif
2. Pelapor ajukan peminjaman → cek bentrok via `BookingValidationService` → status `pending` → RTK setujui/tolak
3. Pelapor laporkan kerusakan → upload foto (resize via Intervention Image v3) → status `pending`
4. RTK tugaskan teknisi → status laporan `diproses` → teknisi terima tugas
5. Teknisi selesaikan tugas → upload bukti + keterangan → status `selesai` (transaksi DB)
6. RTK ekspor rekapitulasi bulanan → PDF via DomPDF

---

## 4. Struktur Database / Skema Data

### Tabel: `users`

| Kolom                       | Tipe                                 | Constraint                  | Keterangan   |
| --------------------------- | ------------------------------------ | --------------------------- | ------------ |
| `id`                        | BIGINT UNSIGNED                      | PRIMARY KEY, AUTO_INCREMENT |              |
| `nomor_induk`               | VARCHAR(50)                          | UNIQUE, NOT NULL            | NIM/NIP/NIDN |
| `nama`                      | VARCHAR(255)                         | NOT NULL                    |              |
| `email`                     | VARCHAR(255)                         | UNIQUE, NOT NULL            |              |
| `password`                  | VARCHAR(255)                         | NOT NULL                    |              |
| `status_akun`               | ENUM('pending','active','suspended') | DEFAULT 'pending'           |              |
| `remember_token`            | VARCHAR(100)                         | NULLABLE                    |              |
| `created_at` / `updated_at` | TIMESTAMP                            | NULLABLE                    |              |
| `deleted_at`                | TIMESTAMP                            | NULLABLE                    | Soft Delete  |
| **Indeks**                  | `status_akun`                        |                             |              |

---

### Tabel: `fasilitas`

| Kolom                       | Tipe                                                    | Constraint                  | Keterangan                           |
| --------------------------- | ------------------------------------------------------- | --------------------------- | ------------------------------------ |
| `id`                        | BIGINT UNSIGNED                                         | PRIMARY KEY, AUTO_INCREMENT |                                      |
| `nama_fasilitas`            | VARCHAR(255)                                            | NOT NULL                    |                                      |
| `kategori`                  | ENUM('sarana','prasarana')                              | NOT NULL                    | sarana = barang, prasarana = ruangan |
| `lokasi_detail`             | VARCHAR(255)                                            | NOT NULL                    |                                      |
| `stok_total`                | INTEGER                                                 | DEFAULT 1                   |                                      |
| `status_kondisi`            | ENUM('baik','rusak_ringan','rusak_berat','maintenance') | DEFAULT 'baik'              |                                      |
| `created_at` / `updated_at` | TIMESTAMP                                               | NULLABLE                    |                                      |
| `deleted_at`                | TIMESTAMP                                               | NULLABLE                    | Soft Delete                          |
| **Indeks**                  | `kategori`, `status_kondisi`                            |                             |                                      |

---

### Tabel: `pengajuan_kerusakan`

| Kolom                       | Tipe                                           | Constraint                                 | Keterangan      |
| --------------------------- | ---------------------------------------------- | ------------------------------------------ | --------------- |
| `id`                        | BIGINT UNSIGNED                                | PRIMARY KEY, AUTO_INCREMENT                |                 |
| `pelapor_id`                | BIGINT UNSIGNED                                | FOREIGN KEY → `users.id` ON DELETE CASCADE |                 |
| `jenis_kerusakan`           | VARCHAR(255)                                   | NOT NULL                                   |                 |
| `lokasi_detail`             | VARCHAR(255)                                   | NOT NULL                                   |                 |
| `deskripsi`                 | TEXT                                           | NOT NULL                                   |                 |
| `foto_kerusakan`            | VARCHAR(255)                                   | NOT NULL                                   | Path di storage |
| `tingkat_urgensi`           | ENUM('rendah','sedang','tinggi')               | DEFAULT 'sedang'                           |                 |
| `status_laporan`            | ENUM('pending','ditolak','diproses','selesai') | DEFAULT 'pending'                          |                 |
| `catatan_rtk`               | TEXT                                           | NULLABLE                                   |                 |
| `created_at` / `updated_at` | TIMESTAMP                                      | NULLABLE                                   |                 |
| **Indeks**                  | `status_laporan`, `tingkat_urgensi`            |                                            |                 |

---

### Tabel: `penugasan_teknisi`

| Kolom                       | Tipe                | Constraint                                               | Keterangan |
| --------------------------- | ------------------- | -------------------------------------------------------- | ---------- |
| `id`                        | BIGINT UNSIGNED     | PRIMARY KEY, AUTO_INCREMENT                              |            |
| `pengajuan_id`              | BIGINT UNSIGNED     | FOREIGN KEY → `pengajuan_kerusakan.id` ON DELETE CASCADE |            |
| `teknisi_id`                | BIGINT UNSIGNED     | FOREIGN KEY → `users.id` ON DELETE CASCADE               |            |
| `jadwal_penanganan`         | DATETIME            | NOT NULL                                                 |            |
| `instruksi_kerja`           | TEXT                | NULLABLE                                                 |            |
| `created_at` / `updated_at` | TIMESTAMP           | NULLABLE                                                 |            |
| **Indeks**                  | `jadwal_penanganan` |                                                          |            |

---

### Tabel: `bukti_perbaikan`

| Kolom                | Tipe                                    | Constraint                                             | Keterangan      |
| -------------------- | --------------------------------------- | ------------------------------------------------------ | --------------- |
| `id`                 | BIGINT UNSIGNED                         | PRIMARY KEY, AUTO_INCREMENT                            |                 |
| `penugasan_id`       | BIGINT UNSIGNED                         | FOREIGN KEY → `penugasan_teknisi.id` ON DELETE CASCADE |                 |
| `foto_selesai`       | VARCHAR(255)                            | NOT NULL                                               | Path di storage |
| `keterangan_selesai` | TEXT                                    | NOT NULL                                               |                 |
| `tanggal_selesai`    | TIMESTAMP                               | DEFAULT CURRENT_TIMESTAMP                              |                 |
| **Catatan**          | Model menggunakan `$timestamps = false` |                                                        |                 |

---

### Tabel: `peminjaman_fasilitas`

| Kolom                       | Tipe                                                       | Constraint                                     | Keterangan |
| --------------------------- | ---------------------------------------------------------- | ---------------------------------------------- | ---------- |
| `id`                        | BIGINT UNSIGNED                                            | PRIMARY KEY, AUTO_INCREMENT                    |            |
| `peminjam_id`               | BIGINT UNSIGNED                                            | FOREIGN KEY → `users.id` ON DELETE CASCADE     |            |
| `fasilitas_id`              | BIGINT UNSIGNED                                            | FOREIGN KEY → `fasilitas.id` ON DELETE CASCADE |            |
| `jumlah_dipinjam`           | INTEGER                                                    | DEFAULT 1                                      |            |
| `waktu_mulai`               | DATETIME                                                   | NOT NULL                                       |            |
| `waktu_selesai`             | DATETIME                                                   | NOT NULL                                       |            |
| `keperluan`                 | TEXT                                                       | NOT NULL                                       |            |
| `status_pinjam`             | ENUM('pending','disetujui','ditolak','berjalan','selesai') | DEFAULT 'pending'                              |            |
| `created_at` / `updated_at` | TIMESTAMP                                                  | NULLABLE                                       |            |
| **Indeks**                  | `(waktu_mulai, waktu_selesai)` (compound), `status_pinjam` |                                                |            |

---

## 5. API Endpoints / Modul Fungsi

### 5.1 Public Routes

| Method | Path                     | Input | Output                                                                      | Tujuan                                                          |
| ------ | ------------------------ | ----- | --------------------------------------------------------------------------- | --------------------------------------------------------------- |
| GET    | `/`                      | -     | View `welcome.blade.php`                                                    | Landing page dengan FullCalendar                                |
| GET    | `/api/peminjaman-events` | -     | JSON `[{id, title, start, end, fasilitas_id, peminjam, keperluan, status}]` | Sumber data kalender (hanya `status_pinjam` disetujui/berjalan) |

### 5.2 Authentication (Breeze + Custom)

| Method | Path                | Input                                                       | Output                           | Tujuan                                |
| ------ | ------------------- | ----------------------------------------------------------- | -------------------------------- | ------------------------------------- |
| GET    | `/register-pelapor` | -                                                           | View `auth.register-pelapor`     | Form registrasi pelapor               |
| POST   | `/register-pelapor` | `nomor_induk, nama, email, password, password_confirmation` | Redirect ke `/pelapor/dashboard` | Registrasi pelapor (status `pending`) |

### 5.3 Pelapor Routes (Middleware: `auth`, `account.active`, `role:pelapor`)

| Method | Path                                    | Input                                                                        | Output                           | Tujuan                                      |
| ------ | --------------------------------------- | ---------------------------------------------------------------------------- | -------------------------------- | ------------------------------------------- |
| GET    | `/pelapor/dashboard`                    | -                                                                            | View `pelapor.dashboard`         | Dashboard pelapor                           |
| GET    | `/pelapor/pengajuan`                    | -                                                                            | View `pelapor.pengajuan.index`   | Daftar laporan kerusakan                    |
| GET    | `/pelapor/pengajuan/create`             | -                                                                            | View `pelapor.pengajuan.create`  | Form lapor kerusakan                        |
| POST   | `/pelapor/pengajuan`                    | `jenis_kerusakan, lokasi_detail, deskripsi, foto_kerusakan, tingkat_urgensi` | Redirect                         | Simpan laporan (resize foto)                |
| GET    | `/pelapor/pengajuan/{pengajuan}`        | -                                                                            | View `pelapor.pengajuan.show`    | Detail laporan                              |
| GET    | `/pelapor/pengajuan/{pengajuan}/edit`   | -                                                                            | View `pelapor.pengajuan.edit`    | Form edit (hanya pending)                   |
| PUT    | `/pelapor/pengajuan/{pengajuan}`        | `jenis_kerusakan, lokasi_detail, deskripsi, foto_kerusakan, tingkat_urgensi` | Redirect                         | Update laporan (hanya pending)              |
| DELETE | `/pelapor/pengajuan/{pengajuan}`        | -                                                                            | Redirect                         | Hapus laporan (hanya pending)               |
| GET    | `/pelapor/peminjaman`                   | -                                                                            | View `pelapor.peminjaman.index`  | Daftar peminjaman                           |
| GET    | `/pelapor/peminjaman/create`            | -                                                                            | View `pelapor.peminjaman.create` | Form peminjaman                             |
| POST   | `/pelapor/peminjaman`                   | `fasilitas_id, jumlah_dipinjam, waktu_mulai, waktu_selesai, keperluan`       | Redirect                         | Simpan peminjaman (cek bentrok via service) |
| GET    | `/pelapor/peminjaman/{peminjaman}`      | -                                                                            | View `pelapor.peminjaman.show`   | Detail peminjaman                           |
| GET    | `/pelapor/peminjaman/{peminjaman}/edit` | -                                                                            | View `pelapor.peminjaman.edit`   | Form edit (hanya pending)                   |
| PUT    | `/pelapor/peminjaman/{peminjaman}`      | `fasilitas_id, jumlah_dipinjam, waktu_mulai, waktu_selesai, keperluan`       | Redirect                         | Update peminjaman (hanya pending)           |
| DELETE | `/pelapor/peminjaman/{peminjaman}`      | -                                                                            | Redirect                         | Hapus peminjaman (hanya pending)            |

### 5.4 RTK Routes (Middleware: `auth`, `account.active`, `role:rtk`)

| Method | Path                              | Input                                                                 | Output                                  | Tujuan                               |
| ------ | --------------------------------- | --------------------------------------------------------------------- | --------------------------------------- | ------------------------------------ |
| GET    | `/rtk/dashboard`                  | -                                                                     | View `rtk.dashboard`                    | Dashboard RTK                        |
| GET    | `/rtk/users`                      | -                                                                     | View `rtk.users.index` (DataTables)     | Manajemen user                       |
| GET    | `/rtk/users/data`                 | -                                                                     | JSON DataTables                         | Data user server-side                |
| PATCH  | `/rtk/users/{user}/approve`       | -                                                                     | Redirect                                | Aktifkan akun (status → active)      |
| PATCH  | `/rtk/users/{user}/reject`        | -                                                                     | Redirect                                | Tolak akun (status → suspended)      |
| PATCH  | `/rtk/users/{user}/suspend`       | -                                                                     | Redirect                                | Tangguhkan akun                      |
| PATCH  | `/rtk/users/{user}/activate`      | -                                                                     | Redirect                                | Aktifkan kembali akun                |
| GET    | `/rtk/fasilitas`                  | -                                                                     | View `rtk.fasilitas.index` (DataTables) | Manajemen fasilitas                  |
| GET    | `/rtk/fasilitas/create`           | -                                                                     | View `rtk.fasilitas.create`             | Form tambah fasilitas                |
| POST   | `/rtk/fasilitas`                  | `nama_fasilitas, kategori, lokasi_detail, stok_total, status_kondisi` | Redirect                                | Simpan fasilitas                     |
| GET    | `/rtk/fasilitas/{fasilitas}`      | -                                                                     | View `rtk.fasilitas.show`               | Detail fasilitas                     |
| GET    | `/rtk/fasilitas/{fasilitas}/edit` | -                                                                     | View `rtk.fasilitas.edit`               | Form edit fasilitas                  |
| PUT    | `/rtk/fasilitas/{fasilitas}`      | `nama_fasilitas, kategori, lokasi_detail, stok_total, status_kondisi` | Redirect                                | Update fasilitas                     |
| DELETE | `/rtk/fasilitas/{fasilitas}`      | -                                                                     | Redirect                                | Hapus (cek relasi peminjaman aktif)  |
| GET    | `/rtk/penugasan`                  | -                                                                     | View `rtk.penugasan.index` (DataTables) | Manajemen penugasan                  |
| GET    | `/rtk/penugasan/create`           | -                                                                     | View `rtk.penugasan.create`             | Form tugaskan teknisi                |
| POST   | `/rtk/penugasan`                  | `pengajuan_id, teknisi_id, jadwal_penanganan, instruksi_kerja`        | Redirect                                | Simpan penugasan (status → diproses) |
| GET    | `/rtk/penugasan/{penugasan}/edit` | -                                                                     | View `rtk.penugasan.edit`               | Form edit penugasan                  |
| PUT    | `/rtk/penugasan/{penugasan}`      | `teknisi_id, jadwal_penanganan, instruksi_kerja`                      | Redirect                                | Update penugasan                     |
| DELETE | `/rtk/penugasan/{penugasan}`      | -                                                                     | Redirect                                | Batalkan (status → pending)          |
| GET    | `/rtk/laporan/rekapitulasi`       | `bulan, tahun` (query)                                                | View `rtk.laporan.rekapitulasi`         | Tampilkan rekapitulasi bulanan       |
| GET    | `/rtk/laporan/export-pdf`         | `bulan, tahun` (query)                                                | File PDF                                | Unduh rekapitulasi PDF               |

### 5.5 Teknisi Routes (Middleware: `auth`, `account.active`, `role:teknisi`)

| Method | Path                               | Input                              | Output                     | Tujuan                                      |
| ------ | ---------------------------------- | ---------------------------------- | -------------------------- | ------------------------------------------- |
| GET    | `/teknisi/dashboard`               | -                                  | View `teknisi.dashboard`   | Dashboard teknisi (mobile-first)            |
| GET    | `/teknisi/tugas`                   | -                                  | View `teknisi.dashboard`   | Daftar tugas (redirect ke dashboard)        |
| GET    | `/teknisi/tugas/{penugasan}`       | -                                  | View `teknisi.tugas.show`  | Detail tugas                                |
| GET    | `/teknisi/tugas/{penugasan}/close` | -                                  | View `teknisi.tugas.close` | Form selesaikan tugas                       |
| POST   | `/teknisi/tugas/{penugasan}/close` | `foto_selesai, keterangan_selesai` | Redirect                   | Simpan bukti & tutup tugas (DB transaction) |

---

## 6. Fitur yang Sudah Selesai

- ✅ Landing page publik dengan FullCalendar.js (menampilkan jadwal peminjaman)
- ✅ Registrasi mandiri pelapor (validasi unik `nomor_induk` dan `email`, status `pending`)
- ✅ Verifikasi akun oleh RTK (Approve/Reject via DataTables server-side)
- ✅ CRUD pengajuan kerusakan (pelapor) dengan upload & resize foto (Intervention Image v3)
- ✅ CRUD peminjaman fasilitas (pelapor) dengan validasi bentrok jadwal (BookingValidationService)
- ✅ CRUD fasilitas (RTK) dengan DataTables server-side dan soft delete
- ✅ Disposisi teknisi (RTK) — menugaskan teknisi ke laporan kerusakan
- ✅ Dashboard teknisi mobile-first dengan daftar tugas (aktif, menunggu, selesai)
- ✅ Close ticket teknisi (upload bukti perbaikan + update status dalam transaksi DB)
- ✅ Rekapitulasi bulanan dengan filter bulan/tahun (RTK)
- ✅ Ekspor PDF rekapitulasi (Barryvdh DomPDF)
- ✅ RBAC dengan Spatie Permission (3 role: rtk, pelapor, teknisi)
- ✅ Middleware `account.active` untuk memblokir user non-aktif
- ✅ Unit test untuk RegisterPelaporRequest dan StorePengajuanKerusakanRequest
- ✅ Feature test untuk booking bentrok dan close ticket teknisi
- ✅ Eager Loading (`with()`) pada semua Controller untuk mencegah N+1 query
- ### Fitur Tambahan (Post-Sprint)
- ✅ **Manajemen Stok Real-Time** — validasi kuantitas terpakai vs `stok_total` pada rentang waktu
- ✅ **Riwayat Status (Status History)** — tabel `status_logs` polymorphic untuk semua model (User, Fasilitas, PengajuanKerusakan, PeminjamanFasilitas, PenugasanTeknisi)
- ✅ **Notifikasi In-App** — tabel `notifications` dengan read/unread, badge counter di navbar, mark all as read
- ✅ **Email Notifications** — untuk event: account_approved, account_rejected, task_assigned, ticket_completed, booking_approved
- ✅ **SLA & Eskalasi Otomatis** — target: tinggi=24jam, sedang=72jam, rendah=168jam; cron job `sla:check` hourly
- ✅ **Manajemen Role & Permission via UI** — CRUD role, assign/remove role ke user, protected role 'rtk'
- ✅ **Dashboard Analytics (Chart.js)** — RTK (line, pie, bar charts + widget SLA), Teknisi (statistik + prioritas tugas), Pelapor (ringkasan + timeline)
- ✅ **Pencarian & Filter Lanjutan** — filter status, tanggal, kategori, urgensi di semua DataTables
- ✅ **Pengingat Otomatis (Reminder)** — cron job `reminders:send` daily at 08:00 & 20:00
- ✅ **Manajemen Referensi Lokasi** — tabel `lokasi` dengan data 2 kampus, dropdown di form pengajuan
- ✅ **Kalender Ketersediaan Teknisi** — cek bentrok via `BookingValidationService`, real-time availability di form disposisi
- ✅ **Ekspor Laporan Multi-Format** — Excel (.xlsx) dan CSV via Maatwebsite Excel
- ✅ **QR Code per Aset** — generate, download SVG, scan untuk lapor kerusakan otomatis

---

## 7. Fitur yang Belum / Diketahui Bermasalah


- ⚠️ **Belum ada notifikasi real-time** — semua notifikasi masih menggunakan session flash, belum menggunakan WebSocket atau email.
- ## 7. Fitur yang Belum / Diketahui Bermasalah

| #   | Fitur                   | Status  | Keterangan                                    |
| --- | ----------------------- | ------- | --------------------------------------------- |
| 1   | Multi-level Approval    | ⏸️ TUNDA | Opsional, tergantung kebijakan kampus         |
| 2   | Rating/Feedback Pelapor | ❌ SKIP  | Tidak sesuai untuk lingkungan internal kampus |
| 3   | PWA untuk Teknisi       | ❌ SKIP  | Diluar scope project saat ini                 |
| 4   | Peta Interaktif Lokasi  | ❌ SKIP  | Diluar scope project saat ini                 |
| 5   | Integrasi SSO           | ❌ SKIP  | Diluar scope project saat ini                 |
| 6   | Preventive Maintenance  | ❌ SKIP  | Diluar scope project saat ini                 |
| 7   | Gamifikasi/Leaderboard  | ❌ SKIP  | Diluar scope project saat ini                 |

---

```
8.1 Environment Variables:
CACHE_DRIVER=redis

# Mail (gunakan Mailtrap untuk development)
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@ucic.ac.id
MAIL_FROM_NAME="UCIC Facility Management"
### 8.3 Akun Default dari Seeder

| Role    | Email              | Password    |
| ------- | ------------------ | ----------- |
| RTK     | rtk@ucic.ac.id     | password123 |
| Pelapor | pelapor@ucic.ac.id | password123 |
| Teknisi | teknisi@ucic.ac.id | password123 |

---

## 9. Catatan Teknis Penting

1. **Intervention Image v3** — Menggunakan API `ImageManager` dengan driver GD, **bukan** sintaks statis `Image::make()` dari v2.
2. **Middleware Registration** — Semua middleware (termasuk Spatie dan custom `account.active`) didaftarkan di `app/Http/Kernel.php` properti `$middlewareAliases`, **bukan** `bootstrap/app.php` (pola Laravel 11 ke atas).
3. **Booking Conflict Logic** — Menggunakan teorema interval $[S_1, E_1] \cap [S_2, E_2] \neq \emptyset$ dengan kondisi `waktu_mulai < waktu_selesai_baru AND waktu_selesai > waktu_mulai_baru`.
4. **Bukti Perbaikan** — Model `BuktiPerbaikan` menggunakan `$timestamps = false` karena kolom `tanggal_selesai` sudah merepresentasikan waktu kejadian; tidak ada `created_at`/`updated_at` di migration.
5. **DB Transaction Close Ticket** — Penyimpanan bukti perbaikan dan update status laporan dilakukan dalam satu `DB::transaction()` untuk menjaga konsistensi data.
6. **Soft Delete** — Hanya tabel `users` dan `fasilitas` yang menggunakan `SoftDeletes`; tabel lain tidak memiliki soft delete sesuai Kamus Data.
7. **Foto Upload & Resize** — Semua foto (kerusakan dan bukti perbaikan) di-resize ke maksimal 800x600 pixel dengan kualitas 80% JPEG untuk menghemat storage.
8. **Yajra Datatables** — Menggunakan server-side processing dengan `DataTable` class dan route khusus `/data` untuk setiap resource.
9. **Eager Loading Audit** — Semua Controller yang menampilkan data relasional telah dilengkapi dengan `with()` untuk mencegah N+1 query.
10. **Laravel 10 vs 11** — Proyek ini secara eksplisit menggunakan Laravel 10; jangan menggunakan sintaks registrasi middleware via `bootstrap/app.php` yang hanya valid di Laravel 11+.
11. **SLA Logic** — Target selesai berdasarkan urgensi: `tinggi` = 24 jam, `sedang` = 72 jam, `rendah` = 168 jam. Cron job `sla:check` berjalan setiap jam.
12. **Cache** — Menggunakan `CACHE_DRIVER=array` untuk development. Untuk production, gunakan `redis` karena mendukung tagging.
13. **Soft Delete** — Semua tabel transaksional (`pengajuan_kerusakan`, `peminjaman_fasilitas`, `penugasan_teknisi`, `bukti_perbaikan`) sekarang menggunakan SoftDeletes.
14. **QR Code** — URL mengarah ke `pelapor.pengajuan.create?fasilitas_id={id}`. Scan QR akan otomatis mengisi fasilitas di form laporan.
15. **Notifikasi** — Menggunakan tabel `notifications` dengan read/unread status. Badge di navbar update via AJAX setiap 30 detik.
16. **Dashboard API** — Menggunakan route `web.php` dengan middleware `auth` (session-based), bukan `auth:sanctum`.
17. **Role-based Layout** — Halaman notifikasi menggunakan layout dinamis berdasarkan role user.