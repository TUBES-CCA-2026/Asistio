<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Pertemuan extends Model {
    protected $table    = 'pertemuan';
    protected $fillable = ['praktikum_id','pertemuan_ke','hari','tanggal','materi'];
    protected $casts    = ['tanggal' => 'date'];

    public function praktikum() { return $this->belongsTo(Praktikum::class); }
}