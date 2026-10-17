ucic/
├── app/
│ ├── Console/
│ │ ├── Commands/
│ │ │ ├── CheckSlaCommand.php
│ │ │ └── SendRemindersCommand.php
│ │ └── Kernel.php
│ ├── DataTables/
│ │ ├── FasilitasDataTable.php
│ │ ├── PeminjamanDataTable.php
│ │ ├── PenugasanDataTable.php
│ │ └── UserDataTable.php
│ ├── Exceptions/
│ │ └── Handler.php
│ ├── Exports/
│ │ ├── LaporanKerusakanExport.php
│ │ ├── PeminjamanExport.php
│ │ ├── PenugasanExport.php
│ │ └── RekapitulasiMultiSheetExport.php
│ ├── Http/
│ │ ├── Controllers/
│ │ │ ├── Admin/
│ │ │ │ ├── FasilitasController.php
│ │ │ │ ├── LaporanController.php
│ │ │ │ ├── PeminjamanController.php
│ │ │ │ ├── PenugasanController.php
│ │ │ │ ├── RoleController.php
│ │ │ │ └── UserController.php
│ │ │ ├── Api/
│ │ │ │ ├── DashboardController.php
│ │ │ │ ├── PeminjamanEventController.php
│ │ │ │ └── TeknisiScheduleController.php
│ │ │ ├── Auth/
│ │ │ │ ├── AuthenticatedSessionController.php
│ │ │ │ ├── RegisterPelaporController.php
│ │ │ │ └── RegisteredUserController.php
│ │ │ ├── NotificationController.php
│ │ │ ├── PelaporController.php
│ │ │ ├── PengajuanKerusakanController.php
│ │ │ ├── PeminjamanFasilitasController.php
│ │ │ ├── ProfileController.php
│ │ │ ├── RtkController.php
│ │ │ └── TeknisiController.php
│ │ ├── Middleware/
│ │ │ ├── Authenticate.php
│ │ │ ├── EnsureAccountIsActive.php
│ │ │ ├── EnsureSuperAdmin.php
│ │ │ ├── RedirectIfAuthenticated.php
│ │ │ └── ... (middleware bawaan Laravel lainnya)
│ │ └── Kernel.php
│ ├── Mail/
│ │ ├── AccountApprovedMail.php
│ │ ├── AccountRejectedMail.php
│ │ ├── BookingApprovedMail.php
│ │ ├── TaskAssignedMail.php
│ │ └── TicketCompletedMail.php
│ ├── Models/
│ │ ├── BuktiPerbaikan.php
│ │ ├── Fasilitas.php
│ │ ├── Lokasi.php
│ │ ├── Notification.php
│ │ ├── PeminjamanFasilitas.php
│ │ ├── PenugasanTeknisi.php
│ │ ├── PengajuanKerusakan.php
│ │ ├── StatusLog.php
│ │ └── User.php
│ ├── Providers/
│ │ └── ... (provider bawaan Laravel)
│ ├── Services/
│ │ ├── Dashboard/
│ │ │ ├── BaseDashboardService.php
│ │ │ ├── PelaporDashboardService.php
│ │ │ ├── RtkDashboardService.php
│ │ │ └── TeknisiDashboardService.php
│ │ ├── BookingValidationService.php
│ │ ├── NotificationService.php
│ │ ├── QrCodeService.php
│ │ ├── ReminderService.php
│ │ └── SlaService.php
│ └── Traits/
│ ├── DataTableFilterTrait.php
│ ├── HasDashboardCache.php
│ ├── HasStatusLogs.php
│ └── LogsStatusChanges.php
│
├── bootstrap/
│ └── app.php
│
├── config/
│ ├── app.php
│ ├── auth.php
│ ├── database.php
│ ├── datatables.php
│ ├── dompdf.php
│ ├── filesystems.php
│ ├── permission.php
│ ├── session.php
│ └── ... (konfigurasi lainnya)
│
├── database/
│ ├── factories/
│ │ ├── FasilitasFactory.php
│ │ ├── PeminjamanFasilitasFactory.php
│ │ ├── PengajuanKerusakanFactory.php
│ │ └── UserFactory.php
│ ├── migrations/
│ │ ├── xxxx_xx_xx_xxxxxx_create_users_table.php
│ │ ├── xxxx_xx_xx_xxxxxx_create_fasilitas_table.php
│ │ ├── xxxx_xx_xx_xxxxxx_create_pengajuan_kerusakan_table.php
│ │ ├── xxxx_xx_xx_xxxxxx_create_penugasan_teknisi_table.php
│ │ ├── xxxx_xx_xx_xxxxxx_create_bukti_perbaikan_table.php
│ │ ├── xxxx_xx_xx_xxxxxx_create_peminjaman_fasilitas_table.php
│ │ ├── xxxx_xx_xx_xxxxxx_create_lokasi_table.php
│ │ ├── xxxx_xx_xx_xxxxxx_add_fasilitas_id_to_pengajuan_kerusakan.php
│ │ ├── xxxx_xx_xx_xxxxxx_add_lokasi_id_to_fasilitas_and_pengajuan.php
│ │ ├── xxxx_xx_xx_xxxxxx_create_status_logs_table.php
│ │ ├── xxxx_xx_xx_xxxxxx_create_notifications_table.php
│ │ ├── xxxx_xx_xx_xxxxxx_add_sla_columns_to_pengajuan_kerusakan.php
│ │ ├── xxxx_xx_xx_xxxxxx_add_soft_deletes_to_transactional_tables.php
│ │ └── ... (migration Spatie Permission)
│ ├── seeders/
│ │ ├── DatabaseSeeder.php
│ │ ├── LokasiSeeder.php
│ │ ├── QrCodeSeeder.php
│ │ └── UserSeeder.php
│ └── .gitkeep
│
├── public/
│ ├── index.php
│ ├── .htaccess
│ └── storage/ (symlink ke storage/app/public)
│
├── resources/
│ ├── css/
│ │ ├── app.css
│ │ └── teknisi.css
│ ├── js/
│ │ ├── app.js
│ │ └── bootstrap.js
│ ├── views/
│ │ ├── auth/
│ │ │ ├── register-pelapor.blade.php
│ │ │ ├── login.blade.php
│ │ │ └── ... (Breeze default)
│ │ ├── components/
│ │ │ └── status-timeline.blade.php
│ │ ├── emails/
│ │ │ ├── account-approved.blade.php
│ │ │ ├── account-rejected.blade.php
│ │ │ ├── booking-approved.blade.php
│ │ │ ├── task-assigned.blade.php
│ │ │ └── ticket-completed.blade.php
│ │ ├── layouts/
│ │ │ ├── pelapor.blade.php
│ │ │ ├── rtk.blade.php
│ │ │ └── teknisi.blade.php
│ │ ├── notifications/
│ │ │ └── index.blade.php
│ │ ├── pelapor/
│ │ │ ├── dashboard.blade.php
│ │ │ ├── pengajuan/
│ │ │ │ ├── index.blade.php
│ │ │ │ ├── create.blade.php
│ │ │ │ ├── edit.blade.php
│ │ │ │ └── show.blade.php
│ │ │ └── peminjaman/
│ │ │ ├── index.blade.php
│ │ │ ├── create.blade.php
│ │ │ ├── edit.blade.php
│ │ │ └── show.blade.php
│ │ ├── rtk/
│ │ │ ├── dashboard.blade.php
│ │ │ ├── fasilitas/
│ │ │ │ ├── index.blade.php
│ │ │ │ ├── create.blade.php
│ │ │ │ ├── edit.blade.php
│ │ │ │ └── show.blade.php
│ │ │ ├── laporan/
│ │ │ │ ├── rekapitulasi.blade.php
│ │ │ │ └── pdf-rekapitulasi.blade.php
│ │ │ ├── penugasan/
│ │ │ │ ├── index.blade.php
│ │ │ │ ├── create.blade.php
│ │ │ │ └── edit.blade.php
│ │ │ ├── peminjaman/
│ │ │ │ ├── index.blade.php
│ │ │ │ └── show.blade.php
│ │ │ ├── roles/
│ │ │ │ ├── index.blade.php
│ │ │ │ ├── create.blade.php
│ │ │ │ ├── edit.blade.php
│ │ │ │ └── assign.blade.php
│ │ │ └── users/
│ │ │ └── index.blade.php
│ │ ├── teknisi/
│ │ │ ├── dashboard.blade.php
│ │ │ └── tugas/
│ │ │ ├── show.blade.php
│ │ │ └── close.blade.php
│ │ ├── welcome.blade.php
│ │ └── dashboard.blade.php
│ └── ... (file lain)
│
├── routes/
│ ├── web.php
│ ├── api.php
│ ├── auth.php (Breeze)
│ ├── console.php
│ └── channels.php
│
├── storage/
│ ├── app/
│ │ └── public/
│ │ ├── foto_kerusakan/
│ │ ├── foto_perbaikan/
│ │ └── qrcodes/
│ └── ... (log, framework, dll)
│
├── tests/
│ ├── Feature/
│ │ ├── Auth/
│ │ │ ├── LoginPageTest.php
│ │ │ └── RegistrationTest.php
│ │ ├── BookingConflictTest.php
│ │ └── TeknisiCloseTicketTest.php
│ ├── Unit/
│ │ └── Requests/
│ │ ├── RegisterPelaporRequestTest.php
│ │ └── StorePengajuanKerusakanRequestTest.php
│ ├── TestCase.php
│ └── CreatesApplication.php
│
├── vendor/ (composer dependencies)
│
├── .env
├── .env.example
├── .gitignore
├── composer.json
├── composer.lock
├── package.json
├── package-lock.json
├── vite.config.js
├── phpunit.xml
├── tailwind.config.js
├── postcss.config.js
└── README.md