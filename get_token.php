<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$user = \App\Models\User::whereHas('role', function($q) { $q->where('nama_role', 'anggota'); })->first();
$token = Auth::guard('api')->login($user);
echo $token;
