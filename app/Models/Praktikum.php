<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Praktikum extends Model {
    protected $fillable = ['mata_kuliah_id','nama_kelas','jadwal','ruangan_id','dosen_id','asisten_id'];
    public function mataKuliah() { return $this->belongsTo(MataKuliah::class); }
    public function ruangan()    { return $this->belongsTo(Ruangan::class); }
    public function dosen()      { return $this->belongsTo(Dosen::class); }
    public function asisten()    { return $this->belongsTo(Asisten::class); }
    public function mahasiswa()  { return $this->hasMany(Mahasiswa::class); }
    public function presensi()   { return $this->hasMany(Presensi::class); }
    public function nilaiAsistensi() { return $this->hasMany(NilaiAsistensi::class); }
    public function nilaiUjian()     { return $this->hasMany(NilaiUjian::class); }
    public function nilaiEvaluasi()  { return $this->hasMany(NilaiEvaluasi::class); }
    public function rekap()          { return $this->hasMany(RekapDetailNilai::class); }
    // Jumlah pertemuan yang sudah dicatat (dari data presensi)
    public function getJumlahPertemuanAttribute(): int {
        return $this->presensi()->max('pertemuan_ke') ?? 0;
    }
}
