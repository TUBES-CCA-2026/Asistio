<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model {
    protected $table    = 'mahasiswa';
    // HAPUS 'praktikum_id' dari fillable — sekarang via pivot
    protected $fillable = ['nim_mahasiswa', 'nama_mahasiswa'];

    public const BATAS_ALPA = 4;

    // many-to-many — 1 mahasiswa bisa banyak kelas
    public function praktikum() {
        return $this->belongsToMany(Praktikum::class, 'mahasiswa_praktikum');
    }

    // Ambil presensi mahasiswa untuk 1 kelas tertentu
    public function presensi() {
        return $this->hasMany(Presensi::class);
    }
    public function presensiDiKelas(int $praktikumId) {
        return $this->presensi()->where('praktikum_id', $praktikumId);
    }

    // Nilai per kelas (tetap pakai praktikum_id di tabel nilai)
    public function nilaiAsistensi()  { return $this->hasMany(NilaiAsistensi::class); }
    public function nilaiUjian()      { return $this->hasMany(NilaiUjian::class); }
    public function nilaiEvaluasi()   { return $this->hasMany(NilaiEvaluasi::class); }
    public function rekap()           { return $this->hasMany(RekapDetailNilai::class); }

    public function getInitialsAttribute(): string {
        $w = explode(' ', trim($this->nama_mahasiswa));
        return strtoupper(substr($w[0], 0, 1) . (isset($w[1]) ? substr($w[1], 0, 1) : ''));
    }

    // Jumlah alpa di kelas tertentu
    public function jumlahAlpaDiKelas(int $praktikumId): int {
        return $this->presensi()
            ->where('praktikum_id', $praktikumId)
            ->where('status_kehadiran', 'A')
            ->count();
    }

    public function melebihiBatasAlpaDiKelas(int $praktikumId): bool {
        return $this->jumlahAlpaDiKelas($praktikumId) >= self::BATAS_ALPA;
    }
}