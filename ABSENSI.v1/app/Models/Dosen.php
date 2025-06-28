<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    use HasFactory;

    protected $table = 'dosen'; // Nama tabel di database adalah 'dosen'

    protected $fillable = [
        'user_id',
        'nip',
        'telepon',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Jadwal (seorang dosen bisa mengajar banyak jadwal)
    public function jadwal()
    {
        return $this->hasMany(Jadwal::class);
    }
}