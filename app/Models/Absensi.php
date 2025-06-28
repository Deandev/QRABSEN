<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi'; // Nama tabel di database adalah 'absensi'

    protected $fillable = [
        'mahasiswa_id',
        'jadwal_id',
        'tanggal',
        'waktu_scan',
        'status',
    ];

    // Casting untuk tipe data tanggal dan waktu
    protected $casts = [
        'tanggal' => 'date',
        'waktu_scan' => 'datetime',
    ];

    // Relasi ke Mahasiswa
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    // Relasi ke Jadwal
    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }
}