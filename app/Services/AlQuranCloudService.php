<?php

namespace App\Services;

use App\Models\Surah;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AlQuranCloudService
{
    private const BASE_URL = 'https://api.alquran.cloud/v1';
    private const CACHE_TTL = 86400; // 24 hours

    /**
     * Get all surahs from Al-Quran Cloud API
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
                            'juz' => null,
                        ];
                    })->values()->toArray();
                }
            }

            return [];
        });
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
