<?php

namespace App\Http\Controllers\Santri;

use App\Http\Controllers\Controller;
use App\Models\DetailPresensi;
use App\Models\Santri;
use App\Models\Jadwal;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function index(Request $request)
    {
        $santri = auth()->user()->santri;

        $blank = [
            'details' => collect(),
            'jadwals' => collect(),
            'jadwalDipilih' => null,
            'bulan' => now()->month,
            'tahun' => now()->year,
        ];

        if (!$santri) {
            return view('santri.presensi.index', $blank);
        }

        $bulan    = $request->has('bulan') ? $request->input('bulan') : now()->month;
        $tahun    = $request->has('tahun') ? $request->input('tahun') : now()->year;
        $jadwalId = $request->input('jadwal_id');

        // Ambil daftar jadwal di majelis santri ini untuk filter
        $jadwals = Jadwal::with('kitab')
            ->where('majelis_id', $santri->majelis_id)
            ->orderBy('hari')
            ->get();

        $query = DetailPresensi::with([
            'presensi.jadwal.majelis',
            'presensi.jadwal.kitab',
        ])
            ->where('santri_id', $santri->id);

        if ($bulan || $tahun) {
            $query->whereHas('presensi', function($q) use ($bulan, $tahun) {
                if ($bulan) $q->whereMonth('tanggal', $bulan);
                if ($tahun) $q->whereYear('tanggal', $tahun);
            });
        }

        if ($jadwalId) {
            $query->whereHas('presensi', function($q) use ($jadwalId) {
                $q->where('jadwal_id', $jadwalId);
            });
        }

        $details = $query->orderByDesc('created_at')->get();

        return view('santri.presensi.index', [
            'details' => $details,
            'jadwals' => $jadwals,
            'jadwalDipilih' => $jadwalId,
            'bulan' => $bulan,
            'tahun' => $tahun,
        ]);
    }

    /**
     * Tampilkan presensi semua santri di majelis yang sama,
     * dikelompokkan per jadwal/kitab.
     */
    public function indexSemua(Request $request)
    {
        $santri = auth()->user()->santri;

        $blank = [
            'semuaSantri'   => collect(),
            'jadwals'       => collect(),
            'jadwalDipilih' => null,
            'groupedData'   => collect(),
            'majelis'       => null,
            'bulan'         => now()->month,
            'tahun'         => now()->year,
            'santriSaya'    => null,
        ];

        if (!$santri || !$santri->majelis_id) {
            return view('santri.presensi.semua', $blank);
        }

        $bulan    = $request->filled('bulan') ? $request->input('bulan') : now()->month;
        $tahun    = $request->filled('tahun') ? $request->input('tahun') : now()->year;
        $jadwalId = $request->input('jadwal_id');

        // Semua santri di majelis yang sama
        $semuaSantri = Santri::with('user')
            ->where('majelis_id', $santri->majelis_id)
            ->orderBy('id', 'asc')
            ->get();

        // Semua jadwal di majelis (untuk dropdown filter)
        $jadwals = Jadwal::with(['kitab', 'ustadz.user'])
            ->where('majelis_id', $santri->majelis_id)
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        // Query base: detail presensi di majelis ini, bulan/tahun terpilih
        $query = DetailPresensi::with([
            'santri.user',
            'presensi.jadwal.kitab',
            'presensi.jadwal.ustadz.user',
        ])
            ->whereHas('presensi.jadwal', fn($q) => $q->where('majelis_id', $santri->majelis_id));

        if ($bulan || $tahun) {
            $query->whereHas('presensi', function($q) use ($bulan, $tahun) {
                if ($bulan) $q->whereMonth('tanggal', $bulan);
                if ($tahun) $q->whereYear('tanggal', $tahun);
            });
        }

        // Filter jadwal tertentu
        if ($jadwalId) {
            $query->whereHas('presensi', fn($q) => $q->where('jadwal_id', $jadwalId));
        }

        $allDetails = $query->get();

        // Kelompokkan per jadwal → per sesi (tanggal) → per santri
        $groupedData = $allDetails
            ->groupBy(fn($d) => $d->presensi?->jadwal_id)
            ->map(function ($detailsByJadwal) {
                $jadwalObj = $detailsByJadwal->first()->presensi?->jadwal;

                $sesiList = $detailsByJadwal
                    ->groupBy('presensi_id')
                    ->map(function ($detailsBySesi) {
                        return [
                            'tanggal' => $detailsBySesi->first()->presensi?->tanggal,
                            'details' => $detailsBySesi->keyBy('santri_id'),
                        ];
                    })
                    ->sortBy('tanggal')
                    ->values();

                return [
                    'jadwal' => $jadwalObj,
                    'sesis'  => $sesiList,
                ];
            })
            ->sortBy(fn($g) => $g['jadwal']?->kitab?->nama_kitab)
            ->values();

        return view('santri.presensi.semua', [
            'semuaSantri'   => $semuaSantri,
            'jadwals'       => $jadwals,
            'jadwalDipilih' => $jadwalId,
            'groupedData'   => $groupedData,
            'majelis'       => $santri->majelis,
            'bulan'         => $bulan,
            'tahun'         => $tahun,
            'santriSaya'    => $santri,
        ]);
    }
}
