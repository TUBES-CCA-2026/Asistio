<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PresensiAsistensi extends Model {
    protected $table    = 'presensi_asistensi';
    protected $fillable = ['mahasiswa_id','praktikum_id','asistensi_ke','hadir'];
    protected $casts    = ['hadir' => 'boolean'];
    public function mahasiswa()  { return $this->belongsTo(Mahasiswa::class); }
    public function praktikum()  { return $this->belongsTo(Praktikum::class); }
}
