<?php

namespace App\Http\Controllers\Santri;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\Jadwal;
use App\Models\DetailPresensi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AbsenMandiriController extends Controller
{
    /**
     * Tampilkan halaman absen mandiri.
     */
    public function index()
    {
        $santri = auth()->user()->santri;

        if (!$santri || !$santri->majelis_id) {
            return view('santri.absen_mandiri.index', [
                'jadwals'           => collect(),
                'presensiJadwalMap' => [],
                'waktuAbsenMap'     => [],
            ]);
        }

        $hariMap = [
            'Sunday'    => 'Ahad',   'Monday'  => 'Senin',  'Tuesday'  => 'Selasa',
            'Wednesday' => 'Rabu',   'Thursday'=> 'Kamis',  'Friday'   => 'Jumat',
            'Saturday'  => 'Sabtu',
        ];
        $hariId = $hariMap[now()->format('l')] ?? now()->format('l');

        $jadwals = Jadwal::with(['majelis', 'kitab', 'ustadz.user', 'presensis' => fn($q) => $q->whereDate('tanggal', today())])
            ->where('majelis_id', $santri->majelis_id)
            ->where('hari', $hariId)
            ->orderBy('jam_mulai')
            ->get();

        // Santri yang sudah absen hari ini
        $sudahAbsenIds = DetailPresensi::whereHas('presensi', fn($q) =>
                $q->whereDate('tanggal', today())->whereIn('jadwal_id', $jadwals->pluck('id'))
            )->where('santri_id', $santri->id)->pluck('presensi_id');

        $presensiJadwalMap = Presensi::whereIn('id', $sudahAbsenIds)->pluck('jadwal_id')->toArray();

        // Hitung info waktu untuk setiap jadwal (hanya dari jam_buka/jam_tutup presensi)
        $now           = now();
        $waktuAbsenMap = [];

        foreach ($jadwals as $jadwal) {
            $jamMulai   = Carbon::parse(today()->format('Y-m-d') . ' ' . $jadwal->jam_mulai);
            $jamSelesai = Carbon::parse(today()->format('Y-m-d') . ' ' . $jadwal->jam_selesai);

            $presensiHariIni = $jadwal->presensis->first();
            if ($presensiHariIni && $presensiHariIni->jam_buka && $presensiHariIni->jam_tutup) {
                $waktuBuka  = Carbon::parse(today()->format('Y-m-d') . ' ' . $presensiHariIni->jam_buka);
                $waktuTutup = Carbon::parse(today()->format('Y-m-d') . ' ' . $presensiHariIni->jam_tutup);

                $waktuAbsenMap[$jadwal->id] = [
                    'jam_mulai'      => $jamMulai->format('H:i'),
                    'jam_selesai'    => $jamSelesai->format('H:i'),
                    'waktu_buka'     => $waktuBuka->format('H:i'),
                    'waktu_tutup'    => $waktuTutup->format('H:i'),
                    'dalam_waktu'    => $now->between($waktuBuka, $waktuTutup),
                    'belum_waktunya' => $now->lt($waktuBuka),
                    'sudah_lewat'    => $now->gt($waktuTutup),
                ];
            } else {
                // Presensi belum dibuka ustadz
                $waktuAbsenMap[$jadwal->id] = [
                    'jam_mulai'      => $jamMulai->format('H:i'),
                    'jam_selesai'    => $jamSelesai->format('H:i'),
                    'waktu_buka'     => null,
                    'waktu_tutup'    => null,
                    'dalam_waktu'    => false,
                    'belum_waktunya' => false,
                    'sudah_lewat'    => false,
                ];
            }
        }

        return view('santri.absen_mandiri.index', compact('jadwals', 'presensiJadwalMap', 'waktuAbsenMap'));
    }

    /**
     * Simpan absen mandiri santri.
     */
    public function store(Request $request)
    {
        $request->validate(['jadwal_id' => 'required|exists:jadwals,id']);

        $santri = auth()->user()->santri;
        if (!$santri) return back()->with('error', 'Data santri tidak ditemukan.');

        $jadwal = Jadwal::findOrFail($request->jadwal_id);

        // Pastikan santri terdaftar di majelis jadwal ini
        if ($jadwal->majelis_id !== $santri->majelis_id) {
            return back()->with('error', 'Anda tidak terdaftar di majelis ini.');
        }

        $presensi = Presensi::where('jadwal_id', $jadwal->id)->whereDate('tanggal', today())->first();
        if (!$presensi || $presensi->status !== 'dibuka') {
            return back()->with('error', 'Akses absen untuk majelis ini belum dibuka atau sudah ditutup oleh Ustadz.');
        }

        // ── Validasi Waktu berdasarkan jam_buka & jam_tutup yang di-set Ustadz ──
        if (!$presensi->jam_buka || !$presensi->jam_tutup) {
            return back()->with('error', 'Ustadz belum mengatur waktu absen untuk majelis ini.');
        }

        $now        = now();
        $waktuBuka  = Carbon::parse(today()->format('Y-m-d') . ' ' . $presensi->jam_buka);
        $waktuTutup = Carbon::parse(today()->format('Y-m-d') . ' ' . $presensi->jam_tutup);

        if ($now->lt($waktuBuka)) {
            $sisaMenit = (int) $now->diffInMinutes($waktuBuka);
            return back()->with('error',
                "Absen belum dibuka. Waktu absen dimulai pukul {$waktuBuka->format('H:i')} ({$sisaMenit} menit lagi)."
            );
        }

        if ($now->gt($waktuTutup)) {
            return back()->with('error',
                "Waktu absen sudah berakhir sejak pukul {$waktuTutup->format('H:i')}. Hubungi Ustadz untuk pencatatan manual."
            );
        }
        // ──────────────────────────────────────────────────────────────────────

        if (DetailPresensi::where('presensi_id', $presensi->id)->where('santri_id', $santri->id)->exists()) {
            return back()->with('info', 'Anda sudah melakukan absen untuk majelis ini hari ini.');
        }

        DetailPresensi::create([
            'presensi_id'      => $presensi->id,
            'santri_id'        => $santri->id,
            'status_kehadiran' => 'Hadir',
        ]);

        return back()->with('success', 'Absen berhasil dicatat! Hadir pada majelis ' . ($jadwal->kitab->nama_kitab ?? '-'));
    }
}
