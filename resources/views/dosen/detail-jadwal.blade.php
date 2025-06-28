@extends('layouts.app') {{-- Menggunakan layout utama --}}

@section('title', 'Detail Jadwal - ' . $jadwal->mataKuliah->nama) {{-- Menetapkan judul halaman --}}

@section('css')
    {{-- Tidak ada CSS inline di sini, semua sudah di styles.css --}}
@endsection

@section('content') {{-- Bagian konten spesifik halaman ini --}}
<div class="container">
    <a href="{{ route('dosen.dashboard') }}" class="btn btn-secondary btn-back"><i class="fas fa-arrow-left"></i> Kembali ke
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
                        {{-- Menampilkan nama dan NIM mahasiswa --}}
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
            {{-- Tambahkan kelas 'btn' agar styling umum dari styles.css diterapkan --}}
            <button id="open-class-btn" class="open-class-btn btn">Buka Kelas</button>
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
@endsection

@section('js')
    {{-- Script JavaScript Instascan dan SweetAlert2 tetap di sini --}}
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
            // Perlu melakukan AJAX request ke backend untuk update status 'is_open'
            fetch("{{ route('dosen.jadwal.open', $jadwal->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ is_open: true })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
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
                } else {
                     Swal.fire({
                        icon: 'error',
                        title: 'Gagal Membuka Kelas',
                        text: data.message || 'Terjadi kesalahan saat membuka kelas.'
                    });
                }
            })
            .catch(error => {
                console.error('Error opening class:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan!',
                    text: 'Gagal berkomunikasi dengan server saat membuka kelas.'
                });
            });
        }

        // Fungsi untuk menutup kelas (menghentikan scanner dan timer)
        function closeClass() {
            // Perlu melakukan AJAX request ke backend untuk update status 'is_open'
            fetch("{{ route('dosen.jadwal.close', $jadwal->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ is_open: false })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (waktuKelasBerjalan) {
                        stopScanner();
                        clearInterval(timerInterval);
                        qrScannerSection.classList.add('hidden');
                        openClassBtn.textContent = 'Buka Kelas';
                        openClassBtn.style.backgroundColor = '#007bff';
                        openClassBtn.removeEventListener('click', closeClass);
                        openClassBtn.addEventListener('click', openClass);
                        waktuKelasBerjalan = false;

                        Array.from(absensiList.children).forEach(item => {
                            const nim = item.dataset.nim;
                            const statusSpan = item.querySelector('.mahasiswa-status');
                            if (!statusSpan.classList.contains('status-hadir')) {
                                statusSpan.classList.remove('status-pending');
                                statusSpan.classList.add('status-tidak_hadir');
                                statusSpan.textContent = 'Tidak Hadir';
                            }
                        });
                        Swal.fire({
                            icon: 'info',
                            title: 'Kelas Ditutup',
                            text: 'QR Scanner dinonaktifkan.'
                        });
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menutup Kelas',
                        text: data.message || 'Terjadi kesalahan saat menutup kelas.'
                    });
                }
            })
            .catch(error => {
                console.error('Error closing class:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan!',
                    text: 'Gagal berkomunikasi dengan server saat menutup kelas.'
                });
            });
        }


        // Inisialisasi:
        // Cek status `is_open` awal dari server saat halaman dimuat
        const initialIsOpen = {{ $jadwal->is_open ? 'true' : 'false' }};

        document.addEventListener('DOMContentLoaded', () => {
            waktuKelasBerjalan = initialIsOpen;
            // Setel teks dan warna tombol sesuai status awal
            if (waktuKelasBerjalan) {
                openClassBtn.textContent = 'Tutup Kelas';
                openClassBtn.style.backgroundColor = '#dc3545'; // Merah
                qrScannerSection.classList.remove('hidden');
                // Mulai scanner dan timer jika kelas sudah dibuka
                hitungDurasiKelas();
                waktuSisaDetik = durasiKelasDetik;
                startTimer();
                startScanner();
            } else {
                openClassBtn.textContent = 'Buka Kelas';
                openClassBtn.style.backgroundColor = '#007bff'; // Biru
                qrScannerSection.classList.add('hidden');
            }

            // Tambahkan event listener sesuai status awal
            if (waktuKelasBerjalan) {
                openClassBtn.addEventListener('click', closeClass);
            } else {
                openClassBtn.addEventListener('click', openClass);
            }
        });


        // Jika ada error dari server (misal, validasi), tampilkan
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}'
            });
        @endif
    </script>
@endsection