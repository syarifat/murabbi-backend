<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelasRombel extends Model
{
    use HasFactory;

    protected $table = 'kelas_rombels';

    protected $fillable = [
        'tahun_ajaran_id',
        'nama_kelas',
        'tingkat',
        'jenis_kelamin',
    ];

    public function santris()
    {
        return $this->hasMany(Santri::class, 'kelas_id');
    }

    public function pengampus()
    {
        return $this->hasMany(PengampuKelas::class, 'kelas_id');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }
}
