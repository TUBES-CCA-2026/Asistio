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
}
