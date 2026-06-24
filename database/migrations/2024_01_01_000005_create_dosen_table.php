<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('dosen', function (Blueprint $table) {
            $table->id();
            $table->string('nama_dosen');
            $table->string('nidn', 20)->nullable()->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            // CATATAN: relasi dosen-mata_kuliah ada di tabel praktikum (dosen_id FK)
            // Tidak perlu mata_kuliah_id di sini karena satu dosen bisa mengampu banyak kelas
        });
    }
    public function down(): void { Schema::dropIfExists('dosen'); }
};
