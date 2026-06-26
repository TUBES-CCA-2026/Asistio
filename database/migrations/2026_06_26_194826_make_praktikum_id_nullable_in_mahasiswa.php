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
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->dropForeign(['praktikum_id']);
            $table->foreignId('praktikum_id')->nullable()->change();
            $table->foreign('praktikum_id')->references('id')->on('praktikum')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->dropForeign(['praktikum_id']);
            $table->foreignId('praktikum_id')->nullable(false)->change();
            $table->foreign('praktikum_id')->references('id')->on('praktikum')->cascadeOnDelete();
        });
    }
};
