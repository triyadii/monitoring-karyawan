<?php

namespace Database\Seeders;

use App\Models\KegiatanAnggota;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class KegiatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the user "anggota1" that we seeded previously
        $anggota = User::where('username', 'anggota1')->first();

        if ($anggota) {
            KegiatanAnggota::create([
                'user_id' => $anggota->id,
                'nama_kegiatan_user' => 'Kunjungan Client HO',
                'tanggal_kegiatan' => Carbon::now()->subDays(2),
                'foto' => null, // Dummy foto
            ]);

            KegiatanAnggota::create([
                'user_id' => $anggota->id,
                'nama_kegiatan_user' => 'Presentasi Produk',
                'tanggal_kegiatan' => Carbon::now()->subDay(),
                'foto' => null,
            ]);
        }
    }
}
