<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_induk', 50)->unique();
            $table->string('nama', 255);
            $table->string('email', 255)->unique();
            $table->string('password', 255);
            $table->enum('status_akun', ['pending', 'active', 'suspended'])->default('pending');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status_akun');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};