<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bukti_perbaikan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penugasan_id')->constrained('penugasan_teknisi')->onDelete('cascade');
            $table->string('foto_selesai', 255);
            $table->text('keterangan_selesai');
            $table->timestamp('tanggal_selesai')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bukti_perbaikan');
    }
};