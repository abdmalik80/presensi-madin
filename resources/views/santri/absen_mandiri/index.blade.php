<x-app-layout>
    <x-slot name="header">Presensi Mandiri</x-slot>

    <div class="max-w-2xl mx-auto space-y-5">
        <x-alert />

        @php
            $santri = auth()->user()->santri;
            $hariMap = ['Sunday'=>'Ahad','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
            $hariId  = $hariMap[now()->format('l')] ?? now()->format('l');
        @endphp

        {{-- Banner Header --}}
        <div class="relative overflow-hidden rounded-2xl mb-6 shadow-xl text-white p-6" style="background: linear-gradient(135deg, #1a4731 0%, #166534 40%, #15803d 70%, #16a34a 100%);">
            <div class="absolute -top-8 -right-8 w-48 h-48 rounded-full opacity-10" style="background: radial-gradient(circle, #bbf7d0, transparent 70%)"></div>
            <div class="absolute bottom-0 left-1/3 w-32 h-32 rounded-full opacity-10" style="background: radial-gradient(circle, #6ee7b7, transparent 70%)"></div>
            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-3xl shrink-0" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.25);"><i class="fas fa-fingerprint text-white"></i></div>
                <div class="flex-1 min-w-0">
                    <p class="text-emerald-200 text-xs font-medium tracking-wide uppercase mb-0.5">Presensi Hari Ini</p>
                    <h2 class="text-2xl font-extrabold text-white leading-tight truncate">{{ $hariId }}, {{ now()->translatedFormat('d M Y') }}</h2>
                    <div class="flex flex-wrap items-center gap-2 mt-1.5 text-xs text-white/80">
                        <i class="fas fa-clock text-[9px]"></i>
                        <span id="live-clock" class="truncate">{{ now()->format('H:i:s') }} WIB</span>
                    </div>
                </div>
                @if($santri && $santri->majelis)
                <div class="flex flex-col items-end gap-1.5 flex-shrink-0">
                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2);">
                        <i class="fas fa-mosque text-[10px]"></i>
                        <span class="text-[10px] font-semibold">{{ $santri->majelis->nama_majelis }}</span>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Jadwal Hari Ini --}}
        @if($jadwals->isEmpty())
            <div class="bg-white/60 backdrop-blur-xl rounded-2xl border border-white/50 py-16 text-center shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-gray-100 flex items-center justify-center mb-4">
                    <i class="fas fa-calendar-times text-gray-300 text-2xl"></i>
                </div>
                <p class="font-semibold text-gray-600 mb-1">Tidak ada Majelis Hari Ini</p>
                <p class="text-gray-400 text-xs px-4">
                    @if(!$santri || !$santri->majelis_id)
                        Anda belum terdaftar di majelis apapun.
                    @else
                        Tidak ada Jadwal Majelis untuk hari {{ $hariId }}.
                    @endif
                </p>
            </div>
        @else
            <div class="space-y-3">
                <div class="flex items-center justify-between px-1">
                    <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-widest">Jadwal Majelis Hari Ini</p>
                    <span class="text-[10px] font-semibold bg-hijau-tua/10 text-hijau-tua px-2.5 py-1 rounded-full">{{ $jadwals->count() }} Sesi</span>
                </div>

                @foreach($jadwals as $jadwal)
                @php
                    $sudahAbsen   = in_array($jadwal->id, $presensiJadwalMap ?? []);
                    $waktuInfo    = $waktuAbsenMap[$jadwal->id] ?? null;
                    $dalamWaktu   = $waktuInfo['dalam_waktu']    ?? false;
                    $belumWaktu   = $waktuInfo['belum_waktunya'] ?? false;
                    $sudahLewat   = $waktuInfo['sudah_lewat']    ?? false;
                    $presensiToday = $jadwal->presensis->first();
                    $isOpen        = $presensiToday && $presensiToday->status === 'dibuka';
                @endphp

                {{-- Card bergaya sama dengan jadwal majelis --}}
                <div class="bg-white rounded-xl shadow-[0_2px_10px_rgb(0,0,0,0.04)] border border-gray-100 overflow-hidden mb-3 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3 p-3">
                        <div class="text-center min-w-[52px]">
                            <p class="text-xs font-mono font-bold text-gray-700">{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}</p>
                            <p class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</p>
                        </div>
                        <div class="w-px h-10 bg-gray-200 shrink-0"></div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ $jadwal->kitab->nama_kitab ?? 'Majelis' }}</p>
                            </div>
                            <p class="text-xs text-gray-500 truncate mt-0.5"><i class="fas fa-chalkboard-teacher text-[10px] text-gray-400 mr-1"></i>{{ $jadwal->ustadz->user->name ?? '-' }}</p>
                        </div>
                        
                        <div class="shrink-0 flex items-center">
                            @if($sudahAbsen)
                                <span class="inline-flex flex-col items-center justify-center w-14 h-14 bg-green-100 text-green-700 rounded-xl border border-green-200">
                                    <i class="fas fa-check-circle text-base mb-0.5"></i>
                                    <span class="text-[8px] font-bold">Hadir</span>
                                </span>
                            @elseif(!$isOpen)
                                <span class="inline-flex flex-col items-center justify-center w-14 h-14 bg-amber-50 text-amber-600 rounded-xl border border-amber-200">
                                    <i class="fas fa-hourglass-half text-base mb-0.5"></i>
                                    <span class="text-[8px] font-bold leading-tight text-center">Menunggu<br>Ustadz</span>
                                </span>
                            @elseif($belumWaktu)
                                <span class="inline-flex flex-col items-center justify-center w-14 h-14 bg-amber-50 text-amber-600 rounded-xl border border-amber-200">
                                    <i class="fas fa-clock text-base mb-0.5"></i>
                                    <span class="text-[8px] font-bold leading-tight text-center">Belum<br>Waktunya</span>
                                </span>
                            @elseif($sudahLewat)
                                <span class="inline-flex flex-col items-center justify-center w-14 h-14 bg-red-50 text-red-500 rounded-xl border border-red-200">
                                    <i class="fas fa-times-circle text-base mb-0.5"></i>
                                    <span class="text-[8px] font-bold">Tutup</span>
                                </span>
                            @else
                                <form method="POST" action="{{ route('santri.absen-mandiri.store') }}">
                                    @csrf
                                    <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
                                    <button type="submit"
                                            onclick="return confirm('Konfirmasi kehadiran Anda pada majelis ini?')"
                                            class="inline-flex flex-col items-center justify-center w-14 h-14 text-white text-center rounded-xl transition-all shadow-md hover:shadow-lg active:scale-95"
                                            style="background: linear-gradient(135deg, #22C55E, #14532D);">
                                        <i class="fas fa-fingerprint text-base mb-0.5"></i>
                                        <span class="text-[8px] font-bold">Presensi</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    {{-- Footer Info Waktu Presensi --}}
                    @if($waktuInfo && !$sudahAbsen)
                        <div class="bg-gray-50/50 px-4 py-2 border-t border-gray-100 flex items-center justify-between">
                            <p class="text-[10px] text-gray-500">
                                <i class="fas fa-info-circle mr-1"></i> Batas waktu presensi:
                            </p>
                            @if($belumWaktu)
                                <p class="text-[10px] font-semibold text-amber-500">Buka pukul {{ $waktuInfo['waktu_buka'] }}</p>
                            @elseif($dalamWaktu)
                                <p class="text-[10px] font-semibold text-green-600"><span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse inline-block mr-1"></span> Aktif s/d {{ $waktuInfo['waktu_tutup'] }}</p>
                            @elseif($sudahLewat)
                                <p class="text-[10px] font-semibold text-red-500">Berakhir {{ $waktuInfo['waktu_tutup'] }}</p>
                            @endif
                        </div>
                    @elseif($sudahAbsen)
                        <div class="bg-green-50 px-4 py-2 border-t border-green-100 flex items-center gap-2">
                            <i class="fas fa-check-circle text-green-500 text-[10px]"></i>
                            <p class="text-[10px] text-green-700 font-medium">Kehadiran Anda telah tercatat hari ini</p>
                        </div>
                    @endif
                </div>
                @endforeach

            </div>

            {{-- Warning Note --}}
            <div class="flex items-start gap-3 p-4 rounded-xl bg-amber-50/80 border border-amber-100">
                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-amber-500 text-xs"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-amber-800">Perhatian</p>
                    <p class="text-[11px] text-amber-700 mt-0.5">
                        Pastikan Anda hadir secara fisik di majelis sebelum melakukan presensi mandiri. Presensi yang dilakukan tanpa kehadiran merupakan pelanggaran.
                    </p>
                </div>
            </div>
        @endif
    </div>

    {{-- Live Clock --}}
    <script>
        (function () {
            function pad(n) { return String(n).padStart(2, '0'); }
            function tick() {
                const now = new Date();
                const el  = document.getElementById('live-clock');
                if (el) el.textContent = pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());
            }
            tick();
            setInterval(tick, 1000);
        })();
    </script>
</x-app-layout>
