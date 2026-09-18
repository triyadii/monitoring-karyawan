<?php

namespace Database\Seeders;

use App\Models\ManajemenAksi;
use App\Models\ManajemenCanvasing;
use App\Models\ManajemenHo;
use App\Models\ManajemenPpd;
use App\Models\ManajemenVisit;
use App\Models\MasterStatusClient;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ManajemenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bersihkan data lama
        ManajemenAksi::query()->delete();
        ManajemenCanvasing::query()->delete();
        ManajemenHo::query()->delete();
        ManajemenPpd::query()->delete();
        ManajemenVisit::query()->delete();

        // Ambil user dengan role selain Leader dan superadmin
        $users = User::whereHas('role', function($q) {
            $q->whereNotIn('nama_role', ['superadmin', 'Leader']);
        })->get();

        if ($users->isEmpty()) {
            $users = User::all();
        }

        if ($users->isEmpty()) {
            return;
        }

        $status = MasterStatusClient::first();
        if (!$status) {
            $status = MasterStatusClient::create([
                'namaStatus' => 'Status Dummy',
            ]);
        }

        // Loop untuk membagi data ke tiap user
        foreach ($users as $index => $user) {
            // Buat 3 data untuk masing-masing user
            for ($i = 1; $i <= 3; $i++) {
                // Manajemen Aksi
                ManajemenAksi::create([
                    'user_id' => $user->id,
                    'idAksi' => 'AKS-' . strtoupper(Str::random(4)) . '-' . $i,
                    'namaAksi' => 'Aksi User ' . $user->nama . ' ' . $i,
                    'kegiatan' => 'Kegiatan Aksi ' . $i,
                    'status' => 1,
                ]);

                // Manajemen Canvasing
                ManajemenCanvasing::create([
                    'user_id' => $user->id,
                    'namaClient' => 'Client Canvasing ' . $user->nama . ' ' . $i,
                    'nomorTelepon' => '0812345678' . $i,
                    'alamat' => 'Alamat Canvasing ' . $i,
                    'status_id' => $status->id,
                ]);

                // Manajemen HO
                ManajemenHo::create([
                    'user_id' => $user->id,
                    'namaClient' => 'Client HO ' . $user->nama . ' ' . $i,
                    'nomorTelepon' => '0812345678' . $i,
                    'alamat' => 'Alamat HO ' . $i,
                    'status_id' => $status->id,
                ]);

                // Manajemen PPD
                ManajemenPpd::create([
                    'user_id' => $user->id,
                    'namaClient' => 'Client PPD ' . $user->nama . ' ' . $i,
                    'nomorTelepon' => '0812345678' . $i,
                    'alamat' => 'Alamat PPD ' . $i,
                    'nomorKontrak' => 'KONTRAK-' . strtoupper(Str::random(4)) . '-' . $i,
                    'tenor' => '12 Bulan',
                    'angsuran' => '1000000',
                    'merk' => 'Honda',
                    'type' => 'Motor',
                    'jenisKendaraan' => 'Roda Dua',
                    'pinjaman' => '10000000',
                    'jatuhTempo' => now()->addMonths(1)->format('Y-m-d'),
                ]);

                // Manajemen Visit
                ManajemenVisit::create([
                    'user_id' => $user->id,
                    'namaClient' => 'Client Visit ' . $user->nama . ' ' . $i,
                    'nomorTelepon' => '0812345678' . $i,
                    'alamat' => 'Alamat Visit ' . $i,
                    'kegiatan' => 'Kegiatan Visit ' . $i,
                    'status_id' => $status->id,
                ]);
            }
        }
    }
}
