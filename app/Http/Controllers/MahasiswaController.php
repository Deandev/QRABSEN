<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Jadwal;
use App\Models\Absensi;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MahasiswaController extends Controller
{
    /**
     * Menampilkan dasbor mahasiswa dengan jadwal mata kuliah.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $mahasiswa = Auth::user()->mahasiswa; // Ambil objek mahasiswa
        $mahasiswaId = $mahasiswa->id;
        $tanggalHariIni = Carbon::today()->toDateString();

        // Ambil semua jadwal mata kuliah (atau filter sesuai kebutuhan)
        $jadwalMahasiswa = Jadwal::with(['mataKuliah', 'dosen.user'])
                                    ->orderBy('hari')
                                    ->orderBy('waktu_mulai')
                                    ->get();

        // Inisialisasi atau perbarui status absensi untuk setiap jadwal
        foreach ($jadwalMahasiswa as $jadwal) {
            // Coba temukan record absensi untuk mahasiswa, jadwal, dan tanggal ini
            $absensi = Absensi::firstOrCreate(
                [
                    'mahasiswa_id' => $mahasiswaId,
                    'jadwal_id' => $jadwal->id,
                    'tanggal' => $tanggalHariIni,
                ],
                [
                    'status' => 'tidak_hadir', // Default status jika record baru dibuat
                    'waktu_scan' => null
                ]
            );
            // Simpan status absensi yang ditemukan/dibuat ke dalam objek jadwal
            // Agar bisa diakses di view
            $jadwal->absensi_status_hari_ini = $absensi->status;
            $jadwal->absensi_waktu_scan_hari_ini = $absensi->waktu_scan;
        }

        return view('mahasiswa.dashboard', compact('jadwalMahasiswa'));
    }

    /**
     * Menampilkan halaman detail jadwal dan QR Code untuk absensi.
     *
     * @param  \App\Models\Jadwal  $jadwal
     * @return \Illuminate\Http\Response
     */
    public function showQrCode(Jadwal $jadwal)
    {
        $mahasiswa = Auth::user()->mahasiswa;
        $tanggalHariIni = Carbon::today()->toDateString();

        // Check if the class is currently open for attendance
        if (!$jadwal->is_open) {
            return redirect()->back()->with('error', 'QR Code hanya dapat di-generate saat kelas dibuka oleh dosen.');
        }

        // Ambil record absensi untuk mahasiswa, jadwal, dan hari ini.
        // Asumsi record sudah ada karena diinisialisasi di dashboard().
        $absensi = Absensi::where('mahasiswa_id', $mahasiswa->id)
                            ->where('jadwal_id', $jadwal->id)
                            ->where('tanggal', $tanggalHariIni)
                            ->first();

        // Jika ada record absensi dan statusnya sudah 'hadir', beri pesan peringatan
        if ($absensi && $absensi->status === 'hadir') {
            return redirect()->back()->with('error', 'Anda sudah berhasil absen untuk mata kuliah ini.');
        }

        // Jika record absensi belum ada (seharusnya tidak terjadi jika fungsi dashboard dipanggil)
        // atau statusnya tidak 'tidak_hadir', maka kita tidak bisa generate QR.
        // Ini sebagai fallback, memastikan hanya status 'tidak_hadir' yang bisa generate QR.
        if (!$absensi || $absensi->status !== 'tidak_hadir') {
             return redirect()->back()->with('error', 'Anda tidak dapat absen untuk jadwal ini saat ini.');
        }

        $dataQrCode = $mahasiswa->nim;
        $qrCode = QrCode::size(300)->generate($dataQrCode);

        return view('mahasiswa.qr-code', compact('jadwal', 'qrCode', 'mahasiswa'));
    }
}   