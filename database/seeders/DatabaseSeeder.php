<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            ClientSeeder::class,
            KegiatanSeeder::class,
        ]);

        $superadminRole = Role::where('nama_role', 'superadmin')->first();
        $leaderRole = Role::where('nama_role', 'leader')->first();
        $anggotaRole = Role::where('nama_role', 'anggota')->first();

        User::create([
            'role_id' => $superadminRole ? $superadminRole->id : null,
            'username' => 'superadmin',
            'password' => Hash::make('password123'),
            'nama' => 'Super Administrator',
            'status' => 1,
        ]);

        User::create([
            'role_id' => $leaderRole ? $leaderRole->id : null,
            'username' => 'leader1',
            'password' => Hash::make('password123'),
            'nama' => 'Leader Pertama',
            'status' => 1,
        ]);

        User::create([
            'role_id' => $anggotaRole ? $anggotaRole->id : null,
            'username' => 'anggota1',
            'password' => Hash::make('password123'),
            'nama' => 'Anggota Pertama',
            'status' => 1,
        ]);
    }
}
