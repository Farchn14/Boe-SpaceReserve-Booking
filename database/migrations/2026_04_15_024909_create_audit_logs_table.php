<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('admin_nama')->nullable();       // Who did it
            $table->string('admin_role')->nullable();       // Owner / Admin
            $table->string('aksi');                        // e.g. "Quick Block", "Buka Blokir", "Lihat Detail"
            $table->string('target_tipe')->nullable();     // 'booking', 'jadwal_blokir', 'fasilitas'
            $table->unsignedBigInteger('target_id')->nullable();
            $table->text('deskripsi')->nullable();         // Human readable description
            $table->string('fasilitas_nama')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
