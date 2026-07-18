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
        Schema::table('nilai_evaluasi', function (Blueprint $table) {
            for ($i = 1; $i <= 14; $i++) {
                $table->float("p{$i}_kegiatan")->nullable()->after("p{$i}");
                $table->float("p{$i}_evaluasi")->nullable()->after("p{$i}_kegiatan");
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nilai_evaluasi', function (Blueprint $table) {
            for ($i = 1; $i <= 14; $i++) {
                $table->dropColumn(["p{$i}_kegiatan", "p{$i}_evaluasi"]);
            }
        });
    }
};
