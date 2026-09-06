<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    protected $fillable = ['nama', 'aktif'];

    protected function casts(): array
    {
        return ['aktif' => 'boolean'];
    }

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    public function kelas()
    {
        return $this->hasMany(KelasRombel::class, 'tahun_ajaran_id');
    }

    public function santris()
    {
        return $this->hasMany(Santri::class, 'tahun_ajaran_id');
    }
}
