<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dasbor Dosen</title>
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
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .jadwal-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .jadwal-card h3 {
            margin-top: 0;
            color: #007bff;
        }
        .jadwal-card p {
            margin: 5px 0;
            color: #666;
            font-size: 0.95em;
        }
        .no-jadwal {
            text-align: center;
            color: #777;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <form action="{{ route('logout') }}" method="POST" class="logout-form">
            @csrf
            <button type="submit">Logout</button>
        </form>

        <h1>Dasbor Dosen</h1>
        <p class="welcome-message">Selamat datang, {{ Auth::user()->nama }}!</p>

        <h2>Semua Jadwal Mengajar Anda</h2>

        <div class="jadwal-list">
            @forelse ($jadwalDosen as $jadwal) {{-- Ganti $jadwalHariIni menjadi $jadwalDosen --}}
                <a href="{{ route('dosen.jadwal.detail', $jadwal->id) }}" style="text-decoration: none; color: inherit;">
                    <div class="jadwal-card">
                        <h3>{{ $jadwal->mataKuliah->nama }} ({{ $jadwal->mataKuliah->kode }})</h3>
                        <p>Hari: {{ $jadwal->hari }}</p> {{-- Tambahkan tampilan hari --}}
                        <p>Waktu: {{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') }}</p>
                        <p>Ruangan: {{ $jadwal->ruangan ?? 'Tidak ada' }}</p>
                    </div>
                </a>
            @empty
                <p class="no-jadwal">Anda belum memiliki jadwal mengajar.</p>
            @endforelse
        </div>
    </div>
</body>
</html>