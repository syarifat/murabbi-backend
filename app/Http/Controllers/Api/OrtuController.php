<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use App\Models\Setoran;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class OrtuController extends Controller
{
    private function tahunAjaranAktif(): TahunAjaran
    {
        return TahunAjaran::aktif()->firstOrFail();
    }

    public function dashboard(Request $request)
    {
        $tahunAjaran = $this->tahunAjaranAktif();
        $santris = Santri::with(['kelas', 'setorans' => fn ($q) => $q->where('tahun_ajaran_id', $tahunAjaran->id)->latest('waktu_setor')->with(['surah', 'guru'])])
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->where('wali_id', $request->user()->id)
            ->get();

        $santriData = $santris->map(function ($s) {
            $arr = $s->toArray();
            $setorans = $s->setorans;

            $tuntasCount = $setorans->where('status', '!=', 'mengulang')->filter(function ($item) {
                return $item->surah && $item->ayat_selesai >= $item->surah->jumlah_ayat;
            })->pluck('surah_id')->unique()->count();

            $totalAyat = $setorans->where('status', '!=', 'mengulang')
                ->groupBy('surah_id')
                ->map(fn ($items) => $items->max('ayat_selesai'))
                ->sum();

            $targetAyat = 6236;
            $realPct = min(100, (int) round(($totalAyat / $targetAyat) * 100));

            $arr['surat_selesai'] = $tuntasCount;
            $arr['total_ayat_hafal'] = $totalAyat;
            $arr['progress_pct'] = $realPct;
            $arr['total_setoran'] = $setorans->count();
            $arr['capaian_terbaru'] = $setorans->first();

            return $arr;
        });

        return response()->json([
            'success' => true,
            'data' => [
                'wali' => $request->user(),
                'tahun_ajaran' => $tahunAjaran,
                'santris' => $santriData,
                'capaian_terbaru' => $santriData->first()['capaian_terbaru'] ?? null,
            ],
        ]);
    }

    public function timeline(Request $request, $santriId)
    {
        $tahunAjaran = $this->tahunAjaranAktif();
        $santri = Santri::where('tahun_ajaran_id', $tahunAjaran->id)->where('wali_id', $request->user()->id)->findOrFail($santriId);

        return response()->json([
            'success' => true,
            'data' => Setoran::with(['surah', 'guru'])->where('tahun_ajaran_id', $tahunAjaran->id)->where('santri_id', $santri->id)->latest('waktu_setor')->get(),
        ]);
    }

    public function rekap(Request $request, $santriId)
    {
        $tahunAjaran = $this->tahunAjaranAktif();
        $santri = Santri::with('kelas')->where('tahun_ajaran_id', $tahunAjaran->id)->where('wali_id', $request->user()->id)->findOrFail($santriId);

        $setorans = Setoran::with(['surah', 'guru'])
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->where('santri_id', $santri->id)
            ->latest('waktu_setor')
            ->get();

        // 1. Aktivitas 6 bulan terakhir
        $monthlyStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->translatedFormat('M');
            $count = $setorans->filter(function ($item) use ($date) {
                $waktu = \Carbon\Carbon::parse($item->waktu_setor);
                return $waktu->year === $date->year && $waktu->month === $date->month;
            })->count();

            $monthlyStats[] = [
                'month' => $monthName,
                'year' => $date->year,
                'count' => $count,
            ];
        }

        // 2. Rekap capaian per surah riil
        $surahGroups = $setorans->where('status', '!=', 'mengulang')->groupBy('surah_id');
        $surahProgress = [];

        foreach ($surahGroups as $surahId => $items) {
            $surah = $items->first()->surah;
            if (!$surah) continue;

            $maxAyat = $items->max('ayat_selesai');
            $isTuntas = $maxAyat >= $surah->jumlah_ayat;

            $surahProgress[] = [
                'surah_id' => $surah->id,
                'nomor' => $surah->nomor,
                'nama_latin' => $surah->nama_latin,
                'nama_arab' => $surah->nama_arab,
                'jumlah_ayat' => $surah->jumlah_ayat,
                'ayat_hafal' => $maxAyat,
                'is_tuntas' => $isTuntas,
                'status_label' => $isTuntas ? 'TUNTAS' : "PROSES ($maxAyat/{$surah->jumlah_ayat})",
                'terakhir_setor' => $items->first()->waktu_setor,
            ];
        }

        // Urutkan surah berdasarkan nomor surah
        usort($surahProgress, fn ($a, $b) => $a['nomor'] <=> $b['nomor']);

        $tuntasCount = collect($surahProgress)->where('is_tuntas', true)->count();
        $totalAyatHafal = collect($surahProgress)->sum('ayat_hafal');
        $targetAyat = 6236;
        $progressPct = min(100, (int) round(($totalAyatHafal / $targetAyat) * 100));

        return response()->json([
            'success' => true,
            'data' => [
                'santri' => $santri,
                'summary' => [
                    'total_setoran' => $setorans->count(),
                    'surat_selesai' => $tuntasCount,
                    'total_ayat' => $totalAyatHafal,
                    'progress_pct' => $progressPct,
                    'target_juz' => null,
                ],
                'monthly_stats' => $monthlyStats,
                'surahs' => $surahProgress,
            ],
        ]);
    }
}
