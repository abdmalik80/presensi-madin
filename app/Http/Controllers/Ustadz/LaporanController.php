<?php

namespace App\Http\Controllers\Ustadz;

use App\Http\Controllers\Controller;
use App\Models\DetailPresensi;
use App\Models\Majelis;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /** Helper: ambil majelis IDs yang diajar ustadz ini */
    private function getMajelisIds()
    {
        return auth()->user()->ustadz->jadwals()->pluck('majelis_id')->unique();
    }

    /** Helper: query + build laporan per santri */
    private function buildData(Request $request, $majelisIds): array
    {
        $query = DetailPresensi::with(['santri.user', 'santri.majelis', 'presensi.jadwal.majelis']);

        if ($request->dari)   $query->whereHas('presensi', fn($q) => $q->where('tanggal', '>=', $request->dari));
        if ($request->sampai) $query->whereHas('presensi', fn($q) => $q->where('tanggal', '<=', $request->sampai));

        if ($request->majelis_id) {
            $query->whereHas('presensi.jadwal', fn($q) => $q->where('majelis_id', $request->majelis_id));
        } else {
            $query->whereHas('presensi.jadwal', fn($q) => $q->whereIn('majelis_id', $majelisIds));
        }

        if ($request->search) {
            $query->whereHas('santri', function ($q) use ($request) {
                $q->where('nis', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function ($q2) use ($request) {
                      $q2->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $details = $query->get();

        $laporan = $details->groupBy('santri_id')->map(function ($rows) {
            $santri = $rows->first()->santri;
            return [
                'nama'    => $santri->user->name ?? '-',
                'nis'     => $santri->nis ?? '-',
                'majelis' => $santri->majelis->nama_majelis ?? '-',
                'hadir'   => $rows->where('status_kehadiran', 'Hadir')->count(),
                'izin'    => $rows->where('status_kehadiran', 'Izin')->count(),
                'sakit'   => $rows->where('status_kehadiran', 'Sakit')->count(),
                'alpa'    => $rows->where('status_kehadiran', 'Alpha')->count(),
                'total'   => $rows->count(),
            ];
        })->values();

        $summary = [
            'total' => $details->count(),
            'hadir' => $details->where('status_kehadiran', 'Hadir')->count(),
            'izin'  => $details->where('status_kehadiran', 'Izin')->count(),
            'sakit' => $details->where('status_kehadiran', 'Sakit')->count(),
            'alpa'  => $details->where('status_kehadiran', 'Alpha')->count(),
        ];

        return [$laporan, $summary];
    }

    /**
     * Rekap presensi majelis — ustadz hanya melihat majelis yang diajarnya.
     */
    public function index(Request $request)
    {
        $majelisIds  = $this->getMajelisIds();
        $majelisList = Majelis::whereIn('id', $majelisIds)->orderBy('nama_majelis')->get();

        $laporan = collect();
        $summary = null;
        $selected = null;

        $selected = request('majelis_id');
        [$laporan, $summary] = $this->buildData($request, $majelisIds);

        return view('ustadz.laporan.index', compact('majelisList', 'laporan', 'summary', 'selected'));
    }

    /**
     * Preview PDF laporan dengan kop surat.
     */
    public function pdf(Request $request)
    {
        $majelisIds = $this->getMajelisIds();
        $laporan    = collect();
        $summary    = null;

        if ($request->anyFilled(['majelis_id', 'dari', 'sampai', 'search'])) {
            [$laporan, $summary] = $this->buildData($request, $majelisIds);
        }

        $selectedMajelis = $request->majelis_id ? Majelis::find($request->majelis_id) : null;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('ustadz.laporan.pdf', compact('laporan', 'summary', 'selectedMajelis', 'request'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('Laporan_Presensi.pdf');
    }
}
