<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjaman_fasilitas', function (Blueprint $table) {
            // Verifikasi Serah Terima Awal
            $table->foreignId('verifikasi_serah_terima_by')
                ->nullable()
                ->after('status_pinjam')
                ->constrained('users')
                ->onDelete('set null');
            $table->timestamp('verifikasi_serah_terima_at')->nullable()->after('verifikasi_serah_terima_by');
            $table->enum('kondisi_serah_terima', ['baik', 'rusak'])->nullable()->after('verifikasi_serah_terima_at');
            $table->text('catatan_serah_terima')->nullable()->after('kondisi_serah_terima');

            // Verifikasi Pengembalian
            $table->foreignId('verifikasi_pengembalian_by')
                ->nullable()
                ->after('catatan_serah_terima')
                ->constrained('users')
                ->onDelete('set null');
            $table->timestamp('verifikasi_pengembalian_at')->nullable()->after('verifikasi_pengembalian_by');
            $table->enum('kondisi_pengembalian', ['baik', 'rusak'])->nullable()->after('verifikasi_pengembalian_at');
            $table->text('catatan_pengembalian')->nullable()->after('kondisi_pengembalian');
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman_fasilitas', function (Blueprint $table) {
            $table->dropForeign(['verifikasi_serah_terima_by']);
            $table->dropForeign(['verifikasi_pengembalian_by']);
            $table->dropColumn([
                'verifikasi_serah_terima_by',
                'verifikasi_serah_terima_at',
                'kondisi_serah_terima',
                'catatan_serah_terima',
                'verifikasi_pengembalian_by',
                'verifikasi_pengembalian_at',
                'kondisi_pengembalian',
                'catatan_pengembalian',
            ]);
        });
    }
};