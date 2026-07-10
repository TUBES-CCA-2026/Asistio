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
        // Update semua kelas yang masih pakai bobot lama (total 110%) ke bobot baru (total 100%)
        // Penanda: bobot_asistensi = 30 DAN (kehadiran+praktikum+asistensi+mid+uas) = 110
        DB::table('praktikum')
            ->whereRaw('(bobot_kehadiran + bobot_praktikum + bobot_asistensi + bobot_mid + bobot_uas) > 100')
            ->update(['bobot_asistensi' => 20]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('praktikum')
            ->whereRaw('(bobot_kehadiran + bobot_praktikum + bobot_asistensi + bobot_mid + bobot_uas) = 100')
            ->where('bobot_asistensi', 20)
            ->update(['bobot_asistensi' => 30]);
    }
};
