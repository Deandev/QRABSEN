<?php

namespace Database\Seeders;

use App\Models\Jadwal;
use App\Models\MataKuliah;
use App\Models\Dosen;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JadwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mataKuliahPWL = MataKuliah::where('kode', 'CS101')->first();
        $mataKuliahASD = MataKuliah::where('kode', 'MA202')->first();
        $dosenBudi = Dosen::first(); // Ambil dosen yang pertama dibuat

        Jadwal::create([
            'mata_kuliah_id' => $mataKuliahPWL->id,
            'dosen_id' => $dosenBudi->id,
            'hari' => 'Senin',
            'waktu_mulai' => '09:00:00',
            'waktu_selesai' => '11:00:00',
            'ruangan' => 'A101',
        ]);

        Jadwal::create([
            'mata_kuliah_id' => $mataKuliahASD->id,
            'dosen_id' => $dosenBudi->id,
            'hari' => 'sabtu',
            'waktu_mulai' => '13:00:00',
            'waktu_selesai' => '15:00:00',
            'ruangan' => 'B202',
        ]);
    }
}