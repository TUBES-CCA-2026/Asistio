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
        // Tambah sub-kolom kegiatan & evaluasi untuk setiap pertemuan
        Schema::table('nilai_evaluasi', function (Blueprint $table) {
            for ($i = 1; $i <= 14; $i++) {
                $table->decimal("p{$i}_kegiatan",  5, 2)->nullable()->after("p{$i}");
                $table->decimal("p{$i}_evaluasi",  5, 2)->nullable()->after("p{$i}_kegiatan");
            }
        });

        // Tambah bobot sub-kolom praktikum ke tabel praktikum
        Schema::table('praktikum', function (Blueprint $table) {
            $table->decimal('bobot_kegiatan',          5, 2)->default(50)->after('bobot_kehadiran');
            $table->decimal('bobot_evaluasi_praktikum',5, 2)->default(50)->after('bobot_kegiatan');
        });

        // Hapus bobot_kehadiran dari perhitungan (set ke 0, kolom tetap ada untuk kompatibilitas)
        \DB::table('praktikum')->update(['bobot_kehadiran' => 0]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nilai_evaluasi', function (Blueprint $table) {
            $cols = [];
            for ($i = 1; $i <= 14; $i++) {
                $cols[] = "p{$i}_kegiatan";
                $cols[] = "p{$i}_evaluasi";
            }
            $table->dropColumn($cols);
        });
        Schema::table('praktikum', function (Blueprint $table) {
            $table->dropColumn(['bobot_kegiatan','bobot_evaluasi_praktikum']);
        });
    }
};
