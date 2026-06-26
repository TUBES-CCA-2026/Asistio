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
        // 1. Tambah kolom baru p1..p14
        Schema::table('nilai_evaluasi', function (Blueprint $table) {
            for ($i = 1; $i <= 14; $i++) {
                $table->decimal("p{$i}", 5, 2)->nullable()->after($i === 1 ? 'praktikum_id' : 'p'.($i-1));
            }
        });

        // 2. Migrasi data lama: eval1->p1, eval2->p2, eval3->p3, eval4->p4
        DB::table('nilai_evaluasi')->select('id','nilai_evaluasi1','nilai_evaluasi2','nilai_evaluasi3','nilai_evaluasi4')
            ->orderBy('id')->each(function ($row) {
                DB::table('nilai_evaluasi')->where('id', $row->id)->update([
                    'p1' => $row->nilai_evaluasi1,
                    'p2' => $row->nilai_evaluasi2,
                    'p3' => $row->nilai_evaluasi3,
                    'p4' => $row->nilai_evaluasi4,
                ]);
            });

        // 3. Hapus kolom lama
        Schema::table('nilai_evaluasi', function (Blueprint $table) {
            $table->dropColumn(['nilai_evaluasi1','nilai_evaluasi2','nilai_evaluasi3','nilai_evaluasi4']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Tambah kembali kolom lama
        Schema::table('nilai_evaluasi', function (Blueprint $table) {
            $table->decimal('nilai_evaluasi1', 5, 2)->nullable()->after('praktikum_id');
            $table->decimal('nilai_evaluasi2', 5, 2)->nullable()->after('nilai_evaluasi1');
            $table->decimal('nilai_evaluasi3', 5, 2)->nullable()->after('nilai_evaluasi2');
            $table->decimal('nilai_evaluasi4', 5, 2)->nullable()->after('nilai_evaluasi3');
        });

        // 2. Kembalikan data: p1->eval1, p2->eval2, p3->eval3, p4->eval4
        DB::table('nilai_evaluasi')->select('id','p1','p2','p3','p4')
            ->orderBy('id')->each(function ($row) {
                DB::table('nilai_evaluasi')->where('id', $row->id)->update([
                    'nilai_evaluasi1' => $row->p1,
                    'nilai_evaluasi2' => $row->p2,
                    'nilai_evaluasi3' => $row->p3,
                    'nilai_evaluasi4' => $row->p4,
                ]);
            });

        // 3. Hapus kolom p1..p14
        Schema::table('nilai_evaluasi', function (Blueprint $table) {
            $columns = [];
            for ($i = 1; $i <= 14; $i++) { $columns[] = "p{$i}"; }
            $table->dropColumn($columns);
        });
    }
};
