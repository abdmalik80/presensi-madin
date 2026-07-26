<?php

namespace App\Http\Controllers\Santri;

use App\Http\Controllers\Controller;
use App\Models\Izin;
use Illuminate\Http\Request;

class IzinController extends Controller
{
    public function index()
    {
        $santri = auth()->user()->santri;
        $izins  = Izin::where('santri_id', $santri->id)->latest()->paginate(15);
        return view('santri.izin.index', compact('izins'));
    }

    public function create() 
    { 
        $santri = auth()->user()->santri;
        // Fetch jadwals for the santri's majelis
        $jadwals = \App\Models\Jadwal::with(['kitab', 'ustadz.user'])
            ->where('majelis_id', $santri->majelis_id)
            ->get()
            ->sortBy('hari'); // Sort by day or keep it as is
            
        return view('santri.izin.create', compact('jadwals')); 
    }

    public function store(Request $request)
    {
        $request->validate([
            'jadwal_id'    => 'required|exists:jadwals,id',
            'tanggal_izin' => 'required|date',
            'alasan'       => 'required|string',
            'upload_bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);
        $santri = auth()->user()->santri;
        
        // Cek jika sudah pernah izin di jadwal dan tanggal yang sama
        $exists = Izin::where('santri_id', $santri->id)
            ->where('jadwal_id', $request->jadwal_id)
            ->where('tanggal_izin', $request->tanggal_izin)
            ->exists();
            
        if ($exists) {
            return back()->withErrors(['error' => 'Anda sudah mengajukan izin untuk jadwal ini pada tanggal tersebut.']);
        }

        $bukti  = null;
        if ($request->hasFile('upload_bukti')) {
            $bukti = $request->file('upload_bukti')->store('bukti_izin', 'public');
        }
        Izin::create([
            'santri_id'    => $santri->id,
            'jadwal_id'    => $request->jadwal_id,
            'tanggal_izin' => $request->tanggal_izin,
            'alasan'       => $request->alasan,
            'upload_bukti' => $bukti,
        ]);
        return redirect()->route('santri.izin.index')->with('success', 'Pengajuan izin berhasil dikirim.');
    }
}
