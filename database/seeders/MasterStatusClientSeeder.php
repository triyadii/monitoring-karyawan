<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\MasterStatusClient;
use Illuminate\Support\Str;

class MasterStatusClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            'Berminat',
            'Belum berminat',
            'Tidak aktif',
            'Aktif tidak diangkat'
        ];

        foreach ($statuses as $status) {
            MasterStatusClient::firstOrCreate(['nama_status' => $status], [
                'id' => Str::uuid()->toString(),
                'nama_status' => $status
            ]);
        }
    }
}
