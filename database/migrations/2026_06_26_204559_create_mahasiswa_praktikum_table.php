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
        // Buat tabel pivot mahasiswa ↔ praktikum
        Schema::create('mahasiswa_praktikum', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->foreignId('praktikum_id')->constrained('praktikum')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['mahasiswa_id', 'praktikum_id'], 'mhs_prakt_unique');
        });

        // Migrasi data lama dari kolom praktikum_id ke pivot table
        // (jalankan sebelum drop kolom!)
        \DB::table('mahasiswa')
            ->whereNotNull('praktikum_id')
            ->get()
            ->each(function ($row) {
                \DB::table('mahasiswa_praktikum')->insertOrIgnore([
                    'mahasiswa_id' => $row->id,
                    'praktikum_id' => $row->praktikum_id,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            });

        // Hapus kolom lama setelah data dimigrasikan
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->dropForeign(['praktikum_id']);
            $table->dropColumn('praktikum_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->foreignId('praktikum_id')->nullable()->constrained('praktikum')->nullOnDelete();
        });
        Schema::dropIfExists('mahasiswa_praktikum');
    }
};
