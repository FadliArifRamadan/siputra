<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_kerusakan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelapor_id')->constrained('users')->onDelete('cascade');
            $table->string('jenis_kerusakan', 255);
            $table->string('lokasi_detail', 255);
            $table->text('deskripsi');
            $table->string('foto_kerusakan', 255);
            $table->enum('tingkat_urgensi', ['rendah', 'sedang', 'tinggi'])->default('sedang');
            $table->enum('status_laporan', ['pending', 'ditolak', 'diproses', 'selesai'])->default('pending');
            $table->text('catatan_rtk')->nullable();
            $table->timestamps();

            $table->index('status_laporan');
            $table->index('tingkat_urgensi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_kerusakan');
    }
};