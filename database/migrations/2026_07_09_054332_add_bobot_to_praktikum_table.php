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
            $table->decimal('bobot_kehadiran',  5, 2)->default(10)->after('asisten2_id');
            $table->decimal('bobot_praktikum',  5, 2)->default(20)->after('bobot_kehadiran');
            $table->decimal('bobot_asistensi',  5, 2)->default(30)->after('bobot_praktikum');
            $table->decimal('bobot_mid',        5, 2)->default(20)->after('bobot_asistensi');
            $table->decimal('bobot_uas',        5, 2)->default(30)->after('bobot_mid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void 
    {
        Schema::table('praktikum', function (Blueprint $table) {
            $table->dropColumn(['bobot_kehadiran','bobot_praktikum','bobot_asistensi','bobot_mid','bobot_uas']);
        });
    }
};
