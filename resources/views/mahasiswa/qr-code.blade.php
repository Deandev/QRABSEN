<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Absensi - {{ $jadwal->mataKuliah->nama }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e9ebee;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            flex-direction: column;
        }
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
            max-width: 500px;
            box-sizing: border-box;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .qr-code-display {
            margin: 30px auto;
            border: 2px solid #ddd;
            padding: 10px;
            border-radius: 8px;
            display: inline-block; /* Agar border pas dengan QR */
        }
        .qr-code-display svg {
            display: block; /* Menghilangkan spasi ekstra di bawah SVG */
            max-width: 100%;
            height: auto;
        }
        .info-text {
            font-size: 1.1em;
            color: #555;
            margin-top: 20px;
        }
        .btn-back {
            display: inline-block;
            background-color: #6c757d;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }
        .btn-back:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>QR Code Absensi</h1>
        <p>Tunjukkan QR Code ini kepada Dosen Anda.</p>

        <div class="qr-code-display">
            {!! $qrCode !!} {{-- Tampilkan QR Code yang sudah di-generate (SVG) --}}
        </div>

        <p class="info-text">
            Mata Kuliah: <strong>{{ $jadwal->mataKuliah->nama }}</strong><br>
            NIM Anda: <strong>{{ $mahasiswa->nim }}</strong><br>
            Tanggal: <strong>{{ \Carbon\Carbon::today()->isoFormat('D MMMM YYYY') }}</strong>
        </p>

        <a href="{{ route('mahasiswa.dashboard') }}" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali ke Dasbor</a>
    </div>
</body>
</html>