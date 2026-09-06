<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Santri extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun_ajaran_id',
        'nis',
        'nama_lengkap',
        'kelas_id',
        'wali_id',
        'target_juz',
        'progress_pct',
        'status_aktif',
        'alamat',
    ];

    public function kelas()
    {
        return $this->belongsTo(KelasRombel::class, 'kelas_id');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function wali()
    {
        return $this->belongsTo(User::class, 'wali_id');
    }

    public function setorans()
    {
        return $this->hasMany(Setoran::class, 'santri_id');
    }
}
