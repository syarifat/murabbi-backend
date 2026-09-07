<?php

// Script untuk mengenerate file SQL siap import: seed_realistic_data.sql

$passwordHash = password_hash('password123', PASSWORD_BCRYPT);
$now = date('Y-m-d H:i:s');

$sql = "-- ==============================================================================\n";
$sql .= "-- MURABBI APP - SEED DATA REALISTIS 1 BULAN TERAKHIR\n";
$sql .= "-- 1 Admin, 5 Guru, 9 Kelas (25-30 Santri), 5 Akun Wali Utama Demo, ~1500 Setoran Realistis\n";
$sql .= "-- Generated on " . date('Y-m-d H:i:s') . "\n";
$sql .= "-- ==============================================================================\n\n";

$sql .= "SET FOREIGN_KEY_CHECKS = 0;\n";
$sql .= "TRUNCATE TABLE setorans;\n";
$sql .= "TRUNCATE TABLE pengampu_kelas;\n";
$sql .= "TRUNCATE TABLE santris;\n";
$sql .= "TRUNCATE TABLE kelas_rombels;\n";
$sql .= "DELETE FROM users WHERE role IN ('admin', 'guru', 'ortu');\n";
$sql .= "TRUNCATE TABLE tahun_ajarans;\n";
$sql .= "SET FOREIGN_KEY_CHECKS = 1;\n\n";

// 1. Tahun Ajaran
$sql .= "-- 1. Tahun Ajaran\n";
$sql .= "INSERT INTO tahun_ajarans (id, nama, aktif, created_at, updated_at) VALUES\n";
$sql .= "(1, '2026/2027', 1, '$now', '$now');\n\n";

// 2. Admin
$sql .= "-- 2. Admin User\n";
$sql .= "INSERT INTO users (id, name, email, password, role, nip, no_hp, created_at, updated_at) VALUES\n";
$sql .= "(1, 'Administrator Utama', 'admin@murabbi.id', '$passwordHash', 'admin', '198001012000031001', '081122334455', '$now', '$now');\n\n";

// 3. 5 Guru
$guruData = [
    ['id' => 2, 'name' => 'Guru 1', 'email' => 'guru1@murabbi.id', 'nip' => '198501102010011001', 'no_hp' => '081234567801', 'jadwal' => 'Senin - Kamis, 07:30 - 09:00 WIB'],
    ['id' => 3, 'name' => 'Guru 2', 'email' => 'guru2@murabbi.id', 'nip' => '198603152011011002', 'no_hp' => '081234567802', 'jadwal' => 'Senin - Kamis, 07:30 - 09:00 WIB'],
    ['id' => 4, 'name' => 'Guru 3', 'email' => 'guru3@murabbi.id', 'nip' => '198807202012011003', 'no_hp' => '081234567803', 'jadwal' => 'Senin - Kamis, 13:30 - 15:00 WIB'],
    ['id' => 5, 'name' => 'Guru 4', 'email' => 'guru4@murabbi.id', 'nip' => '199005122013022004', 'no_hp' => '081234567804', 'jadwal' => 'Senin - Kamis, 13:30 - 15:00 WIB'],
    ['id' => 6, 'name' => 'Guru 5', 'email' => 'guru5@murabbi.id', 'nip' => '199209182014011005', 'no_hp' => '081234567805', 'jadwal' => 'Senin - Kamis, 07:30 - 09:00 WIB'],
];

$sql .= "-- 3. 5 Akun Guru\n";
$sql .= "INSERT INTO users (id, name, email, password, role, nip, no_hp, created_at, updated_at) VALUES\n";
$guruInserts = [];
foreach ($guruData as $g) {
    $guruInserts[] = "({$g['id']}, '{$g['name']}', '{$g['email']}', '$passwordHash', 'guru', '{$g['nip']}', '{$g['no_hp']}', '$now', '$now')";
}
$sql .= implode(",\n", $guruInserts) . ";\n\n";

// 4. 9 Kelas
$kelasList = [
    ['id' => 1, 'nama' => '7A', 'tingkat' => 7, 'guru_id' => 2, 'count' => 27],
    ['id' => 2, 'nama' => '7B', 'tingkat' => 7, 'guru_id' => 2, 'count' => 27],
    ['id' => 3, 'nama' => '7C', 'tingkat' => 7, 'guru_id' => 3, 'count' => 26],
    ['id' => 4, 'nama' => '8A', 'tingkat' => 8, 'guru_id' => 3, 'count' => 27],
    ['id' => 5, 'nama' => '8B', 'tingkat' => 8, 'guru_id' => 4, 'count' => 27],
    ['id' => 6, 'nama' => '8C', 'tingkat' => 8, 'guru_id' => 4, 'count' => 26],
    ['id' => 7, 'nama' => '9A', 'tingkat' => 9, 'guru_id' => 5, 'count' => 27],
    ['id' => 8, 'nama' => '9B', 'tingkat' => 9, 'guru_id' => 5, 'count' => 27],
    ['id' => 9, 'nama' => '9C', 'tingkat' => 9, 'guru_id' => 6, 'count' => 26],
];

