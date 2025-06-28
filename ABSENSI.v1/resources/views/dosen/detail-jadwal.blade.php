<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Jadwal - {{ $jadwal->mataKuliah->nama }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e9ebee;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: 20px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1,
        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        .header-info {
            text-align: center;
            margin-bottom: 30px;
        }

        .header-info p {
            margin: 5px 0;
            color: #555;
        }

        .btn-back {
            display: inline-block;
            background-color: #6c757d;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            margin-bottom: 20px;
        }

        .btn-back:hover {
            background-color: #5a6268;
        }

        .absensi-section {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .mahasiswa-list-card {
            flex: 2;
            /* Akan mengambil 2 bagian dari 3 */
            min-width: 300px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .mahasiswa-list-card h3 {
            margin-top: 0;
            color: #007bff;
        }

        .mahasiswa-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .mahasiswa-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px dashed #eee;
        }

        .mahasiswa-item:last-child {
            border-bottom: none;
        }

        .mahasiswa-item span {
            color: #333;
        }

        .mahasiswa-status {
            font-weight: bold;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.9em;
        }

        /* Hilangkan .status-pending */
        .status-hadir {
            background-color: #28a745;
            color: white;
        }

        /* Hijau */
        .status-tidak_hadir {
            background-color: #dc3545;
            color: white;
        }

        /* Merah */


        .scanner-card {
            flex: 1;
            /* Akan mengambil 1 bagian dari 3 */
            min-width: 300px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            text-align: center;
        }

        .scanner-card h3 {
            margin-top: 0;
            color: #007bff;
        }

        #qr-scanner-container {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            position: relative;
        }

        #qr-scanner-container video {
            width: 100%;
            height: auto;
            border: 2px solid #ccc;
            border-radius: 8px;
        }

        #qr-scanner-container canvas {
            display: none;
            /* Kanvas untuk hasil scan, biasanya disembunyikan */
        }

        .open-class-btn {
            background-color: #007bff;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1em;
            margin-bottom: 15px;
            transition: background-color 0.3s ease;
        }

        .open-class-btn:hover {
            background-color: #0056b3;
        }

        #timer-display {
            font-size: 1.5em;
            font-weight: bold;
            color: #28a745;
            /* Hijau */
            margin-top: 15px;
        }

        #scanner-status {
            margin-top: 10px;
            font-style: italic;
            color: #777;
        }

        .hidden {
            display: none !important;
        }

        .scan-result-message {
            margin-top: 15px;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
        }

        .scan-result-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .scan-result-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>

