<?php

use App\Http\Controllers\Auth\RegisterPelaporController;
use App\Http\Controllers\PelaporController;
use App\Http\Controllers\PengajuanKerusakanController;
use App\Http\Controllers\PeminjamanFasilitasController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RtkController;
use App\Http\Controllers\TeknisiController;
use App\Http\Controllers\Bikem\BikemController;
use App\Http\Controllers\Bikem\PeminjamanBikemController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\FasilitasController;
use App\Http\Controllers\Admin\FasilitasKampusController;
use App\Models\Fasilitas;
use App\Http\Controllers\Admin\PenugasanController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\PeminjamanController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\TeknisiScheduleController;
use App\Http\Controllers\Admin\LaporanKerusakanController;
use App\Http\Controllers\Admin\LokasiController;
use App\Exports\UsersTemplateExport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\SuratController;

Route::get('/', function () {
    $fasilitasKampus = Fasilitas::kampus()
        ->landingPage()
        ->with('lokasi')
        ->get();
    return view('welcome', compact('fasilitasKampus'));
})->name('home');

// ==========================================
// API INTERNAL (Session-based, untuk web)
// ==========================================
Route::middleware(['auth'])->group(function () {
    Route::post('/api/cek-stok', [StockController::class, 'cekStok'])
        ->name('api.cek-stok');
});

// ==========================================
// ROUTE DASHBOARD DEFAULT (Redirect ke role masing-masing)
// ==========================================
Route::get('/dashboard', function () {
    $user = auth()->user();

    // Jika tidak login, redirect ke login
    if (!$user) {
        return redirect()->route('login');
    }

    // Jika status_akun belum active, redirect ke login dengan pesan
    if ($user->status_akun !== 'active') {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login')
            ->with('error', 'Akun Anda belum aktif. Silakan hubungi RTK.');
    }

    // Redirect berdasarkan role
    if ($user->hasRole('rtk')) {
        return redirect()->route('rtk.dashboard');
    } elseif ($user->hasRole('bikem')) {
        return redirect()->route('bikem.dashboard');
    } elseif ($user->hasRole('teknisi')) {
        return redirect()->route('teknisi.dashboard');
    } elseif ($user->hasRole('pelapor')) {
        return redirect()->route('pelapor.dashboard');
    }

    // Fallback
    return redirect()->route('login');
})->middleware(['auth'])->name('dashboard');

// ==========================================
// ROUTE DASHBOARD PELAPOR
// ==========================================
Route::middleware(['auth', 'account.active'])->group(function () {
    Route::get('/pelapor/dashboard', [PelaporController::class, 'index'])
        ->name('pelapor.dashboard')
        ->middleware('role:pelapor');

    // CRUD PENGAJUAN KERUSAKAN
    Route::prefix('pelapor/pengajuan')
        ->name('pelapor.pengajuan.')
        ->middleware('role:pelapor|bikem')
        ->group(function () {
            Route::get('/', [PengajuanKerusakanController::class, 'index'])->name('index');
            Route::get('/create', [PengajuanKerusakanController::class, 'create'])->name('create')->middleware('qr.redirect');
            Route::post('/', [PengajuanKerusakanController::class, 'store'])->name('store');
            Route::get('/{pengajuan}', [PengajuanKerusakanController::class, 'show'])->name('show');
            Route::get('/{pengajuan}/edit', [PengajuanKerusakanController::class, 'edit'])->name('edit');
            Route::put('/{pengajuan}', [PengajuanKerusakanController::class, 'update'])->name('update');
            Route::delete('/{pengajuan}', [PengajuanKerusakanController::class, 'destroy'])->name('destroy');
        });

    // CRUD PEMINJAMAN FASILITAS
    Route::prefix('pelapor/peminjaman')
        ->name('pelapor.peminjaman.')
        ->middleware('role:pelapor|bikem')
        ->group(function () {
            Route::get('/', [PeminjamanFasilitasController::class, 'index'])->name('index');
            Route::get('/create', [PeminjamanFasilitasController::class, 'create'])->name('create');
            Route::post('/', [PeminjamanFasilitasController::class, 'store'])->name('store');
            Route::get('/cek-stok', [PeminjamanFasilitasController::class, 'cekStok'])->name('cekStok');
            Route::get('/cek-semua-stok', [PeminjamanFasilitasController::class, 'cekSemuaStok'])->name('cekSemuaStok');
            Route::get('/{peminjaman}', [PeminjamanFasilitasController::class, 'show'])->name('show')->whereNumber('peminjaman');
            Route::get('/{peminjaman}/edit', [PeminjamanFasilitasController::class, 'edit'])->name('edit')->whereNumber('peminjaman');
            Route::put('/{peminjaman}', [PeminjamanFasilitasController::class, 'update'])->name('update')->whereNumber('peminjaman');
            Route::delete('/{peminjaman}', [PeminjamanFasilitasController::class, 'destroy'])->name('destroy')->whereNumber('peminjaman');
        });
});

