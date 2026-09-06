<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KelasRombel;
use App\Models\PengampuKelas;
use App\Models\Santri;
use App\Models\Setoran;
use App\Models\Surah;
use App\Models\TahunAjaran;
use App\Services\AlQuranCloudService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GuruController extends Controller
{
    private AlQuranCloudService $alQuranService;

    public function __construct(AlQuranCloudService $alQuranService)
    {
        $this->alQuranService = $alQuranService;
    }
    private function tahunAjaranAktif(): TahunAjaran
    {
        return TahunAjaran::aktif()->firstOrFail();
    }

    private function kelasIdsGuru(int $guruId, int $tahunAjaranId)
    {
        return PengampuKelas::where('guru_id', $guruId)->where('tahun_ajaran_id', $tahunAjaranId)->pluck('kelas_id');
    }

    public function dashboard(Request $request)
    {
        $tahunAjaran = $this->tahunAjaranAktif();
        $guruId = $request->user()->id;
        $kelasIds = $this->kelasIdsGuru($guruId, $tahunAjaran->id);

        // Get all kelas pengampu with santris
        $pengampus = PengampuKelas::with(['kelas', 'kelas.santris'])
            ->where('guru_id', $guruId)
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->get();

        $kelasBinaan = $pengampus->map(function ($p) {
            return [
                'id' => $p->kelas->id,
                'nama_kelas' => $p->kelas->nama_kelas,
                'jadwal' => $p->jadwal_halaqah,
                'santri_count' => $p->kelas->santris->count(),
                'santris' => $p->kelas->santris->map(fn ($s) => [
                    'id' => $s->id,
                    'nama_lengkap' => $s->nama_lengkap,
                    'nis' => $s->nis,
                    'progress_pct' => $s->progress_pct,
                ])->values()->toArray(),
            ];
        })->values()->toArray();

        return response()->json([
            'success' => true,
            'data' => [
                'guru' => $request->user(),
                'tahun_ajaran' => $tahunAjaran,
                'kelas_binaan' => $kelasBinaan,
                'stats' => [
                    'santri_terampu' => Santri::where('tahun_ajaran_id', $tahunAjaran->id)->whereIn('kelas_id', $kelasIds)->count(),
                    'setoran_hari_ini' => Setoran::where('tahun_ajaran_id', $tahunAjaran->id)->where('guru_id', $guruId)->whereDate('waktu_setor', today())->count(),
                ],
                'recent_feed' => Setoran::with(['santri', 'surah'])
                    ->where('tahun_ajaran_id', $tahunAjaran->id)
                    ->where('guru_id', $guruId)
                    ->latest('waktu_setor')
                    ->take(5)
                    ->get(),
            ],
        ]);
    }

    public function listKelas(Request $request)
    {
        $tahunAjaran = $this->tahunAjaranAktif();
        $kelasIds = $this->kelasIdsGuru($request->user()->id, $tahunAjaran->id);

        return response()->json(['success' => true, 'data' => KelasRombel::withCount('santris')->where('tahun_ajaran_id', $tahunAjaran->id)->whereIn('id', $kelasIds)->get()]);
    }

    public function listSantri(Request $request)
    {
        $tahunAjaran = $this->tahunAjaranAktif();
        $kelasIds = $this->kelasIdsGuru($request->user()->id, $tahunAjaran->id);

        $query = Santri::with('kelas')->where('tahun_ajaran_id', $tahunAjaran->id)->whereIn('kelas_id', $kelasIds);
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        return response()->json(['success' => true, 'data' => $query->get()]);
    }

    public function listSurah()
    {
        // Surah hafalan harus berasal dari database lokal (Juz 30: Surah 78-114) agar ID cocok 1:1 dengan primary key tabel surahs
        $surahs = Surah::orderBy('nomor')->get();

        return response()->json([
            'success' => true,
            'data' => $surahs,
            'source' => 'database',
        ]);
    }

    public function listSetoran(Request $request)
    {
        $tahunAjaran = $this->tahunAjaranAktif();
        $kelasIds = $this->kelasIdsGuru($request->user()->id, $tahunAjaran->id);
        $query = Setoran::with(['santri.kelas', 'surah', 'guru'])->where('tahun_ajaran_id', $tahunAjaran->id)->where('guru_id', $request->user()->id)->latest('waktu_setor');

        if ($request->filled('status') && $request->status !== 'Semua') {
            $query->where('status', strtolower($request->status));
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('santri', fn ($sq) => $sq->where('nama_lengkap', 'like', "%$search%"))
                    ->orWhereHas('surah', fn ($sq) => $sq->where('nama_latin', 'like', "%$search%"));
            });
        }

        return response()->json(['success' => true, 'data' => $query->paginate(20)]);
    }

    public function storeSetoran(Request $request)
    {
        $tahunAjaran = $this->tahunAjaranAktif();
        $kelasIds = $this->kelasIdsGuru($request->user()->id, $tahunAjaran->id);

        $data = $request->validate([
            'santri_id' => ['required', Rule::exists('santris', 'id')->where('tahun_ajaran_id', $tahunAjaran->id)->whereIn('kelas_id', $kelasIds)],
            'surah_id' => ['required', Rule::exists('surahs', 'id')],
            'ayat_mulai' => ['required', 'integer', 'min:1'],
            'ayat_selesai' => ['required', 'integer', 'gte:ayat_mulai'],
            'status' => ['required', Rule::in(['lancar', 'kurang', 'mengulang'])],
            'catatan' => ['nullable', 'string'],
        ]);

        $surah = Surah::findOrFail($data['surah_id']);
        abort_if($data['ayat_selesai'] > $surah->jumlah_ayat, 422, 'Ayat selesai melebihi jumlah ayat surah.');

        $setoran = Setoran::create($data + [
            'tahun_ajaran_id' => $tahunAjaran->id,
            'guru_id' => $request->user()->id,
            'nilai' => $data['status'] === 'lancar' ? 95 : ($data['status'] === 'kurang' ? 78 : 65),
            'waktu_setor' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Setoran hafalan berhasil disimpan', 'data' => $setoran->load(['santri', 'surah'])], 201);
    }

    public function updateSetoran(Request $request, $id)
    {
        $tahunAjaran = $this->tahunAjaranAktif();
        $setoran = Setoran::where('tahun_ajaran_id', $tahunAjaran->id)->where('guru_id', $request->user()->id)->findOrFail($id);

        $data = $request->validate([
            'ayat_mulai' => ['sometimes', 'integer', 'min:1'],
            'ayat_selesai' => ['sometimes', 'integer'],
            'status' => ['sometimes', Rule::in(['lancar', 'kurang', 'mengulang'])],
            'catatan' => ['nullable', 'string'],
            'nilai' => ['sometimes', 'integer', 'min:0', 'max:100'],
        ]);

        $setoran->update($data);

        return response()->json(['success' => true, 'message' => 'Data setoran berhasil diperbarui', 'data' => $setoran->load(['santri', 'surah'])]);
    }

    public function deleteSetoran(Request $request, $id)
    {
        $tahunAjaran = $this->tahunAjaranAktif();
        $setoran = Setoran::where('tahun_ajaran_id', $tahunAjaran->id)->where('guru_id', $request->user()->id)->findOrFail($id);
        $setoran->delete();

        return response()->json(['success' => true, 'message' => 'Data setoran berhasil dihapus']);
    }

    public function completedSurahs(Request $request, $santriId)
    {
        $tahunAjaran = $this->tahunAjaranAktif();
        $kelasIds = $this->kelasIdsGuru($request->user()->id, $tahunAjaran->id);

        $santri = Santri::where('tahun_ajaran_id', $tahunAjaran->id)
            ->whereIn('kelas_id', $kelasIds)
            ->findOrFail($santriId);

        $completedSurahIds = Setoran::where('santri_id', $santri->id)
            ->where('status', '!=', 'mengulang')
            ->join('surahs', 'setorans.surah_id', '=', 'surahs.id')
            ->whereRaw('setorans.ayat_selesai >= surahs.jumlah_ayat')
            ->distinct()
            ->pluck('surahs.id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->toArray();

        return response()->json([
            'success' => true,
            'data' => [
                'santri_id' => (int) $santri->id,
                'completed_surah_ids' => $completedSurahIds,
            ],
        ]);
    }
}
