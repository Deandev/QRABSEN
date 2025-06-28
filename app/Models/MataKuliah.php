<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataKuliah extends Model
{
    use HasFactory;

    protected $table = 'mata_kuliah'; // Nama tabel di database adalah 'mata_kuliah'

    protected $fillable = [
        'kode',
        'nama',
    ];

    // Relasi ke Jadwal (satu mata kuliah bisa ada di banyak jadwal)
    public function jadwal()
    {
        return $this->hasMany(Jadwal::class);
    }
}