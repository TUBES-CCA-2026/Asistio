<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class NilaiEvaluasi extends Model {
    protected $table    = 'nilai_evaluasi';
    protected $fillable = ['mahasiswa_id','praktikum_id','p1','p2','p3','p4','p5','p6','p7','p8','p9','p10','p11','p12','p13','p14'];
    public function mahasiswa() { return $this->belongsTo(Mahasiswa::class); }
    public function praktikum() { return $this->belongsTo(Praktikum::class); }
    public function getRataRataAttribute(): ?float {
        $vals = array_filter([
            $this->p1,$this->p2,$this->p3,$this->p4,$this->p5,$this->p6,$this->p7,
            $this->p8,$this->p9,$this->p10,$this->p11,$this->p12,$this->p13,$this->p14,
        ], fn($v)=>$v!==null);
        return count($vals) ? round(array_sum($vals)/count($vals), 2) : null;
    }
}