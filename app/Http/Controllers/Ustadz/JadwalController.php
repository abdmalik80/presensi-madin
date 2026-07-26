<?php

namespace App\Http\Controllers\Ustadz;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index()
    {
        // Fitur Jadwal Mengajar kini telah dilebur menjadi satu dengan Presensi
        return redirect()->route('ustadz.presensi.index');
    }

    public function show(Jadwal $jadwal)
    {
        $ustadz = auth()->user()->ustadz;

        // Pastikan jadwal ini milik ustadz yg login
        if ($jadwal->ustadz_id !== $ustadz->id) {
            return redirect()->route('ustadz.jadwal.index')->with('error', 'Akses ditolak.');
        }

        $jadwal->load(['majelis.santris.user', 'kitab', 'ustadz.user']);

        // Berkas/dokumentasi khusus jadwal ini
        $berkas = $jadwal->dokumentasis()
            ->where('pengampu_id', $ustadz->id)
            ->latest()
            ->get();

        // Statistik berkas
        $statBerkas = [
            'total'  => $berkas->count(),
            'image'  => $berkas->where('tipe_file', 'image')->count(),
            'pdf'    => $berkas->where('tipe_file', 'pdf')->count(),
            'doc'    => $berkas->where('tipe_file', 'doc')->count(),
            'other'  => $berkas->whereNotIn('tipe_file', ['image','pdf','doc'])->count(),
        ];

        // Riwayat presensi di jadwal ini
        $presensis = $jadwal->presensis()->latest('tanggal')->take(10)->get();

        // Daftar santri di majelis + statistik kehadiran bulan ini
        $santriList = collect();
        if ($jadwal->majelis) {
            $bulanIni = now()->month;
            $tahunIni = now()->year;

            $santriList = $jadwal->majelis->santris->map(function ($santri) use ($jadwal, $bulanIni, $tahunIni) {
                $details = $santri->detailPresensis()
                    ->whereHas('presensi', function ($q) use ($jadwal, $bulanIni, $tahunIni) {
                        $q->where('jadwal_id', $jadwal->id)
                          ->whereMonth('tanggal', $bulanIni)
                          ->whereYear('tanggal', $tahunIni);
                    })
                    ->get();

                $total = $details->count();
                $hadir = $details->where('status_kehadiran', 'Hadir')->count();
                $izin  = $details->whereIn('status_kehadiran', ['Izin', 'Sakit'])->count();
                $alpha = $details->where('status_kehadiran', 'Alpha')->count();
                $pct   = $total > 0 ? round(($hadir / $total) * 100) : null;

                return [
                    'santri' => $santri,
                    'total'  => $total,
                    'hadir'  => $hadir,
                    'izin'   => $izin,
                    'alpha'  => $alpha,
                    'pct'    => $pct,
                ];
            })->sortBy(fn($s) => $s['santri']->user->name ?? '');
        }

        return view('ustadz.jadwal.show', compact('jadwal', 'berkas', 'statBerkas', 'presensis', 'santriList'));
    }
}
