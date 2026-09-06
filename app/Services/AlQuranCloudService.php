<?php

namespace App\Services;

use App\Models\Surah;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AlQuranCloudService
{
    private const BASE_URL = 'https://api.alquran.cloud/v1';
    private const CACHE_TTL = 86400; // 24 hours

    /**
     * Get all surahs from Al-Quran Cloud API with local DB IDs (or number fallback)
     */
    public function getAllSurah(): array
    {
        return Cache::remember('alquran_surahs', self::CACHE_TTL, function () {
            $response = Http::timeout(30)->get(self::BASE_URL . '/surah');

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['data']) && is_array($data['data'])) {
                    return collect($data['data'])->map(function ($surah) {
                        // Find matching local Surah record to get database ID
                        $localSurah = Surah::where('nomor', $surah['number'])->first();
                        return [
                            'id' => $localSurah?->id ?? $surah['number'],
                            'nomor' => $surah['number'],
                            'nama_latin' => $surah['englishName'],
                            'nama_arab' => $surah['name'],
                            'nama_inggris' => $surah['englishNameTranslation'],
                            'jumlah_ayat' => $surah['numberOfAyahs'],
                            'tempat_turun' => $surah['revelationType'],
                            'juz' => $localSurah?->juz,
                        ];
                    })->values()->toArray();
                }
            }

            return [];
        });
    }

    /**
     * Sync all 114 surahs into the local database table `surahs`.
     * Preserves referential integrity and maps id = nomor (1..114).
     */
    public function syncAllSurahsToDatabase(): int
    {
        $surahs = $this->getAllSurah();
        if (empty($surahs)) {
            return 0;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        // Check if old structure was present (id 1 was nomor 78 An-Naba)
        $hasOldStructure = Surah::where('id', 1)->where('nomor', 78)->exists();
        if ($hasOldStructure) {
            DB::table('setorans')
                ->where('surah_id', '<=', 37)
                ->increment('surah_id', 77);
        }

        Surah::truncate();

        foreach ($surahs as $s) {
            Surah::create([
                'id' => $s['nomor'],
                'nomor' => $s['nomor'],
                'nama_latin' => $s['nama_latin'],
                'nama_arab' => $s['nama_arab'],
                'jumlah_ayat' => $s['jumlah_ayat'],
                'tempat_turun' => $s['tempat_turun'] ?? 'Makkiyyah',
                'juz' => $s['juz'] ?? 30,
            ]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        $this->clearCache();

        return count($surahs);
    }

    /**
     * Get specific surah by number
     */
    public function getSurah(int $number): ?array
    {
        $cacheKey = "alquran_surah_{$number}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($number) {
            $response = Http::timeout(30)->get(self::BASE_URL . "/surah/{$number}");

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['data'])) {
                    $surah = $data['data'];
                    return [
                        'id' => $surah['number'],
                        'nomor' => $surah['number'],
                        'nama_latin' => $surah['englishName'],
                        'nama_arab' => $surah['name'],
                        'nama_inggris' => $surah['englishNameTranslation'],
                        'jumlah_ayat' => $surah['numberOfAyahs'],
                        'tempat_turun' => $surah['revelationType'],
                    ];
                }
            }

            return null;
        });
    }

    /**
     * Clear cache
     */
    public function clearCache(): void
    {
        Cache::forget('alquran_surahs');
    }
}
