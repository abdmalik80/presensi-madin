<?php

namespace App\Http\Controllers\Santri;

use App\Http\Controllers\Controller;
use App\Models\Dokumentasi;
use App\Models\Jadwal;
use Illuminate\Http\Request;

class DokumentasiController extends Controller
{
    public function index(Request $request)
    {
        $santri = auth()->user()->santri;

        // Jadwal yang tersedia di majelis santri (untuk filter)
        $jadwals = Jadwal::where('majelis_id', $santri->majelis_id)
                        ->with(['kitab'])
                        ->get();

        $query = Dokumentasi::with(['majelis', 'pengampu.user', 'jadwal.kitab', 'kitab'])
            ->where(function($q) use ($santri) {
                $q->where('majelis_id', $santri->majelis_id)
                  ->orWhereNull('majelis_id');
            });

        // Filter by jadwal/kitab
        if ($request->filled('jadwal_id')) {
            $query->where('jadwal_id', $request->jadwal_id);
        }
        // Filter by tipe file
        if ($request->filled('tipe')) {
            $query->where('tipe_file', $request->tipe);
        }
        // Filter by bulan
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }
        // Filter by tahun
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }

        $dokumentasis = $query->latest()->paginate(12)->withQueryString();

        // Daftar tahun untuk dropdown filter
        $tahunList = Dokumentasi::where(function($q) use ($santri) {
                            $q->where('majelis_id', $santri->majelis_id)
                              ->orWhereNull('majelis_id');
                        })
                        ->selectRaw('YEAR(tanggal) as tahun')
                        ->distinct()
                        ->orderByDesc('tahun')
                        ->pluck('tahun');

        return view('santri.dokumentasi.index', compact('dokumentasis', 'jadwals', 'tahunList'));
    }
}
