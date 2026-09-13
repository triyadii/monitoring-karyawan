<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\JenisKegiatanController;
use App\Http\Controllers\Api\KegiatanAnggotaController;
use App\Http\Controllers\Api\MasterStatusClientController;
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
    Route::apiResource('/users', UserController::class);
    Route::apiResource('/jenis-kegiatan', JenisKegiatanController::class);
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
