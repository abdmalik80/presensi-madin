<header id="main-navbar" class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-50 transition-transform duration-300">
    @php
        use Carbon\Carbon;
        use App\Models\Izin;
        use App\Models\Dokumentasi;
        use App\Models\Presensi;
        use App\Models\DetailPresensi;
        use App\Models\UserNotifState;

        $userId   = auth()->id();
        $role     = auth()->user()->role;
        $readAt   = auth()->user()->notif_read_at
                        ? Carbon::parse(auth()->user()->notif_read_at)
                        : null;
        $since24h = now()->subHours(24);

        $userStates = UserNotifState::where('user_id', $userId)->get()->keyBy('notif_id');

        // Helper: apakah item ini "baru" (belum dibaca)
        $isNew = fn($ts, $notifId) => 
            (isset($userStates[$notifId]) && $userStates[$notifId]->is_read) 
            ? false 
            : (is_null($readAt) || Carbon::parse($ts)->gt($readAt));

        $allNotifs  = collect();

        /* ---- ADMIN ---- */
        if ($role === 'admin') {

            // 1) Izin menunggu validasi (24 jam terakhir)
            Izin::with('santri.user')->where('status_validasi', 'Menunggu')
                ->where('created_at', '>=', $since24h)->latest()->take(5)->get()
                ->each(fn($i) => $allNotifs->push([
                    'icon'  => 'fa-envelope-open-text',
                    'color' => '#F59E0B',
                    'text'  => ($i->santri->user->name ?? 'Santri') . ' mengajukan izin',
                    'sub'   => Carbon::parse($i->tanggal_izin)->translatedFormat('d M Y'),
                    'time'  => $i->created_at->diffForHumans(),
                    'raw_time' => $i->created_at,
                    'url'   => route('admin.izin.index'),
                ]));

            // 2) Dokumentasi baru diunggah (24 jam terakhir)
            Dokumentasi::with('pengampu.user')->where('created_at', '>=', $since24h)
                ->latest()->take(4)->get()
                ->each(fn($d) => $allNotifs->push([
                    'icon'  => 'fa-camera',
                    'color' => '#10B981',
                    'text'  => 'Dokumentasi: ' . \Illuminate\Support\Str::limit($d->judul, 25),
                    'sub'   => 'oleh ' . ($d->pengampu->user->name ?? 'Ustadz'),
                    'time'  => $d->created_at->diffForHumans(),
                    'raw_time' => $d->created_at,
                    'url'   => route('admin.dokumentasi.index'),
                ]));

            // 3) Presensi baru dicatat (24 jam terakhir)
            Presensi::with('jadwal.majelis')->where('created_at', '>=', $since24h)
                ->latest()->take(4)->get()
                ->each(fn($p) => $allNotifs->push([
                    'icon'  => 'fa-clipboard-check',
                    'color' => '#6366F1',
                    'text'  => 'Presensi dicatat: ' . ($p->jadwal->majelis->nama_majelis ?? 'Majelis'),
                    'sub'   => Carbon::parse($p->tanggal)->translatedFormat('d M Y'),
                    'time'  => $p->created_at->diffForHumans(),
                    'raw_time' => $p->created_at,
                    'url'   => route('admin.presensi.index'),
                ]));

            // 4) Perubahan profil santri/ustadz (24 jam terakhir)
            \App\Models\ProfileLog::with('user')->where('created_at', '>=', $since24h)
                ->latest()->take(5)->get()
                ->each(fn($log) => $allNotifs->push([
                    'icon'  => 'fa-user-pen',
                    'color' => '#8B5CF6',
                    'text'  => ($log->user->name ?? 'Pengguna') . ' memperbarui profil',
                    'sub'   => 'Diubah: ' . $log->keterangan,
                    'time'  => $log->created_at->diffForHumans(),
                    'raw_time' => $log->created_at,
                    'url'   => route('admin.pengguna.index'),
                ]));

        /* ---- USTADZ ---- */
        } elseif ($role === 'ustadz') {
            $ustadz = auth()->user()->ustadz;
            if ($ustadz) {
                $majelisIds = $ustadz->jadwals()->pluck('majelis_id');

                // 1) Izin santri menunggu validasi (hanya untuk jadwal ustadz ini)
                Izin::with('santri.user')
                    ->where('status_validasi', 'Menunggu')
                    ->where('created_at', '>=', $since24h)
                    ->where(function($query) use ($ustadz, $majelisIds) {
                        $query->whereHas('jadwal', fn($q) => $q->where('ustadz_id', $ustadz->id))
                              ->orWhere(function($q) use ($majelisIds) {
                                  // Fallback untuk data lama yang belum punya jadwal_id
                                  $q->whereNull('jadwal_id')
                                    ->whereHas('santri', fn($q2) => $q2->whereIn('majelis_id', $majelisIds));
                              });
                    })
                    ->latest()->take(5)->get()
                    ->each(fn($i) => $allNotifs->push([
                        'icon'  => 'fa-envelope-open-text',
                        'color' => '#F59E0B',
                        'text'  => ($i->santri->user->name ?? 'Santri') . ' mengajukan izin',
                        'sub'   => Carbon::parse($i->tanggal_izin)->translatedFormat('d M Y'),
                        'time'  => $i->created_at->diffForHumans(),
                        'raw_time' => $i->created_at,
                        'url'   => route('ustadz.izin.index'),
                    ]));

                // 2) Dokumentasi baru di majelis yang diajar
                Dokumentasi::with('majelis')
                    ->whereIn('majelis_id', $majelisIds)
                    ->where('created_at', '>=', $since24h)
                    ->latest()->take(4)->get()
                    ->each(fn($d) => $allNotifs->push([
                        'icon'  => 'fa-camera',
                        'color' => '#10B981',
                        'text'  => 'Dokumentasi: ' . \Illuminate\Support\Str::limit($d->judul, 25),
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

                // 1) Status izin diperbarui
                Izin::where('santri_id', $santri->id)
                    ->whereIn('status_validasi', ['Disetujui', 'Ditolak'])
                    ->where('updated_at', '>=', $since24h)
                    ->latest()->take(4)->get()
                    ->each(fn($i) => $allNotifs->push([
                        'icon'  => $i->status_validasi === 'Disetujui' ? 'fa-check-circle' : 'fa-times-circle',
                        'color' => $i->status_validasi === 'Disetujui' ? '#10B981' : '#EF4444',
                        'text'  => 'Izin Anda ' . strtolower($i->status_validasi),
                        'sub'   => Carbon::parse($i->tanggal_izin)->translatedFormat('d M Y'),
                        'time'  => $i->updated_at->diffForHumans(),
                        'raw_time' => $i->updated_at,
                        'url'   => route('santri.izin.index'),
                    ]));

                // 2) Dokumentasi baru di majelis
                Dokumentasi::with('majelis')
                    ->where('majelis_id', $santri->majelis_id)
                    ->where('created_at', '>=', $since24h)
                    ->latest()->take(4)->get()
                    ->each(fn($d) => $allNotifs->push([
                        'icon'  => 'fa-camera',
                        'color' => '#10B981',
                        'text'  => 'Dokumentasi: ' . \Illuminate\Support\Str::limit($d->judul, 25),
                        'sub'   => Carbon::parse($d->tanggal)->translatedFormat('d M Y'),
                        'time'  => $d->created_at->diffForHumans(),
                        'raw_time' => $d->created_at,
                        'url'   => route('santri.dokumentasi.index'),
                    ]));

                // 3) Presensi baru tercatat
                DetailPresensi::with('presensi.jadwal.majelis')
                    ->where('santri_id', $santri->id)
                    ->where('created_at', '>=', $since24h)
                    ->latest()->take(3)->get()
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

        // Urutkan: terbaru dulu
        $allNotifs = $allNotifs->sortByDesc(fn($n) => $n['raw_time'])->values();
        $notifCount = $allNotifs->where('isNew', true)->count();

        $lihatSemuaUrl = route('notifikasi.index');
    @endphp

    <div class="flex items-center justify-between px-6 py-3">
        {{-- Judul halaman --}}
        <div class="flex items-center gap-3">
            <button onclick="toggleSidebar()" class="text-gray-400 lg:hidden focus:outline-none">
                <i class="fas fa-bars"></i>
            </button>
            <h2 class="text-base font-semibold text-gray-800">{{ $header ?? 'Dashboard' }}</h2>
        </div>

        <div class="flex items-center gap-2">

            {{-- ===== NOTIFICATION BELL ===== --}}
            <div class="relative" id="notif-wrapper">
                {{-- Tombol Bell (Link langsung ke halaman) --}}
                <a href="{{ route('notifikasi.index') }}" id="notif-btn"
                        class="relative flex items-center justify-center w-9 h-9 rounded-full text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition focus:outline-none">
                    <i class="fas fa-bell" style="font-size:16px"></i>
                    <span id="notif-badge"
                          class="{{ $notifCount > 0 ? '' : 'hidden' }}"
                          style="position:absolute;top:2px;right:2px;min-width:16px;height:16px;border-radius:999px;background:#EF4444;color:#fff;font-size:9px;font-weight:700;display:flex;align-items:center;justify-content:center;padding:0 3px;line-height:1;">
                        {{ $notifCount > 9 ? '9+' : $notifCount }}
                    </span>
                </a>
            </div>
            {{-- ===== END NOTIFICATION ===== --}}

            {{-- User avatar --}}
            <div class="flex items-center gap-2 ml-1">
                <img class="w-8 h-8 rounded-full object-cover border-2 border-green-400"
                     src="{{ auth()->user()->foto_profil ? asset('storage/'.auth()->user()->foto_profil) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&color=14532D&background=F1F5F9' }}"
                     alt="avatar">
                <span class="hidden sm:block text-sm font-medium text-gray-700">{{ auth()->user()->name }}</span>
            </div>
        </div>
    </div>
</header>

@push('scripts')
<script>
    // ===== Hide on scroll down, show on scroll up (DESKTOP ONLY) =====
    (function() {
        const navbar      = document.getElementById('main-navbar');
        const scrollArea  = document.getElementById('content-area');
        if (!navbar || !scrollArea) return;

        // Hanya aktifkan di layar desktop (lebar >= 1024px / lg ke atas)
        // Di HP, navbar selalu terlihat agar tidak macet
        if (window.innerWidth < 1024) return;

        let lastScrollY = scrollArea.scrollTop;
        let ticking = false;

        scrollArea.addEventListener('scroll', function() {
            if (!ticking) {
                window.requestAnimationFrame(function() {
                    const currentScrollY = scrollArea.scrollTop;
                    const delta = currentScrollY - lastScrollY;

                    // Hanya sembunyikan jika scroll ke bawah lebih dari 5px
                    // dan posisi sudah melewati 80px dari atas
                    if (delta > 5 && currentScrollY > 80) {
                        navbar.style.transform = 'translateY(-100%)';
                    } else if (delta < -5) {
                        // Hanya tampilkan jika scroll ke atas lebih dari 5px
                        navbar.style.transform = 'translateY(0)';
                    }

                    lastScrollY = currentScrollY;
                    ticking = false;
                });
                ticking = true;
            }
        });

        // Pastikan navbar selalu muncul kembali saat scroll berhenti
        scrollArea.addEventListener('scrollend', function() {
            navbar.style.transform = 'translateY(0)';
        });
    })();
</script>
@endpush
