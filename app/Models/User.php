<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'no_hp',
        'role',
        'nip',
        'alamat',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function santris()
    {
        return $this->hasMany(Santri::class, 'wali_id');
    }

    public function setorans()
    {
        return $this->hasMany(Setoran::class, 'guru_id');
    }

    public function pengampuKelas()
    {
        return $this->hasMany(PengampuKelas::class, 'guru_id');
    }

    public function scopeRole($query, string $role)
    {
        return $query->where('role', $role);
    }
}
