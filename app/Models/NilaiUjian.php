<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class NilaiUjian extends Model {
    protected $table    = 'nilai_ujian';
    protected $fillable = ['mahasiswa_id','praktikum_id','nilai_MID','nilai_UAS'];
    public function mahasiswa() { return $this->belongsTo(Mahasiswa::class); }
    public function praktikum() { return $this->belongsTo(Praktikum::class); }
}