$sql .= "-- 4. 9 Kelas Rombel\n";
$sql .= "INSERT INTO kelas_rombels (id, nama_kelas, tingkat, tahun_ajaran_id, created_at, updated_at) VALUES\n";
$kelasInserts = [];
foreach ($kelasList as $k) {
    $kelasInserts[] = "({$k['id']}, 'Kelas {$k['nama']}', {$k['tingkat']}, 1, '$now', '$now')";
}
$sql .= implode(",\n", $kelasInserts) . ";\n\n";

// 5. Pengampu Kelas
$sql .= "-- 5. Pemetaan Pengampu Kelas\n";
$sql .= "INSERT INTO pengampu_kelas (id, tahun_ajaran_id, guru_id, kelas_id, jadwal_halaqah, created_at, updated_at) VALUES\n";
$pengampuInserts = [];
$jadwalMap = [
    2 => 'Senin - Kamis, 07:30 - 09:00 WIB',
    3 => 'Senin - Kamis, 07:30 - 09:00 WIB',
    4 => 'Senin - Kamis, 13:30 - 15:00 WIB',
    5 => 'Senin - Kamis, 13:30 - 15:00 WIB',
    6 => 'Senin - Kamis, 07:30 - 09:00 WIB',
];
foreach ($kelasList as $idx => $k) {
    $pId = $idx + 1;
    $jadwal = $jadwalMap[$k['guru_id']];
    $pengampuInserts[] = "($pId, 1, {$k['guru_id']}, {$k['id']}, '$jadwal', '$now', '$now')";
}
$sql .= implode(",\n", $pengampuInserts) . ";\n\n";

// 6. Wali & Santri (240 Siswa & 240 Wali)
$primaryWaliMap = [
    '7A' => 1,
    '7B' => 2,
    '8A' => 3,
    '8B' => 4,
    '9A' => 5,
];

$nextStudentId = 6;
$allSantris = [];
$userInserts = [];
$santriInserts = [];

foreach ($kelasList as $k) {
    for ($i = 0; $i < $k['count']; $i++) {
        if ($i === 0 && isset($primaryWaliMap[$k['nama']])) {
            $studentNum = $primaryWaliMap[$k['nama']];
        } else {
            $studentNum = $nextStudentId++;
        }

        $waliEmail = 'wali' . $studentNum . '@murabbi.id';
        $waliName = 'Wali ' . $studentNum;
        $santriName = 'Siswa ' . $studentNum;

        $wUserId = 6 + $studentNum;
        $phone = '0812' . rand(10000000, 99999999);
        $userInserts[] = "($wUserId, '$waliName', '$waliEmail', '$passwordHash', 'ortu', NULL, '$phone', '$now', '$now')";

        $nis = '2026' . str_pad($k['tingkat'], 2, '0', STR_PAD_LEFT) . str_pad($studentNum, 4, '0', STR_PAD_LEFT);
        $santriInserts[] = "($studentNum, 1, '$nis', '$santriName', {$k['id']}, $wUserId, 'Juz 30', 0, 1, '$now', '$now')";

        $allSantris[] = [
            'id' => $studentNum,
            'name' => $santriName,
            'kelas_id' => $k['id'],
            'guru_id' => $k['guru_id'],
            'tingkat' => $k['tingkat'],
            'idx' => $i,
        ];
    }
}

$sql .= "-- 6. Akun Wali Murid (" . count($userInserts) . " Akun)\n";
$sql .= "INSERT INTO users (id, name, email, password, role, nip, no_hp, created_at, updated_at) VALUES\n";
$sql .= implode(",\n", $userInserts) . ";\n\n";

$sql .= "-- 7. Data Santri (" . count($santriInserts) . " Siswa di 9 Kelas)\n";
$sql .= "INSERT INTO santris (id, tahun_ajaran_id, nis, nama_lengkap, kelas_id, wali_id, target_juz, progress_pct, status_aktif, created_at, updated_at) VALUES\n";
$sql .= implode(",\n", $santriInserts) . ";\n\n";

// 7. Realistic Setoran
// Surah Juz 30 (114 s/d 78) dengan total ayat
$surahAyatMap = [
    114 => 6, 113 => 5, 112 => 4, 111 => 5, 110 => 3, 109 => 6, 108 => 3, 107 => 7, 106 => 4, 105 => 5,
    104 => 9, 103 => 3, 102 => 8, 101 => 11, 100 => 11, 99 => 8, 98 => 8, 97 => 5, 96 => 19, 95 => 8,
    94 => 8, 93 => 11, 92 => 21, 91 => 15, 90 => 20, 89 => 30, 88 => 26, 87 => 19, 86 => 17, 85 => 22,
    84 => 25, 83 => 36, 82 => 19, 81 => 29, 80 => 42, 79 => 46, 78 => 40
];
$shortSurahs = array_keys($surahAyatMap);

