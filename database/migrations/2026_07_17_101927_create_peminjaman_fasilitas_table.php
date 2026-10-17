<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman_fasilitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjam_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('fasilitas_id')->constrained('fasilitas')->onDelete('cascade');
            $table->integer('jumlah_dipinjam')->default(1);
            $table->dateTime('waktu_mulai');
            $table->dateTime('waktu_selesai');
            $table->text('keperluan');
            $table->enum('status_pinjam', ['pending', 'disetujui', 'ditolak', 'berjalan', 'selesai'])->default('pending');
            $table->timestamps();

            $table->index(['waktu_mulai', 'waktu_selesai']);
            $table->index('status_pinjam');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman_fasilitas');
    }
};