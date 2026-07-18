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
                $table->decimal("p{$i}_kegiatan", 5, 2)->nullable()->after("p{$i}");
                $table->decimal("p{$i}_evaluasi", 5, 2)->nullable()->after("p{$i}_kegiatan");
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nilai_evaluasi', function (Blueprint $table) {
            $columns = [];
            for ($i = 1; $i <= 14; $i++) {
                $columns[] = "p{$i}_kegiatan";
                $columns[] = "p{$i}_evaluasi";
            }
            $table->dropColumn($columns);
        });
    }
};
