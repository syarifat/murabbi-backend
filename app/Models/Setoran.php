<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setoran extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun_ajaran_id',
        'santri_id',
        'guru_id',
        'surah_id',
        'ayat_mulai',
        'ayat_selesai',
        'status',
        'nilai',
        'catatan',
        'waktu_setor',
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function surah()
    {
        return $this->belongsTo(Surah::class, 'surah_id');
    }
}
