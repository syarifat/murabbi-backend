<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surah extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor',
        'nama_latin',
        'nama_arab',
        'jumlah_ayat',
        'tempat_turun',
        'juz',
    ];

    public function setorans()
    {
        return $this->hasMany(Setoran::class, 'surah_id');
    }
}
