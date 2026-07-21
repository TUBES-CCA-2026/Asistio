<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pertemuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('praktikum_id')->constrained('praktikum')->cascadeOnDelete();
            $table->unsignedTinyInteger('pertemuan_ke');
            $table->string('hari', 20)->nullable();
            $table->date('tanggal')->nullable();
            $table->text('materi')->nullable();
            $table->timestamps();
            $table->unique(['praktikum_id', 'pertemuan_ke']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pertemuan');
    }
};
