<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Jadwal;
use App\Models\Absensi;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode; // Import facade QrCode

class MahasiswaController extends Controller
{
    /**
     * Menampilkan dasbor mahasiswa dengan jadwal mata kuliah.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $mahasiswaId = Auth::user()->mahasiswa->id; // Ambil ID mahasiswa yang sedang login
        $hariIni = Carbon::now()->isoFormat('dddd'); // Dapatkan nama hari saat ini (e.g., Senin, Selasa)

        // Ambil semua jadwal mata kuliah untuk hari ini (atau semua jadwal jika kita abaikan hari)
        // Untuk demo, kita tampilkan semua jadwal yang ada, tidak peduli hari ini
        $jadwalMahasiswa = Jadwal::with(['mataKuliah', 'dosen.user'])
                                    // ->where('hari', $hariIni) // Jika ingin hanya jadwal hari ini
                                    ->orderBy('hari')
                                    ->orderBy('waktu_mulai')
                                    ->get();

        // Anda bisa menambahkan filter agar hanya jadwal yang sesuai dengan mahasiswa
        // Jika ada relasi mahasiswa_mata_kuliah atau mahasiswa_jadwal

        // Contoh status absensi mahasiswa untuk jadwal hari ini
        // Ini akan digunakan untuk menampilkan status di card jadwal
        $absensiHariIni = Absensi::where('mahasiswa_id', $mahasiswaId)
                                ->where('tanggal', Carbon::today()->toDateString())
                                ->get()
                                ->keyBy('jadwal_id'); // Kunci koleksi berdasarkan jadwal_id

        return view('mahasiswa.dashboard', compact('jadwalMahasiswa', 'absensiHariIni'));
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

        // Cek apakah ada record absensi pending untuk mahasiswa, jadwal, dan hari ini
        $absensi = Absensi::where('mahasiswa_id', $mahasiswa->id)
                            ->where('jadwal_id', $jadwal->id)
                            ->where('tanggal', $tanggalHariIni)
                            ->first();

        // Jika tidak ada atau statusnya bukan 'pending', bisa arahkan kembali
        if (!$absensi || $absensi->status !== 'pending') {
            return redirect()->back()->with('error', 'Anda tidak dapat absen untuk jadwal ini saat ini.');
        }

        // Data yang akan dienkripsi dalam QR Code adalah NIM mahasiswa
        // Dosen akan menscan QR ini untuk mendapatkan NIM
        $dataQrCode = $mahasiswa->nim;

        // Generate QR Code sebagai SVG (Skalable Vector Graphics)
        $qrCode = QrCode::size(300)->generate($dataQrCode);

        // Anda bisa menambahkan logika untuk memastikan QR hanya berlaku dalam durasi tertentu
        // Atau hanya bisa di-generate jika kelas belum tertutup oleh dosen.
        // Untuk tugas, kita asumsikan QR bisa di-generate kapan saja untuk jadwal yang pending.

        return view('mahasiswa.qr-code', compact('jadwal', 'qrCode', 'mahasiswa'));
    }
}