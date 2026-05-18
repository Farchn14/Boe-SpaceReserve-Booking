<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_blokir', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fasilitas_id');
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            // 'blocked' = internal block (Hitam), 'maintenance' = Oranye
            $table->enum('tipe', ['blocked', 'maintenance'])->default('blocked');
            $table->string('nama_pic')->nullable();         // Nama internal person
            $table->string('divisi')->nullable();           // Divisi
            $table->string('whatsapp')->nullable();         // WA
            $table->integer('durasi')->nullable();          // Durasi (hari)
            $table->text('catatan')->nullable();            // Catatan maintenance
            $table->unsignedBigInteger('created_by')->nullable(); // admin id
            $table->timestamps();

            // Plain index (no FK to avoid collation mismatch with existing fasilitas table)
            $table->index('fasilitas_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_blokir');
    }
};
