<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'mahasiswa'; // Nama tabel di database adalah 'mahasiswa'

    protected $fillable = [
        'user_id',
        'nim',
        'jurusan',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Absensi (seorang mahasiswa memiliki banyak record absensi)
    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }
}