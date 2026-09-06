<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GuruController;
use App\Http\Controllers\Api\OrtuController;
use App\Http\Controllers\Api\TahunAjaranController;
use Illuminate\Support\Facades\Route;

// Public Auth Routes
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes (Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::prefix('guru')->middleware('role:guru')->group(function () {
        Route::get('/dashboard', [GuruController::class, 'dashboard']);
        Route::get('/master/kelas', [GuruController::class, 'listKelas']);
        Route::get('/master/surahs', [GuruController::class, 'listSurah']);
        Route::get('/santris', [GuruController::class, 'listSantri']);
        Route::get('/santris/{santriId}/completed-surahs', [GuruController::class, 'completedSurahs']);
        Route::get('/setorans', [GuruController::class, 'listSetoran']);
        Route::post('/setorans', [GuruController::class, 'storeSetoran']);
        Route::put('/setorans/{id}', [GuruController::class, 'updateSetoran']);
        Route::delete('/setorans/{id}', [GuruController::class, 'deleteSetoran']);
    });

    Route::prefix('ortu')->middleware('role:ortu')->group(function () {
        Route::get('/dashboard', [OrtuController::class, 'dashboard']);
        Route::get('/santri/{santriId}/timeline', [OrtuController::class, 'timeline']);
    });

    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard']);
        Route::get('/users', [AdminController::class, 'listUsers']);
        Route::get('/users', [AdminController::class, 'index']);
        Route::post('/users', [AdminController::class, 'store']);
        Route::put('/users/{user}', [AdminController::class, 'update']);
        Route::delete('/users/{user}', [AdminController::class, 'destroy']);
        Route::get('/santris', [AdminController::class, 'listSantri']);
        Route::post('/santris', [AdminController::class, 'storeSantri']);
        Route::put('/santris/{santri}', [AdminController::class, 'updateSantri']);
        Route::delete('/santris/{santri}', [AdminController::class, 'destroySantri']);
        Route::get('/mapping', [AdminController::class, 'mappingGuru']);
        Route::post('/mapping', [AdminController::class, 'storeMapping']);
        Route::put('/mapping/{mapping}', [AdminController::class, 'updateMapping']);
        Route::delete('/mapping/{mapping}', [AdminController::class, 'deleteMapping']);
        Route::get('/laporan', [AdminController::class, 'laporanGlobal']);

        // Master Data Routes
        Route::get('/master/tahun-ajaran', [TahunAjaranController::class, 'index']);
        Route::post('/master/tahun-ajaran', [TahunAjaranController::class, 'store']);
        Route::put('/master/tahun-ajaran/{tahunAjaran}', [TahunAjaranController::class, 'update']);
        Route::delete('/master/tahun-ajaran/{tahunAjaran}', [TahunAjaranController::class, 'destroy']);
        Route::get('/master/santris-all', [AdminController::class, 'masterSantriAll']);
        Route::get('/master/kelas-all', [AdminController::class, 'masterKelasAll']);
        Route::get('/master/ortu', [AdminController::class, 'listOrtu']);
        Route::put('/master/ortu/{user}', [AdminController::class, 'updateOrtu']);
        Route::get('/master/surahs', [AdminController::class, 'masterSurahs']);

        // Rombel Routes
        Route::get('/rombel', [AdminController::class, 'rombelList']);
        Route::post('/rombel/assign', [AdminController::class, 'rombelAssign']);
        Route::post('/rombel/remove', [AdminController::class, 'rombelRemove']);

        // Master Kelas Routes
        Route::post('/master/kelas', [AdminController::class, 'storeKelas']);
        Route::put('/master/kelas/{kelas}', [AdminController::class, 'updateKelas']);
        Route::delete('/master/kelas/{kelas}', [AdminController::class, 'deleteKelas']);

        // Riwayat Setoran
        Route::get('/setorans', [AdminController::class, 'listSetorans']);
    });
});
