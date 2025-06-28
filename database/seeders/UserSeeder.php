<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat Pengguna Dosen
        $userDosen = User::create([
            'nama' => 'Dosen Budi Santoso',
            'email' => 'dosen@example.com',
            'password' => Hash::make('password'), // Password: password
            'peran' => 'dosen',
        ]);
        Dosen::create([
            'user_id' => $userDosen->id,
            'nip' => '1234567890',
            'telepon' => '081234567890',
        ]);

        // Buat Pengguna Mahasiswa 1
        $userMahasiswa1 = User::create([
            'nama' => 'Mahasiswa Ani Lestari',
            'email' => 'mahasiswa@example.com',
            'password' => Hash::make('password'), // Password: password
            'peran' => 'mahasiswa',
        ]);
        Mahasiswa::create([
            'user_id' => $userMahasiswa1->id,
            'nim' => '20230001',
            'jurusan' => 'Teknik Informatika',
        ]);

        // Buat Pengguna Mahasiswa 2
        $userMahasiswa2 = User::create([
            'nama' => 'Mahasiswa Rio Pratama',
            'email' => 'mahasiswa2@example.com',
            'password' => Hash::make('password'), // Password: password
            'peran' => 'mahasiswa',
        ]);
        Mahasiswa::create([
            'user_id' => $userMahasiswa2->id,
            'nim' => '20230002',
            'jurusan' => 'Teknik Informatika',
        ]);
    }
}