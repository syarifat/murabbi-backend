<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengampuKelas extends Model
{
    use HasFactory;

    protected $table = 'pengampu_kelas';

    protected $fillable = [
        'tahun_ajaran_id',
        'guru_id',
        'kelas_id',
        'jadwal_halaqah',
    ];

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function kelas()
    {
        return $this->belongsTo(KelasRombel::class, 'kelas_id');
    }
}
