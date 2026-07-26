<?php

namespace App\Http\Controllers\Ustadz;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\Jadwal;
use App\Models\DetailPresensi;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    private function getUstadz()
    {
        return auth()->user()->ustadz
            ?? auth()->user()->ustadz()->firstOrCreate(['user_id' => auth()->id()]);
    }

    public function index(Request $request)
    {
        $ustadz   = $this->getUstadz();
        
        // Ambil hari ini dalam bahasa Indonesia
        $hariMap = [
            'Sunday' => 'Ahad', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];
        $hariIniStr = $hariMap[now()->format('l')];

        // Fetch jadwal ustadz pada hari ini
        $jadwalHariIni = Jadwal::with(['majelis', 'kitab'])
            ->where('ustadz_id', $ustadz->id)
            ->where('hari', $hariIniStr)
            ->get();

        // Cek dan tutup presensi yang sudah habis waktunya secara otomatis
        $presensiHariIni = Presensi::whereIn('jadwal_id', $jadwalHariIni->pluck('id'))
            ->whereDate('tanggal', today())
            ->where('status', 'dibuka')
            ->whereNotNull('jam_tutup')
            ->get();

        $now = now();
        foreach ($presensiHariIni as $p) {
            $tutup = \Carbon\Carbon::parse(today()->format('Y-m-d') . ' ' . $p->jam_tutup);
            if ($now->gt($tutup)) {
                $p->update(['status' => 'ditutup']);
            }
        }

        // Ambil semua jadwal milik ustadz ini (untuk filter dropdown dan daftar majelis)
        $jadwalIds = Jadwal::where('ustadz_id', $ustadz->id)->pluck('id');
        $jadwals   = Jadwal::with(['majelis', 'kitab', 'presensis' => function($q) {
                $q->whereDate('tanggal', today());
            }])
            ->where('ustadz_id', $ustadz->id)
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        // Query riwayat presensi berdasarkan jadwal ustadz + filter
        $query = Presensi::with(['jadwal.majelis', 'jadwal.kitab', 'details'])
            ->whereIn('jadwal_id', $jadwalIds);

        if ($request->dari)   $query->whereDate('tanggal', '>=', $request->dari);
        if ($request->sampai) $query->whereDate('tanggal', '<=', $request->sampai);
        if ($request->jadwal_id) $query->where('jadwal_id', $request->jadwal_id);

        $presensis = $query->latest('tanggal')->paginate(15)->appends($request->query());
            
        return view('ustadz.presensi.index', compact('presensis', 'jadwalHariIni', 'hariIniStr', 'jadwals'));
    }

    public function create()
    {
        $ustadz  = $this->getUstadz();
        $jadwals = Jadwal::with(['majelis', 'kitab'])
            ->where('ustadz_id', $ustadz->id)
            ->get();

        $santris = collect();
        $selectedJadwal = null;
        if (request('jadwal_id')) {
            $selectedJadwal = Jadwal::with(['majelis', 'kitab'])->find(request('jadwal_id'));
        }

        return view('ustadz.presensi.create', compact('jadwals', 'santris', 'selectedJadwal'));
    }

    public function toggleStatus(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required|exists:jadwals,id',
            'jam_buka'  => 'nullable|date_format:H:i',
            'jam_tutup' => 'nullable|date_format:H:i',
        ]);

        $ustadz = $this->getUstadz();
        $jadwal = Jadwal::findOrFail($request->jadwal_id);

        if ($jadwal->ustadz_id !== $ustadz->id) {
            return back()->with('error', 'Akses ditolak.');
        }

        $presensi = Presensi::firstOrCreate(
            ['jadwal_id' => $jadwal->id, 'tanggal' => today()],
            ['dicatat_oleh' => $ustadz->id, 'status' => 'ditutup']
        );

        $newStatus = $presensi->status === 'dibuka' ? 'ditutup' : 'dibuka';
        
        $updateData = ['status' => $newStatus];
        if ($newStatus === 'dibuka') {
            $updateData['jam_buka'] = $request->jam_buka;
            $updateData['jam_tutup'] = $request->jam_tutup;
        }

        $presensi->update($updateData);

        return back()->with('success', 'Akses absen santri berhasil ' . $newStatus . '.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required|exists:jadwals,id',
            'tanggal'   => 'required|date',
            'kehadiran' => 'nullable|array',
        ]);

        $ustadz = $this->getUstadz();

        // Create or retrieve the presensi for the given schedule and date
        $presensi = Presensi::firstOrCreate(
            ['jadwal_id' => $request->jadwal_id, 'tanggal' => $request->tanggal],
            ['dicatat_oleh' => $ustadz->id]
        );

        // Mapping from request status keys to database status values
        $statusMap = [
            'hadir' => 'Hadir',
            'izin'  => 'Izin',
            'sakit' => 'Sakit',
            'alpa'  => 'Alpha',
        ];

        // Process explicit kehadiran input
        if ($request->has('kehadiran')) {
            foreach ($request->kehadiran as $santri_id => $statusKey) {
                // If statusKey is already capitalized (Hadir, Izin, etc), use it. 
                // Otherwise map it.
                $finalStatus = in_array($statusKey, ['Hadir', 'Izin', 'Sakit', 'Alpha']) 
                    ? $statusKey 
                    : ($statusMap[strtolower($statusKey)] ?? 'Hadir');

                $keterangan = $request->input("keterangan.$santri_id", '');

                DetailPresensi::updateOrCreate(
                    ['presensi_id' => $presensi->id, 'santri_id' => $santri_id],
                    ['status_kehadiran' => $finalStatus, 'keterangan' => $keterangan]
                );
            }
        }

        // Ensure santri with a denied izin are automatically marked Alpha
        $deniedSantriIds = \App\Models\Izin::where('jadwal_id', $request->jadwal_id)
            ->where('tanggal_izin', $request->tanggal)
            ->where('status_validasi', 'Ditolak')
            ->pluck('santri_id')
            ->toArray();
        foreach ($deniedSantriIds as $sid) {
            // Skip if this santri already has an explicit kehadiran entry (to avoid overwriting manual input)
            if ($request->has('kehadiran') && array_key_exists($sid, $request->kehadiran)) {
                continue;
            }
            DetailPresensi::updateOrCreate(
                ['presensi_id' => $presensi->id, 'santri_id' => $sid],
                ['status_kehadiran' => 'Alpha']
            );
        }

        return redirect()->route('ustadz.presensi.index')
            ->with('success', 'Presensi berhasil disimpan.');
    }

    /**
     * AJAX: Kembalikan daftar santri berdasarkan jadwal_id (JSON)
     */
    public function getSantris(Request $request)
    {
        $jadwal = Jadwal::with('majelis.santris.user')->find($request->jadwal_id);

        if (!$jadwal || !$jadwal->majelis) {
            return response()->json(['santris' => [], 'denied' => []]);
        }

        $santris = $jadwal->majelis->santris->map(function ($santri) {
            $name = $santri->user->name ?? '-';
            return [
                'id'     => $santri->id,
                'name'   => $name,
                'nis'    => $santri->nis ?? null,
                'avatar' => $santri->user->foto_profil
                    ? asset('storage/' . $santri->user->foto_profil)
                    : 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&size=40&color=14532D&background=F1F5F9',
            ];
        });

        // Ambil santri yang izinnya ditolak untuk jadwal & tanggal ini
        $tanggal = $request->tanggal ?? now()->format('Y-m-d');
        $deniedIds = \App\Models\Izin::where('jadwal_id', $jadwal->id)
            ->where('tanggal_izin', $tanggal)
            ->where('status_validasi', 'Ditolak')
            ->pluck('santri_id')
            ->toArray();

        // Santri dengan izin disetujui
        $approvedIds = \App\Models\Izin::where('jadwal_id', $jadwal->id)
            ->where('tanggal_izin', $tanggal)
            ->where('status_validasi', 'Disetujui')
            ->pluck('santri_id')
            ->toArray();

        // Ambil status presensi yang SUDAH ADA (misal dari absen mandiri)
        $presensi = \App\Models\Presensi::where('jadwal_id', $jadwal->id)
            ->whereDate('tanggal', $tanggal)
            ->first();
            
        $existingStatus = [];
        if ($presensi) {
            // Map: santri_id => status (lowercase)
            foreach ($presensi->details as $detail) {
                $existingStatus[$detail->santri_id] = strtolower($detail->status_kehadiran);
            }
        }

        return response()->json([
            'santris'  => $santris,
            'denied'   => $deniedIds,
            'approved' => $approvedIds,
            'existing' => $existingStatus,
        ]);
    }

    public function show(Presensi $presensi)
    {
        $presensi->load(['jadwal.majelis', 'jadwal.kitab', 'details.santri.user']);
        return view('ustadz.presensi.show', compact('presensi'));
    }

    public function edit(Presensi $presensi) { return $this->show($presensi); }
    public function update(Request $request, Presensi $presensi) { return redirect()->route('ustadz.presensi.index'); }
    public function destroy(Presensi $presensi)
    {
        $presensi->delete();
        return redirect()->route('ustadz.presensi.index')->with('success', 'Presensi dihapus.');
    }
}
