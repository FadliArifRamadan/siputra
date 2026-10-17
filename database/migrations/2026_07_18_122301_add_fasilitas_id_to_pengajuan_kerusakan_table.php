<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_kerusakan', function (Blueprint $table) {
            $table->foreignId('fasilitas_id')
                ->nullable()
                ->after('pelapor_id')
                ->constrained('fasilitas')
                ->onDelete('set null');
            
            $table->index('fasilitas_id');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_kerusakan', function (Blueprint $table) {
            $table->dropForeign(['fasilitas_id']);
            $table->dropColumn('fasilitas_id');
        });
    }
};