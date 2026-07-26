<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Izin;
use App\Models\Majelis;
use Illuminate\Http\Request;

class IzinController extends Controller
{
    public function index(Request $request)
    {
        $query = Izin::with(['santri.user', 'santri.majelis', 'jadwal.kitab', 'jadwal.ustadz.user'])->latest();

        if ($request->filled('status')) {
            $query->where('status_validasi', $request->status);
        }
        if ($request->filled('majelis_id')) {
            $query->whereHas('santri', fn($q) => $q->where('majelis_id', $request->majelis_id));
        }
        if ($request->filled('dari')) {
            $query->whereDate('tanggal_izin', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('tanggal_izin', '<=', $request->sampai);
        }
        if ($request->filled('search')) {
            $query->whereHas('santri.user', fn($q) => $q->where('name', 'like', '%'.$request->search.'%'));
        }

        $izins       = $query->paginate(15)->withQueryString();
        $majelisList = Majelis::orderBy('nama_majelis')->get();

        // Hitung badge per status
        $countMenunggu  = Izin::where('status_validasi', 'Menunggu')->count();
        $countDisetujui = Izin::where('status_validasi', 'Disetujui')->count();
        $countDitolak   = Izin::where('status_validasi', 'Ditolak')->count();

        return view('admin.izin.index', compact(
            'izins', 'majelisList',
            'countMenunggu', 'countDisetujui', 'countDitolak'
        ));
    }

    public function show(Izin $izin)
    {
        return view('admin.izin.show', compact('izin'));
    }

    public function validasi(Request $request, Izin $izin)
    {
        $request->validate([
            'status_validasi'  => 'required|in:Disetujui,Ditolak',
            'catatan_pengampu' => 'nullable|string',
        ]);
        $izin->update([
            'status_validasi'  => $request->status_validasi,
            'catatan_pengampu' => $request->catatan_pengampu,
        ]);

        if ($izin->jadwal_id) {
            $statusKehadiran = $request->status_validasi === 'Disetujui' ? 'Izin' : 'Alpha';

            $presensi = \App\Models\Presensi::firstOrCreate(
                ['jadwal_id' => $izin->jadwal_id, 'tanggal' => $izin->tanggal_izin],
                ['dicatat_oleh' => $izin->jadwal->ustadz_id, 'status' => 'ditutup']
            );

            \App\Models\DetailPresensi::updateOrCreate(
                ['presensi_id' => $presensi->id, 'santri_id' => $izin->santri_id],
                ['status_kehadiran' => $statusKehadiran]
            );
        }

        return back()->with('success', 'Izin berhasil divalidasi dan presensi otomatis diperbarui.');
    }

    public function create() { return redirect()->route('admin.izin.index'); }
    public function store(Request $request) { return redirect()->route('admin.izin.index'); }
    public function edit(Izin $izin) { return view('admin.izin.show', compact('izin')); }
    public function update(Request $request, Izin $izin) { return redirect()->route('admin.izin.index'); }
    public function destroy(Izin $izin) { $izin->delete(); return redirect()->route('admin.izin.index')->with('success', 'Izin dihapus.'); }
}
