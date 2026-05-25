<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fasilitas_id');
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->integer('max_dewasa')->default(0);
            $table->integer('max_anak')->default(0);
            $table->decimal('harga_harian', 15, 2)->default(0);
            $table->decimal('harga_mingguan', 15, 2)->nullable();
            $table->decimal('harga_bulanan', 15, 2)->nullable();
            $table->decimal('harga_tahunan', 15, 2)->nullable();
            $table->json('gallery')->nullable();
            $table->integer('stok')->default(0);
            $table->timestamps();

            $table->foreign('fasilitas_id')->references('id')->on('fasilitas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_types');
    }
};
