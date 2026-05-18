<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('penyewas', function (Blueprint $table) {
            $table->string('provinsi')->nullable()->after('email');
            $table->string('kabupaten')->nullable()->after('provinsi');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->timestamp('expired_at')->nullable()->after('rejection_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penyewas', function (Blueprint $table) {
            $table->dropColumn(['provinsi', 'kabupaten']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('expired_at');
        });
    }
};
