<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class User extends Authenticatable {
    use Notifiable;
    protected $fillable = ['username','password','role_id'];
    protected $hidden   = ['password','remember_token'];
    protected function casts(): array { return ['password' => 'hashed']; }
    public function role()    { return $this->belongsTo(Role::class); }
    public function asisten() { return $this->hasOne(Asisten::class); }
    public function dosen()   { return $this->hasOne(Dosen::class); }
    public function getRoleNameAttribute(): string { return $this->role?->role_name ?? ''; }
    public function isLaboran(): bool { return $this->role_name === 'laboran'; }
    public function isAsisten(): bool { return $this->role_name === 'asisten'; }
    public function isDosen():   bool { return $this->role_name === 'dosen'; }
    public function getNamaAttribute(): string {
        if ($this->isAsisten()) return $this->asisten?->nama_asisten ?? $this->username;
        if ($this->isDosen())   return $this->dosen?->nama_dosen    ?? $this->username;
        return $this->username;
    }
    public function getInitialsAttribute(): string {
        $w = explode(' ', trim($this->nama));
        return strtoupper(substr($w[0],0,1) . (isset($w[1]) ? substr($w[1],0,1) : ''));
    }
}
