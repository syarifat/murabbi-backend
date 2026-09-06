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

    public function updateProgressPct(): int
    {
        $setorans = $this->setorans()
            ->where('status', '!=', 'mengulang')
            ->selectRaw('surah_id, MAX(ayat_selesai) as max_ayat')
            ->groupBy('surah_id')
            ->get();

        $totalAyatHafal = $setorans->sum('max_ayat');
        $targetAyat = ($this->target_juz === 'Juz 30') ? 564 : 6236;
        $pct = $targetAyat > 0 ? min(100, (int) round(($totalAyatHafal / $targetAyat) * 100)) : 0;

        $this->update(['progress_pct' => $pct]);

        return $pct;
    }
}
