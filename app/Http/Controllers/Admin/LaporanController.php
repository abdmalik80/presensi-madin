<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DetailPresensi;
use App\Models\Jadwal;
use App\Models\Majelis;
use App\Models\Santri;
use App\Models\Kitab;
use Illuminate\Http\Request;
// SimpleXLSXGen removed — laporan sekarang menggunakan PDF preview

class LaporanController extends Controller
{
    /** Ambil data laporan sesuai filter request */
    private function queryLaporan(Request $request)
    {
        $query = DetailPresensi::with([
            'santri.user',
            'santri.majelis',
            'presensi.jadwal.majelis',
            'presensi.jadwal.kitab',
        ]);

        if ($request->dari) {
            $query->whereHas('presensi', fn($q) => $q->where('tanggal', '>=', $request->dari));
        }
        if ($request->sampai) {
            $query->whereHas('presensi', fn($q) => $q->where('tanggal', '<=', $request->sampai));
        }
        if ($request->majelis_id) {
            $query->whereHas('presensi.jadwal', fn($q) => $q->where('majelis_id', $request->majelis_id));
        }
        if ($request->kitab_id) {
            $query->whereHas('presensi.jadwal', fn($q) => $q->where('kitab_id', $request->kitab_id));
        }

        if ($request->search) {
            $query->whereHas('santri', function ($q) use ($request) {
                $q->where('nis', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function ($q2) use ($request) {
                      $q2->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        return $query->get();
    }

    private function buildLaporan($details): \Illuminate\Support\Collection
    {
        $santriIds = \App\Models\Santri::orderBy('id', 'asc')->pluck('id')->toArray();
        $globalRanks = array_flip($santriIds);

        return $details->groupBy('santri_id')->map(function ($rows) use ($globalRanks) {
            $santri = $rows->first()->santri;
            return [
                'nomor_urut' => isset($globalRanks[$santri->id]) ? $globalRanks[$santri->id] + 1 : '-',
                'nama'    => $santri->user->name ?? '-',
                'nis'     => $santri->nis ?? '-',
                'majelis' => $santri->majelis->nama_majelis ?? '-',
                'hadir'   => $rows->where('status_kehadiran', 'Hadir')->count(),
                'izin'    => $rows->where('status_kehadiran', 'Izin')->count(),
                'sakit'   => $rows->where('status_kehadiran', 'Sakit')->count(),
                'alpa'    => $rows->where('status_kehadiran', 'Alpha')->count(),
                'total'   => $rows->count(),
            ];
        })->sortBy('nomor_urut')->values();
    }

    public function index(Request $request)
    {
        $majelis  = Majelis::orderBy('nama_majelis')->get();
        $kitabs   = Kitab::orderBy('nama_kitab')->get();
        $laporan  = collect();
        $summary  = null;

        $details = $this->queryLaporan($request);
        $laporan = $this->buildLaporan($details);

        $summary = [
            'total' => $details->count(),
            'hadir' => $details->where('status_kehadiran', 'Hadir')->count(),
            'izin'  => $details->where('status_kehadiran', 'Izin')->count(),
            'sakit' => $details->where('status_kehadiran', 'Sakit')->count(),
            'alpa'  => $details->where('status_kehadiran', 'Alpha')->count(),
        ];

        return view('admin.laporan.index', compact('majelis', 'kitabs', 'laporan', 'summary'));
    }

    public function cetak(Request $request)
    {
        $majelis  = Majelis::orderBy('nama_majelis')->get();
        $laporan  = collect();
        $summary  = null;

        if ($request->anyFilled(['majelis_id', 'kitab_id', 'dari', 'sampai', 'search'])) {
            $details = $this->queryLaporan($request);
            $laporan = $this->buildLaporan($details);

            $summary = [
                'total' => $details->count(),
                'hadir' => $details->where('status_kehadiran', 'Hadir')->count(),
                'izin'  => $details->where('status_kehadiran', 'Izin')->count(),
                'sakit' => $details->where('status_kehadiran', 'Sakit')->count(),
                'alpa'  => $details->where('status_kehadiran', 'Alpha')->count(),
            ];
        }

        $selectedMajelis = $request->majelis_id ? Majelis::find($request->majelis_id) : null;
        $selectedKitab   = $request->kitab_id ? Kitab::find($request->kitab_id) : null;

        return view('admin.laporan.cetak', compact('laporan', 'summary', 'selectedMajelis', 'selectedKitab', 'request'));
    }

    public function pdf(Request $request)
    {
        $laporan = collect();
        $summary = null;

        if ($request->anyFilled(['majelis_id', 'kitab_id', 'dari', 'sampai', 'search'])) {
            $details = $this->queryLaporan($request);
            $laporan = $this->buildLaporan($details);

            $summary = [
                'total' => $details->count(),
                'hadir' => $details->where('status_kehadiran', 'Hadir')->count(),
                'izin'  => $details->where('status_kehadiran', 'Izin')->count(),
                'sakit' => $details->where('status_kehadiran', 'Sakit')->count(),
                'alpa'  => $details->where('status_kehadiran', 'Alpha')->count(),
            ];
        }

        $selectedMajelis = $request->majelis_id ? Majelis::find($request->majelis_id) : null;
        $selectedKitab   = $request->kitab_id ? Kitab::find($request->kitab_id) : null;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.laporan.pdf', compact('laporan', 'summary', 'selectedMajelis', 'selectedKitab', 'request'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('Laporan_Presensi.pdf');
    }
}
