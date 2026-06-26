<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Asisten extends Model {
    protected $table = 'asisten';
    protected $fillable = ['nama_asisten','nim','user_id'];

    public function user()      { return $this->belongsTo(User::class); }

    /** Kelas di mana asisten ini sebagai Asisten 1 (asisten_id) */
    public function praktikum() { return $this->hasMany(Praktikum::class); }

    /** Kelas di mana asisten ini sebagai Asisten 2 (asisten2_id) */
    public function praktikumSebagaiAsisten2() {
        return $this->hasMany(Praktikum::class, 'asisten2_id');
    }

    /**
     * Gabungkan semua kelas (sebagai Asisten 1 maupun Asisten 2).
     * Mengembalikan Eloquent Collection dengan eager-load relasi.
     */
    public function semuaPraktikum() {
        $asAsisten1 = $this->praktikum()->with(['mataKuliah','ruangan'])->withCount('mahasiswa')->get();
        $asAsisten2 = $this->praktikumSebagaiAsisten2()->with(['mataKuliah','ruangan'])->withCount('mahasiswa')->get();
        return $asAsisten1->merge($asAsisten2)->unique('id');
    }
}