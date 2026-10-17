<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_kerusakan', function (Blueprint $table) {
            $table->dateTime('target_selesai')->nullable()->after('catatan_rtk');
            $table->timestamp('escalated_at')->nullable()->after('target_selesai');
            $table->enum('sla_status', ['on_track', 'warning', 'breached'])
                ->default('on_track')
                ->after('escalated_at');
            
            $table->index('sla_status');
            $table->index('target_selesai');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_kerusakan', function (Blueprint $table) {
            $table->dropColumn(['target_selesai', 'escalated_at', 'sla_status']);
        });
    }
};