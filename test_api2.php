<?php

use App\Models\JenisKegiatan;
use App\Models\User;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);

$user = User::whereHas('role', function ($q) {
    $q->where('nama_role', 'anggota');
})->first();
$token = Auth::guard('api')->login($user);
$jenis = JenisKegiatan::first();

$request = Request::create('/api/kegiatan-anggota', 'POST', [
    'nama_kegiatan_user' => 'Test',
    'jenis_kegiatan_id' => $jenis->id,
    'tanggal_kegiatan' => '2026-09-12T12:00',
]);
$request->headers->set('Authorization', 'Bearer '.$token);
$request->headers->set('Accept', 'application/json');

$response = $kernel->handle($request);
echo 'Status: '.$response->getStatusCode()."\n";
echo 'Content: '.$response->getContent()."\n";
