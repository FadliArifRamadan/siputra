<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fasilitas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_fasilitas', 255);
            $table->enum('kategori', ['sarana', 'prasarana']);
            $table->string('lokasi_detail', 255);
            $table->integer('stok_total')->default(1);
            $table->enum('status_kondisi', ['baik', 'rusak_ringan', 'rusak_berat', 'maintenance'])->default('baik');
            $table->timestamps();
            $table->softDeletes();

            $table->index('kategori');
            $table->index('status_kondisi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fasilitas');
    }
};