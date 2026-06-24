<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('presensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->foreignId('praktikum_id')->constrained('praktikum')->cascadeOnDelete();
            $table->unsignedTinyInteger('pertemuan_ke');
            $table->enum('status_kehadiran', ['H','I','S','A'])->default('H');
            // HAPUS jumlah_alpa — dihitung on-the-fly: COUNT WHERE status='A'
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->unique(['mahasiswa_id','praktikum_id','pertemuan_ke'], 'presensi_unique');
        });
    }
    public function down(): void { Schema::dropIfExists('presensi'); }
};
