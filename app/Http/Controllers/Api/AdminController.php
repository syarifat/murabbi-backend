<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KelasRombel;
use App\Models\PengampuKelas;
use App\Models\Santri;
use App\Models\Setoran;
use App\Models\Surah;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Services\AlQuranCloudService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    private function tahunAjaranAktif(): TahunAjaran
    {
        return TahunAjaran::aktif()->firstOrFail();
    }

    public function dashboard()
    {
        $tahunAjaran = $this->tahunAjaranAktif();
        $setoran = Setoran::where('tahun_ajaran_id', $tahunAjaran->id);
        $totalSetoran = max(1, (clone $setoran)->count());

        return response()->json([
            'success' => true,
            'data' => [
                'tahun_ajaran' => $tahunAjaran,
                'total_santri' => Santri::where('tahun_ajaran_id', $tahunAjaran->id)->count(),
                'total_guru' => User::role('guru')->count(),
                'setoran_hari_ini' => (clone $setoran)->whereDate('waktu_setor', today())->count(),
                'rata_kelancaran' => round(((clone $setoran)->where('status', 'lancar')->count() / $totalSetoran) * 100).'%',
                'alert_belum_setor' => 0,
            ],
        ]);
    }

    public function index(Request $request)
    {
        return $this->listUsers($request);
    }

    public function listUsers(Request $request)
    {
        $query = User::query()->with('santris:id,wali_id,nama_lengkap');

        if ($request->filled('role') && $request->role !== 'Semua') {
            $role = $request->role === 'Orang Tua' ? 'ortu' : strtolower($request->role);
            $query->where('role', $role);
        }

        return response()->json(['success' => true, 'data' => $query->latest()->get()]);
    }

    public function store(Request $request)
    {
        return $this->storeUser($request);
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(['admin', 'guru', 'ortu'])],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'nip' => ['nullable', 'string', 'max:50'],
            'santri_ids' => ['array'],
            'santri_ids.*' => ['exists:santris,id'],
        ]);

        $user = User::create($data);

        if ($user->role === 'ortu' && ! empty($data['santri_ids'])) {
            Santri::whereIn('id', $data['santri_ids'])->update(['wali_id' => $user->id]);
        }

        return response()->json(['success' => true, 'message' => 'Pengguna baru berhasil ditambahkan', 'data' => $user->load('santris')], 201);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['sometimes', Rule::in(['admin', 'guru', 'ortu'])],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'nip' => ['nullable', 'string', 'max:50'],
            'santri_ids' => ['nullable', 'array'],
            'santri_ids.*' => ['exists:santris,id'],
        ]);

        if (isset($data['password']) && $data['password'] === '') {
            unset($data['password']);
        }

        // Handle ortu-anak reassignment
        if ($user->role === 'ortu' && isset($data['santri_ids'])) {
            // Unlink all current santris from this ortu
            Santri::where('wali_id', $user->id)->update(['wali_id' => null]);
            // Link new santris
            Santri::whereIn('id', $data['santri_ids'])->update(['wali_id' => $user->id]);
            unset($data['santri_ids']);
        }

        $user->update($data);

        return response()->json(['success' => true, 'data' => $user->load('santris')]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(['success' => true, 'message' => 'Pengguna dihapus']);
    }

    public function resetPassword(User $user)
    {
        $user->update([
            'password' => Hash::make('murabbiapp'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kata sandi pengguna ' . $user->name . ' berhasil direset menjadi "murabbiapp".',
        ]);
    }

    public function destroySantri(Santri $santri)
    {
        $santri->delete();

        return response()->json(['success' => true, 'message' => 'Santri dihapus']);
    }

    public function listSantri()
    {
        $tahunAjaran = $this->tahunAjaranAktif();

        return response()->json(['success' => true, 'data' => Santri::with(['kelas', 'wali'])->where('tahun_ajaran_id', $tahunAjaran->id)->latest()->get()]);
    }

    public function storeSantri(Request $request)
    {
        $tahunAjaran = $this->tahunAjaranAktif();
        $data = $request->validate([
            'nis' => ['required', 'string', 'max:50', 'unique:santris,nis'],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'kelas_id' => ['nullable', Rule::exists('kelas_rombels', 'id')->where('tahun_ajaran_id', $tahunAjaran->id)],
            'wali_id' => ['nullable', Rule::exists('users', 'id')->where('role', 'ortu')],
            'wali_nama' => ['nullable', 'string', 'max:255'],
            'wali_email' => ['nullable', 'email', 'unique:users,email'],
            'wali_hp' => ['nullable', 'string', 'max:30'],
            'wali_password' => ['nullable', 'string', 'min:6'],
            'wali_alamat' => ['nullable', 'string', 'max:500'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'target_juz' => ['nullable', 'string', 'max:50'],
            'create_wali_account' => ['sometimes', 'boolean'],
        ]);

        $waliId = $data['wali_id'] ?? null;

        // Buat akun ortu jika diminta
        if (! empty($data['create_wali_account']) && ! empty($data['wali_nama'])) {
            $waliEmail = $data['wali_email'] ?? ($data['nis'] . '@wali.local');
            $waliPassword = $data['wali_password'] ?? 'password123';
            $waliUser = User::create([
                'name' => $data['wali_nama'],
                'email' => $waliEmail,
                'password' => bcrypt($waliPassword),
                'role' => 'ortu',
                'no_hp' => $data['wali_hp'] ?? null,
                'alamat' => $data['wali_alamat'] ?? null,
            ]);
            $waliId = $waliUser->id;
        }

        $santri = Santri::create([
            'tahun_ajaran_id' => $tahunAjaran->id,
            'nis' => $data['nis'],
            'nama_lengkap' => $data['nama_lengkap'],
            'kelas_id' => $data['kelas_id'] ?? null,
            'wali_id' => $waliId,
            'target_juz' => $data['target_juz'] ?? 'Juz 30',
            'progress_pct' => 0,
            'alamat' => $data['alamat'] ?? null,
        ]);

        $message = ! empty($data['create_wali_account']) && ! empty($data['wali_nama'])
            ? 'Santri dan akun orang tua berhasil didaftarkan'
            : 'Santri baru berhasil didaftarkan';

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $santri->load(['kelas', 'wali']),
        ], 201);
    }

    public function updateSantri(Request $request, Santri $santri)
    {
        $tahunAjaran = $this->tahunAjaranAktif();
        abort_unless($santri->tahun_ajaran_id === $tahunAjaran->id, 404);

        $data = $request->validate([
            'nis' => ['sometimes', 'string', 'max:50', Rule::unique('santris', 'nis')->ignore($santri->id)],
            'nama_lengkap' => ['sometimes', 'string', 'max:255'],
            'kelas_id' => ['nullable', Rule::exists('kelas_rombels', 'id')->where('tahun_ajaran_id', $tahunAjaran->id)],
            'wali_id' => ['nullable', Rule::exists('users', 'id')->where('role', 'ortu')],
            'alamat' => ['nullable', 'string', 'max:500'],
            'target_juz' => ['nullable', 'string', 'max:50'],
            'progress_pct' => ['sometimes', 'integer', 'min:0', 'max:100'],
            'status_aktif' => ['sometimes', 'boolean'],
        ]);

        $santri->update($data);

        return response()->json(['success' => true, 'data' => $santri->fresh(['kelas', 'wali'])]);
    }

    // Update ortu user data
    public function updateOrtu(Request $request, User $user)
    {
        abort_unless($user->role === 'ortu', 403, 'User ini bukan orang tua');

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        if (isset($data['password']) && $data['password'] === '') {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json(['success' => true, 'data' => $user]);
    }

    public function mappingGuru()
    {
        $tahunAjaran = $this->tahunAjaranAktif();

        return response()->json(['success' => true, 'data' => PengampuKelas::with(['guru', 'kelas'])->where('tahun_ajaran_id', $tahunAjaran->id)->get()]);
    }

    public function storeMapping(Request $request)
    {
        $tahunAjaran = $this->tahunAjaranAktif();
        $data = $request->validate([
            'guru_id' => ['required', Rule::exists('users', 'id')->where('role', 'guru')],
            'kelas_id' => ['required', Rule::exists('kelas_rombels', 'id')->where('tahun_ajaran_id', $tahunAjaran->id)],
            'jadwal_halaqah' => ['nullable', 'string', 'max:100'],
        ]);

        $mapping = PengampuKelas::create($data + ['tahun_ajaran_id' => $tahunAjaran->id]);

        return response()->json(['success' => true, 'data' => $mapping->load(['guru', 'kelas'])], 201);
    }

    public function updateMapping(Request $request, PengampuKelas $mapping)
    {
        $tahunAjaran = $this->tahunAjaranAktif();
        abort_unless($mapping->tahun_ajaran_id === $tahunAjaran->id, 404);

        $data = $request->validate([
            'guru_id' => ['sometimes', Rule::exists('users', 'id')->where('role', 'guru')],
            'kelas_id' => ['sometimes', Rule::exists('kelas_rombels', 'id')->where('tahun_ajaran_id', $tahunAjaran->id)],
            'jadwal_halaqah' => ['nullable', 'string', 'max:100'],
        ]);

        $mapping->update($data);

        return response()->json(['success' => true, 'data' => $mapping->fresh(['guru', 'kelas'])]);
    }

    public function deleteMapping(PengampuKelas $mapping)
    {
        $mapping->delete();

        return response()->json(['success' => true, 'message' => 'Mapping dihapus']);
    }

    public function laporanGlobal()
    {
        $tahunAjaran = $this->tahunAjaranAktif();
        $rekapKelas = KelasRombel::with(['santris' => fn ($q) => $q->where('tahun_ajaran_id', $tahunAjaran->id)])
            ->withCount(['santris' => fn ($q) => $q->where('tahun_ajaran_id', $tahunAjaran->id)])
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->get()
            ->map(function ($kelas) use ($tahunAjaran) {
                $setoran = Setoran::where('tahun_ajaran_id', $tahunAjaran->id)
                    ->whereHas('santri', fn ($q) => $q->where('kelas_id', $kelas->id))
                    ->get();
                $total = $setoran->count();

                return [
                    'kelas' => $kelas->nama_kelas,
                    'santri' => $kelas->santris_count,
                    'lancar' => $setoran->where('status', 'lancar')->count(),
                    'pct' => ($total ? round(($setoran->where('status', 'lancar')->count() / $total) * 100) : 0).'%',
                    'ulang' => $setoran->where('status', 'mengulang')->count(),
                ];
            });

        return response()->json(['success' => true, 'data' => ['tahun_ajaran' => $tahunAjaran, 'rekap_kelas' => $rekapKelas]]);
    }

    // Master Data - All Santris (no filter by tahun ajaran)
    public function masterSantriAll()
    {
        return response()->json([
            'success' => true,
            'data' => Santri::with(['kelas', 'wali', 'tahunAjaran'])
                ->withCount('setorans')
                ->latest()
                ->get(),
        ]);
    }

    // Master Data - All Kelas (no filter by tahun ajaran)
    public function masterKelasAll()
    {
        return response()->json([
            'success' => true,
            'data' => KelasRombel::with(['tahunAjaran'])
                ->withCount('santris')
                ->latest()
                ->get(),
        ]);
    }

    // Master Data - List all Ortu/Wali users
    public function listOrtu()
    {
        return response()->json([
            'success' => true,
            'data' => User::where('role', 'ortu')
                ->withCount('santris')
                ->latest()
                ->get(),
        ]);
    }

    // Rombel - Get all rombel kelas with students for current tahun ajaran
    public function rombelList()
    {
        $tahunAjaran = $this->tahunAjaranAktif();

        $rombels = KelasRombel::with(['santris' => fn ($q) => $q->where('tahun_ajaran_id', $tahunAjaran->id)])
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->withCount(['santris' => fn ($q) => $q->where('tahun_ajaran_id', $tahunAjaran->id)])
            ->get()
            ->map(function ($rombel) {
                return [
                    'id' => $rombel->id,
                    'nama_kelas' => $rombel->nama_kelas,
                    'santris_count' => $rombel->santris_count,
                    'santris' => $rombel->santris->map(fn ($s) => [
                        'id' => $s->id,
                        'nama_lengkap' => $s->nama_lengkap,
                        'nis' => $s->nis,
                    ]),
                ];
            });

        // Students without kelas
        $tanpaKelas = Santri::where('tahun_ajaran_id', $tahunAjaran->id)
            ->whereNull('kelas_id')
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'nama_lengkap' => $s->nama_lengkap,
                'nis' => $s->nis,
            ]);

        return response()->json([
            'success' => true,
            'data' => [
                'tahun_ajaran' => $tahunAjaran,
                'rombels' => $rombels,
                'tanpa_kelas' => $tanpaKelas,
            ],
        ]);
    }

    // Rombel - Assign student to kelas
    public function rombelAssign(Request $request)
    {
        $tahunAjaran = $this->tahunAjaranAktif();
        $data = $request->validate([
            'santri_id' => ['required', 'exists:santris,id'],
            'kelas_id' => ['required', 'exists:kelas_rombels,id'],
        ]);

        // Verify tahun ajaran match
        $santri = Santri::findOrFail($data['santri_id']);
        abort_unless($santri->tahun_ajaran_id === $tahunAjaran->id, 403, 'Santri tidak ada di tahun ajaran aktif');

        $kelas = KelasRombel::findOrFail($data['kelas_id']);
        abort_unless($kelas->tahun_ajaran_id === $tahunAjaran->id, 403, 'Kelas tidak ada di tahun ajaran aktif');

        $santri->update(['kelas_id' => $data['kelas_id']]);

        return response()->json([
            'success' => true,
            'message' => 'Santri berhasil dimasukkan ke kelas',
            'data' => $santri->load('kelas'),
        ]);
    }

    // Rombel - Remove student from kelas
    public function rombelRemove(Request $request)
    {
        $tahunAjaran = $this->tahunAjaranAktif();
        $data = $request->validate([
            'santri_id' => ['required', 'exists:santris,id'],
        ]);

        $santri = Santri::findOrFail($data['santri_id']);
        abort_unless($santri->tahun_ajaran_id === $tahunAjaran->id, 403, 'Santri tidak ada di tahun ajaran aktif');

        $santri->update(['kelas_id' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Santri dikeluarkan dari kelas',
        ]);
    }

    // Master Kelas - Store new kelas rombel
    public function storeKelas(Request $request)
    {
        $tahunAjaran = $this->tahunAjaranAktif();
        $data = $request->validate([
            'nama_kelas' => ['required', 'string', 'max:100'],
        ]);

        $kelas = KelasRombel::create([
            'nama_kelas' => $data['nama_kelas'],
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kelas berhasil ditambahkan',
            'data' => $kelas->load('tahunAjaran'),
        ], 201);
    }

    // Master Kelas - Update kelas rombel
    public function updateKelas(Request $request, KelasRombel $kelas)
    {
        $data = $request->validate([
            'nama_kelas' => ['sometimes', 'string', 'max:100'],
        ]);

        $kelas->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Kelas berhasil diupdate',
            'data' => $kelas->fresh('tahunAjaran'),
        ]);
    }

    // Master Kelas - Delete kelas rombel
    public function deleteKelas(KelasRombel $kelas)
    {
        if ($kelas->santris()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak bisa hapus kelas yang sudah punya santri',
            ], 422);
        }

        $kelas->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kelas berhasil dihapus',
        ]);
    }

    // Master Surahs - List all surahs from Al-Quran API with local DB IDs
    public function masterSurahs(AlQuranCloudService $alQuranService)
    {
        $surahs = $alQuranService->getAllSurah();

        return response()->json([
            'success' => true,
            'data' => $surahs,
        ]);
    }

    // Sync Surahs - Populate/sync all 114 surahs to local database
    public function syncSurahs(AlQuranCloudService $alQuranService)
    {
        $count = $alQuranService->syncAllSurahsToDatabase();

        return response()->json([
            'success' => true,
            'message' => "Berhasil sinkronisasi $count surah ke database",
            'data' => Surah::orderBy('nomor')->get(),
        ]);
    }

    // Riwayat Setoran - All setorans with filters
    public function listSetorans(Request $request)
    {
        $tahunAjaran = $this->tahunAjaranAktif();

        $query = Setoran::with(['santri:id,nama_lengkap,nis,kelas_id', 'guru:id,name', 'santri.kelas', 'surah'])
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->orderBy('waktu_setor', 'desc');

        // Filter by kelas
        if ($request->filled('kelas_id')) {
            $query->whereHas('santri', fn ($q) => $q->where('kelas_id', $request->kelas_id));
        }

        // Filter by date range
        if ($request->filled('tanggal')) {
            $query->whereDate('waktu_setor', $request->tanggal);
        }
        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('waktu_setor', [$request->tanggal_awal, $request->tanggal_akhir]);
        }

        // Search by nama/nis
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('santri', fn ($q) => $q->where('nama_lengkap', 'like', "%$search%")->orWhere('nis', 'like', "%$search%"));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $setorans = $query->paginate($request->get('per_page', 50));

        return response()->json([
            'success' => true,
            'data' => $setorans,
        ]);
    }
}
