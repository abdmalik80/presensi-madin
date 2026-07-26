<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\Jadwal;
use App\Models\Santri;
use App\Models\DetailPresensi;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function index(Request $request)
    {
        $jadwals = Jadwal::with('majelis')->get();

        // Ambil hari ini dalam bahasa Indonesia
        $hariMap = [
            'Sunday' => 'Ahad', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];
        $hariIniStr = $hariMap[now()->format('l')];

        // Fetch semua jadwal pada hari ini untuk admin
        $jadwalHariIni = Jadwal::with(['majelis', 'kitab', 'ustadz.user', 'presensis' => function($q) {
                $q->whereDate('tanggal', today());
            }])
            ->where('hari', $hariIniStr)
            ->get();

        $query = Presensi::with(['jadwal.majelis', 'jadwal.kitab', 'jadwal.ustadz.user', 'details'])->latest();

        if ($request->dari)   $query->whereDate('tanggal', '>=', $request->dari);
        if ($request->sampai) $query->whereDate('tanggal', '<=', $request->sampai);
        if ($request->jadwal_id) $query->where('jadwal_id', $request->jadwal_id);

        $presensis = $query->paginate(15);
        return view('admin.presensi.index', compact('presensis', 'jadwals', 'jadwalHariIni', 'hariIniStr'));
    }

    public function create()
    {
        $jadwals = Jadwal::with(['majelis', 'kitab', 'ustadz.user'])->get();
        $santris = collect();
        $selectedJadwal = null;
        if (request('jadwal_id')) {
            $selectedJadwal = Jadwal::with(['majelis', 'kitab', 'ustadz.user'])->find(request('jadwal_id'));
        }
        return view('admin.presensi.create', compact('jadwals', 'santris', 'selectedJadwal'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jadwal_id'  => 'required|exists:jadwals,id',
            'tanggal'    => 'required|date',
            'kehadiran'  => 'nullable|array',
        ]);

        $jadwal = Jadwal::findOrFail($request->jadwal_id);

        $presensi = Presensi::firstOrCreate(
            ['jadwal_id' => $request->jadwal_id, 'tanggal' => $request->tanggal],
            ['dicatat_oleh' => $jadwal->ustadz_id]
        );

        // Map status request
        $statusMap = [
            'hadir' => 'Hadir',
            'izin'  => 'Izin',
            'sakit' => 'Sakit',
            'alpa'  => 'Alpha',
        ];

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

        // Pastikan santri yang izin ditolak otomatis alpa jika tidak diinput
        $deniedSantriIds = \App\Models\Izin::where('jadwal_id', $request->jadwal_id)
            ->where('tanggal_izin', $request->tanggal)
            ->where('status_validasi', 'Ditolak')
            ->pluck('santri_id')
            ->toArray();
        foreach ($deniedSantriIds as $sid) {
            if ($request->has('kehadiran') && array_key_exists($sid, $request->kehadiran)) {
                continue;
            }
            DetailPresensi::updateOrCreate(
                ['presensi_id' => $presensi->id, 'santri_id' => $sid],
                ['status_kehadiran' => 'Alpha']
            );
        }

        return redirect()->route('admin.presensi.index')
            ->with('success', 'Presensi berhasil disimpan.');
    }

    public function toggleStatus(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required|exists:jadwals,id',
            'jam_buka'  => 'nullable|date_format:H:i',
            'jam_tutup' => 'nullable|date_format:H:i',
        ]);

        $jadwal = Jadwal::findOrFail($request->jadwal_id);

        $presensi = Presensi::where('jadwal_id', $jadwal->id)
            ->whereDate('tanggal', today())
            ->first();

        if ($presensi) {
            if ($presensi->status === 'dibuka') {
                $presensi->update(['status' => 'ditutup']);
                $msg = 'Presensi mandiri santri berhasil ditutup.';
            } else {
                $presensi->update([
                    'status'    => 'dibuka',
                    'jam_buka'  => $request->jam_buka ?? now()->format('H:i'),
                    'jam_tutup' => $request->jam_tutup ?? null,
                ]);
                $msg = 'Presensi mandiri santri berhasil dibuka.';
            }
        } else {
            Presensi::create([
                'jadwal_id'    => $jadwal->id,
                'tanggal'      => today(),
                'status'       => 'dibuka',
                'dicatat_oleh' => auth()->id(), // Admin ID
                'jam_buka'     => $request->jam_buka ?? now()->format('H:i'),
                'jam_tutup'    => $request->jam_tutup ?? null,
            ]);
            $msg = 'Presensi mandiri santri berhasil dibuka.';
        }

        return back()->with('success', $msg);
    }

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

        $tanggal = $request->tanggal ?? now()->format('Y-m-d');
        $deniedIds = \App\Models\Izin::where('jadwal_id', $jadwal->id)
            ->where('tanggal_izin', $tanggal)
            ->where('status_validasi', 'Ditolak')
            ->pluck('santri_id')
            ->toArray();

        $approvedIds = \App\Models\Izin::where('jadwal_id', $jadwal->id)
            ->where('tanggal_izin', $tanggal)
            ->where('status_validasi', 'Disetujui')
            ->pluck('santri_id')
            ->toArray();

        return response()->json([
            'santris'  => $santris,
            'denied'   => $deniedIds,
            'approved' => $approvedIds,
        ]);
    }

    public function show(Presensi $presensi)
    {
        $presensi->load(['jadwal.majelis', 'jadwal.kitab', 'jadwal.ustadz.user', 'details.santri.user']);
        return view('admin.presensi.show', compact('presensi'));
    }

    public function edit(Presensi $presensi)
    {
        $presensi->load([
            'jadwal.majelis.santris.user',
            'jadwal.kitab',
            'jadwal.ustadz.user',
            'details.santri.user',
        ]);

        // Santri di majelis ini
        $santris = $presensi->jadwal->majelis->santris ?? collect();

        return view('admin.presensi.edit', compact('presensi', 'santris'));
    }

    public function update(Request $request, Presensi $presensi)
    {
        $request->validate([
            'kehadiran'   => 'nullable|array',
            'kehadiran.*' => 'in:Hadir,Izin,Sakit,Alpha',
            'keterangan'  => 'nullable|array',
        ]);

        if ($request->has('kehadiran')) {
            foreach ($request->kehadiran as $santri_id => $status) {
                DetailPresensi::updateOrCreate(
                    ['presensi_id' => $presensi->id, 'santri_id' => $santri_id],
                    [
                        'status_kehadiran' => $status,
                        'keterangan'       => $request->keterangan[$santri_id] ?? null,
                    ]
                );
            }
        }

        return redirect()->route('admin.presensi.show', $presensi)
            ->with('success', 'Data kehadiran berhasil diperbarui.');
    }

    public function destroy(Presensi $presensi)
    {
        $presensi->delete();
        return redirect()->route('admin.presensi.index')->with('success', 'Presensi dihapus.');
    }
}
