<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fasilitas', function (Blueprint $table) {
            $table->enum('tipe_fasilitas', ['pinjam', 'kampus'])->default('pinjam')->after('nama_fasilitas');
            $table->string('foto')->nullable()->after('status_kondisi');
            $table->text('fasilitas_ruangan')->nullable()->after('foto');
            $table->text('deskripsi')->nullable()->after('fasilitas_ruangan');
            $table->boolean('is_landing_page')->default(true)->after('deskripsi');

            $table->index('tipe_fasilitas');
            $table->index('is_landing_page');
        });
    }

    public function down(): void
    {
        Schema::table('fasilitas', function (Blueprint $table) {
            $table->dropIndex(['tipe_fasilitas']);
            $table->dropIndex(['is_landing_page']);
            $table->dropColumn([
                'tipe_fasilitas',
                'foto',
                'fasilitas_ruangan',
                'deskripsi',
                'is_landing_page',
            ]);
        });
    }
};
