<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        // Seed roles
        $data_roles = [
            [
                'id' => '01a08c25-f0de-7350-854b-1f879715ee08',
                'nama_role' => 'superadmin',
                'created_at' => '2026-09-10 16:28:11',
                'updated_at' => '2026-09-10 16:28:11',
            ],
            [
                'id' => '01a0adec-c949-7091-8335-4267dcbcad43',
                'nama_role' => 'Channeling',
                'created_at' => '2026-09-17 05:52:51',
                'updated_at' => '2026-09-17 05:52:51',
            ],
            [
                'id' => '01a0adec-e74a-72c5-a16a-b3556a040dd4',
                'nama_role' => 'Retention',
                'created_at' => '2026-09-17 05:52:59',
                'updated_at' => '2026-09-17 05:52:59',
            ],
            [
                'id' => '01a08c25-f0e2-73a8-b488-b55fae9bf5bb',
                'nama_role' => 'Leader',
                'created_at' => '2026-09-10 16:28:11',
                'updated_at' => '2026-09-17 06:07:40',
            ],
        ];
        foreach ($data_roles as $row) {
            DB::table('roles')->updateOrInsert(['id' => $row['id']], $row);
        }

        // Seed jenis_pegawais
        $data_jenis_pegawais = [
            [
                'uuid' => '01a0adf8-eb52-7086-96cf-23a1caf6db95',
                'jenisPegawai' => 'CCO',
                'created_at' => '2026-09-17 06:06:06',
                'updated_at' => '2026-09-17 06:06:06',
            ],
            [
                'uuid' => '01a0adf9-35c3-7275-ab8a-384dfc132e42',
                'jenisPegawai' => 'SPG',
                'created_at' => '2026-09-17 06:06:25',
                'updated_at' => '2026-09-17 06:06:25',
            ],
            [
                'uuid' => '01a0adf9-5c92-72e5-9dd4-d93229f2a1b4',
                'jenisPegawai' => 'CRO',
                'created_at' => '2026-09-17 06:06:35',
                'updated_at' => '2026-09-17 06:06:35',
            ],
            [
                'uuid' => '01a0adf9-9a30-72bc-8bd1-6ef77625252d',
                'jenisPegawai' => 'CBD',
                'created_at' => '2026-09-17 06:06:51',
                'updated_at' => '2026-09-17 06:06:51',
            ],
            [
                'uuid' => '01a0adff-0136-7260-b0f9-bfef27638d8b',
                'jenisPegawai' => 'Leader',
                'created_at' => '2026-09-17 06:12:45',
                'updated_at' => '2026-09-17 06:12:45',
            ],
            [
                'uuid' => '01a0ae00-25f8-73ab-818c-d98119b576d2',
                'jenisPegawai' => 'Superadmin',
                'created_at' => '2026-09-17 06:14:00',
                'updated_at' => '2026-09-17 06:14:06',
            ],
        ];
        foreach ($data_jenis_pegawais as $row) {
            DB::table('jenis_pegawais')->updateOrInsert(['id' => $row['id']], $row);
        }

        // Seed master_status_clients
        $data_master_status_clients = [
            [
                'id' => '285b5061-1c11-495c-825a-15cc7d3eb5d4',
                'nama_status' => 'Berminat',
                'created_at' => '2026-09-12 17:13:57',
                'updated_at' => '2026-09-12 17:13:57',
            ],
            [
                'id' => '6db93064-898c-4f17-9084-29e3414ac84b',
                'nama_status' => 'Belum berminat',
                'created_at' => '2026-09-12 17:13:57',
                'updated_at' => '2026-09-12 17:13:57',
            ],
            [
                'id' => 'c8d9c3e6-ccdc-4aee-b00f-2b39cb853de1',
                'nama_status' => 'Tidak aktif',
                'created_at' => '2026-09-12 17:13:57',
                'updated_at' => '2026-09-12 17:13:57',
            ],
            [
                'id' => '5a4a0f42-e57b-49bb-8e8c-23bc6539d25a',
                'nama_status' => 'Aktif tidak diangkat',
                'created_at' => '2026-09-12 17:13:57',
                'updated_at' => '2026-09-12 17:13:57',
            ],
        ];
        foreach ($data_master_status_clients as $row) {
            DB::table('master_status_clients')->updateOrInsert(['id' => $row['id']], $row);
        }

        // Seed jenis_kegiatans
        $data_jenis_kegiatans = [
            [
                'id' => '01a09658-f108-73dd-8581-41dbdec91752',
                'nama_jenis_kegiatan' => 'Canvas',
                'created_at' => '2026-09-12 16:00:06',
                'updated_at' => '2026-09-12 16:00:06',
            ],
            [
                'id' => '01a09659-0a13-7126-9e87-c4a8613d7fa7',
                'nama_jenis_kegiatan' => 'Visit',
                'created_at' => '2026-09-12 16:00:12',
                'updated_at' => '2026-09-12 16:00:12',
            ],
        ];
        foreach ($data_jenis_kegiatans as $row) {
            DB::table('jenis_kegiatans')->updateOrInsert(['id' => $row['id']], $row);
        }

        // Seed users
        $data_users = [
            [
                'id' => '01a08c25-f308-73d2-a658-5205c9db76c9',
                'role_id' => '01a08c25-f0e2-73a8-b488-b55fae9bf5bb',
                'username' => 'leader1',
                'password' => '$2y$12$uhyox5e1SoDzOjux4GHaVuRYjT2Ic9dkkhkiyZbFejzANP8ca3eYe',
                'nama' => 'Leader Pertama',
                'status' => 1,
                'remember_token' => null,
                'created_at' => '2026-09-10 16:28:12',
                'updated_at' => '2026-09-17 06:12:56',
                'jenis_pegawai_id' => '01a0adff-0136-7260-b0f9-bfef27638d8b',
            ],
            [
                'id' => '01a0ae04-2714-73bd-b376-29db697a6e7e',
                'role_id' => '01a0adec-e74a-72c5-a16a-b3556a040dd4',
                'username' => 'budiman_spg',
                'password' => '$2y$12$L94wi43yrDciSffHAlpH1ugrtmSFrzUp6CXJdx4x3QCHAHaetISgG',
                'nama' => 'Budiman SPG',
                'status' => 1,
                'remember_token' => null,
                'created_at' => '2026-09-17 06:18:22',
                'updated_at' => '2026-09-17 06:18:22',
                'jenis_pegawai_id' => '01a0adf9-35c3-7275-ab8a-384dfc132e42',
            ],
            [
                'id' => '01a0ae04-ad52-715a-82c5-07441a09b529',
                'role_id' => '01a0adec-e74a-72c5-a16a-b3556a040dd4',
                'username' => 'budiman_cro',
                'password' => '$2y$12$yPHpcZcWz3osN55o9LsdMOXNZGhfaRonkq2eCrM0miaGstdepmjS.',
                'nama' => 'Budiman CRO',
                'status' => 1,
                'remember_token' => null,
                'created_at' => '2026-09-17 06:18:57',
                'updated_at' => '2026-09-17 06:18:57',
                'jenis_pegawai_id' => '01a0adf9-5c92-72e5-9dd4-d93229f2a1b4',
            ],
            [
                'id' => '01a0ae03-c99d-7029-9962-0bc9ec533b14',
                'role_id' => '01a0adec-c949-7091-8335-4267dcbcad43',
                'username' => 'budiman_cco',
                'password' => '$2y$12$s6vSn/WeTRd93Z4u2kTDjO3LkeKkHaYRaVJ7aK56NJFOWrvaQGN6i',
                'nama' => 'Budiman CCO',
                'status' => 1,
                'remember_token' => null,
                'created_at' => '2026-09-17 06:17:58',
                'updated_at' => '2026-09-17 11:25:21',
                'jenis_pegawai_id' => '01a0adf8-eb52-7086-96cf-23a1caf6db95',
            ],
            [
                'id' => '01a08c25-f1fd-70ea-a73c-cdf7920fd725',
                'role_id' => '01a08c25-f0de-7350-854b-1f879715ee08',
                'username' => 'superadmin',
                'password' => '$2y$12$WwV8IuZ4emr0m8nN2hkhPuURePb3rBe3B1P5BE6FkfjREAGZqyiK2',
                'nama' => 'Super Administrator',
                'status' => 1,
                'remember_token' => null,
                'created_at' => '2026-09-10 16:28:12',
                'updated_at' => '2026-09-18 03:39:28',
                'jenis_pegawai_id' => '01a0ae00-25f8-73ab-818c-d98119b576d2',
            ],
            [
                'id' => '01a08c25-f40f-71b1-809e-2047821f9b75',
                'role_id' => '01a0adec-c949-7091-8335-4267dcbcad43',
                'username' => 'budiman_cbd',
                'password' => '$2y$12$QixIkCSHOuzh2comZf8VyuZWxZnIY5VtDYYejZrC1qcDWo4NK0t36',
                'nama' => 'Budiman CBD',
                'status' => 1,
                'remember_token' => null,
                'created_at' => '2026-09-10 16:28:12',
                'updated_at' => '2026-09-18 03:39:59',
                'jenis_pegawai_id' => '01a0adf9-9a30-72bc-8bd1-6ef77625252d',
            ],
        ];
        foreach ($data_users as $row) {
            DB::table('users')->updateOrInsert(['id' => $row['id']], $row);
        }

    }
}