// ==========================================
// SURAT IZIN PEMINJAMAN
// ==========================================
Route::middleware(['auth', 'account.active'])->group(function () {
    Route::prefix('surat')
        ->name('surat.')
        ->group(function () {
            Route::get('/peminjaman/{peminjaman}/preview', [SuratController::class, 'preview'])
                ->name('preview')
                ->middleware('role:pelapor|bikem');
            Route::get('/peminjaman/{peminjaman}/download', [SuratController::class, 'download'])
                ->name('download')
                ->middleware('role:pelapor|bikem');
        });
});

// ==========================================
// ROUTE DASHBOARD RTK
// ==========================================
Route::middleware(['auth', 'account.active'])->group(function () {
    Route::get('/rtk/dashboard', [RtkController::class, 'index'])
        ->name('rtk.dashboard')
        ->middleware('role:rtk');

    // MANAJEMEN USER (RTK)
    Route::prefix('rtk/users')
        ->name('rtk.users.')
        ->middleware('role:rtk')
        ->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/data', [UserController::class, 'getData'])->name('data');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
            Route::get('/import', [UserController::class, 'importForm'])->name('import');
            Route::post('/import', [UserController::class, 'import'])->name('import.process');
            Route::get('/export-template', function () {
                return Excel::download(new UsersTemplateExport, 'template_import_user.xlsx');
            })->name('export-template');
            Route::patch('/{user}/approve', [UserController::class, 'approve'])->name('approve');
            Route::patch('/{user}/reject', [UserController::class, 'reject'])->name('reject');
            Route::patch('/{user}/suspend', [UserController::class, 'suspend'])->name('suspend');
            Route::patch('/{user}/activate', [UserController::class, 'activate'])->name('activate');
        });

    // CRUD FASILITAS (RTK)
    Route::prefix('rtk/fasilitas')
        ->name('rtk.fasilitas.')
        ->middleware('role:rtk')
        ->group(function () {
            Route::get('/', [FasilitasController::class, 'index'])->name('index');
            Route::get('/create', [FasilitasController::class, 'create'])->name('create');
            Route::post('/', [FasilitasController::class, 'store'])->name('store');
            Route::get('/{fasilitas}', [FasilitasController::class, 'show'])->name('show');
            Route::get('/{fasilitas}/edit', [FasilitasController::class, 'edit'])->name('edit');
            Route::put('/{fasilitas}', [FasilitasController::class, 'update'])->name('update');
            Route::delete('/{fasilitas}', [FasilitasController::class, 'destroy'])->name('destroy');
            Route::post('/{fasilitas}/generate-qr', [FasilitasController::class, 'generateQrCode'])->name('generate-qr');
            Route::post('/{fasilitas}/regenerate-qr', [FasilitasController::class, 'regenerateQrCode'])->name('regenerate-qr');
            Route::get('/{fasilitas}/download-qr', [FasilitasController::class, 'downloadQrCode'])->name('download-qr');
            Route::get('/{fasilitas}/history', [FasilitasController::class, 'history'])->name('history');
        });

    // CRUD FASILITAS KAMPUS (RTK)
    Route::prefix('rtk/fasilitas-kampus')
        ->name('rtk.fasilitas-kampus.')
        ->middleware('role:rtk')
        ->group(function () {
            Route::get('/', [FasilitasKampusController::class, 'index'])->name('index');
            Route::get('/create', [FasilitasKampusController::class, 'create'])->name('create');
            Route::post('/', [FasilitasKampusController::class, 'store'])->name('store');
            Route::get('/{fasilitas}', [FasilitasKampusController::class, 'show'])->name('show');
            Route::get('/{fasilitas}/edit', [FasilitasKampusController::class, 'edit'])->name('edit');
            Route::put('/{fasilitas}', [FasilitasKampusController::class, 'update'])->name('update');
            Route::delete('/{fasilitas}', [FasilitasKampusController::class, 'destroy'])->name('destroy');
            Route::post('/{fasilitas}/generate-qr', [FasilitasKampusController::class, 'generateQrCode'])->name('generate-qr');
            Route::get('/{fasilitas}/download-qr', [FasilitasKampusController::class, 'downloadQrCode'])->name('download-qr');
        });

    // MANAJEMEN LOKASI (RTK)
    Route::prefix('rtk/lokasi')
        ->name('rtk.lokasi.')
        ->middleware('role:rtk')
        ->group(function () {
            Route::get('/', [LokasiController::class, 'index'])->name('index');
            Route::get('/create', [LokasiController::class, 'create'])->name('create');
            Route::post('/', [LokasiController::class, 'store'])->name('store');
            Route::get('/{lokasi}/edit', [LokasiController::class, 'edit'])->name('edit');
            Route::put('/{lokasi}', [LokasiController::class, 'update'])->name('update');
            Route::delete('/{lokasi}', [LokasiController::class, 'destroy'])->name('destroy');
        });

    // PENUGASAN TEKNISI (RTK)
    Route::prefix('rtk/penugasan')
        ->name('rtk.penugasan.')
        ->middleware('role:rtk')
        ->group(function () {
            Route::get('/', [PenugasanController::class, 'index'])->name('index');
            Route::get('/create', [PenugasanController::class, 'create'])->name('create');
            Route::post('/', [PenugasanController::class, 'store'])->name('store');
            Route::get('/{penugasan}/edit', [PenugasanController::class, 'edit'])->name('edit');
            Route::put('/{penugasan}', [PenugasanController::class, 'update'])->name('update');
            Route::delete('/{penugasan}', [PenugasanController::class, 'destroy'])->name('destroy');
        });

    // LAPORAN & EKSPOR PDF (RTK)
    Route::prefix('rtk/laporan')
        ->name('rtk.laporan.')
        ->middleware('role:rtk')
        ->group(function () {
            Route::get('/rekapitulasi', [LaporanController::class, 'rekapitulasiBulanan'])->name('rekapitulasi');
            Route::get('/export-pdf', [LaporanController::class, 'eksporPdf'])->name('export-pdf');
            Route::get('/export-excel', [LaporanController::class, 'eksporExcel'])->name('export-excel');
            Route::get('/export-kerusakan', [LaporanController::class, 'eksporKerusakan'])->name('export-kerusakan');
            Route::get('/export-peminjaman', [LaporanController::class, 'eksporPeminjaman'])->name('export-peminjaman');
            Route::get('/export-penugasan', [LaporanController::class, 'eksporPenugasan'])->name('export-penugasan');
        });

    // MANAJEMEN PEMINJAMAN (RTK)
    Route::prefix('rtk/peminjaman')
        ->name('rtk.peminjaman.')
        ->middleware('role:rtk')
        ->group(function () {
            Route::get('/', [PeminjamanController::class, 'index'])->name('index');
            Route::get('/{peminjaman}', [PeminjamanController::class, 'show'])->name('show');
            Route::patch('/{peminjaman}/approve', [PeminjamanController::class, 'approve'])->name('approve');
            Route::patch('/{peminjaman}/reject', [PeminjamanController::class, 'reject'])->name('reject');
            Route::patch('/{peminjaman}/start', [PeminjamanController::class, 'start'])->name('start');
            Route::patch('/{peminjaman}/complete', [PeminjamanController::class, 'complete'])->name('complete');
        });

    // MANAJEMEN ROLE & PERMISSION (RTK)
    Route::prefix('rtk/roles')
        ->name('rtk.roles.')
        ->middleware('role:rtk')
        ->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::get('/create', [RoleController::class, 'create'])->name('create');
            Route::post('/', [RoleController::class, 'store'])->name('store');
            Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
            Route::put('/{role}', [RoleController::class, 'update'])->name('update');
            Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
            Route::get('/assign', [RoleController::class, 'assignForm'])->name('assign');
            Route::post('/assign', [RoleController::class, 'assign'])->name('assign.store');
            Route::delete('/{user}/remove-role', [RoleController::class, 'removeRole'])->name('remove');
        });

    // LAPORAN KERUSAKAN (RTK)
    Route::prefix('rtk/laporan-kerusakan')
        ->name('rtk.laporan-kerusakan.')
        ->middleware('role:rtk')
        ->group(function () {
            Route::get('/', [LaporanKerusakanController::class, 'index'])->name('index');
            Route::get('/{laporanKerusakan}', [LaporanKerusakanController::class, 'show'])->name('show');
        });
});

