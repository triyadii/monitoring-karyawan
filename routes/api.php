<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\JenisKegiatanController;
use App\Http\Controllers\Api\JenisPegawaiController;
use App\Http\Controllers\Api\KegiatanAnggotaController;
use App\Http\Controllers\Api\ManajemenAksiController;
use App\Http\Controllers\Api\ManajemenCanvasingController;
use App\Http\Controllers\Api\ManajemenHoController;
use App\Http\Controllers\Api\ManajemenPpdController;
use App\Http\Controllers\Api\ManajemenVisitController;
use App\Http\Controllers\Api\MasterStatusClientController;
use App\Http\Controllers\Api\MemberStatsController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\WilayahController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

// Wilayah Routes (Public)
Route::get('/wilayah/provinces', [WilayahController::class, 'provinces']);
Route::get('/wilayah/regencies/{province_code}', [WilayahController::class, 'regencies']);
Route::get('/wilayah/districts/{regency_code}', [WilayahController::class, 'districts']);
Route::get('/wilayah/villages/{district_code}', [WilayahController::class, 'villages']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('/roles', RoleController::class);
    Route::patch('/users/{id}/password', [UserController::class, 'updatePassword']);
    Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword']);
    Route::post('/users/{id}/location', [UserController::class, 'updateLocation']);
    Route::get('/users/{id}/location', [UserController::class, 'getLocation']);
    Route::get('/users-locations', [UserController::class, 'locations']);
    Route::apiResource('/users', UserController::class);
    Route::apiResource('/jenis-kegiatan', JenisKegiatanController::class);
    Route::apiResource('/jenis-pegawai', JenisPegawaiController::class);
    Route::post('/manajemen-aksi/{uuid}', [ManajemenAksiController::class, 'update']);
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('/member-stats/{type}', [MemberStatsController::class, 'getStats']);
    Route::get('/export/manajemen-aksi', [ManajemenAksiController::class, 'export']);
    Route::get('/manajemen-aksi/user/{userId}', [ManajemenAksiController::class, 'getByUser']);
    Route::apiResource('/manajemen-aksi', ManajemenAksiController::class)->except(['update']);

    Route::post('/manajemen-visit/{uuid}', [ManajemenVisitController::class, 'update']);
    Route::get('/export/manajemen-visit', [ManajemenVisitController::class, 'export']);
    Route::get('/manajemen-visit/user/{userId}', [ManajemenVisitController::class, 'getByUser']);
    Route::apiResource('/manajemen-visit', ManajemenVisitController::class)->except(['update']);

    Route::post('/manajemen-ppd/{uuid}', [ManajemenPpdController::class, 'update']);
    Route::get('/export/manajemen-ppd', [ManajemenPpdController::class, 'export']);
    Route::get('/manajemen-ppd/user/{userId}', [ManajemenPpdController::class, 'getByUser']);
    Route::apiResource('/manajemen-ppd', ManajemenPpdController::class)->except(['update']);

    Route::get('/export/manajemen-ho', [ManajemenHoController::class, 'export']);
    Route::get('/manajemen-ho/user/{userId}', [ManajemenHoController::class, 'getByUser']);
    Route::apiResource('/manajemen-ho', ManajemenHoController::class);

    Route::get('/export/manajemen-canvasing', [ManajemenCanvasingController::class, 'export']);
    Route::get('/manajemen-canvasing/user/{userId}', [ManajemenCanvasingController::class, 'getByUser']);
    Route::apiResource('/manajemen-canvasing', ManajemenCanvasingController::class);
    Route::apiResource('/master-status-clients', MasterStatusClientController::class);
    Route::post('/clients/import', [ClientController::class, 'import']);
    Route::post('/clients/bulk-disposisi', [ClientController::class, 'bulkDisposisi']);
    Route::post('/clients/{id}/disposisi', [ClientController::class, 'disposisi']);
    Route::patch('/clients/{id}/status', [ClientController::class, 'updateStatus']);
    Route::get('/clients/stats/status', [ClientController::class, 'getStatusStats']);
    Route::get('/clients/stats/sumber-data', [ClientController::class, 'getSumberDataStats']);
    Route::get('/clients/stats/user', [ClientController::class, 'getUserStats']);
    Route::get('/clients/my-disposisi', [ClientController::class, 'myDisposisi']);
    Route::apiResource('/clients', ClientController::class);

    // Explicit POST route for update to handle multipart/form-data properly
    Route::get('/kegiatan-anggota/user/{userId}', [KegiatanAnggotaController::class, 'getByUser']);
    Route::post('/kegiatan-anggota/{id}', [KegiatanAnggotaController::class, 'update']);
    Route::get('/kegiatan-anggota/my-kegiatan', [KegiatanAnggotaController::class, 'myKegiatan']);
    Route::apiResource('/kegiatan-anggota', KegiatanAnggotaController::class)->except(['update']);
});
