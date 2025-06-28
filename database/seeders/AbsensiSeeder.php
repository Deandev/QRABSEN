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
        $jadwalSenin = Jadwal::where('hari', 'Senin')->first();
        $mahasiswaSemua = Mahasiswa::all();

        if ($jadwalSenin && $mahasiswaSemua->count() > 0) {
            foreach ($mahasiswaSemua as $mhs) {
                Absensi::firstOrCreate(
                    [
                        'mahasiswa_id' => $mhs->id,
                        'jadwal_id' => $jadwalSenin->id,
                        'tanggal' => Carbon::today()->toDateString(),
                    ],
                    [
                        'status' => 'tidak_hadir', // Mengatur status default menjadi 'tidak_hadir'
                        'waktu_scan' => null
                    ]
                );
            }
        }
    }
}