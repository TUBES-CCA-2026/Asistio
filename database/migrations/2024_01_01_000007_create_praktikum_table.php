<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('praktikum', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliah')->cascadeOnDelete();
            $table->string('nama_kelas');                    // "Kelas A", "Kelas B" — lebih deskriptif dari frekuensi_kelas
            $table->string('jadwal')->nullable();            // "Senin, 08:00–10:00"
            $table->foreignId('ruangan_id')->nullable()->constrained('ruangan')->nullOnDelete();
            $table->foreignId('dosen_id')->nullable()->constrained('dosen')->nullOnDelete();
            $table->foreignId('asisten_id')->nullable()->constrained('asisten')->nullOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('praktikum'); }
};
