<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade'); // Foreign key ke tabel mahasiswa
            $table->foreignId('jadwal_id')->constrained('jadwal')->onDelete('cascade'); // Foreign key ke tabel jadwal
            $table->date('tanggal'); // Tanggal absensi
            $table->time('waktu_scan')->nullable(); // Waktu QR di-scan
            $table->enum('status', ['pending', 'hadir', 'tidak_hadir'])->default('pending'); // pending (belum absen), hadir, tidak_hadir
            $table->timestamps();

            // Pastikan satu mahasiswa hanya bisa absen sekali untuk satu jadwal pada tanggal tertentu
            $table->unique(['mahasiswa_id', 'jadwal_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};