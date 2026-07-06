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
        Schema::table('praktikum', function (Blueprint $table) {
            $table->string('hari', 10)->nullable()->after('jadwal');
            $table->string('jam_mulai', 5)->nullable()->after('hari');
            $table->string('jam_selesai', 5)->nullable()->after('jam_mulai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('praktikum', function (Blueprint $table) {
            $table->dropColumn(['hari', 'jam_mulai', 'jam_selesai']);
        });
    }
};
