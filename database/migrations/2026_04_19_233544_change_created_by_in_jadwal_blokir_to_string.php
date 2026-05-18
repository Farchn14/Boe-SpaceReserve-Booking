<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Change created_by from unsignedBigInteger to string so that
     * the admin's name (e.g. 'Yanto') can be stored directly.
     */
    public function up(): void
    {
        Schema::table('jadwal_blokir', function (Blueprint $table) {
            $table->string('created_by', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migration back to an integer column.
     */
    public function down(): void
    {
        Schema::table('jadwal_blokir', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->change();
        });
    }
};
