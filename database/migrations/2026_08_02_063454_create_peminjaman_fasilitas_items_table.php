<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ubah struktur tabel peminjaman_fasilitas (header)
        Schema::table('peminjaman_fasilitas', function (Blueprint $table) {
            // Hapus kolom yang dipindahkan ke item
            $table->dropForeign(['fasilitas_id']);
            $table->dropColumn(['fasilitas_id', 'jumlah_dipinjam']);
            
            // Tambah kolom untuk header
            $table->text('catatan_peminjam')->nullable()->after('keperluan');
        });

        // 2. Buat tabel peminjaman_fasilitas_item (detail)
        Schema::create('peminjaman_fasilitas_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')
                ->constrained('peminjaman_fasilitas')
                ->onDelete('cascade');
            $table->foreignId('fasilitas_id')
                ->constrained('fasilitas')
                ->onDelete('restrict');
            $table->integer('jumlah_dipinjam')->default(1);
            $table->integer('jumlah_dikembalikan')->default(0);
            $table->enum('status_item', ['pending', 'disetujui', 'ditolak', 'berjalan', 'selesai'])
                ->default('pending');
            $table->text('catatan_item')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Unique constraint: satu fasilitas hanya bisa dipinjam sekali dalam satu transaksi
            $table->unique(['peminjaman_id', 'fasilitas_id']);
            
            $table->index('status_item');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman_fasilitas_item');
        
        Schema::table('peminjaman_fasilitas', function (Blueprint $table) {
            $table->foreignId('fasilitas_id')->nullable()->constrained('fasilitas');
            $table->integer('jumlah_dipinjam')->default(1);
            $table->dropColumn('catatan_peminjam');
        });
    }
};