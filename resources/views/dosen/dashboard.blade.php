@extends('layouts.app') {{-- Menggunakan layout utama --}}

@section('title', 'Dashboard Dosen') {{-- Menetapkan judul halaman --}}

@section('content') {{-- Bagian konten spesifik halaman ini --}}
<div class="container">
    {{-- Logout form dipindahkan ke header di layouts/app.blade.php --}}

    <h1>Dashboard Dosen</h1>
    <p class="welcome-message">Selamat datang, {{ Auth::user()->nama }}!</p>

    <h2>Jadwal Mengajar Anda</h2>

    <div class="dashboard-grid"> {{-- Menggunakan kelas dari styles.css --}}
        @forelse ($jadwalDosen as $jadwal)
            <a href="{{ route('dosen.jadwal.detail', $jadwal->id) }}" class="card-link"> {{-- Tambahkan kelas untuk link card --}}
                <div class="card"> {{-- Menggunakan kelas card dari styles.css --}}
                    <h4>{{ $jadwal->mataKuliah->nama }} ({{ $jadwal->mataKuliah->kode }})</h4>
                    <p>Hari: <strong>{{ $jadwal->hari }}</strong></p>
                    <p>Waktu: <strong>{{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') }}</strong></p>
                    <p>Ruangan: <strong>{{ $jadwal->ruangan ?? 'Tidak ada' }}</strong></p>
                </div>
            </a>
        @empty
            <p class="no-jadwal">Anda belum memiliki jadwal mengajar.</p>
        @endforelse
    </div>
</div>
@endsection