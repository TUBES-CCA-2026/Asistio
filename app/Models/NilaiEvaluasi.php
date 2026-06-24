<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class NilaiEvaluasi extends Model {
    protected $table    = 'nilai_evaluasi';
    protected $fillable = ['mahasiswa_id','praktikum_id','nilai_evaluasi1','nilai_evaluasi2','nilai_evaluasi3','nilai_evaluasi4'];
    public function mahasiswa() { return $this->belongsTo(Mahasiswa::class); }
    public function praktikum() { return $this->belongsTo(Praktikum::class); }
    public function getRataRataAttribute(): ?float {
        $vals = array_filter([$this->nilai_evaluasi1,$this->nilai_evaluasi2,$this->nilai_evaluasi3,$this->nilai_evaluasi4], fn($v)=>$v!==null);
        return count($vals) ? round(array_sum($vals)/count($vals), 2) : null;
    }
}
