<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Mahasiswa extends Model {
    protected $table = 'mahasiswa';
    protected $fillable = ['nim_mahasiswa','nama_mahasiswa','praktikum_id'];
    public const BATAS_ALPA = 4;
    public function praktikum()      { return $this->belongsTo(Praktikum::class); }
    // Shortcut: mata kuliah melalui praktikum
    public function getMataKuliahAttribute() { return $this->praktikum?->mataKuliah; }
    public function presensi()        { return $this->hasMany(Presensi::class); }
    public function nilaiAsistensi()  { return $this->hasOne(NilaiAsistensi::class); }
    public function nilaiUjian()      { return $this->hasOne(NilaiUjian::class); }
    public function nilaiEvaluasi()   { return $this->hasOne(NilaiEvaluasi::class); }
    public function rekap()           { return $this->hasOne(RekapDetailNilai::class); }
    public function getInitialsAttribute(): string {
        $w = explode(' ', trim($this->nama_mahasiswa));
        return strtoupper(substr($w[0],0,1) . (isset($w[1]) ? substr($w[1],0,1) : ''));
    }
    // Hitung alpa secara dinamis (tidak disimpan di DB)
    public function getJumlahAlpaAttribute(): int {
        return $this->presensi()->where('status_kehadiran','A')->count();
    }
    // Cek apakah jumlah alpa sudah melebihi batas (4)
    public function melebihiBatasAlpa(): bool {
        return $this->jumlah_alpa >= self::BATAS_ALPA;
    }
    // Persentase kehadiran dihitung dari jumlah pertemuan yang SUDAH BERJALAN di kelas
    // (Praktikum::jumlah_pertemuan), bukan dari jumlah record presensi mahasiswa ini saja.
    // Sebab saat mengisi presensi, asisten bisa melewati (skip) mahasiswa yang belum
    // dipilih statusnya sehingga record presensi mahasiswa bisa tidak lengkap/lebih sedikit
    // dari jumlah pertemuan yang sebenarnya sudah terjadi — jika dipakai sebagai penyebut,
    // persentase akan ter-inflate (mis. baru hadir 5 dari 14 pertemuan tapi tampil 100%).
    public function getPersentaseHadirAttribute(): string {
        $totalPertemuan = $this->praktikum?->jumlah_pertemuan ?? 0;
        if ($totalPertemuan === 0) return '0%';
        $hadir = $this->presensi()->where('status_kehadiran','H')->count();
        return round(($hadir / $totalPertemuan) * 100, 1) . '%';
    }
}