// ==========================================
// ROUTE DASHBOARD BIKEM (Kemahasiswaan)
// ==========================================
Route::middleware(['auth', 'account.active', 'role:bikem'])->prefix('bikem')->name('bikem.')->group(function () {
    Route::get('/dashboard', [BikemController::class, 'index'])->name('dashboard');
    Route::prefix('peminjaman')->name('peminjaman.')->group(function () {
        Route::get('/', [PeminjamanBikemController::class, 'index'])->name('index');
        Route::get('/{peminjaman}', [PeminjamanBikemController::class, 'show'])->name('show');
        Route::patch('/{peminjaman}/approve', [PeminjamanBikemController::class, 'approve'])->name('approve');
        Route::patch('/{peminjaman}/revisi', [PeminjamanBikemController::class, 'requestRevision'])->name('revisi');
        Route::patch('/{peminjaman}/reject', [PeminjamanBikemController::class, 'reject'])->name('reject');
    });
});

// ==========================================
// ROUTE DASHBOARD TEKNISI
// ==========================================
Route::middleware(['auth', 'account.active'])->group(function () {
    Route::get('/teknisi/dashboard', [TeknisiController::class, 'index'])
        ->name('teknisi.dashboard')
        ->middleware('role:teknisi');

    // Daftar Tugas Teknisi
    Route::prefix('teknisi/tugas')
        ->name('teknisi.tugas.')
        ->middleware('role:teknisi')
        ->group(function () {
            Route::get('/', [TeknisiController::class, 'index'])->name('index');
            Route::get('/{penugasan}', [TeknisiController::class, 'show'])->name('show');
            Route::get('/{penugasan}/close', [TeknisiController::class, 'closeForm'])->name('close');
            Route::post('/{penugasan}/close', [TeknisiController::class, 'storeClose'])->name('store-close');
            
            // ✅ ROUTE BARU UNTUK AUTO CLOSE (TANPA BUKTI)
            Route::post('/{penugasan}/auto-close', [TeknisiController::class, 'autoClose'])->name('auto-close');
            
            Route::patch('/{penugasan}/progress', [TeknisiController::class, 'updateProgress'])->name('update-progress');
        });

    // LAPORAN KERUSAKAN
    Route::prefix('teknisi/laporan-kerusakan')
        ->name('teknisi.laporan-kerusakan.')
        ->middleware('role:teknisi')
        ->group(function () {
            Route::get('/', [TeknisiController::class, 'indexLaporan'])->name('index');
            Route::get('/{pengajuan}', [TeknisiController::class, 'showLaporan'])->name('show');
        });

    // FASILITAS
    Route::prefix('teknisi/fasilitas')
        ->name('teknisi.fasilitas.')
        ->middleware('role:teknisi')
        ->group(function () {
            Route::get('/', [TeknisiController::class, 'indexFasilitas'])->name('index');
            Route::get('/{fasilitas}', [TeknisiController::class, 'showFasilitas'])->name('show');
            Route::patch('/{fasilitas}/status', [TeknisiController::class, 'updateStatusFasilitas'])->name('update-status');
        });

    // PEMINJAMAN ALAT
    Route::prefix('teknisi/peminjaman-alat')
        ->name('teknisi.peminjaman-alat.')
        ->middleware('role:teknisi')
        ->group(function () {
            Route::get('/', [TeknisiController::class, 'indexPinjamanAlat'])->name('index');
            Route::get('/create', [TeknisiController::class, 'createPinjamanAlat'])->name('create');
            Route::post('/', [TeknisiController::class, 'storePinjamanAlat'])->name('store');
            Route::get('/{peminjaman}', [TeknisiController::class, 'showPinjamanAlat'])->name('show');
            Route::patch('/{peminjaman}/serah-terima', [TeknisiController::class, 'verifikasiSerahTerima'])->name('serah-terima');
            Route::patch('/{peminjaman}/pengembalian', [TeknisiController::class, 'verifikasiPengembalian'])->name('pengembalian');
        });
});

