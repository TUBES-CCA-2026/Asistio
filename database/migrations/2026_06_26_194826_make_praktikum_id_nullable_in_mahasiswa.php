<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->dropForeign(['praktikum_id']);
        });
        // Pakai raw SQL untuk MODIFY supaya tidak butuh package doctrine/dbal
        DB::statement('ALTER TABLE mahasiswa MODIFY praktikum_id BIGINT UNSIGNED NULL');
        Schema::table('mahasiswa', function (Blueprint $table) {
            // nullOnDelete: kalau kelasnya dihapus, mahasiswa TIDAK ikut terhapus — cuma jadi tanpa kelas
            $table->foreign('praktikum_id')->references('id')->on('praktikum')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->dropForeign(['praktikum_id']);
        });
        DB::statement('ALTER TABLE mahasiswa MODIFY praktikum_id BIGINT UNSIGNED NOT NULL');
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->foreign('praktikum_id')->references('id')->on('praktikum')->cascadeOnDelete();
        });
    }
};
