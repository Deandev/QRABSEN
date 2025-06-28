<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Jadwal;
use App\Models\Absensi;
use Carbon\Carbon;

class DosenController extends Controller
{
    /**
     * Menampilkan dasbor dosen dengan semua jadwal mengajar.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $dosenId = Auth::user()->dosen->id; // Ambil ID dosen yang sedang login

        // Ambil SEMUA jadwal dosen, urutkan berdasarkan hari dan waktu
        $jadwalDosen = Jadwal::with('mataKuliah')
            ->where('dosen_id', $dosenId)
            ->orderBy('hari') // Urutkan berdasarkan hari
            ->orderBy('waktu_mulai') // Lalu berdasarkan waktu mulai
            ->get();

        // Kita ubah nama variabel dari $jadwalHariIni menjadi $jadwalDosen
        // agar lebih sesuai dengan isinya yang sekarang menampilkan semua jadwal.
        return view('dosen.dashboard', compact('jadwalDosen'));
    }

    /**
     * Menampilkan detail jadwal dan daftar mahasiswa untuk absensi.
     *
     * @param  \App\Models\Jadwal  $jadwal
     * @return \Illuminate\View\View
     */
    public function showJadwalDetail(Jadwal $jadwal)
    {
        if ($jadwal->dosen_id !== Auth::user()->dosen->id) {
            abort(403, 'Anda tidak memiliki akses ke jadwal ini.');
        }

        $tanggalHariIni = Carbon::today()->toDateString();

        // Ambil daftar absensi untuk jadwal ini pada hari ini
        $absensiMahasiswa = Absensi::with('mahasiswa.user')
            ->where('jadwal_id', $jadwal->id)
            ->where('tanggal', $tanggalHariIni)
            ->get();

        // Jika tidak ada data absensi untuk hari ini, inisialisasi dengan 'tidak_hadir'
        if ($absensiMahasiswa->isEmpty()) {
            $mahasiswaTerdaftar = \App\Models\Mahasiswa::all();
            foreach ($mahasiswaTerdaftar as $mhs) {
                Absensi::firstOrCreate(
                    [
                        'mahasiswa_id' => $mhs->id,
                        'jadwal_id' => $jadwal->id,
                        'tanggal' => $tanggalHariIni,
                    ],
                    [
                        'status' => 'tidak_hadir', // Inisialisasi menjadi 'tidak_hadir'
                        'waktu_scan' => null
                    ]
                );
            }
            // Setelah inisialisasi, ambil ulang data
            $absensiMahasiswa = Absensi::with('mahasiswa.user')
                ->where('jadwal_id', $jadwal->id)
                ->where('tanggal', $tanggalHariIni)
                ->get();
        }

        return view('dosen.detail-jadwal', compact('jadwal', 'absensiMahasiswa'));
    }

    // Ubah fungsi scanAbsensi
    public function scanAbsensi(Request $request, Jadwal $jadwal)
    {
        if ($jadwal->dosen_id !== Auth::user()->dosen->id) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $request->validate([
            'mahasiswa_nim_qr' => 'required|string',
        ]);

        $nimMahasiswa = $request->input('mahasiswa_nim_qr');
        $tanggalHariIni = Carbon::today()->toDateString();
        $waktuScan = Carbon::now();

        $mahasiswa = \App\Models\Mahasiswa::where('nim', $nimMahasiswa)->first();

        if (!$mahasiswa) {
            return response()->json(['success' => false, 'message' => 'Mahasiswa tidak ditemukan.'], 404);
        }

        $absensi = Absensi::where('mahasiswa_id', $mahasiswa->id)
            ->where('jadwal_id', $jadwal->id)
            ->where('tanggal', $tanggalHariIni)
            ->first();

        if ($absensi) {
            // Hanya perbarui jika statusnya masih 'tidak_hadir'
            if ($absensi->status === 'tidak_hadir') {
                $absensi->status = 'hadir';
                $absensi->waktu_scan = $waktuScan;
                $absensi->save();
                return response()->json([
                    'success' => true,
                    'message' => $mahasiswa->user->nama . ' berhasil absen!',
                    'mahasiswa_nama' => $mahasiswa->user->nama,
                    'mahasiswa_nim' => $mahasiswa->nim,
                    'status' => $absensi->status,
                    'waktu_scan' => $absensi->waktu_scan->format('H:i:s')
                ]);
            } else {
                return response()->json(['success' => false, 'message' => $mahasiswa->user->nama . ' sudah absen sebelumnya.'], 409);
            }
        } else {
            // Fallback: Jika record absensi belum ada, buat baru dengan status 'hadir'
            $absensi = Absensi::create([
                'mahasiswa_id' => $mahasiswa->id,
                'jadwal_id' => $jadwal->id,
                'tanggal' => $tanggalHariIni,
                'waktu_scan' => $waktuScan,
                'status' => 'hadir',
            ]);
            return response()->json([
                'success' => true,
                'message' => $mahasiswa->user->nama . ' berhasil absen!',
                'mahasiswa_nama' => $mahasiswa->user->nama,
                'mahasiswa_nim' => $mahasiswa->nim,
                'status' => $absensi->status,
                'waktu_scan' => $absensi->waktu_scan->format('H:i:s')
            ]);
        }
    }
}