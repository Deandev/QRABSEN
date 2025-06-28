<?php

namespace Database\Seeders;

use App\Models\MataKuliah;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MataKuliahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MataKuliah::create([
            'kode' => 'CS101',
            'nama' => 'Pemrograman Web Lanjut',
        ]);
        MataKuliah::create([
            'kode' => 'MA202',
            'nama' => 'Algoritma dan Struktur Data',
        ]);
    }
}