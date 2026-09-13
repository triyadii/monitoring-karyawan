<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        $sqlFile = database_path('seeders/wilayah.sql');
        if (! File::exists($sqlFile)) {
            $this->command->error('File wilayah.sql tidak ditemukan. Silakan download terlebih dahulu.');

            return;
        }

        $this->command->info('Mengeksekusi file dump wilayah.sql... (Ini akan memakan waktu beberapa detik)');
        DB::unprepared(File::get($sqlFile));

        $this->command->info('Memindahkan data provinsi...');
        DB::statement('INSERT INTO provinces (code, name) SELECT kode, nama FROM wilayah WHERE LENGTH(kode) = 2 ON CONFLICT DO NOTHING');

        $this->command->info('Memindahkan data kabupaten/kota...');
        DB::statement('INSERT INTO regencies (code, province_code, name) SELECT kode, SUBSTRING(kode, 1, 2), nama FROM wilayah WHERE LENGTH(kode) = 5 ON CONFLICT DO NOTHING');

        $this->command->info('Memindahkan data kecamatan...');
        DB::statement('INSERT INTO districts (code, regency_code, name) SELECT kode, SUBSTRING(kode, 1, 5), nama FROM wilayah WHERE LENGTH(kode) = 8 ON CONFLICT DO NOTHING');

        $this->command->info('Memindahkan data kelurahan/desa...');
        DB::statement('INSERT INTO villages (code, district_code, name) SELECT kode, SUBSTRING(kode, 1, 8), nama FROM wilayah WHERE LENGTH(kode) = 13 ON CONFLICT DO NOTHING');

        $this->command->info('Menghapus tabel temporary wilayah...');
        DB::statement('DROP TABLE IF EXISTS wilayah');

        $this->command->info('Seeding data wilayah selesai!');
    }
}
