<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->string('nim_mahasiswa', 20)->unique();
            $table->string('nama_mahasiswa');
            // PERBAIKAN UTAMA: mahasiswa terhubung ke praktikum (kelas) bukan langsung ke mata_kuliah
            // Dari sini kita bisa dapat: mata_kuliah, asisten, dosen, ruangan, jadwal
            $table->foreignId('praktikum_id')->constrained('praktikum')->cascadeOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('mahasiswa'); }
};
