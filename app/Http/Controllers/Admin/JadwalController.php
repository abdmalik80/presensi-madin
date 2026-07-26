<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Majelis;
use App\Models\Kitab;
use App\Models\Ustadz;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $query = Jadwal::with(['majelis', 'kitab', 'ustadz.user']);
        if ($request->hari) $query->where('hari', $request->hari);
        if ($request->majelis_id) $query->where('majelis_id', $request->majelis_id);
        $jadwals  = $query->orderBy('hari')->orderBy('jam_mulai')->paginate(10);
        $majelis  = Majelis::all();
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Ahad'];
        return view('admin.jadwal.index', compact('jadwals', 'majelis', 'hariList'));
    }

    public function create()
    {
        $majelis  = Majelis::all();
        $kitabs   = Kitab::all();
        $ustadzs  = Ustadz::with('user')->get();
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Ahad'];
        return view('admin.jadwal.create', compact('majelis', 'kitabs', 'ustadzs', 'hariList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'hari'       => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Ahad',
            'jam_mulai'  => 'required',
            'jam_selesai'=> 'required',
            'majelis_id' => 'required|exists:majelis,id',
            'kitab_id'   => 'required|exists:kitabs,id',
            'ustadz_id'  => 'required|exists:ustadzs,id',
        ]);
        Jadwal::create($request->only('hari','jam_mulai','jam_selesai','majelis_id','kitab_id','ustadz_id','keterangan'));
        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function edit(Jadwal $jadwal)
    {
        $majelis  = Majelis::all();
        $kitabs   = Kitab::all();
        $ustadzs  = Ustadz::with('user')->get();
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Ahad'];
        return view('admin.jadwal.edit', compact('jadwal', 'majelis', 'kitabs', 'ustadzs', 'hariList'));
    }

    public function update(Request $request, Jadwal $jadwal)
    {
        $request->validate([
            'hari'       => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Ahad',
            'jam_mulai'  => 'required',
            'jam_selesai'=> 'required',
            'majelis_id' => 'required|exists:majelis,id',
            'kitab_id'   => 'required|exists:kitabs,id',
            'ustadz_id'  => 'required|exists:ustadzs,id',
        ]);
        $jadwal->update($request->only('hari','jam_mulai','jam_selesai','majelis_id','kitab_id','ustadz_id','keterangan'));
        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Jadwal $jadwal)
    {
        $jadwal->delete();
        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil dihapus.');
    }

        public function show(Jadwal $jadwal)
    {
        $jadwal->load(['majelis.santris.user', 'kitab', 'ustadz.user']);

        // Berkas/dokumentasi khusus jadwal ini
        $berkas = $jadwal->dokumentasis()
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

        return view('admin.jadwal.show', compact('jadwal', 'berkas', 'statBerkas', 'presensis', 'santriList'));
    }

    public function bulkAction(Request $request)
    {
        $ids = $request->input('ids', []);
        $action = $request->input('action');

        if (empty($ids)) {
            return redirect()->route('admin.jadwal.index')->with('error', 'Tidak ada jadwal yang dipilih.');
        }

        $jadwals = \App\Models\Jadwal::whereIn('id', $ids)->get();
        $count   = $jadwals->count();

        if ($action === 'delete') {
            foreach ($jadwals as $jadwal) {
                $jadwal->delete();
            }
            return redirect()->route('admin.jadwal.index')->with('success', "{$count} data jadwal berhasil dihapus.");
        }

        return redirect()->route('admin.jadwal.index')->with('error', 'Aksi tidak valid.');
    }
}