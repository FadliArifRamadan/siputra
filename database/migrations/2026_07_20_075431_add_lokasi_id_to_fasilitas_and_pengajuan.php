<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambahkan ke tabel fasilitas
        Schema::table('fasilitas', function (Blueprint $table) {
            $table->foreignId('lokasi_id')
                ->nullable()
                ->after('kategori')
                ->constrained('lokasi')
                ->onDelete('set null');
            
            $table->index('lokasi_id');
        });

        // Tambahkan ke tabel pengajuan_kerusakan
        Schema::table('pengajuan_kerusakan', function (Blueprint $table) {
            $table->foreignId('lokasi_id')
                ->nullable()
                ->after('fasilitas_id')
                ->constrained('lokasi')
                ->onDelete('set null');
            
            $table->index('lokasi_id');
        });
    }

    public function down(): void
    {
        Schema::table('fasilitas', function (Blueprint $table) {
            $table->dropForeign(['lokasi_id']);
            $table->dropColumn('lokasi_id');
        });

        Schema::table('pengajuan_kerusakan', function (Blueprint $table) {
            $table->dropForeign(['lokasi_id']);
            $table->dropColumn('lokasi_id');
        });
    }
};