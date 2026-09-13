<?php

use App\Models\Client;
use App\Models\KegiatanAnggota;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/master-status-client', function () {
    return view('master_status_client');
})->name('master-status-client');

Route::get('/dashboard', function () {
    $jumlahAnggota = User::count();
    $jumlahClientHO = Client::where('sumber_data', 1)->count();
    $jumlahClientPenginputan = Client::where('sumber_data', 2)->count();
    $jumlahKegiatan = KegiatanAnggota::count();

    return view('dashboard', compact('jumlahAnggota', 'jumlahClientHO', 'jumlahClientPenginputan', 'jumlahKegiatan'));
})->name('dashboard');

Route::get('/users', function () {
    return view('users');
})->name('users');

Route::get('/roles', function () {
    return view('roles');
})->name('roles');

Route::get('/jenis-kegiatan', function () {
    return view('jenis_kegiatan');
})->name('jenis-kegiatan');

Route::get('/kegiatan-anggota', function () {
    return view('kegiatan_anggota');
})->name('kegiatan-anggota');

Route::get('/clients', function () {
    return view('clients');
})->name('clients');

Route::get('/api-wilayah/{path}', function ($path) {
    $url = 'https://wilayah.id/api/'.$path;
    $response = Http::get($url);

    return response($response->body(), $response->status())->header('Content-Type', 'application/json');
})->where('path', '.*');
