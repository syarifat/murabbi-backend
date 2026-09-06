<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KelasRombel;
use App\Models\PengampuKelas;
use App\Models\Santri;
use App\Models\Setoran;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TahunAjaranController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => TahunAjaran::withCount(['kelas', 'santris'])->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:50', 'unique:tahun_ajarans,nama'],
            'aktif' => ['sometimes', 'boolean'],
        ]);

        // If this tahun ajaran will be active, deactivate others
        if (! empty($data['aktif'])) {
            TahunAjaran::where('aktif', true)->update(['aktif' => false]);
        }

        $tahunAjaran = TahunAjaran::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Tahun ajaran berhasil ditambahkan',
            'data' => $tahunAjaran->loadCount(['kelas', 'santris']),
        ], 201);
    }

    public function update(Request $request, TahunAjaran $tahunAjaran)
    {
        $data = $request->validate([
            'nama' => ['sometimes', 'string', 'max:50', 'unique:tahun_ajarans,nama,' . $tahunAjaran->id],
            'aktif' => ['sometimes', 'boolean'],
        ]);

        // If activating this tahun ajaran, deactivate others first
        if (! empty($data['aktif']) && ! $tahunAjaran->aktif) {
            DB::transaction(function () use ($tahunAjaran) {
                TahunAjaran::where('aktif', true)->update(['aktif' => false]);
                $tahunAjaran->update(['aktif' => true]);
            });
        } else {
            $tahunAjaran->update($data);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tahun ajaran berhasil diperbarui',
            'data' => $tahunAjaran->fresh()->loadCount(['kelas', 'santris']),
        ]);
    }

    public function destroy(TahunAjaran $tahunAjaran)
    {
        // Check if there are related data
        $hasSantris = Santri::where('tahun_ajaran_id', $tahunAjaran->id)->exists();
        $hasKelas = KelasRombel::where('tahun_ajaran_id', $tahunAjaran->id)->exists();

        if ($hasSantris || $hasKelas) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus tahun ajaran yang memiliki data siswa atau kelas. Pindahkan atau hapus data terlebih dahulu.',
            ], 422);
        }

        // If deleting active tahun ajaran, activate another one
        $wasActive = $tahunAjaran->aktif;
        $tahunAjaran->delete();

        if ($wasActive) {
            $latest = TahunAjaran::latest('id')->first();
            if ($latest) {
                $latest->update(['aktif' => true]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Tahun ajaran berhasil dihapus',
        ]);
    }
}