// ==========================================
// ROUTE BAWAAN LARAVEL BREEZE
// ==========================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    // Notifications
    Route::prefix('notifications')
        ->name('notifications.')
        ->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
            Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
            Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
        });
});

// ==========================================
// DASHBOARD API (via Web Routes - Session Auth)
// ==========================================
Route::middleware(['auth'])->group(function () {
    Route::get('/api/dashboard-stats/rtk', [DashboardController::class, 'rtk'])->name('api.dashboard.rtk')->middleware('role:rtk');
    Route::get('/api/dashboard-stats/teknisi', [DashboardController::class, 'teknisi'])->name('api.dashboard.teknisi')->middleware('role:teknisi');
    Route::get('/api/dashboard-stats/pelapor', [DashboardController::class, 'pelapor'])->name('api.dashboard.pelapor')->middleware('role:pelapor');
    Route::get('/api/teknisi-schedule', [TeknisiScheduleController::class, 'index'])->name('api.teknisi.schedule')->middleware('role:rtk');
    Route::get('/api/teknisi-availability', [TeknisiScheduleController::class, 'checkAvailability'])->name('api.teknisi.availability')->middleware('role:rtk');
});

Route::get('/buat-symlink', function () {
    $targetFolder = storage_path('app/public');
    $linkFolder = $_SERVER['DOCUMENT_ROOT'] . '/storage';
    
    // Gunakan fungsi symlink bawaan PHP
    try {
        symlink($targetFolder, $linkFolder);
        return 'Symlink berhasil dibuat!';
    } catch (\Exception $e) {
        return 'Gagal: ' . $e->getMessage();
    }
});
Route::get('/clear-cache', function() {
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    return 'Cache berhasil dibersihkan!';
});
require __DIR__ . '/auth.php';