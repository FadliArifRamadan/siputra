<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lokasi', function (Blueprint $table) {
            $table->id();
            $table->string('kampus', 50); // 'Kampus 1' atau 'Kampus 2'
            $table->string('lantai', 20); // 'Lantai 1', 'Lantai 2', dst
            $table->string('kode_ruangan', 50)->nullable(); // 'A.1', 'B.2', dst
            $table->string('nama_ruangan', 255); // 'Security', 'Rectorate Office', dst
            $table->text('keterangan')->nullable(); // Catatan tambahan
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['kampus', 'lantai']);
            $table->index('kode_ruangan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lokasi');
    }
};