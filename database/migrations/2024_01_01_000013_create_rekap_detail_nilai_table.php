<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('rekap_detail_nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->foreignId('praktikum_id')->constrained('praktikum')->cascadeOnDelete();
            $table->decimal('nilai_praktikum', 5, 2)->nullable()->comment('Rata-rata evaluasi (bobot 20%)');
            $table->decimal('nilai_asistensi', 5, 2)->nullable()->comment('Rata-rata asistensi (bobot 30%)');
            $table->decimal('nilai_MID',       5, 2)->nullable()->comment('Nilai MID (bobot 20%)');
            $table->decimal('nilai_UAS',       5, 2)->nullable()->comment('Nilai UAS (bobot 30%)');
            $table->decimal('nilai_akhir',     5, 2)->nullable()->comment('NA = eval*0.2 + asist*0.3 + MID*0.2 + UAS*0.3');
            $table->string('nilai_huruf', 2)->nullable()->comment('A/AB/B/BC/C/D/E');
            // HAPUS status_kehadiran — hitung dinamis dari tabel presensi
            $table->timestamps();
            $table->unique(['mahasiswa_id','praktikum_id'], 'rekap_unique');
        });
    }
    public function down(): void { Schema::dropIfExists('rekap_detail_nilai'); }
};
