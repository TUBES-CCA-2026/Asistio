<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('nilai_asistensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->foreignId('praktikum_id')->constrained('praktikum')->cascadeOnDelete();
            $table->decimal('nilai_asistensi1', 5, 2)->nullable();
            $table->decimal('nilai_asistensi2', 5, 2)->nullable();
            $table->decimal('nilai_asistensi3', 5, 2)->nullable();
            $table->timestamps();
            $table->unique(['mahasiswa_id','praktikum_id'], 'na_unique');
        });
    }
    public function down(): void { Schema::dropIfExists('nilai_asistensi'); }
};
