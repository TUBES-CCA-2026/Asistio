<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class NilaiEvaluasi extends Model {
    protected $table    = 'nilai_evaluasi';
    protected $fillable = [
        'mahasiswa_id','praktikum_id',
        // Kolom lama (tetap dipertahankan untuk kompatibilitas)
        'p1','p2','p3','p4','p5','p6','p7','p8','p9','p10','p11','p12','p13','p14',
        // Kolom baru: kegiatan dan evaluasi per pertemuan
        'p1_kegiatan','p1_evaluasi','p2_kegiatan','p2_evaluasi',
        'p3_kegiatan','p3_evaluasi','p4_kegiatan','p4_evaluasi',
        'p5_kegiatan','p5_evaluasi','p6_kegiatan','p6_evaluasi',
        'p7_kegiatan','p7_evaluasi','p8_kegiatan','p8_evaluasi',
        'p9_kegiatan','p9_evaluasi','p10_kegiatan','p10_evaluasi',
        'p11_kegiatan','p11_evaluasi','p12_kegiatan','p12_evaluasi',
        'p13_kegiatan','p13_evaluasi','p14_kegiatan','p14_evaluasi',
    ];

    public function getRataRataAttribute(): ?float {
        $vals = [];
        $bKeg  = 0.5;
        $bEval = 0.5;
        for ($i = 1; $i <= 14; $i++) {
            $keg  = $this->{'p'.$i.'_kegiatan'};
            $eval = $this->{'p'.$i.'_evaluasi'};
            if ($keg !== null && $eval !== null) {
                $vals[] = ($keg * $bKeg) + ($eval * $bEval);
            } elseif ($keg !== null) {
                $vals[] = $keg;
            } elseif ($eval !== null) {
                $vals[] = $eval;
            }
            // Fallback ke kolom lama jika kolom baru kosong
            elseif ($this->{'p'.$i} !== null) {
                $vals[] = $this->{'p'.$i};
            }
        }
        return count($vals) ? round(array_sum($vals) / count($vals), 2) : null;
    }

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