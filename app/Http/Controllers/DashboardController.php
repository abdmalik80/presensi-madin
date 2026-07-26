<?php

namespace App\Http\Controllers;

use App\Models\DetailPresensi;
use App\Models\Izin;
use App\Models\Jadwal;
use App\Models\Majelis;
use App\Models\Presensi;
use App\Models\Santri;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private array $hariMap = [
        'Sunday'    => 'Ahad',
        'Monday'    => 'Senin',
        'Tuesday'   => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday'  => 'Kamis',
        'Friday'    => 'Jumat',
        'Saturday'  => 'Sabtu',
    ];

    public function index()
    {
        $user = auth()->user();
        $role = $user->role;
        $data = match ($role) {
            'admin'  => $this->adminData($user),
            'ustadz' => $this->ustadzData($user),
            'santri' => $this->santriData($user),
            default  => [],
        };

        return view('dashboard', array_merge(['role' => $role], $data));
    }

    // =========================================================
    // ADMIN DATA
    // =========================================================
    private function adminData($user): array
    {
        $hariIni = $this->hariMap[date('l')] ?? date('l');

        // Hitung semua statistik dalam 1 query group-by (bukan 7 query terpisah)
        $totalSantri   = Santri::count();
        $totalUstadz   = User::where('role', 'ustadz')->count();
        $totalMajelis  = Majelis::count();

        // Kehadiran hari ini - 1 query
        $todayDetails = DetailPresensi::whereHas(
            'presensi', fn($q) => $q->whereDate('tanggal', today())
        )->select('status_kehadiran')->get();

        $totalToday       = $todayDetails->count();
        $hadirToday       = $todayDetails->where('status_kehadiran', 'Hadir')->count();
        $persentaseHadir  = $totalToday > 0 ? round(($hadirToday / $totalToday) * 100) : 0;

        // Statistik 7 hari terakhir - SATU query, bukan 7 query terpisah
        $tujuhHariLalu = now()->subDays(6)->startOfDay();
        $chartRaw = DetailPresensi::select(
                DB::raw('DATE(presensis.tanggal) as tgl'),
                'detail_presensis.status_kehadiran'
            )
            ->join('presensis', 'presensis.id', '=', 'detail_presensis.presensi_id')
            ->where('presensis.tanggal', '>=', $tujuhHariLalu)
            ->get()
            ->groupBy('tgl');

        $chartLabels = [];
        $chartHadir  = [];
        $chartAlpha  = [];
        $chartIzin   = [];
        $hariSingkat = ['Ahad', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

        for ($i = 6; $i >= 0; $i--) {
            $tgl    = now()->subDays($i)->toDateString();
            $dayIdx = (int) Carbon::parse($tgl)->format('w');
            $chartLabels[] = $hariSingkat[$dayIdx] . ' ' . Carbon::parse($tgl)->format('d/m');
            $details = $chartRaw->get($tgl, collect());
            $chartHadir[] = $details->where('status_kehadiran', 'Hadir')->count();
            $chartAlpha[] = $details->where('status_kehadiran', 'Alpha')->count();
            $chartIzin[]  = $details->whereIn('status_kehadiran', ['Izin', 'Sakit'])->count();
        }

        // Jadwal hari ini - dengan eager loading
        $jadwalHariIni = Jadwal::with(['majelis', 'kitab', 'ustadz.user'])
            ->where('hari', $hariIni)
            ->orderBy('jam_mulai')
            ->get();

        // Cek presensi hari ini untuk semua jadwal dalam 1 query
        $jadwalIds = $jadwalHariIni->pluck('id');
        $presensiHariIni = Presensi::whereIn('jadwal_id', $jadwalIds)
            ->whereDate('tanggal', today())
            ->pluck('jadwal_id')
            ->flip(); // jadwal_id => true

        // Tambahkan flag ke masing-masing jadwal
        $jadwalHariIni->each(function ($j) use ($presensiHariIni) {
            $j->sudah_dipresensi = $presensiHariIni->has($j->id);
        });

        // Aktivitas terbaru
        $recentActivities = Presensi::with(['pencatat.user', 'jadwal.majelis'])
            ->latest()->take(5)->get();

        // Izin pending (status DB = 'Menunggu')
        $izinPendingAdmin = Izin::where('status_validasi', 'Menunggu')->count();

        // Stat tambahan untuk dashboard
        $totalKitab        = \App\Models\Kitab::count();
        $totalJadwal       = Jadwal::count();
        $presensiBuilanIni = Presensi::whereMonth('tanggal', date('m'))
                                     ->whereYear('tanggal', date('Y'))
                                     ->count();

        $jamNow     = (int) date('H');
        $salamAdmin = $jamNow < 11 ? 'Selamat pagi' : ($jamNow < 15 ? 'Selamat siang' : ($jamNow < 18 ? 'Selamat sore' : 'Selamat malam'));

        return compact(
            'totalSantri', 'totalUstadz', 'totalMajelis', 'totalKitab', 'totalJadwal', 'presensiBuilanIni',
            'persentaseHadir', 'chartLabels', 'chartHadir', 'chartAlpha', 'chartIzin',
            'jadwalHariIni', 'recentActivities', 'izinPendingAdmin', 'hariIni', 'salamAdmin'
        );
    }

    // =========================================================
    // USTADZ DATA
    // =========================================================
    private function ustadzData($user): array
    {
        $ustadz = $user->ustadz
            ?? $user->ustadz()->firstOrCreate(['user_id' => $user->id]);

        $hariIniU = $this->hariMap[date('l')] ?? date('l');

        // Jadwal hari ini dengan eager loading presensis hari ini
        $jadwalHariIniU = Jadwal::with([
                'majelis', 'kitab',
                'presensis' => fn($q) => $q->whereDate('tanggal', today()),
            ])
            ->where('ustadz_id', $ustadz->id)
            ->where('hari', $hariIniU)
            ->orderBy('jam_mulai')
            ->get();

        $totalJadwalU    = Jadwal::where('ustadz_id', $ustadz->id)->count();
        $presensiBuilanU = Presensi::where('dicatat_oleh', $ustadz->id)
            ->whereMonth('tanggal', date('m'))
            ->whereYear('tanggal', date('Y'))
            ->count();

        // Izin pending - dioptimasi dengan query akurat sesuai izin controller
        $jadwalIds = $ustadz->jadwals()->pluck('id');
        $majelisIds = $ustadz->jadwals()->pluck('majelis_id');
        
        $izinPendingU = Izin::where(function ($q) use ($jadwalIds, $majelisIds) {
                $q->whereIn('jadwal_id', $jadwalIds)
                  ->orWhere(function ($q2) use ($majelisIds) {
                      $q2->whereNull('jadwal_id')
                        ->whereHas('santri', fn($sq) => $sq->whereIn('majelis_id', $majelisIds));
                  });
            })
            ->where('status_validasi', 'Menunggu')
            ->count();

        // Presensi terakhir
        $recentU = Presensi::with(['jadwal.majelis', 'jadwal.kitab', 'details'])
            ->where('dicatat_oleh', $ustadz->id)
            ->latest('tanggal')->take(5)->get();

        $jamNowU = (int) date('H');
        $salamU  = $jamNowU < 11 ? 'Selamat pagi' : ($jamNowU < 15 ? 'Selamat siang' : ($jamNowU < 18 ? 'Selamat sore' : 'Selamat malam'));
        $sapaanU = ($user->jenis_kelamin === 'P') ? 'Ustadzah' : 'Ustadz';

        return compact(
            'ustadz', 'hariIniU', 'jadwalHariIniU',
            'totalJadwalU', 'presensiBuilanU', 'izinPendingU',
            'recentU', 'salamU', 'sapaanU'
        );
    }

    // =========================================================
    // SANTRI DATA
    // =========================================================
    private function santriData($user): array
    {
        $santri   = $user->santri;
        $hariIniS = $this->hariMap[date('l')] ?? date('l');

        if (!$santri) {
            return [
                'santri' => null, 'hariIniS' => $hariIniS,
                'totalPertemuanS' => 0, 'hadirS' => 0, 'izinS' => 0,
                'alpaS' => 0, 'pctHadirS' => 0,
                'jadwalHariIniS' => collect(),
                'riwayatS' => collect(),
                'izinPendingS' => 0,
            ];
        }

        // Statistik bulan ini - satu query
        $detailBulanIni = DetailPresensi::where('santri_id', $santri->id)
            ->whereHas('presensi', fn($q) => $q
                ->whereMonth('tanggal', date('m'))
                ->whereYear('tanggal', date('Y'))
            )
            ->select('status_kehadiran')
            ->get();

        $totalPertemuanS = $detailBulanIni->count();
        $hadirS  = $detailBulanIni->where('status_kehadiran', 'Hadir')->count();
        $izinS   = $detailBulanIni->whereIn('status_kehadiran', ['Izin', 'Sakit'])->count();
        $alpaS   = $detailBulanIni->where('status_kehadiran', 'Alpha')->count();
        $pctHadirS = $totalPertemuanS > 0 ? round(($hadirS / $totalPertemuanS) * 100) : 0;

        // Jadwal hari ini dengan eager loading
        $jadwalHariIniS = Jadwal::with([
                'kitab', 'ustadz.user',
                'presensis' => fn($q) => $q->whereDate('tanggal', today()),
            ])
            ->where('majelis_id', $santri->majelis_id)
            ->where('hari', $hariIniS)
            ->orderBy('jam_mulai')
            ->get();

        // Status kehadiran santri di setiap jadwal hari ini - satu query
        $presensiIds = $jadwalHariIniS
            ->map(fn($j) => $j->presensis->first()?->id)
            ->filter()
            ->values();

        $statusHariIni = DetailPresensi::where('santri_id', $santri->id)
            ->whereIn('presensi_id', $presensiIds)
            ->get()
            ->keyBy('presensi_id');

        // Tambahkan status ke masing-masing jadwal
        $jadwalHariIniS->each(function ($j) use ($statusHariIni) {
            $presensiId = $j->presensis->first()?->id;
            $j->status_santri_hari_ini = $presensiId
                ? $statusHariIni->get($presensiId)?->status_kehadiran
                : null;
        });

        // Riwayat 5 presensi terakhir
        $riwayatS = DetailPresensi::with(['presensi.jadwal.majelis', 'presensi.jadwal.kitab'])
            ->where('santri_id', $santri->id)
            ->orderByDesc('created_at')
            ->take(5)->get();

        // Izin pending
        $izinPendingS = $santri->izins()->where('status_validasi', 'Menunggu')->count();

        return compact(
            'santri', 'hariIniS',
            'totalPertemuanS', 'hadirS', 'izinS', 'alpaS', 'pctHadirS',
            'jadwalHariIniS', 'riwayatS', 'izinPendingS'
        );
    }
}
