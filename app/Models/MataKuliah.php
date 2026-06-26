<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MataKuliah extends Model {
    protected $table    = 'mata_kuliah';
    protected $fillable = ['kode_mk','nama_mk'];
    public function praktikum() { return $this->hasMany(Praktikum::class); }
    // Mahasiswa didapat melalui praktikum, bukan langsung
    public function mahasiswa() {
    return $this->hasManyThrough(Mahasiswa::class, Praktikum::class);
    }
    public function getMahasiswaCountAttribute(): int {
        return \DB::table('mahasiswa_praktikum')
            ->join('praktikum', 'praktikum.id', '=', 'mahasiswa_praktikum.praktikum_id')
            ->where('praktikum.mata_kuliah_id', $this->id)
            ->distinct('mahasiswa_praktikum.mahasiswa_id')
            ->count('mahasiswa_praktikum.mahasiswa_id');
    }
}
