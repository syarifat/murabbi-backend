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

        return response()->json([
            'success' => true,
            'data' => [
                'wali' => $request->user(),
                'tahun_ajaran' => $tahunAjaran,
                'santris' => $santris,
                'capaian_terbaru' => optional($santris->first()?->setorans->first())->load('surah', 'guru'),
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
}
