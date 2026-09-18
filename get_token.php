<?php

use App\Models\User;
use Illuminate\Contracts\Http\Kernel;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);

$user = User::whereHas('role', function ($q) {
    $q->where('nama_role', 'anggota');
})->first();
$token = Auth::guard('api')->login($user);
echo $token;
