<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Client::create([
            'nama' => 'Client Satu (HO)',
            'alamat' => 'Jl. Kebon Kacang No 1',
            'kelurahan' => 'Kebon Kacang',
            'kecamatan' => 'Tanah Abang',
            'kabupaten' => 'Jakarta Pusat',
            'nomor_telepon' => '081234567890',
            'status_client' => 1,
            'sumber_data' => 1, // HO
        ]);

        Client::create([
            'nama' => 'Client Dua (Anggota)',
            'alamat' => 'Jl. Melati No 2',
            'kelurahan' => 'Melati',
            'kecamatan' => 'Gambir',
            'kabupaten' => 'Jakarta Pusat',
            'nomor_telepon' => '081987654321',
            'status_client' => 1,
            'sumber_data' => 2, // Anggota
        ]);
    }
}
