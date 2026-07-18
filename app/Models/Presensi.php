<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Presensi extends Model {
    protected $table    = 'presensi';
    protected $fillable = ['mahasiswa_id','praktikum_id','pertemuan_ke','status_kehadiran','catatan','bukti_foto','foto_uploaded_at','foto_is_temporary'];
    protected $casts    = ['foto_is_temporary' => 'boolean'];
    public function mahasiswa()  { return $this->belongsTo(Mahasiswa::class); }
    public function praktikum()  { return $this->belongsTo(Praktikum::class); }
    public static function statusOptions(): array {
        return ['H' => 'Hadir', 'I' => 'Izin', 'S' => 'Sakit', 'A' => 'Alpha'];
    }
    // Hitung jumlah alpha secara dinamis
    public static function hitungAlpa(int $mahasiswaId, int $praktikumId): int {
        return self::where('mahasiswa_id', $mahasiswaId)
                   ->where('praktikum_id', $praktikumId)
                   ->where('status_kehadiran', 'A')
                   ->count();
    }
}
