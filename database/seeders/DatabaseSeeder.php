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

        // 4. Create Surahs ( Juz 30 )
        $surahs = [
            ['nomor' => 78, 'nama_latin' => 'An-Naba', 'nama_arab' => 'النبأ', 'jumlah_ayat' => 40],
            ['nomor' => 79, 'nama_latin' => 'An-Naziat', 'nama_arab' => 'النازعات', 'jumlah_ayat' => 46],
            ['nomor' => 80, 'nama_latin' => 'Abasa', 'nama_arab' => 'عبس', 'jumlah_ayat' => 42],
            ['nomor' => 81, 'nama_latin' => 'At-Takwir', 'nama_arab' => 'التكوير', 'jumlah_ayat' => 29],
            ['nomor' => 82, 'nama_latin' => 'Al-Infitar', 'nama_arab' => 'الانفطار', 'jumlah_ayat' => 19],
            ['nomor' => 83, 'nama_latin' => 'Al-Mutaffifin', 'nama_arab' => 'المطففين', 'jumlah_ayat' => 36],
            ['nomor' => 84, 'nama_latin' => 'Al-Insyiqaq', 'nama_arab' => 'الانشقاق', 'jumlah_ayat' => 25],
            ['nomor' => 85, 'nama_latin' => 'Al-Buruj', 'nama_arab' => 'البروج', 'jumlah_ayat' => 22],
            ['nomor' => 86, 'nama_latin' => 'At-Tariq', 'nama_arab' => 'الطارق', 'jumlah_ayat' => 17],
            ['nomor' => 87, 'nama_latin' => 'Al-Ala', 'nama_arab' => 'الأعلى', 'jumlah_ayat' => 19],
            ['nomor' => 88, 'nama_latin' => 'Al-Gasyiyah', 'nama_arab' => 'الغاشية', 'jumlah_ayat' => 26],
            ['nomor' => 89, 'nama_latin' => 'Al-Fajr', 'nama_arab' => 'الفجر', 'jumlah_ayat' => 30],
            ['nomor' => 90, 'nama_latin' => 'Al-Balad', 'nama_arab' => 'البلد', 'jumlah_ayat' => 20],
            ['nomor' => 91, 'nama_latin' => 'Asy-Syams', 'nama_arab' => 'الشمس', 'jumlah_ayat' => 15],
            ['nomor' => 92, 'nama_latin' => 'Al-Lail', 'nama_arab' => 'الليل', 'jumlah_ayat' => 21],
            ['nomor' => 93, 'nama_latin' => 'Ad-Duha', 'nama_arab' => 'الضحى', 'jumlah_ayat' => 11],
            ['nomor' => 94, 'nama_latin' => 'Al-Insyirah', 'nama_arab' => 'الشرح', 'jumlah_ayat' => 8],
            ['nomor' => 95, 'nama_latin' => 'At-Tin', 'nama_arab' => 'التين', 'jumlah_ayat' => 8],
            ['nomor' => 96, 'nama_latin' => 'Al-Alaq', 'nama_arab' => 'العلق', 'jumlah_ayat' => 19],
            ['nomor' => 97, 'nama_latin' => 'Al-Qadr', 'nama_arab' => 'القدر', 'jumlah_ayat' => 5],
            ['nomor' => 98, 'nama_latin' => 'Al-Bayyinah', 'nama_arab' => 'البينة', 'jumlah_ayat' => 8],
            ['nomor' => 99, 'nama_latin' => 'Az-Zalzalah', 'nama_arab' => 'الزلزلة', 'jumlah_ayat' => 8],
            ['nomor' => 100, 'nama_latin' => 'Al-Adiyat', 'nama_arab' => 'العاديات', 'jumlah_ayat' => 11],
            ['nomor' => 101, 'nama_latin' => 'Al-Qariah', 'nama_arab' => 'القارعة', 'jumlah_ayat' => 11],
            ['nomor' => 102, 'nama_latin' => 'At-Takatsur', 'nama_arab' => 'التكاثر', 'jumlah_ayat' => 8],
            ['nomor' => 103, 'nama_latin' => 'Al-Asr', 'nama_arab' => 'العصر', 'jumlah_ayat' => 3],
            ['nomor' => 104, 'nama_latin' => 'Al-Humazah', 'nama_arab' => 'الهمزة', 'jumlah_ayat' => 9],
            ['nomor' => 105, 'nama_latin' => 'Al-Fil', 'nama_arab' => 'الفيل', 'jumlah_ayat' => 5],
            ['nomor' => 106, 'nama_latin' => 'Quraisy', 'nama_arab' => 'قريش', 'jumlah_ayat' => 4],
            ['nomor' => 107, 'nama_latin' => 'Al-Maun', 'nama_arab' => 'الماعون', 'jumlah_ayat' => 7],
            ['nomor' => 108, 'nama_latin' => 'Al-Kausar', 'nama_arab' => 'الكوثر', 'jumlah_ayat' => 3],
            ['nomor' => 109, 'nama_latin' => 'Al-Kafirun', 'nama_arab' => 'الكافرون', 'jumlah_ayat' => 6],
            ['nomor' => 110, 'nama_latin' => 'An-Nasr', 'nama_arab' => 'النصر', 'jumlah_ayat' => 3],
            ['nomor' => 111, 'nama_latin' => 'Al-Lahab', 'nama_arab' => 'لهب', 'jumlah_ayat' => 5],
            ['nomor' => 112, 'nama_latin' => 'Al-Ikhlas', 'nama_arab' => 'الإخلاص', 'jumlah_ayat' => 4],
            ['nomor' => 113, 'nama_latin' => 'Al-Falaq', 'nama_arab' => 'الفلق', 'jumlah_ayat' => 5],
            ['nomor' => 114, 'nama_latin' => 'An-Nas', 'nama_arab' => 'الناس', 'jumlah_ayat' => 6],
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
