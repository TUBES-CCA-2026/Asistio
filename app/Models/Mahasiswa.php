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
    public function rekap()  { return $this->hasMany(RekapDetailNilai::class); }
    public function rekapDiKelas(int $praktikumId) {
        return $this->hasMany(RekapDetailNilai::class)
                    ->where('praktikum_id', $praktikumId)
                    ->first();
    }

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
<<<<<<< HEAD
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
=======
>>>>>>> 0927e776b329e1701aa6bae4b43be1baf1fa76d7
}