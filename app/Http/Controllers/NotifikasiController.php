<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Izin;
use App\Models\Dokumentasi;
use App\Models\Presensi;
use App\Models\DetailPresensi;
use App\Models\UserNotifState;

class NotifikasiController extends Controller
{
    public function index()
    {
        $userId   = auth()->id();
        $role     = auth()->user()->role;
        $readAt   = auth()->user()->notif_read_at
                        ? Carbon::parse(auth()->user()->notif_read_at)
                        : null;
        $since7d = now()->subDays(7); // Tampilkan notifikasi 7 hari terakhir

        $userStates = UserNotifState::where('user_id', $userId)->get()->keyBy('notif_id');

        $isNew = fn($ts, $notifId) => 
            (isset($userStates[$notifId]) && $userStates[$notifId]->is_read) 
            ? false 
            : (is_null($readAt) || Carbon::parse($ts)->gt($readAt));

        $allNotifs  = collect();

        /* ---- ADMIN ---- */
        if ($role === 'admin') {
            Izin::with('santri.user')->where('created_at', '>=', $since7d)->latest()->get()
                ->each(fn($i) => $allNotifs->push([
                    'icon'  => 'fa-envelope-open-text',
                    'color' => '#F59E0B',
                    'text'  => ($i->santri->user->name ?? 'Santri') . ' mengajukan izin',
                    'sub'   => Carbon::parse($i->tanggal_izin)->translatedFormat('d M Y'),
                    'time'  => $i->created_at->diffForHumans(),
                    'raw_time' => $i->created_at,
                    'url'   => route('admin.izin.index'),
                ]));

            Dokumentasi::with('pengampu.user')->where('created_at', '>=', $since7d)->latest()->get()
                ->each(fn($d) => $allNotifs->push([
                    'icon'  => 'fa-camera',
                    'color' => '#10B981',
                    'text'  => 'Dokumentasi baru: ' . \Illuminate\Support\Str::limit($d->judul, 30),
                    'sub'   => 'oleh ' . ($d->pengampu->user->name ?? 'Ustadz'),
                    'time'  => $d->created_at->diffForHumans(),
                    'raw_time' => $d->created_at,
                    'url'   => route('admin.dokumentasi.index'),
                ]));

            Presensi::with('jadwal.majelis')->where('created_at', '>=', $since7d)->latest()->get()
                ->each(fn($p) => $allNotifs->push([
                    'icon'  => 'fa-clipboard-check',
                    'color' => '#6366F1',
                    'text'  => 'Presensi dicatat: ' . ($p->jadwal->majelis->nama_majelis ?? 'Majelis'),
                    'sub'   => Carbon::parse($p->tanggal)->translatedFormat('d M Y'),
                    'time'  => $p->created_at->diffForHumans(),
                    'raw_time' => $p->created_at,
                    'url'   => route('admin.presensi.index'),
                ]));

            // 4) Perubahan profil santri/ustadz
            \App\Models\ProfileLog::with('user')->where('created_at', '>=', $since7d)->latest()->get()
                ->each(fn($log) => $allNotifs->push([
                    'icon'  => 'fa-user-pen',
                    'color' => '#8B5CF6',
                    'text'  => ($log->user->name ?? 'Pengguna') . ' memperbarui profil',
                    'sub'   => 'Field diubah: ' . $log->keterangan,
                    'time'  => $log->created_at->diffForHumans(),
                    'raw_time' => $log->created_at,
                    'url'   => route('admin.pengguna.index'),
                ]));

        /* ---- USTADZ ---- */
        } elseif ($role === 'ustadz') {
            $ustadz = auth()->user()->ustadz;
            if ($ustadz) {
                $majelisIds = $ustadz->jadwals()->pluck('majelis_id');

                Izin::with('santri.user')
                    ->where('created_at', '>=', $since7d)
                    ->where(function($query) use ($ustadz, $majelisIds) {
                        $query->whereHas('jadwal', fn($q) => $q->where('ustadz_id', $ustadz->id))
                              ->orWhere(function($q) use ($majelisIds) {
                                  // Fallback untuk data lama yang belum punya jadwal_id
                                  $q->whereNull('jadwal_id')
                                    ->whereHas('santri', fn($q2) => $q2->whereIn('majelis_id', $majelisIds));
                              });
                    })
                    ->latest()->get()
                    ->each(fn($i) => $allNotifs->push([
                        'icon'  => 'fa-envelope-open-text',
                        'color' => '#F59E0B',
                        'text'  => ($i->santri->user->name ?? 'Santri') . ' mengajukan izin',
                        'sub'   => Carbon::parse($i->tanggal_izin)->translatedFormat('d M Y'),
                        'time'  => $i->created_at->diffForHumans(),
                        'raw_time' => $i->created_at,
                        'url'   => route('ustadz.izin.index'),
                    ]));

                Dokumentasi::with('majelis')
                    ->whereIn('majelis_id', $majelisIds)
                    ->where('created_at', '>=', $since7d)
                    ->latest()->get()
                    ->each(fn($d) => $allNotifs->push([
                        'icon'  => 'fa-camera',
                        'color' => '#10B981',
                        'text'  => 'Dokumentasi: ' . \Illuminate\Support\Str::limit($d->judul, 30),
                        'sub'   => $d->majelis->nama_majelis ?? '-',
                        'time'  => $d->created_at->diffForHumans(),
                        'raw_time' => $d->created_at,
                        'url'   => route('ustadz.dokumentasi.index'),
                    ]));
            }

        /* ---- SANTRI ---- */
        } elseif ($role === 'santri') {
            $santri = auth()->user()->santri;
            if ($santri) {
                Izin::where('santri_id', $santri->id)
                    ->whereIn('status_validasi', ['Disetujui', 'Ditolak'])
                    ->where('updated_at', '>=', $since7d)
                    ->latest()->get()
                    ->each(fn($i) => $allNotifs->push([
                        'icon'  => $i->status_validasi === 'Disetujui' ? 'fa-check-circle' : 'fa-times-circle',
                        'color' => $i->status_validasi === 'Disetujui' ? '#10B981' : '#EF4444',
                        'text'  => 'Izin Anda ' . strtolower($i->status_validasi),
                        'sub'   => Carbon::parse($i->tanggal_izin)->translatedFormat('d M Y'),
                        'time'  => $i->updated_at->diffForHumans(),
                        'raw_time' => $i->updated_at,
                        'url'   => route('santri.izin.index'),
                    ]));

                Dokumentasi::with('majelis')
                    ->where('majelis_id', $santri->majelis_id)
                    ->where('created_at', '>=', $since7d)
                    ->latest()->get()
                    ->each(fn($d) => $allNotifs->push([
                        'icon'  => 'fa-camera',
                        'color' => '#10B981',
                        'text'  => 'Dokumentasi baru: ' . \Illuminate\Support\Str::limit($d->judul, 30),
                        'sub'   => Carbon::parse($d->tanggal)->translatedFormat('d M Y'),
                        'time'  => $d->created_at->diffForHumans(),
                        'raw_time' => $d->created_at,
                        'url'   => route('santri.dokumentasi.index'),
                    ]));

                DetailPresensi::with('presensi.jadwal.majelis')
                    ->where('santri_id', $santri->id)
                    ->where('created_at', '>=', $since7d)
                    ->latest()->get()
                    ->each(fn($dp) => $allNotifs->push([
                        'icon'  => 'fa-clipboard-check',
                        'color' => '#6366F1',
                        'text'  => 'Presensi tercatat: ' . strtolower($dp->status_kehadiran),
                        'sub'   => $dp->presensi ? Carbon::parse($dp->presensi->tanggal)->translatedFormat('d M Y') : '-',
                        'time'  => $dp->created_at->diffForHumans(),
                        'raw_time' => $dp->created_at,
                        'url'   => route('santri.presensi.index'),
                    ]));
            }
        }

        $allNotifs = $allNotifs->map(function($n) use ($userStates, $isNew) {
            $n['notif_id'] = md5($n['url'] . '_' . $n['raw_time']);
            $n['isNew'] = $isNew($n['raw_time'], $n['notif_id']);
            $n['hidden'] = isset($userStates[$n['notif_id']]) && $userStates[$n['notif_id']]->is_hidden;
            return $n;
        })->filter(function($n) {
            return !$n['hidden'];
        });

        // Urutkan berdasarkan raw_time
        $allNotifs = $allNotifs->sortByDesc('raw_time')->values();

        return view('notifikasi.index', compact('allNotifs'));
    }

    public function action(Request $request)
    {
        $notifId = $request->notif_id;
        $action  = $request->action; // 'read' or 'hide'

        if (!$notifId || !$action) {
            return response()->json(['ok' => false], 400);
        }

        $state = UserNotifState::firstOrCreate(
            ['user_id' => auth()->id(), 'notif_id' => $notifId]
        );

        if ($action === 'read') {
            $state->is_read = true;
        } elseif ($action === 'hide') {
            $state->is_hidden = true;
        }
        $state->save();

        return response()->json(['ok' => true]);
    }
}
