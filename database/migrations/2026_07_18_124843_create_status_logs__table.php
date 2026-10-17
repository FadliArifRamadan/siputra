<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('status_logs', function (Blueprint $table) {
            $table->id();
            
            // Polymorphic relation
            $table->morphs('loggable');
            
            // Status changes
            $table->string('old_status')->nullable();
            $table->string('new_status')->nullable();
            
            // Actor who made the change
            $table->foreignId('actor_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            
            // Optional additional note (e.g., catatan_rtk when rejecting)
            $table->text('keterangan')->nullable();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['loggable_type', 'created_at']);
            $table->index('actor_id');
            $table->index('new_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_logs');
    }
};