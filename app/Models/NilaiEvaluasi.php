<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class NilaiEvaluasi extends Model {
    protected $table    = 'nilai_evaluasi';
    protected $fillable = [
        'mahasiswa_id','praktikum_id',
        'p1','p1_kegiatan','p1_evaluasi',
        'p2','p2_kegiatan','p2_evaluasi',
        'p3','p3_kegiatan','p3_evaluasi',
        'p4','p4_kegiatan','p4_evaluasi',
        'p5','p5_kegiatan','p5_evaluasi',
        'p6','p6_kegiatan','p6_evaluasi',
        'p7','p7_kegiatan','p7_evaluasi',
        'p8','p8_kegiatan','p8_evaluasi',
        'p9','p9_kegiatan','p9_evaluasi',
        'p10','p10_kegiatan','p10_evaluasi',
        'p11','p11_kegiatan','p11_evaluasi',
        'p12','p12_kegiatan','p12_evaluasi',
        'p13','p13_kegiatan','p13_evaluasi',
        'p14','p14_kegiatan','p14_evaluasi',
    ];

    /** Hitung nilai praktikum pertemuan ke-$i berdasarkan bobot kegiatan & evaluasi kelas */
    public function nilaiPertemuan(int $i, float $bobotKegiatan, float $bobotEvaluasi): ?float {
        $kegiatan = $this->{"p{$i}_kegiatan"};
        $evaluasi = $this->{"p{$i}_evaluasi"};
        if ($kegiatan === null && $evaluasi === null) return null;
        $kegiatan = $kegiatan ?? 0;
        $evaluasi = $evaluasi ?? 0;
        $total = $bobotKegiatan + $bobotEvaluasi;
        if ($total <= 0) return null;
        return round(($bobotKegiatan * $kegiatan + $bobotEvaluasi * $evaluasi) / $total, 2);
    }
    public function mahasiswa() { return $this->belongsTo(Mahasiswa::class); }
    public function praktikum() { return $this->belongsTo(Praktikum::class); }
    /** Rata-rata dari kolom p1..p14 (nilai hasil berbobot per pertemuan) */
    public function getRataRataAttribute(): ?float {
        $vals = [];
        for ($i = 1; $i <= 14; $i++) {
            $v = $this->{"p{$i}"};
            if ($v !== null) $vals[] = (float) $v;
        }
        return count($vals) ? round(array_sum($vals) / count($vals), 2) : null;
    }

    /** Hitung ulang semua p1..p14 dari sub-kolom, simpan ke kolom p */
    public function hitungDanSimpanNilaiPertemuan(float $bobotKegiatan, float $bobotEvaluasi): void {
        $update = [];
        for ($i = 1; $i <= 14; $i++) {
            $update["p{$i}"] = $this->nilaiPertemuan($i, $bobotKegiatan, $bobotEvaluasi);
        }
        $this->update($update);
        $this->refresh();
    }
}