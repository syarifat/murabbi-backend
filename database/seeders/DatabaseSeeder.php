<?php

namespace Database\Seeders;

use App\Models\KelasRombel;
use App\Models\PengampuKelas;
use App\Models\Santri;
use App\Models\Setoran;
use App\Models\Surah;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tahun Ajaran
        $ta = TahunAjaran::create([
            'nama' => '2026/2027',
            'aktif' => true,
        ]);

        // 2. Admin user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@murabbi.id',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // 3. Guru/Ustadz (9 ustadz untuk 9 kelas)
        $guruData = [
            ['name' => 'Ust. Ahmad Fauzi, S.Pd.I', 'email' => 'ahmad@murabbi.id', 'nip' => '197501012001011001'],
            ['name' => 'Ust. Hasan Basri, M.Pd', 'email' => 'hasan@murabbi.id', 'nip' => '197602152002011002'],
            ['name' => 'Ust. Abdullah Zidane', 'email' => 'zidane@murabbi.id', 'nip' => '198003202003011003'],
            ['name' => 'Ust. Muhammad Iqbal, Lc', 'email' => 'iqbal@murabbi.id', 'nip' => '198104252004011004'],
            ['name' => 'Ust. Khalid Walidain', 'email' => 'khalid@murabbi.id', 'nip' => '198207082005011005'],
            ['name' => 'Ust. Yusuf Hamdani', 'email' => 'yusuf@murabbi.id', 'nip' => '198309152006011006'],
            ['name' => 'Ust. Sulaiman Akbar', 'email' => 'sulaiman@murabbi.id', 'nip' => '198410202007011007'],
            ['name' => 'Ust. Farid Najmuddin', 'email' => 'farid@murabbi.id', 'nip' => '198512052008011008'],
            ['name' => 'Ust. Tajul Arifin', 'email' => 'tajul@murabbi.id', 'nip' => '198603182009011009'],
        ];

        $guruIds = [];
        foreach ($guruData as $g) {
            $user = User::create([
                'name' => $g['name'],
                'email' => $g['email'],
                'password' => Hash::make('password123'),
                'role' => 'guru',
                'nip' => $g['nip'],
            ]);
            $guruIds[] = $user->id;
        }

        // 4. Create Surahs (All 114 Surahs)
        $surahs = [
            ['id' => 1, 'nomor' => 1, 'nama_latin' => 'Al-Fatihah', 'nama_arab' => 'سُورَةُ ٱلْفَاتِحَةِ', 'jumlah_ayat' => 7, 'tempat_turun' => 'Makkiyyah', 'juz' => 1],
            ['id' => 2, 'nomor' => 2, 'nama_latin' => 'Al-Baqarah', 'nama_arab' => 'سُورَةُ البَقَرَةِ', 'jumlah_ayat' => 286, 'tempat_turun' => 'Madaniyyah', 'juz' => 1],
            ['id' => 3, 'nomor' => 3, 'nama_latin' => 'Ali Imran', 'nama_arab' => 'سُورَةُ آلِ عِمۡرَانَ', 'jumlah_ayat' => 200, 'tempat_turun' => 'Madaniyyah', 'juz' => 3],
            ['id' => 4, 'nomor' => 4, 'nama_latin' => 'An-Nisa', 'nama_arab' => 'سُورَةُ النِّسَاءِ', 'jumlah_ayat' => 176, 'tempat_turun' => 'Madaniyyah', 'juz' => 4],
            ['id' => 5, 'nomor' => 5, 'nama_latin' => 'Al-Maidah', 'nama_arab' => 'سُورَةُ المَائـِدَةِ', 'jumlah_ayat' => 120, 'tempat_turun' => 'Madaniyyah', 'juz' => 6],
            ['id' => 6, 'nomor' => 6, 'nama_latin' => 'Al-Anam', 'nama_arab' => 'سُورَةُ الأَنۡعَامِ', 'jumlah_ayat' => 165, 'tempat_turun' => 'Makkiyyah', 'juz' => 7],
            ['id' => 7, 'nomor' => 7, 'nama_latin' => 'Al-Araf', 'nama_arab' => 'سُورَةُ الأَعۡرَافِ', 'jumlah_ayat' => 206, 'tempat_turun' => 'Makkiyyah', 'juz' => 8],
            ['id' => 8, 'nomor' => 8, 'nama_latin' => 'Al-Anfal', 'nama_arab' => 'سُورَةُ الأَنفَالِ', 'jumlah_ayat' => 75, 'tempat_turun' => 'Madaniyyah', 'juz' => 9],
            ['id' => 9, 'nomor' => 9, 'nama_latin' => 'At-Taubah', 'nama_arab' => 'سُورَةُ التَّوۡبَةِ', 'jumlah_ayat' => 129, 'tempat_turun' => 'Madaniyyah', 'juz' => 10],
            ['id' => 10, 'nomor' => 10, 'nama_latin' => 'Yunus', 'nama_arab' => 'سُورَةُ يُونُسَ', 'jumlah_ayat' => 109, 'tempat_turun' => 'Makkiyyah', 'juz' => 11],
            ['id' => 11, 'nomor' => 11, 'nama_latin' => 'Hud', 'nama_arab' => 'سُورَةُ هُودٍ', 'jumlah_ayat' => 123, 'tempat_turun' => 'Makkiyyah', 'juz' => 11],
            ['id' => 12, 'nomor' => 12, 'nama_latin' => 'Yusuf', 'nama_arab' => 'سُورَةُ يُوسُفَ', 'jumlah_ayat' => 111, 'tempat_turun' => 'Makkiyyah', 'juz' => 12],
            ['id' => 13, 'nomor' => 13, 'nama_latin' => 'Ar-Rad', 'nama_arab' => 'سُورَةُ الرَّعۡدِ', 'jumlah_ayat' => 43, 'tempat_turun' => 'Madaniyyah', 'juz' => 13],
            ['id' => 14, 'nomor' => 14, 'nama_latin' => 'Ibrahim', 'nama_arab' => 'سُورَةُ إِبۡرَاهِيمَ', 'jumlah_ayat' => 52, 'tempat_turun' => 'Makkiyyah', 'juz' => 13],
            ['id' => 15, 'nomor' => 15, 'nama_latin' => 'Al-Hijr', 'nama_arab' => 'سُورَةُ الحِجۡرِ', 'jumlah_ayat' => 99, 'tempat_turun' => 'Makkiyyah', 'juz' => 14],
            ['id' => 16, 'nomor' => 16, 'nama_latin' => 'An-Nahl', 'nama_arab' => 'سُورَةُ النَّحۡلِ', 'jumlah_ayat' => 128, 'tempat_turun' => 'Makkiyyah', 'juz' => 14],
            ['id' => 17, 'nomor' => 17, 'nama_latin' => 'Al-Isra', 'nama_arab' => 'سُورَةُ الإِسۡرَاءِ', 'jumlah_ayat' => 111, 'tempat_turun' => 'Makkiyyah', 'juz' => 15],
            ['id' => 18, 'nomor' => 18, 'nama_latin' => 'Al-Kahf', 'nama_arab' => 'سُورَةُ الكَهۡفِ', 'jumlah_ayat' => 110, 'tempat_turun' => 'Makkiyyah', 'juz' => 15],
            ['id' => 19, 'nomor' => 19, 'nama_latin' => 'Maryam', 'nama_arab' => 'سُورَةُ مَرۡيَمَ', 'jumlah_ayat' => 98, 'tempat_turun' => 'Makkiyyah', 'juz' => 16],
            ['id' => 20, 'nomor' => 20, 'nama_latin' => 'Thaha', 'nama_arab' => 'سُورَةُ طه', 'jumlah_ayat' => 135, 'tempat_turun' => 'Makkiyyah', 'juz' => 16],
            ['id' => 21, 'nomor' => 21, 'nama_latin' => 'Al-Anbiya', 'nama_arab' => 'سُورَةُ الأَنبِيَاءِ', 'jumlah_ayat' => 112, 'tempat_turun' => 'Makkiyyah', 'juz' => 17],
            ['id' => 22, 'nomor' => 22, 'nama_latin' => 'Al-Hajj', 'nama_arab' => 'سُورَةُ الحَجِّ', 'jumlah_ayat' => 78, 'tempat_turun' => 'Madaniyyah', 'juz' => 17],
            ['id' => 23, 'nomor' => 23, 'nama_latin' => 'Al-Muminun', 'nama_arab' => 'سُورَةُ المُؤۡمِنُونَ', 'jumlah_ayat' => 118, 'tempat_turun' => 'Makkiyyah', 'juz' => 18],
            ['id' => 24, 'nomor' => 24, 'nama_latin' => 'An-Nur', 'nama_arab' => 'سُورَةُ النُّورِ', 'jumlah_ayat' => 64, 'tempat_turun' => 'Madaniyyah', 'juz' => 18],
            ['id' => 25, 'nomor' => 25, 'nama_latin' => 'Al-Furqan', 'nama_arab' => 'سُورَةُ الفُرۡقَانِ', 'jumlah_ayat' => 77, 'tempat_turun' => 'Makkiyyah', 'juz' => 18],
            ['id' => 26, 'nomor' => 26, 'nama_latin' => 'Asy-Syuara', 'nama_arab' => 'سُورَةُ الشُّعَرَاءِ', 'jumlah_ayat' => 227, 'tempat_turun' => 'Makkiyyah', 'juz' => 19],
            ['id' => 27, 'nomor' => 27, 'nama_latin' => 'An-Naml', 'nama_arab' => 'سُورَةُ النَّمۡلِ', 'jumlah_ayat' => 93, 'tempat_turun' => 'Makkiyyah', 'juz' => 19],
            ['id' => 28, 'nomor' => 28, 'nama_latin' => 'Al-Qashash', 'nama_arab' => 'سُورَةُ القَصَصِ', 'jumlah_ayat' => 88, 'tempat_turun' => 'Makkiyyah', 'juz' => 20],
            ['id' => 29, 'nomor' => 29, 'nama_latin' => 'Al-Ankabut', 'nama_arab' => 'سُورَةُ العَنكَبُوتِ', 'jumlah_ayat' => 69, 'tempat_turun' => 'Makkiyyah', 'juz' => 20],
            ['id' => 30, 'nomor' => 30, 'nama_latin' => 'Ar-Rum', 'nama_arab' => 'سُورَةُ الرُّومِ', 'jumlah_ayat' => 60, 'tempat_turun' => 'Makkiyyah', 'juz' => 21],
            ['id' => 31, 'nomor' => 31, 'nama_latin' => 'Luqman', 'nama_arab' => 'سُورَةُ لُقۡمَانَ', 'jumlah_ayat' => 34, 'tempat_turun' => 'Makkiyyah', 'juz' => 21],
            ['id' => 32, 'nomor' => 32, 'nama_latin' => 'As-Sajdah', 'nama_arab' => 'سُورَةُ السَّجۡدَةِ', 'jumlah_ayat' => 30, 'tempat_turun' => 'Makkiyyah', 'juz' => 21],
            ['id' => 33, 'nomor' => 33, 'nama_latin' => 'Al-Ahzab', 'nama_arab' => 'سُورَةُ الأَحۡزَابِ', 'jumlah_ayat' => 73, 'tempat_turun' => 'Madaniyyah', 'juz' => 21],
            ['id' => 34, 'nomor' => 34, 'nama_latin' => 'Saba', 'nama_arab' => 'سُورَةُ سَبَإٍ', 'jumlah_ayat' => 54, 'tempat_turun' => 'Makkiyyah', 'juz' => 22],
            ['id' => 35, 'nomor' => 35, 'nama_latin' => 'Fathir', 'nama_arab' => 'سُورَةُ فَاطِرٍ', 'jumlah_ayat' => 45, 'tempat_turun' => 'Makkiyyah', 'juz' => 22],
            ['id' => 36, 'nomor' => 36, 'nama_latin' => 'Yasin', 'nama_arab' => 'سُورَةُ يسٓ', 'jumlah_ayat' => 83, 'tempat_turun' => 'Makkiyyah', 'juz' => 22],
            ['id' => 37, 'nomor' => 37, 'nama_latin' => 'Ash-Shaffat', 'nama_arab' => 'سُورَةُ الصَّافَّاتِ', 'jumlah_ayat' => 182, 'tempat_turun' => 'Makkiyyah', 'juz' => 23],
            ['id' => 38, 'nomor' => 38, 'nama_latin' => 'Shad', 'nama_arab' => 'سُورَةُ صٓ', 'jumlah_ayat' => 88, 'tempat_turun' => 'Makkiyyah', 'juz' => 23],
            ['id' => 39, 'nomor' => 39, 'nama_latin' => 'Az-Zumar', 'nama_arab' => 'سُورَةُ الزُّمَرِ', 'jumlah_ayat' => 75, 'tempat_turun' => 'Makkiyyah', 'juz' => 23],
            ['id' => 40, 'nomor' => 40, 'nama_latin' => 'Ghafir', 'nama_arab' => 'سُورَةُ غَافِرٍ', 'jumlah_ayat' => 85, 'tempat_turun' => 'Makkiyyah', 'juz' => 24],
            ['id' => 41, 'nomor' => 41, 'nama_latin' => 'Fushshilat', 'nama_arab' => 'سُورَةُ فُصِّلَتۡ', 'jumlah_ayat' => 54, 'tempat_turun' => 'Makkiyyah', 'juz' => 24],
            ['id' => 42, 'nomor' => 42, 'nama_latin' => 'Asy-Syura', 'nama_arab' => 'سُورَةُ الشُّورَىٰ', 'jumlah_ayat' => 53, 'tempat_turun' => 'Makkiyyah', 'juz' => 25],
            ['id' => 43, 'nomor' => 43, 'nama_latin' => 'Az-Zukhruf', 'nama_arab' => 'سُورَةُ الزُّخۡرُفِ', 'jumlah_ayat' => 89, 'tempat_turun' => 'Makkiyyah', 'juz' => 25],
            ['id' => 44, 'nomor' => 44, 'nama_latin' => 'Ad-Dukhan', 'nama_arab' => 'سُورَةُ الدُّخَانِ', 'jumlah_ayat' => 59, 'tempat_turun' => 'Makkiyyah', 'juz' => 25],
            ['id' => 45, 'nomor' => 45, 'nama_latin' => 'Al-Jatsiyah', 'nama_arab' => 'سُورَةُ الجَاثِيَةِ', 'jumlah_ayat' => 37, 'tempat_turun' => 'Makkiyyah', 'juz' => 25],
            ['id' => 46, 'nomor' => 46, 'nama_latin' => 'Al-Ahqaf', 'nama_arab' => 'سُورَةُ الأَحۡقَافِ', 'jumlah_ayat' => 35, 'tempat_turun' => 'Makkiyyah', 'juz' => 26],
            ['id' => 47, 'nomor' => 47, 'nama_latin' => 'Muhammad', 'nama_arab' => 'سُورَةُ مُحَمَّدٍ', 'jumlah_ayat' => 38, 'tempat_turun' => 'Madaniyyah', 'juz' => 26],
            ['id' => 48, 'nomor' => 48, 'nama_latin' => 'Al-Fath', 'nama_arab' => 'سُورَةُ الفَتۡحِ', 'jumlah_ayat' => 29, 'tempat_turun' => 'Madaniyyah', 'juz' => 26],
            ['id' => 49, 'nomor' => 49, 'nama_latin' => 'Al-Hujurat', 'nama_arab' => 'سُورَةُ الحُجُرَاتِ', 'jumlah_ayat' => 18, 'tempat_turun' => 'Madaniyyah', 'juz' => 26],
            ['id' => 50, 'nomor' => 50, 'nama_latin' => 'Qaf', 'nama_arab' => 'سُورَةُ قٓ', 'jumlah_ayat' => 45, 'tempat_turun' => 'Makkiyyah', 'juz' => 26],
            ['id' => 51, 'nomor' => 51, 'nama_latin' => 'Adz-Dzariyat', 'nama_arab' => 'سُورَةُ الذَّارِيَاتِ', 'jumlah_ayat' => 60, 'tempat_turun' => 'Makkiyyah', 'juz' => 26],
            ['id' => 52, 'nomor' => 52, 'nama_latin' => 'Ath-Thur', 'nama_arab' => 'سُورَةُ الطُّورِ', 'jumlah_ayat' => 49, 'tempat_turun' => 'Makkiyyah', 'juz' => 27],
            ['id' => 53, 'nomor' => 53, 'nama_latin' => 'An-Najm', 'nama_arab' => 'سُورَةُ النَّجۡمِ', 'jumlah_ayat' => 62, 'tempat_turun' => 'Makkiyyah', 'juz' => 27],
            ['id' => 54, 'nomor' => 54, 'nama_latin' => 'Al-Qamar', 'nama_arab' => 'سُورَةُ القَمَرِ', 'jumlah_ayat' => 55, 'tempat_turun' => 'Makkiyyah', 'juz' => 27],
            ['id' => 55, 'nomor' => 55, 'nama_latin' => 'Ar-Rahman', 'nama_arab' => 'سُورَةُ الرَّحۡمَٰن', 'jumlah_ayat' => 78, 'tempat_turun' => 'Madaniyyah', 'juz' => 27],
            ['id' => 56, 'nomor' => 56, 'nama_latin' => 'Al-Waqiah', 'nama_arab' => 'سُورَةُ الوَاقِعَةِ', 'jumlah_ayat' => 96, 'tempat_turun' => 'Makkiyyah', 'juz' => 27],
            ['id' => 57, 'nomor' => 57, 'nama_latin' => 'Al-Hadid', 'nama_arab' => 'سُورَةُ الحَدِيدِ', 'jumlah_ayat' => 29, 'tempat_turun' => 'Madaniyyah', 'juz' => 27],
            ['id' => 58, 'nomor' => 58, 'nama_latin' => 'Al-Mujadilah', 'nama_arab' => 'سُورَةُ المُجَادلَةِ', 'jumlah_ayat' => 22, 'tempat_turun' => 'Madaniyyah', 'juz' => 28],
            ['id' => 59, 'nomor' => 59, 'nama_latin' => 'Al-Hasyr', 'nama_arab' => 'سُورَةُ الحَشۡرِ', 'jumlah_ayat' => 24, 'tempat_turun' => 'Madaniyyah', 'juz' => 28],
            ['id' => 60, 'nomor' => 60, 'nama_latin' => 'Al-Mumtahanah', 'nama_arab' => 'سُورَةُ المُمۡتَحنَةِ', 'jumlah_ayat' => 13, 'tempat_turun' => 'Madaniyyah', 'juz' => 28],
            ['id' => 61, 'nomor' => 61, 'nama_latin' => 'Ash-Shaff', 'nama_arab' => 'سُورَةُ الصَّفِّ', 'jumlah_ayat' => 14, 'tempat_turun' => 'Madaniyyah', 'juz' => 28],
            ['id' => 62, 'nomor' => 62, 'nama_latin' => 'Al-Jumuah', 'nama_arab' => 'سُورَةُ الجُمُعَةِ', 'jumlah_ayat' => 11, 'tempat_turun' => 'Madaniyyah', 'juz' => 28],
            ['id' => 63, 'nomor' => 63, 'nama_latin' => 'Al-Munafiqun', 'nama_arab' => 'سُورَةُ المُنَافِقُونَ', 'jumlah_ayat' => 11, 'tempat_turun' => 'Madaniyyah', 'juz' => 28],
            ['id' => 64, 'nomor' => 64, 'nama_latin' => 'At-Taghabun', 'nama_arab' => 'سُورَةُ التَّغَابُنِ', 'jumlah_ayat' => 18, 'tempat_turun' => 'Madaniyyah', 'juz' => 28],
            ['id' => 65, 'nomor' => 65, 'nama_latin' => 'Ath-Thalaq', 'nama_arab' => 'سُورَةُ الطَّلَاقِ', 'jumlah_ayat' => 12, 'tempat_turun' => 'Madaniyyah', 'juz' => 28],
            ['id' => 66, 'nomor' => 66, 'nama_latin' => 'At-Tahrim', 'nama_arab' => 'سُورَةُ التَّحۡرِيمِ', 'jumlah_ayat' => 12, 'tempat_turun' => 'Madaniyyah', 'juz' => 28],
            ['id' => 67, 'nomor' => 67, 'nama_latin' => 'Al-Mulk', 'nama_arab' => 'سُورَةُ المُلۡكِ', 'jumlah_ayat' => 30, 'tempat_turun' => 'Makkiyyah', 'juz' => 29],
            ['id' => 68, 'nomor' => 68, 'nama_latin' => 'Al-Qalam', 'nama_arab' => 'سُورَةُ القَلَمِ', 'jumlah_ayat' => 52, 'tempat_turun' => 'Makkiyyah', 'juz' => 29],
            ['id' => 69, 'nomor' => 69, 'nama_latin' => 'Al-Haqqah', 'nama_arab' => 'سُورَةُ الحَاقَّةِ', 'jumlah_ayat' => 52, 'tempat_turun' => 'Makkiyyah', 'juz' => 29],
            ['id' => 70, 'nomor' => 70, 'nama_latin' => 'Al-Maarij', 'nama_arab' => 'سُورَةُ المَعَارِجِ', 'jumlah_ayat' => 44, 'tempat_turun' => 'Makkiyyah', 'juz' => 29],
            ['id' => 71, 'nomor' => 71, 'nama_latin' => 'Nuh', 'nama_arab' => 'سُورَةُ نُوحٍ', 'jumlah_ayat' => 28, 'tempat_turun' => 'Makkiyyah', 'juz' => 29],
            ['id' => 72, 'nomor' => 72, 'nama_latin' => 'Al-Jinn', 'nama_arab' => 'سُورَةُ الجِنِّ', 'jumlah_ayat' => 28, 'tempat_turun' => 'Makkiyyah', 'juz' => 29],
            ['id' => 73, 'nomor' => 73, 'nama_latin' => 'Al-Muzzammil', 'nama_arab' => 'سُورَةُ المُزَّمِّلِ', 'jumlah_ayat' => 20, 'tempat_turun' => 'Makkiyyah', 'juz' => 29],
            ['id' => 74, 'nomor' => 74, 'nama_latin' => 'Al-Muddatstsir', 'nama_arab' => 'سُورَةُ المُدَّثِّرِ', 'jumlah_ayat' => 56, 'tempat_turun' => 'Makkiyyah', 'juz' => 29],
            ['id' => 75, 'nomor' => 75, 'nama_latin' => 'Al-Qiyamah', 'nama_arab' => 'سُورَةُ القِيَامَةِ', 'jumlah_ayat' => 40, 'tempat_turun' => 'Makkiyyah', 'juz' => 29],
            ['id' => 76, 'nomor' => 76, 'nama_latin' => 'Al-Insan', 'nama_arab' => 'سُورَةُ الإِنسَانِ', 'jumlah_ayat' => 31, 'tempat_turun' => 'Madaniyyah', 'juz' => 29],
            ['id' => 77, 'nomor' => 77, 'nama_latin' => 'Al-Mursalat', 'nama_arab' => 'سُورَةُ المُرۡسَلَاتِ', 'jumlah_ayat' => 50, 'tempat_turun' => 'Makkiyyah', 'juz' => 29],
            ['id' => 78, 'nomor' => 78, 'nama_latin' => 'An-Naba', 'nama_arab' => 'سُورَةُ النَّبَإِ', 'jumlah_ayat' => 40, 'tempat_turun' => 'Makkiyyah', 'juz' => 30],
            ['id' => 79, 'nomor' => 79, 'nama_latin' => 'An-Naziat', 'nama_arab' => 'سُورَةُ النَّازِعَاتِ', 'jumlah_ayat' => 46, 'tempat_turun' => 'Makkiyyah', 'juz' => 30],
            ['id' => 80, 'nomor' => 80, 'nama_latin' => 'Abasa', 'nama_arab' => 'سُورَةُ عَبَسَ', 'jumlah_ayat' => 42, 'tempat_turun' => 'Makkiyyah', 'juz' => 30],
            ['id' => 81, 'nomor' => 81, 'nama_latin' => 'At-Takwir', 'nama_arab' => 'سُورَةُ التَّكۡوِيرِ', 'jumlah_ayat' => 29, 'tempat_turun' => 'Makkiyyah', 'juz' => 30],
            ['id' => 82, 'nomor' => 82, 'nama_latin' => 'Al-Infitar', 'nama_arab' => 'سُورَةُ الانفِطَارِ', 'jumlah_ayat' => 19, 'tempat_turun' => 'Makkiyyah', 'juz' => 30],
            ['id' => 83, 'nomor' => 83, 'nama_latin' => 'Al-Mutaffifin', 'nama_arab' => 'سُورَةُ المُطَفِّفِينَ', 'jumlah_ayat' => 36, 'tempat_turun' => 'Makkiyyah', 'juz' => 30],
            ['id' => 84, 'nomor' => 84, 'nama_latin' => 'Al-Insyiqaq', 'nama_arab' => 'سُورَةُ الانشِقَاقِ', 'jumlah_ayat' => 25, 'tempat_turun' => 'Makkiyyah', 'juz' => 30],
            ['id' => 85, 'nomor' => 85, 'nama_latin' => 'Al-Buruj', 'nama_arab' => 'سُورَةُ البُرُوجِ', 'jumlah_ayat' => 22, 'tempat_turun' => 'Makkiyyah', 'juz' => 30],
            ['id' => 86, 'nomor' => 86, 'nama_latin' => 'At-Tariq', 'nama_arab' => 'سُورَةُ الطَّارِقِ', 'jumlah_ayat' => 17, 'tempat_turun' => 'Makkiyyah', 'juz' => 30],
            ['id' => 87, 'nomor' => 87, 'nama_latin' => 'Al-Ala', 'nama_arab' => 'سُورَةُ الأَعۡلَىٰ', 'jumlah_ayat' => 19, 'tempat_turun' => 'Makkiyyah', 'juz' => 30],
            ['id' => 88, 'nomor' => 88, 'nama_latin' => 'Al-Gasyiyah', 'nama_arab' => 'سُورَةُ الغَاشِيَةِ', 'jumlah_ayat' => 26, 'tempat_turun' => 'Makkiyyah', 'juz' => 30],
            ['id' => 89, 'nomor' => 89, 'nama_latin' => 'Al-Fajr', 'nama_arab' => 'سُورَةُ الفَجۡرِ', 'jumlah_ayat' => 30, 'tempat_turun' => 'Makkiyyah', 'juz' => 30],
            ['id' => 90, 'nomor' => 90, 'nama_latin' => 'Al-Balad', 'nama_arab' => 'سُورَةُ البَلَدِ', 'jumlah_ayat' => 20, 'tempat_turun' => 'Makkiyyah', 'juz' => 30],
            ['id' => 91, 'nomor' => 91, 'nama_latin' => 'Asy-Syams', 'nama_arab' => 'سُورَةُ الشَّمۡسِ', 'jumlah_ayat' => 15, 'tempat_turun' => 'Makkiyyah', 'juz' => 30],
            ['id' => 92, 'nomor' => 92, 'nama_latin' => 'Al-Lail', 'nama_arab' => 'سُورَةُ اللَّيۡلِ', 'jumlah_ayat' => 21, 'tempat_turun' => 'Makkiyyah', 'juz' => 30],
            ['id' => 93, 'nomor' => 93, 'nama_latin' => 'Ad-Duha', 'nama_arab' => 'سُورَةُ الضُّحَىٰ', 'jumlah_ayat' => 11, 'tempat_turun' => 'Makkiyyah', 'juz' => 30],
            ['id' => 94, 'nomor' => 94, 'nama_latin' => 'Al-Insyirah', 'nama_arab' => 'سُورَةُ الشَّرۡحِ', 'jumlah_ayat' => 8, 'tempat_turun' => 'Makkiyyah', 'juz' => 30],
            ['id' => 95, 'nomor' => 95, 'nama_latin' => 'At-Tin', 'nama_arab' => 'سُورَةُ التِّينِ', 'jumlah_ayat' => 8, 'tempat_turun' => 'Makkiyyah', 'juz' => 30],
            ['id' => 96, 'nomor' => 96, 'nama_latin' => 'Al-Alaq', 'nama_arab' => 'سُورَةُ العَلَقِ', 'jumlah_ayat' => 19, 'tempat_turun' => 'Makkiyyah', 'juz' => 30],
            ['id' => 97, 'nomor' => 97, 'nama_latin' => 'Al-Qadr', 'nama_arab' => 'سُورَةُ القَدۡرِ', 'jumlah_ayat' => 5, 'tempat_turun' => 'Makkiyyah', 'juz' => 30],
            ['id' => 98, 'nomor' => 98, 'nama_latin' => 'Al-Bayyinah', 'nama_arab' => 'سُورَةُ البَيِّنَةِ', 'jumlah_ayat' => 8, 'tempat_turun' => 'Madaniyyah', 'juz' => 30],
            ['id' => 99, 'nomor' => 99, 'nama_latin' => 'Az-Zalzalah', 'nama_arab' => 'سُورَةُ الزَّلۡزَلَةِ', 'jumlah_ayat' => 8, 'tempat_turun' => 'Madaniyyah', 'juz' => 30],
            ['id' => 100, 'nomor' => 100, 'nama_latin' => 'Al-Adiyat', 'nama_arab' => 'سُورَةُ العَادِيَاتِ', 'jumlah_ayat' => 11, 'tempat_turun' => 'Makkiyyah', 'juz' => 30],
            ['id' => 101, 'nomor' => 101, 'nama_latin' => 'Al-Qariah', 'nama_arab' => 'سُورَةُ القَارِعَةِ', 'jumlah_ayat' => 11, 'tempat_turun' => 'Makkiyyah', 'juz' => 30],
            ['id' => 102, 'nomor' => 102, 'nama_latin' => 'At-Takatsur', 'nama_arab' => 'سُورَةُ التَّكَاثُرِ', 'jumlah_ayat' => 8, 'tempat_turun' => 'Makkiyyah', 'juz' => 30],
            ['id' => 103, 'nomor' => 103, 'nama_latin' => 'Al-Asr', 'nama_arab' => 'سُورَةُ العَصۡرِ', 'jumlah_ayat' => 3, 'tempat_turun' => 'Makkiyyah', 'juz' => 30],
            ['id' => 104, 'nomor' => 104, 'nama_latin' => 'Al-Humazah', 'nama_arab' => 'سُورَةُ الهُمَزَةِ', 'jumlah_ayat' => 9, 'tempat_turun' => 'Makkiyyah', 'juz' => 30],
            ['id' => 105, 'nomor' => 105, 'nama_latin' => 'Al-Fil', 'nama_arab' => 'سُورَةُ الفِيلِ', 'jumlah_ayat' => 5, 'tempat_turun' => 'Makkiyyah', 'juz' => 30],
            ['id' => 106, 'nomor' => 106, 'nama_latin' => 'Quraisy', 'nama_arab' => 'سُورَةُ قُرَيۡشٍ', 'jumlah_ayat' => 4, 'tempat_turun' => 'Makkiyyah', 'juz' => 30],
            ['id' => 107, 'nomor' => 107, 'nama_latin' => 'Al-Maun', 'nama_arab' => 'سُورَةُ المَاعُونِ', 'jumlah_ayat' => 7, 'tempat_turun' => 'Makkiyyah', 'juz' => 30],
            ['id' => 108, 'nomor' => 108, 'nama_latin' => 'Al-Kausar', 'nama_arab' => 'سُورَةُ الكَوۡثَرِ', 'jumlah_ayat' => 3, 'tempat_turun' => 'Makkiyyah', 'juz' => 30],
            ['id' => 109, 'nomor' => 109, 'nama_latin' => 'Al-Kafirun', 'nama_arab' => 'سُورَةُ الكَافِرُونَ', 'jumlah_ayat' => 6, 'tempat_turun' => 'Makkiyyah', 'juz' => 30],
            ['id' => 110, 'nomor' => 110, 'nama_latin' => 'An-Nasr', 'nama_arab' => 'سُورَةُ النَّصۡرِ', 'jumlah_ayat' => 3, 'tempat_turun' => 'Madaniyyah', 'juz' => 30],
            ['id' => 111, 'nomor' => 111, 'nama_latin' => 'Al-Lahab', 'nama_arab' => 'سُورَةُ المَسَدِ', 'jumlah_ayat' => 5, 'tempat_turun' => 'Makkiyyah', 'juz' => 30],
            ['id' => 112, 'nomor' => 112, 'nama_latin' => 'Al-Ikhlas', 'nama_arab' => 'سُورَةُ الإِخۡلَاصِ', 'jumlah_ayat' => 4, 'tempat_turun' => 'Makkiyyah', 'juz' => 30],
            ['id' => 113, 'nomor' => 113, 'nama_latin' => 'Al-Falaq', 'nama_arab' => 'سُورَةُ الفَلَقِ', 'jumlah_ayat' => 5, 'tempat_turun' => 'Makkiyyah', 'juz' => 30],
            ['id' => 114, 'nomor' => 114, 'nama_latin' => 'An-Nas', 'nama_arab' => 'سُورَةُ النَّاسِ', 'jumlah_ayat' => 6, 'tempat_turun' => 'Makkiyyah', 'juz' => 30],
        ];

        foreach ($surahs as $s) {
            Surah::create($s);
        }

        // 5. 9 Kelas
        $kelasData = [
            ['nama' => '7A', 'guru_idx' => 0, 'count' => 30],
            ['nama' => '7B', 'guru_idx' => 1, 'count' => 30],
            ['nama' => '7C', 'guru_idx' => 2, 'count' => 29],
            ['nama' => '8A', 'guru_idx' => 3, 'count' => 30],
            ['nama' => '8B', 'guru_idx' => 4, 'count' => 29],
            ['nama' => '8C', 'guru_idx' => 5, 'count' => 30],
            ['nama' => '9A', 'guru_idx' => 6, 'count' => 30],
            ['nama' => '9B', 'guru_idx' => 7, 'count' => 29],
            ['nama' => '9C', 'guru_idx' => 8, 'count' => 30],
        ];

        $allSantris = [];

        foreach ($kelasData as $kd) {
            $kelas = KelasRombel::create([
                'nama_kelas' => 'Kelas ' . $kd['nama'],
                'tahun_ajaran_id' => $ta->id,
            ]);

            // Mapping guru
            PengampuKelas::create([
                'tahun_ajaran_id' => $ta->id,
                'guru_id' => $guruIds[$kd['guru_idx']],
                'kelas_id' => $kelas->id,
            ]);

            // Create santris
            $prefixes = ['Muhammad', 'Ahmad', 'Hassan', 'Abdullah', 'Farhan', 'Zain', 'Rizki', 'Dimas', 'Fajar', 'Bayu', 'Reza', 'Galang', 'Arya', 'Fikri', 'Haikal', 'Alif', 'Naufal', 'Raffi', 'Azka', 'Zaky', 'Aryo', 'Raka', 'Arga', 'Yoga', 'Bagus', 'Danang', 'Eko', 'Feri', 'Gilang', 'Hendra'];
            $suffixed = ['Ayyubi', 'Tsani', 'Fauzi', 'Hakim', 'Nashir', 'Wafa', 'Saidi', 'Zaelani', 'Pratama', 'Saputra', 'Santoso', 'Wijaya', 'Kusuma', 'Nugroho', 'Hidayat', 'Permana', 'Rahman', 'Firdaus', 'Maulana', 'Makruf', 'Auf', 'Asadi', 'Usman', 'Alfarisy', 'Wardani', 'Sodiq', 'Nabil', 'Faris', 'Hilmi', 'Zafran'];

            $used = [];
            for ($i = 0; $i < $kd['count']; $i++) {
                do {
                    $name = $prefixes[array_rand($prefixes)] . ' ' . $suffixed[array_rand($suffixed)];
                } while (in_array($name, $used));
                $used[] = $name;

                $waliUser = User::create([
                    'name' => 'Ibu ' . explode(' ', $name)[1] . ' (Wali)',
                    'email' => 'wali_' . ($kd['guru_idx'] + 1) . '_' . ($i + 1) . '@murabbi.id',
                    'password' => Hash::make('password123'),
                    'role' => 'ortu',
                ]);

                $santri = Santri::create([
                    'tahun_ajaran_id' => $ta->id,
                    'nis' => '2026' . str_pad(($kd['guru_idx'] + 1), 2, '0', STR_PAD_LEFT) . str_pad(($i + 1), 3, '0', STR_PAD_LEFT),
                    'nama_lengkap' => $name,
                    'kelas_id' => $kelas->id,
                    'wali_id' => $waliUser->id,
                    'target_juz' => $kd['guru_idx'] < 3 ? 'Juz 30' : 'Juz 15',
                    'progress_pct' => rand(0, 100),
                ]);
                $allSantris[] = $santri;
            }
        }

        // 6. Setoran untuk 1 bulan terakhir
        $surahIds = Surah::pluck('id')->toArray();
        // Enum: lancar, kurang, mengulang
        $statuses = ['lancar', 'lancar', 'lancar', 'mengulang', 'kurang'];

        $startDate = now()->subMonth();
        $endDate = now();

        $setoranCount = 0;
        foreach ($allSantris as $santri) {
            $count = rand(5, 15);
            $dates = $this->randomDates($count, $startDate, $endDate);

            foreach ($dates as $date) {
                $ayatMulai = rand(1, 30);
                Setoran::create([
                    'tahun_ajaran_id' => $ta->id,
                    'santri_id' => $santri->id,
                    'guru_id' => $guruIds[array_rand($guruIds)],
                    'surah_id' => $surahIds[array_rand($surahIds)],
                    'ayat_mulai' => $ayatMulai,
                    'ayat_selesai' => $ayatMulai + rand(1, 10),
                    'status' => $statuses[array_rand($statuses)],
                    'catatan' => null,
                    'waktu_setor' => $date,
                ]);
                $setoranCount++;
            }
        }

        echo "Seeded: 1 TA, 1 Admin, 9 Guru, 9 Kelas, " . count($allSantris) . " Santris, $setoranCount Setorans, " . count($surahs) . " Surahs\n";
    }

    private function randomDates(int $count, $start, $end): array
    {
        $dates = [];
        $startTs = $start->timestamp;
        $endTs = $end->timestamp;

        for ($i = 0; $i < $count; $i++) {
            $randomTs = rand($startTs, $endTs);
            $dates[] = date('Y-m-d H:i:s', $randomTs);
        }
        sort($dates);
        return $dates;
    }
}
