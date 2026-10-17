<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penugasan_teknisi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained('pengajuan_kerusakan')->onDelete('cascade');
            $table->foreignId('teknisi_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('jadwal_penanganan');
            $table->text('instruksi_kerja')->nullable();
            $table->timestamps();

            $table->index('jadwal_penanganan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penugasan_teknisi');
    }
};