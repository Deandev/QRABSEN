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
        Schema::create('jadwal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliah')->onDelete('cascade'); // Foreign key ke tabel mata_kuliah
            $table->foreignId('dosen_id')->constrained('dosen')->onDelete('cascade'); // Foreign key ke tabel dosen
            $table->string('hari'); // Misal: Senin, Selasa
            $table->time('waktu_mulai');
            $table->time('waktu_selesai');
            $table->string('ruangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal');
    }
};