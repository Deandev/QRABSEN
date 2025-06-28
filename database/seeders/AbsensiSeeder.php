<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\Jadwal;
use App\Models\Mahasiswa;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AbsensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jadwalSenin = Jadwal::where('hari', 'Senin')->first(); // Ambil jadwal Senin
        $mahasiswaSemua = Mahasiswa::all(); // Ambil semua mahasiswa

        if ($jadwalSenin && $mahasiswaSemua->count() > 0) {
            foreach ($mahasiswaSemua as $mhs) {
                // Membuat entri absensi dengan status 'pending' untuk hari ini
                Absensi::create([
                    'mahasiswa_id' => $mhs->id,
                    'jadwal_id' => $jadwalSenin->id,
                    'tanggal' => Carbon::today()->toDateString(), // Menggunakan tanggal hari ini
                    'status' => 'pending',
                ]);
            }
        }

        // Anda bisa tambahkan seeder untuk jadwal lain atau tanggal lain jika diperlukan untuk pengujian
    }
}