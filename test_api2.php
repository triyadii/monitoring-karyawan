<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$user = \App\Models\User::whereHas('role', function($q) { $q->where('nama_role', 'anggota'); })->first();
$token = Auth::guard('api')->login($user);
$jenis = \App\Models\JenisKegiatan::first();

$request = Illuminate\Http\Request::create('/api/kegiatan-anggota', 'POST', [
    'nama_kegiatan_user' => 'Test',
    'jenis_kegiatan_id' => $jenis->id,
    'tanggal_kegiatan' => '2026-09-12T12:00',
]);
$request->headers->set('Authorization', 'Bearer ' . $token);
$request->headers->set('Accept', 'application/json');

$response = $kernel->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
echo "Content: " . $response->getContent() . "\n";
