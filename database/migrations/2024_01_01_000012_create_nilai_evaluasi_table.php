<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('nilai_evaluasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->foreignId('praktikum_id')->constrained('praktikum')->cascadeOnDelete();
            $table->decimal('nilai_evaluasi1', 5, 2)->nullable();
            $table->decimal('nilai_evaluasi2', 5, 2)->nullable();
            $table->decimal('nilai_evaluasi3', 5, 2)->nullable();
            $table->decimal('nilai_evaluasi4', 5, 2)->nullable();
            $table->timestamps();
            $table->unique(['mahasiswa_id','praktikum_id'], 'ne_unique');
        });
    }
    public function down(): void { Schema::dropIfExists('nilai_evaluasi'); }
};
