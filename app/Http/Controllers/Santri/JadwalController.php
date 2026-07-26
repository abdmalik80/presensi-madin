<?php

namespace App\Http\Controllers\Santri;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index()
    {
        $santri = auth()->user()->santri;

        if (!$santri || !$santri->majelis_id) {
            return view('santri.jadwal.index', ['jadwals' => collect(), 'jadwalStats' => [], 'hariIni' => '']);
        }

        $hariMap = [
            'Sunday'=>'Ahad','Monday'=>'Senin','Tuesday'=>'Selasa',
            'Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'
        ];
        $hariIni = $hariMap[now()->format('l')] ?? '';

        $jadwals = Jadwal::with(['majelis', 'kitab', 'ustadz.user'])
            ->where('majelis_id', $santri->majelis_id)
            ->orderByRaw("
                CASE hari
                    WHEN 'Ahad'   THEN 0
                    WHEN 'Senin'  THEN 1
                    WHEN 'Selasa' THEN 2
                    WHEN 'Rabu'   THEN 3
                    WHEN 'Kamis'  THEN 4
                    WHEN 'Jumat'  THEN 5
                    WHEN 'Sabtu'  THEN 6
                    ELSE 7
                END
            ")
            ->orderBy('jam_mulai')
            ->get();

        // Hitung statistik kehadiran & status hari ini per jadwal
        $jadwalStats = [];
        foreach ($jadwals as $j) {
            $details = \App\Models\DetailPresensi::where('santri_id', $santri->id)
                ->whereHas('presensi', fn($q) => $q->where('jadwal_id', $j->id))
                ->get();

            $total  = $details->count();
            $hadir  = $details->where('status_kehadiran', 'Hadir')->count();
            $pct    = $total > 0 ? round($hadir / $total * 100) : null;

            // Status presensi hari ini (jika hari jadwal = hari ini)
            $presensiHariIni = null;
            $sudahAbsen      = false;
            if ($j->hari === $hariIni) {
                $presensiHariIni = \App\Models\Presensi::where('jadwal_id', $j->id)
                    ->whereDate('tanggal', today())
                    ->first();
                if ($presensiHariIni) {
                    $sudahAbsen = \App\Models\DetailPresensi::where('presensi_id', $presensiHariIni->id)
                        ->where('santri_id', $santri->id)
                        ->exists();
                }
            }

            $jadwalStats[$j->id] = [
                'total'           => $total,
                'hadir'           => $hadir,
                'pct'             => $pct,
                'presensiHariIni' => $presensiHariIni,
                'sudahAbsen'      => $sudahAbsen,
            ];
        }

        return view('santri.jadwal.index', compact('jadwals', 'jadwalStats', 'hariIni'));
    }

    public function show(Jadwal $jadwal)
    {
        $santri = auth()->user()->santri;

        // Pastikan jadwal ini milik majelis santri tersebut
        if ($jadwal->majelis_id !== $santri->majelis_id) {
            return redirect()->route('santri.jadwal.index')->with('error', 'Akses ditolak.');
        }

        $jadwal->load(['majelis', 'kitab', 'ustadz.user']);

        // Ambil riwayat presensi santri khusus untuk jadwal ini
        $riwayatPresensi = \App\Models\DetailPresensi::with('presensi')
            ->where('santri_id', $santri->id)
            ->whereHas('presensi', function ($q) use ($jadwal) {
                $q->where('jadwal_id', $jadwal->id);
            })
            ->oldest()
            ->get();

        // Hitung statistik
        $statistik = [
            'Hadir' => $riwayatPresensi->where('status_kehadiran', 'Hadir')->count(),
            'Izin'  => $riwayatPresensi->where('status_kehadiran', 'Izin')->count(),
            'Sakit' => $riwayatPresensi->where('status_kehadiran', 'Sakit')->count(),
            'Alpha' => $riwayatPresensi->where('status_kehadiran', 'Alpha')->count(),
        ];

        // Berkas/dokumentasi majelis ini
        $berkas = \App\Models\Dokumentasi::where('jadwal_id', $jadwal->id)
            ->latest()
            ->get();

        $lastRead = session('berkas_read_at_' . auth()->id() . '_' . $jadwal->id);
        $newBerkasCount = 0;
        if ($lastRead) {
            $newBerkasCount = $berkas->where('created_at', '>', \Carbon\Carbon::parse($lastRead))->count();
        } else {
            $newBerkasCount = $berkas->count();
        }

        // === ABSEN MANDIRI ===
        // Cek apakah hari ini adalah hari jadwal ini
        $hariMap = [
            'Sunday'=>'Ahad','Monday'=>'Senin','Tuesday'=>'Selasa',
            'Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'
        ];
        $hariIni = $hariMap[now()->format('l')] ?? '';
        $isHariIni = ($jadwal->hari === $hariIni);

        // Presensi hari ini untuk jadwal ini
        $presensiHariIni = \App\Models\Presensi::where('jadwal_id', $jadwal->id)
            ->whereDate('tanggal', today())
            ->first();

        $presensiDibuka = $presensiHariIni && $presensiHariIni->status === 'dibuka';

        $sudahAbsen = false;
        if ($presensiHariIni) {
            $sudahAbsen = \App\Models\DetailPresensi::where('presensi_id', $presensiHariIni->id)
                ->where('santri_id', $santri->id)
                ->exists();
        }

        return view('santri.jadwal.show', compact(
            'jadwal', 'riwayatPresensi', 'statistik', 'berkas', 'newBerkasCount',
            'isHariIni', 'presensiDibuka', 'sudahAbsen', 'presensiHariIni'
        ));
    }

    public function markBerkasRead(Jadwal $jadwal)
    {
        session(['berkas_read_at_' . auth()->id() . '_' . $jadwal->id => now()->toISOString()]);
        return response()->json(['ok' => true]);
    }
}
