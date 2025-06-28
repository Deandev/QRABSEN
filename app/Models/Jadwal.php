<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;

    protected $table = 'jadwal'; // Nama tabel di database adalah 'jadwal'

    protected $fillable = [
        'mata_kuliah_id',
        'dosen_id',
        'hari',
        'waktu_mulai',
        'waktu_selesai',
        'ruangan',
    ];

    // Relasi ke MataKuliah
    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class);
    }

    // Relasi ke Dosen
    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    // Relasi ke Absensi (satu jadwal memiliki banyak absensi)
    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }
}