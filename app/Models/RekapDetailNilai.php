<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class RekapDetailNilai extends Model {
    protected $table    = 'rekap_detail_nilai';
    protected $fillable = ['mahasiswa_id','praktikum_id','nilai_praktikum','nilai_asistensi','nilai_MID','nilai_UAS','nilai_akhir','nilai_huruf'];
    public function mahasiswa() { return $this->belongsTo(Mahasiswa::class); }
    public function praktikum() { return $this->belongsTo(Praktikum::class); }

    /** Hitung NA berdasarkan semua komponen nilai dan simpan ke rekap */
    public static function hitungDanSimpan(int $mahasiswaId, int $praktikumId): self {
        $eval  = NilaiEvaluasi::where(['mahasiswa_id'=>$mahasiswaId,'praktikum_id'=>$praktikumId])->first();
        $asist = NilaiAsistensi::where(['mahasiswa_id'=>$mahasiswaId,'praktikum_id'=>$praktikumId])->first();
        $ujian = NilaiUjian::where(['mahasiswa_id'=>$mahasiswaId,'praktikum_id'=>$praktikumId])->first();
        $mhs   = Mahasiswa::find($mahasiswaId);

        // Ambil bobot dari tabel praktikum (gunakan default jika belum diset)
        $praktikum = Praktikum::find($praktikumId);
        $bPraktikum = (float) ($praktikum?->bobot_praktikum ?? 20) / 100;
        $bAsistensi = (float) ($praktikum?->bobot_asistensi ?? 20) / 100;
        $bMid       = (float) ($praktikum?->bobot_mid       ?? 30) / 100;
        $bUas       = (float) ($praktikum?->bobot_uas       ?? 30) / 100;

        $rEval      = $eval?->rata_rata;
        $rAsist     = $asist?->rata_rata;
        $mid        = $ujian?->nilai_MID;
        $uas        = $ujian?->nilai_UAS;

        // Hitung nilai kehadiran (persentase hadir × 100)
        $akhir = null; $huruf = null;
        if ($rEval !== null && $rAsist !== null && $mid !== null && $uas !== null) {
            $akhir = round(
                ($bPraktikum * $rEval) +
                ($bAsistensi * $rAsist) +
                ($bMid       * $mid) +
                ($bUas       * $uas),
                2
            );
            $huruf = match(true) {
                $akhir >= 85 => 'A',
                $akhir >= 75 => 'B',
                $akhir >= 65 => 'C',
                $akhir >= 55 => 'D',
                default      => 'E'
            };
        }
        return self::updateOrCreate(
            ['mahasiswa_id'=>$mahasiswaId,'praktikum_id'=>$praktikumId],
            ['nilai_praktikum'=>$rEval,'nilai_asistensi'=>$rAsist,
             'nilai_MID'=>$mid,'nilai_UAS'=>$uas,'nilai_akhir'=>$akhir,'nilai_huruf'=>$huruf]
        );
    }
}