$setoranInserts = [];
$setoranId = 1;

foreach ($allSantris as $s) {
    $sCount = $s['tingkat'] === 7 ? rand(4, 9) : ($s['tingkat'] === 8 ? rand(8, 15) : rand(14, 24));
    $chosen = array_slice($shortSurahs, 0, $sCount);

    // 5-8 santri pertama tiap kelas setor HARI INI
    $setorHariIni = ($s['idx'] < rand(6, 8));
    $daysAgo = 28;
    $step = max(1, (int) floor(28 / count($chosen)));

    foreach ($chosen as $k => $surahNo) {
        $maxAyat = $surahAyatMap[$surahNo];
        $isLast = ($k === count($chosen) - 1);

        if ($isLast && $setorHariIni) {
            $hours = rand(1, 4);
            $mins = rand(5, 50);
            $waktu = date('Y-m-d H:i:s', strtotime("-$hours hours -$mins minutes"));
        } else {
            $days = max(1, $daysAgo - ($k * $step));
            $hour = rand(8, 15);
            $min = rand(10, 50);
            $waktu = date('Y-m-d H:i:s', strtotime("-$days days $hour:$min:00"));
        }

        if ($maxAyat > 15 && $isLast && !$setorHariIni) {
            // Setoran sebagian / PROSES
            $selesai = rand(1, 10);
            $setoranInserts[] = "($setoranId, 1, {$s['id']}, {$s['guru_id']}, $surahNo, 1, $selesai, 'lancar', 90, 'Tajwid baik, lanjutkan hafalan.', '$waktu', '$waktu')";
            $setoranId++;
        } else {
            // Evaluasi ulang kadang-kadang
            if (rand(1, 12) === 1) {
                $waktuUlang = date('Y-m-d H:i:s', strtotime("$waktu -1 day"));
                $setoranInserts[] = "($setoranId, 1, {$s['id']}, {$s['guru_id']}, $surahNo, 1, $maxAyat, 'kurang', 76, 'Perhatikan panjang pendek mad dan ghunnah.', '$waktuUlang', '$waktuUlang')";
                $setoranId++;
            }

            $nilai = rand(90, 98);
            $setoranInserts[] = "($setoranId, 1, {$s['id']}, {$s['guru_id']}, $surahNo, 1, $maxAyat, 'lancar', $nilai, 'Alhamdulillah lancar dan mutqin.', '$waktu', '$waktu')";
            $setoranId++;
        }
    }
}

$sql .= "-- 8. Riwayat Setoran Realistis (" . count($setoranInserts) . " Baris Transaksi)\n";
$sql .= "INSERT INTO setorans (id, tahun_ajaran_id, santri_id, guru_id, surah_id, ayat_mulai, ayat_selesai, status, nilai, catatan, waktu_setor, created_at) VALUES\n";

// Batch insert agar tidak melebihi packet limit
$chunks = array_chunk($setoranInserts, 300);
$chunkSqls = [];
foreach ($chunks as $chunk) {
    $chunkSqls[] = "INSERT INTO setorans (id, tahun_ajaran_id, santri_id, guru_id, surah_id, ayat_mulai, ayat_selesai, status, nilai, catatan, waktu_setor, created_at) VALUES\n" . implode(",\n", $chunk) . ";\n";
}
$sql .= implode("\n", $chunkSqls) . "\n";

// 9. Update progress_pct riil untuk seluruh santri
$sql .= "-- 9. Kalkulasi Riil progress_pct Santri Berdasarkan Setoran\n";
$sql .= "UPDATE santris s\n";
$sql .= "JOIN (\n";
$sql .= "    SELECT sub.santri_id, SUM(sub.max_ayat) AS total_ayat\n";
$sql .= "    FROM (\n";
$sql .= "        SELECT santri_id, surah_id, MAX(ayat_selesai) AS max_ayat\n";
$sql .= "        FROM setorans\n";
$sql .= "        WHERE status != 'mengulang'\n";
$sql .= "        GROUP BY santri_id, surah_id\n";
$sql .= "    ) AS sub\n";
$sql .= "    GROUP BY sub.santri_id\n";
$sql .= ") AS p ON s.id = p.santri_id\n";
$sql .= "SET s.progress_pct = LEAST(100, ROUND((p.total_ayat / 564) * 100));\n";

file_put_contents(__DIR__ . '/../seed_realistic_data.sql', $sql);
echo "Berhasil mengenerate seed_realistic_data.sql (" . count($allSantris) . " santri, " . count($setoranInserts) . " setoran).\n";
