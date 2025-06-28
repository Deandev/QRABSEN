@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa')

@section('content')
<div class="container">
    <h1>Dashboard Mahasiswa</h1>

    <h2>Jadwal Mata Kuliah Anda</h2>
    <div class="dashboard-grid">
        @forelse ($jadwalMahasiswa as $jadwal)
            <div class="card">
                <h4>{{ $jadwal->mataKuliah->nama }} ({{ $jadwal->mataKuliah->kode }})</h4>
                <p>Dosen: <strong>{{ $jadwal->dosen->user->nama }}</strong></p>
                <p>Hari: <strong>{{ $jadwal->hari }}</strong></p>
                <p>Waktu: <strong>{{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') }}</strong></p>
                <p>Ruangan: <strong>{{ $jadwal->ruangan ?? 'Tidak ada' }}</strong></p>

                <p class="status-indicator">Status Absensi Hari Ini:
                    <span class="mahasiswa-status status-{{ $jadwal->absensi_status_hari_ini }}">
                        {{ ucfirst(str_replace('_', ' ', $jadwal->absensi_status_hari_ini)) }}
                        @if ($jadwal->absensi_status_hari_ini == 'hadir' && $jadwal->absensi_waktu_scan_hari_ini)
                            ({{ \Carbon\Carbon::parse($jadwal->absensi_waktu_scan_hari_ini)->format('H:i:s') }})
                        @endif
                    </span>
                </p>

                @if ($jadwal->absensi_status_hari_ini === 'tidak_hadir')
                    <a href="{{ route('mahasiswa.jadwal.qrcode', $jadwal->id) }}" class="btn btn-primary">Generate QR Absensi</a>
                @else
                    {{-- Tombol bisa dinonaktifkan atau disembunyikan jika sudah absen --}}
                    <button class="btn btn-secondary" disabled>Sudah Absen</button>
                @endif
            </div>
        @empty
            <p style="grid-column: 1 / -1; text-align: center; font-style: italic; color: #6c757d;">Anda tidak memiliki jadwal mata kuliah terdaftar.</p>
        @endforelse
    </div>
</div>
@endsection