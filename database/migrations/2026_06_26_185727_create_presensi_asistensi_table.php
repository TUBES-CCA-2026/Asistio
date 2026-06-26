<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('presensi_asistensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->foreignId('praktikum_id')->constrained('praktikum')->cascadeOnDelete();
            // Sesi asistensi: 1, 2, atau 3
            $table->unsignedTinyInteger('asistensi_ke');
            // Sederhana: hadir atau tidak hadir saja (beda dari presensi praktikum yang pakai H/I/S/A)
            $table->boolean('hadir')->default(false);
            $table->timestamps();
            $table->unique(['mahasiswa_id','praktikum_id','asistensi_ke'], 'presensi_asistensi_unique');
        });
    }
    public function down(): void { Schema::dropIfExists('presensi_asistensi'); }
};
