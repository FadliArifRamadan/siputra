<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Pengajuan Kerusakan
        Schema::table('pengajuan_kerusakan', function (Blueprint $table) {
            $table->softDeletes();
        });

        // 2. Penugasan Teknisi
        Schema::table('penugasan_teknisi', function (Blueprint $table) {
            $table->softDeletes();
        });

        // 3. Bukti Perbaikan
        Schema::table('bukti_perbaikan', function (Blueprint $table) {
            $table->softDeletes();
        });

        // 4. Peminjaman Fasilitas
        Schema::table('peminjaman_fasilitas', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_kerusakan', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('penugasan_teknisi', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('bukti_perbaikan', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('peminjaman_fasilitas', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};