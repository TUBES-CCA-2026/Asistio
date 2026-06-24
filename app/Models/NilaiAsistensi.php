<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class NilaiAsistensi extends Model {
    protected $table    = 'nilai_asistensi';
    protected $fillable = ['mahasiswa_id','praktikum_id','nilai_asistensi1','nilai_asistensi2','nilai_asistensi3'];
    public function mahasiswa() { return $this->belongsTo(Mahasiswa::class); }
    public function praktikum() { return $this->belongsTo(Praktikum::class); }
    public function getRataRataAttribute(): ?float {
        $vals = array_filter([$this->nilai_asistensi1,$this->nilai_asistensi2,$this->nilai_asistensi3], fn($v)=>$v!==null);
        return count($vals) ? round(array_sum($vals)/count($vals), 2) : null;
    }
}
