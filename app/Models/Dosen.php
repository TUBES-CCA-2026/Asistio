<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Dosen extends Model {
    protected $table = 'dosen';
    protected $fillable = ['nama_dosen','nidn','user_id'];
    public function user()      { return $this->belongsTo(User::class); }
    public function praktikum() { return $this->hasMany(Praktikum::class); }
    // Mata kuliah yang diampu (melalui praktikum)
    public function mataKuliah() {
        return MataKuliah::whereIn('id', $this->praktikum()->pluck('mata_kuliah_id'))->get();
    }
    // Daftar mata kuliah yang diampu (lewat praktikum) — method biasa, BUKAN relasi Eloquent.
    public function daftarMataKuliah() {
        return MataKuliah::whereIn('id', $this->praktikum()->pluck('mata_kuliah_id'))->get();
    }
}
