<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Asisten extends Model {
    protected $fillable = ['nama_asisten','nim','user_id'];
    public function user()      { return $this->belongsTo(User::class); }
    public function praktikum() { return $this->hasMany(Praktikum::class); }
}
