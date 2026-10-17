<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjaman_fasilitas', function (Blueprint $table) {
            $table->foreignId('verifikasi_bikem_by')
                ->nullable()
                ->after('status_pinjam')
                ->constrained('users')
                ->onDelete('set null');
            $table->timestamp('verifikasi_bikem_at')->nullable()->after('verifikasi_bikem_by');
            $table->text('catatan_bikem')->nullable()->after('verifikasi_bikem_at');
        });

        // Update enum status_pinjam pada tabel peminjaman_fasilitas
        DB::statement("ALTER TABLE peminjaman_fasilitas MODIFY COLUMN status_pinjam ENUM(
            'pending',
            'menunggu_bikem',
            'perlu_revisi',
            'menunggu_rtk',
            'disetujui',
            'ditolak',
            'berjalan',
            'selesai'
        ) NOT NULL DEFAULT 'menunggu_bikem'");

        // Update enum status_item pada tabel peminjaman_fasilitas_item
        DB::statement("ALTER TABLE peminjaman_fasilitas_item MODIFY COLUMN status_item ENUM(
            'pending',
            'menunggu_bikem',
            'perlu_revisi',
            'menunggu_rtk',
            'disetujui',
            'ditolak',
            'berjalan',
            'selesai'
        ) NOT NULL DEFAULT 'menunggu_bikem'");
    }

    public function down(): void
    {
        Schema::table('peminjaman_fasilitas', function (Blueprint $table) {
            $table->dropForeign(['verifikasi_bikem_by']);
            $table->dropColumn([
                'verifikasi_bikem_by',
                'verifikasi_bikem_at',
                'catatan_bikem',
            ]);
        });

        DB::statement("ALTER TABLE peminjaman_fasilitas MODIFY COLUMN status_pinjam ENUM(
            'pending',
            'disetujui',
            'ditolak',
            'berjalan',
            'selesai'
        ) NOT NULL DEFAULT 'pending'");

        DB::statement("ALTER TABLE peminjaman_fasilitas_item MODIFY COLUMN status_item ENUM(
            'pending',
            'disetujui',
            'ditolak',
            'berjalan',
            'selesai'
        ) NOT NULL DEFAULT 'pending'");
    }
};
