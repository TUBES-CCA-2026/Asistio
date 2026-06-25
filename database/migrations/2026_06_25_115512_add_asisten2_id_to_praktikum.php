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
        Schema::table('praktikum', function (Blueprint $table) {
            $table->foreignId('asisten2_id')->nullable()->after('asisten_id')
                ->constrained('asisten')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('praktikum', function (Blueprint $table) {
            $table->dropForeign(['asisten2_id']);
            $table->dropColumn('asisten2_id');
        });
    }
};
