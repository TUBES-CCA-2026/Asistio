<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            $table->boolean('foto_is_temporary')->default(false)->after('foto_uploaded_at');
        });
    }

    public function down(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            $table->dropColumn('foto_is_temporary');
        });
    }
};