<body>
    <div class="container">
        <a href="{{ route('dosen.dashboard') }}" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali ke
            Dasbor</a>
        <h1>Detail Jadwal Mata Kuliah</h1>

        <div class="header-info">
            <p><strong>Mata Kuliah:</strong> {{ $jadwal->mataKuliah->nama }} ({{ $jadwal->mataKuliah->kode }})</p>
            <p><strong>Hari:</strong> {{ $jadwal->hari }}</p>
            <p><strong>Waktu:</strong> {{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') }} -
                {{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') }}</p>
            <p><strong>Ruangan:</strong> {{ $jadwal->ruangan ?? 'Tidak ada' }}</p>
        </div>

        <hr>

        <div class="absensi-section">
            <div class="mahasiswa-list-card">
                <h3>Daftar Mahasiswa</h3>
                <ul class="mahasiswa-list" id="mahasiswa-absensi-list">
                    @forelse ($absensiMahasiswa as $absensi)
                        <li class="mahasiswa-item" data-nim="{{ $absensi->mahasiswa->nim }}">
                            <span>{{ $absensi->mahasiswa->user->nama }} ({{ $absensi->mahasiswa->nim }})</span>
                            <span class="mahasiswa-status status-{{ $absensi->status }}" id="status-{{ $absensi->mahasiswa->nim }}">
                                {{ ucfirst(str_replace('_', ' ', $absensi->status)) }}
                                @if ($absensi->status == 'hadir' && $absensi->waktu_scan)
                                    ({{ \Carbon\Carbon::parse($absensi->waktu_scan)->format('H:i:s') }})
                                @endif
                            </span>
                        </li>
                    @empty
                        <p style="text-align: center; font-style: italic;">Tidak ada mahasiswa terdaftar untuk jadwal ini.</p>
                    @endforelse
                </ul>
            </div>

            <div class="scanner-card">
                <h3>QR Absensi</h3>
                <button id="open-class-btn" class="open-class-btn">Buka Kelas</button>
                <div id="qr-scanner-section" class="hidden">
                    <div id="qr-scanner-container">
                        <video id="qr-video"></video>
                    </div>
                    <p id="timer-display"></p>
                    <p id="scanner-status">Memulai scanner...</p>
                    <div id="scan-result" class="scan-result-message hidden"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const openClassBtn = document.getElementById('open-class-btn');
        const qrScannerSection = document.getElementById('qr-scanner-section');
        const qrVideo = document.getElementById('qr-video');
        const timerDisplay = document.getElementById('timer-display');
        const scannerStatus = document.getElementById('scanner-status');
        const scanResultDiv = document.getElementById('scan-result');
        const absensiList = document.getElementById('mahasiswa-absensi-list');

        let scanner = null;
        let timerInterval = null;
        let waktuKelasBerjalan = false;
        let durasiKelasDetik = 0; // Durasi dalam detik, akan dihitung dari jadwal
        let waktuSisaDetik = 0;

        const jadwalMulai = "{{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i:s') }}";
        const jadwalSelesai = "{{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i:s') }}";

        // Hitung durasi kelas dalam detik
        function hitungDurasiKelas() {
            const [jamMulai, menitMulai, detikMulai] = jadwalMulai.split(':').map(Number);
            const [jamSelesai, menitSelesai, detikSelesai] = jadwalSelesai.split(':').map(Number);

            const waktuMulaiObj = new Date();
            waktuMulaiObj.setHours(jamMulai, menitMulai, detikMulai, 0);

            const waktuSelesaiObj = new Date();
            waktuSelesaiObj.setHours(jamSelesai, menitSelesai, detikSelesai, 0);

            // Jika waktu selesai lebih kecil dari waktu mulai (misal, melewati tengah malam), tambahkan 1 hari
            if (waktuSelesaiObj < waktuMulaiObj) {
                waktuSelesaiObj.setDate(waktuSelesaiObj.getDate() + 1);
            }

            durasiKelasDetik = (waktuSelesaiObj.getTime() - waktuMulaiObj.getTime()) / 1000;
        }

        // Fungsi untuk format waktu
        function formatTime(seconds) {
            const h = Math.floor(seconds / 3600);
            const m = Math.floor((seconds % 3600) / 60);
            const s = seconds % 60;
            return [h, m, s]
                .map(v => v < 10 ? "0" + v : v)
                .filter((v, i) => v !== "00" || i > 0)
                .join(":");
        }

        // Fungsi untuk memulai timer
        function startTimer() {
            timerInterval = setInterval(() => {
                waktuSisaDetik--;
                timerDisplay.textContent = 'Waktu tersisa: ' + formatTime(waktuSisaDetik);

                if (waktuSisaDetik <= 0) {
                    clearInterval(timerInterval);
                    timerDisplay.textContent = 'Waktu habis!';
                    closeClass(); // Otomatis tutup kelas
                    Swal.fire({
                        icon: 'info',
                        title: 'Kelas Ditutup',
                        text: 'Waktu absen telah berakhir. Kelas ditutup secara otomatis.'
                    });
                }
            }, 1000);
        }

        // Fungsi untuk memulai scanner
        function startScanner() {
            if (scanner) {
                scanner.stop(); // Hentikan scanner sebelumnya jika ada
            }

            scanner = new Instascan.Scanner({ video: qrVideo, mirror: false });
            scanner.addListener('scan', function (content) {
                // `content` adalah data dari QR Code
                console.log('QR Code terdeteksi:', content);
                scannerStatus.textContent = 'QR Code terdeteksi: ' + content;
                processScan(content); // Panggil fungsi untuk memproses hasil scan
            });

            Instascan.Camera.getCameras().then(function (cameras) {
                if (cameras.length > 0) {
                    // Coba gunakan kamera belakang jika tersedia, kalau tidak, kamera pertama
                    const cameraToUse = cameras.find(camera => camera.name.toLowerCase().includes('back')) || cameras[0];
                    scanner.start(cameraToUse);
                    scannerStatus.textContent = 'Scanner aktif.';
                } else {
                    scannerStatus.textContent = 'Tidak ada kamera yang ditemukan.';
                    console.error('No cameras found.');
                    Swal.fire({
                        icon: 'error',
                        title: 'Kamera Tidak Ditemukan',
                        text: 'Pastikan Anda memberikan izin akses kamera dan perangkat Anda memiliki kamera.'
                    });
                }
            }).catch(function (e) {
                scannerStatus.textContent = 'Gagal mengakses kamera: ' + e;
                console.error(e);
                Swal.fire({
                    icon: 'error',
                    title: 'Akses Kamera Ditolak',
                    text: 'Izinkan akses kamera di pengaturan browser Anda untuk menggunakan fitur ini.'
                });
            });
        }

        // Fungsi untuk menghentikan scanner
        function stopScanner() {
            if (scanner) {
                scanner.stop();
                scannerStatus.textContent = 'Scanner tidak aktif.';
            }
        }

        // Fungsi untuk memproses hasil scan QR
        function processScan(nimMahasiswa) {
            // Kirim NIM mahasiswa ke backend Laravel
            fetch("{{ route('dosen.jadwal.scan', $jadwal->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ mahasiswa_nim_qr: nimMahasiswa })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Absen Berhasil!',
                            text: data.message
                        });
                        // Perbarui status di daftar mahasiswa secara dinamis
                        const studentItem = document.querySelector(`.mahasiswa-item[data-nim="${nimMahasiswa}"]`);
                        if (studentItem) {
                            const statusSpan = studentItem.querySelector('.mahasiswa-status');
                            statusSpan.className = 'mahasiswa-status status-hadir';
                            statusSpan.textContent = `Hadir (${data.waktu_scan})`;
                        }
                        scanResultDiv.classList.remove('hidden', 'scan-result-error');
                        scanResultDiv.classList.add('scan-result-success');
                        scanResultDiv.textContent = data.message;
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Absen Gagal!',
                            text: data.message
                        });
                        scanResultDiv.classList.remove('hidden', 'scan-result-success');
                        scanResultDiv.classList.add('scan-result-error');
                        scanResultDiv.textContent = data.message;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan!',
                        text: 'Gagal memproses absensi. Silakan coba lagi.'
                    });
                    scanResultDiv.classList.remove('hidden', 'scan-result-success');
                    scanResultDiv.classList.add('scan-result-error');
                    scanResultDiv.textContent = 'Gagal memproses absensi.';
                });
        }

        // Fungsi untuk membuka kelas (mengaktifkan scanner dan timer)
        function openClass() {
            if (!waktuKelasBerjalan) {
                hitungDurasiKelas(); // Hitung ulang durasi jika belum
                waktuSisaDetik = durasiKelasDetik; // Set waktu sisa ke durasi penuh
                startTimer();
                startScanner();
                qrScannerSection.classList.remove('hidden');
                openClassBtn.textContent = 'Tutup Kelas';
                openClassBtn.style.backgroundColor = '#dc3545'; // Warna merah untuk tutup
                openClassBtn.removeEventListener('click', openClass); // Hapus listener lama
                openClassBtn.addEventListener('click', closeClass); // Tambahkan listener baru
                waktuKelasBerjalan = true;
                Swal.fire({
                    icon: 'success',
                    title: 'Kelas Dibuka!',
                    text: 'QR Scanner aktif dan waktu absen dimulai.'
                });
            }
        }

        // Fungsi untuk menutup kelas (menghentikan scanner dan timer)
        function closeClass() {
    if (waktuKelasBerjalan) {
        stopScanner();
        clearInterval(timerInterval);
        qrScannerSection.classList.add('hidden');
        openClassBtn.textContent = 'Buka Kelas';
        openClassBtn.style.backgroundColor = '#007bff';
        openClassBtn.removeEventListener('click', closeClass);
        openClassBtn.addEventListener('click', openClass);
        waktuKelasBerjalan = false;

        // Set mahasiswa yang statusnya masih 'tidak_hadir' (jika belum diubah oleh scan)
        // menjadi 'tidak_hadir' secara eksplisit
        Array.from(absensiList.children).forEach(item => {
            const nim = item.dataset.nim;
            const statusSpan = item.querySelector('.mahasiswa-status');
            // Asumsi: jika statusnya belum 'hadir' setelah kelas ditutup, berarti 'tidak_hadir'
            // Kita perlu kirim update ke backend untuk finalisasi status ini.
            // Untuk demo, kita ubah secara visual saja.
            if (!statusSpan.classList.contains('status-hadir')) {
                statusSpan.classList.remove('status-pending'); // Pastikan pending tidak ada
                statusSpan.classList.add('status-tidak_hadir');
                statusSpan.textContent = 'Tidak Hadir';

                // --- LOGIKA PENTING UNTUK UPDATE KE DATABASE ---
                // Anda HARUS mengirim AJAX request ke backend untuk
                // mengubah status 'tidak_hadir' ini di database.
                // Jika tidak, saat halaman di-refresh, status akan kembali ke 'tidak_hadir'
                // dari database, tapi tidak mencerminkan 'hadir' yang sudah discan.
                // Contoh (Anda bisa membuat rute dan fungsi controller baru untuk ini):
                // fetch('/dosen/jadwal/{{ $jadwal->id }}/update-absensi-final', {
                //     method: 'POST',
                //     headers: {
                //         'Content-Type': 'application/json',
                //         'X-CSRF-TOKEN': '{{ csrf_token() }}'
                //     },
                //     body: JSON.stringify({
                //         mahasiswa_nim: nim,
                //         status: 'tidak_hadir'
                //     })
                // }).then(response => response.json()).then(data => console.log(data));
                // ------------------------------------------------
            }
        });
    }
}

        // Inisialisasi: tambahkan event listener untuk tombol "Buka Kelas"
        openClassBtn.addEventListener('click', openClass);

        // Jika ada error dari server (misal, validasi), tampilkan
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}'
            });
        @endif

        // Optional: Atur agar kelas otomatis terbuka jika sudah waktunya atau sebelumnya pernah dibuka
        // Anda mungkin perlu menambahkan kolom di tabel jadwal untuk status 'kelas_dibuka'
        // untuk persistensi status antar refresh halaman. Untuk saat ini, kita biarkan manual.
    </script>
</body>

</html>