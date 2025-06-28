<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dasbor Mahasiswa</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #e9ebee; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: 20px auto; background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; margin-bottom: 20px; text-align: center; }
        .welcome-message { text-align: center; margin-bottom: 30px; font-size: 1.1em; color: #555; }
        .logout-form { text-align: right; margin-top: -50px; }
        .logout-form button {
            background-color: #dc3545;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9em;
        }
        .logout-form button:hover {
            background-color: #c82333;
        }
        .jadwal-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .jadwal-card {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            width: 300px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            text-align: left;
            cursor: pointer; /* Memberi indikasi bisa diklik (jika di masa depan card bisa diklik) */
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        /* Tidak ada hover effect karena tidak ada link di card, hanya tombol di footer */
        /* .jadwal-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        } */
        .jadwal-card h3 {
            margin-top: 0;
            color: #007bff;
        }
        .jadwal-card p {
            margin: 5px 0;
            color: #666;
            font-size: 0.95em;
        }
        .card-footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9em;
        }
        .card-footer .status {
            font-weight: bold;
            padding: 3px 8px;
            border-radius: 4px;
        }
        .status-hadir { background-color: #28a745; color: white; }
        .status-tidak_hadir { background-color: #dc3545; color: white; }
        .qr-button {
            background-color: #17a2b8; /* Info blue */
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .qr-button:hover {
            background-color: #138496;
        }
        .no-jadwal {
            text-align: center;
            color: #777;
            font-style: italic;
            width: 100%;
        }
        .alert-error, .alert-info {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <form action="{{ route('logout') }}" method="POST" class="logout-form">
            @csrf
            <button type="submit">Logout</button>
        </form>

        <h1>Dasbor Mahasiswa</h1>
        <p class="welcome-message">Selamat datang, {{ Auth::user()->nama }}!</p>

        @if (session('error'))
            <div class="alert-error">
                {{ session('error') }}
            </div>
        @endif
        @if (session('info'))
            <div class="alert-info">
                {{ session('info') }}
            </div>
        @endif

        <h2>Jadwal Mata Kuliah Anda</h2>

        <div class="jadwal-list">
            @forelse ($jadwalMahasiswa as $jadwal)
                @php
                    $statusAbsen = $jadwal->absensi_status_hari_ini; // Ambil status dari properti baru
                    $statusTeks = ucfirst(str_replace('_', ' ', $statusAbsen));
                    $statusClass = 'status-' . $statusAbsen;
                @endphp
                <div class="jadwal-card">
                    <h3>{{ $jadwal->mataKuliah->nama }} ({{ $jadwal->mataKuliah->kode }})</h3>
                    <p>Dosen: {{ $jadwal->dosen->user->nama }}</p>
                    <p>Hari: {{ $jadwal->hari }}</p>
                    <p>Waktu: {{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') }}</p>
                    <p>Ruangan: {{ $jadwal->ruangan ?? 'Tidak ada' }}</p>

                    <div class="card-footer">
                        <span class="status {{ $statusClass }}">Status: {{ $statusTeks }}</span>
                        @if ($statusAbsen == 'tidak_hadir') {{-- Kondisi tetap 'tidak_hadir' --}}
                            <a href="{{ route('mahasiswa.jadwal.qrcode', $jadwal->id) }}" class="qr-button">
                                Generate QR
                            </a>
                        @else
                            {{-- Jika status 'hadir', tombol Generate QR tidak muncul --}}
                            <span style="color: #888;">Absensi Selesai</span>
                        @endif
                    </div>
                </div>
            @empty
                <p class="no-jadwal">Tidak ada jadwal mata kuliah yang ditemukan.</p>
            @endforelse
        </div>
    </div>
</body>
</html>