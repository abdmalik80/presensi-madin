<?php

namespace App\Http\Controllers\Ustadz;

use App\Http\Controllers\Controller;
use App\Models\Izin;
use Illuminate\Http\Request;

class IzinController extends Controller
{
    public function index(Request $request)
    {
        $ustadz  = auth()->user()->ustadz;
        $jadwalIds = $ustadz->jadwals()->pluck('id');
        $majelisIds = $ustadz->jadwals()->pluck('majelis_id');

        $baseQuery = Izin::where(function ($q) use ($jadwalIds, $majelisIds) {
                $q->whereIn('jadwal_id', $jadwalIds)
                      ->orWhere(function ($q2) use ($majelisIds) {
                          $q2->whereNull('jadwal_id')
                            ->whereHas('santri', fn($sq) => $sq->whereIn('majelis_id', $majelisIds));
                      });
            });

        $countMenunggu = (clone $baseQuery)->where('status_validasi', 'Menunggu')->count();
        $countDisetujui = (clone $baseQuery)->where('status_validasi', 'Disetujui')->count();
        $countDitolak = (clone $baseQuery)->where('status_validasi', 'Ditolak')->count();

        $query = (clone $baseQuery)->with(['santri.user', 'jadwal.kitab']);

        if ($request->filled('status')) {
            $query->where('status_validasi', $request->status);
        }

        if ($request->filled('dari')) {
            $query->whereDate('tanggal_izin', '>=', $request->dari);
        }
        
        if ($request->filled('sampai')) {
            $query->whereDate('tanggal_izin', '<=', $request->sampai);
        }

        if ($request->filled('search')) {
            $query->whereHas('santri.user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $izins = $query->latest()->paginate(15)->withQueryString();
            
        return view('ustadz.izin.index', compact('izins', 'countMenunggu', 'countDisetujui', 'countDitolak'));
    }

    public function show(Izin $izin) { return view('ustadz.izin.show', compact('izin')); }

    public function validasi(Request $request, Izin $izin)
    {
        $request->validate(['status_validasi' => 'required|in:Disetujui,Ditolak']);
        $izin->update([
            'status_validasi'  => $request->status_validasi,
            'catatan_pengampu' => $request->catatan_pengampu,
        ]);

        if ($izin->jadwal_id) {
            $statusKehadiran = $request->status_validasi === 'Disetujui' ? 'Izin' : 'Alpha';

            $presensi = \App\Models\Presensi::firstOrCreate(
                ['jadwal_id' => $izin->jadwal_id, 'tanggal' => $izin->tanggal_izin],
                ['dicatat_oleh' => $izin->jadwal->ustadz_id ?? auth()->user()->ustadz->id, 'status' => 'ditutup']
            );

            \App\Models\DetailPresensi::updateOrCreate(
                ['presensi_id' => $presensi->id, 'santri_id' => $izin->santri_id],
                ['status_kehadiran' => $statusKehadiran]
            );
        }

        return back()->with('success', 'Izin berhasil divalidasi dan presensi otomatis diperbarui.');
    }

    public function create() { return redirect()->route('ustadz.izin.index'); }
    public function store(Request $r) { return redirect()->route('ustadz.izin.index'); }
    public function edit(Izin $izin) { return $this->show($izin); }
    public function update(Request $r, Izin $izin) { return redirect()->route('ustadz.izin.index'); }
    public function destroy(Izin $izin) { return redirect()->route('ustadz.izin.index'); }
}
