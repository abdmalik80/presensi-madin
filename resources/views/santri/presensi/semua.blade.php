<x-app-layout>
    <x-slot name="header">Presensi</x-slot>

    @php
    $jadwalOptions = []; foreach($jadwals as $j) $jadwalOptions[$j->id] = ($j->kitab->nama_kitab ?? '-') . ' (' . $j->hari . ')'; $bulanOptions = []; foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bln) $bulanOptions[$i+1] = $bln; $tahunOptions = []; $thnSkrg = date('Y'); for($t = $thnSkrg - 2; $t <= $thnSkrg + 1; $t++) $tahunOptions[$t] = $t;
    @endphp


    <div class="space-y-5 pb-4 md:pb-0" x-data="{ showFilterModal: false }">
        {{-- Tab Navigasi --}}
        <div class="flex gap-1.5 bg-white/60 backdrop-blur-xl rounded-2xl p-1.5 border border-white/50 shadow-[0_4px_20px_rgb(0,0,0,0.04)] w-full md:w-fit">
            <a href="{{ route('santri.presensi.index') }}"
               class="flex-1 md:flex-none flex justify-center items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-gray-500 hover:text-hijau-tua hover:bg-hijau-tua/5 transition-all">
                <i class="fas fa-user text-[10px]"></i> Presensi Saya
            </a>
            <span class="flex-1 md:flex-none flex justify-center items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold bg-hijau-tua text-white shadow-sm">
                <i class="fas fa-users text-[10px]"></i> Semua Santri
            </span>
        </div>

        {{-- Header Info Majelis --}}
        @if($majelis)
        <div class="relative overflow-hidden rounded-2xl mb-6 shadow-xl text-white p-6" style="background: linear-gradient(135deg, #1a4731 0%, #166534 40%, #15803d 70%, #16a34a 100%);">
            <div class="absolute -top-8 -right-8 w-48 h-48 rounded-full opacity-10" style="background: radial-gradient(circle, #bbf7d0, transparent 70%)"></div>
            <div class="absolute bottom-0 left-1/3 w-32 h-32 rounded-full opacity-10" style="background: radial-gradient(circle, #6ee7b7, transparent 70%)"></div>
            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-3xl shrink-0" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.25);"><i class="fas fa-mosque text-white"></i></div>
                <div class="flex-1 min-w-0">
                    <p class="text-emerald-200 text-xs font-medium tracking-wide uppercase mb-0.5">Majelis</p>
                    <h2 class="text-2xl font-extrabold text-white leading-tight truncate">{{ $majelis->nama_majelis }}</h2>
                    @if($majelis->tempat)
                    <div class="flex flex-wrap items-center gap-2 mt-1.5 text-xs text-white/80">
                        <i class="fas fa-map-marker-alt text-[9px]"></i>
                        <span class="truncate">{{ $majelis->tempat }}</span>
                    </div>
                    @endif
                </div>
                <div class="flex flex-col items-end gap-1.5 flex-shrink-0">
                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2);">
                        <span class="text-[10px] font-bold">{{ $semuaSantri->count() }}</span>
                        <span class="text-[9px] uppercase tracking-wider text-emerald-100">Santri</span>
                    </div>
                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2);">
                        <span class="text-[10px] font-bold">{{ $jadwals->count() }}</span>
                        <span class="text-[9px] uppercase tracking-wider text-emerald-100">Kitab</span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Mobile Header & Filter Button --}}
        <div class="md:hidden flex items-center justify-between bg-white p-3 rounded-xl shadow-sm border border-gray-100 sticky top-0 z-30">
            <div>
                <h2 class="text-sm font-bold text-gray-800">Semua Santri</h2>
                <p class="text-[10px] text-gray-500">
                    Filter Riwayat Presensi
                </p>
            </div>
            <button type="button" @click="showFilterModal = true" class="p-2 border border-[#187a41] bg-green-50 text-[#187a41] rounded-lg flex items-center justify-center w-10 h-10">
                <i class="fas fa-filter"></i>
            </button>
        </div>

        {{-- Filter Desktop --}}
        <div class="hidden md:block bg-white/60 backdrop-blur-xl rounded-2xl border border-white/50 shadow-[0_4px_20px_rgb(0,0,0,0.04)] p-5 relative z-40">
            <form method="GET" action="{{ route('santri.presensi.semua') }}" class="flex flex-wrap gap-3 items-end">

                {{-- Pilih Jadwal/Kitab --}}
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">
                        <i class="fas fa-book-open mr-1 text-hijau-tua"></i>Filter Kitab / Pelajaran
                    </label>
                    <x-custom-select name="jadwal_id" id="jadwal_id" :options="$jadwalOptions" :selected="$jadwalDipilih" placeholder="— Semua Kitab —" width="w-full" />
                </div>

                {{-- Bulan --}}
                <div class="min-w-[130px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">
                        <i class="fas fa-calendar mr-1 text-hijau-tua"></i>Bulan
                    </label>
                    <x-custom-select name="bulan" id="bulan" :options="$bulanOptions" :selected="$bulan" placeholder="Bulan" width="w-full" />
                </div>

                {{-- Tahun --}}
                <div class="min-w-[100px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">
                        <i class="fas fa-calendar-alt mr-1 text-hijau-tua"></i>Tahun
                    </label>
                    <x-custom-select name="tahun" id="tahun" :options="$tahunOptions" :selected="$tahun" placeholder="Tahun" width="w-full" />
                </div>

                <button type="submit"
                        class="flex items-center gap-2 px-5 py-2.5 bg-hijau-tua text-white text-sm font-semibold rounded-xl hover:bg-green-700 transition shadow-sm">
                    <i class="fas fa-filter text-xs"></i> Tampilkan
                </button>
            </form>
        </div>

        {{-- Konten utama: per Kitab/Jadwal --}}
        @if($groupedData->isEmpty())
            <div class="bg-white/60 backdrop-blur-xl rounded-2xl border border-white/50 shadow-[0_4px_20px_rgb(0,0,0,0.04)] py-16 flex flex-col items-center text-gray-400">
                <i class="fas fa-history text-4xl mb-3 opacity-30"></i>
                <p class="text-sm font-medium">Tidak ada data presensi untuk periode ini.</p>
                <p class="text-xs mt-1">Coba ubah filter bulan, tahun, atau kitab.</p>
            </div>
        @else
            @foreach($groupedData as $group)
            @php
                $jadwalObj  = $group['jadwal'];
                $sesiList   = $group['sesis'];
                $namaKitab  = $jadwalObj?->kitab?->nama_kitab ?? '(tanpa kitab)';
                $namaUstadz = $jadwalObj?->ustadz?->user?->name ?? '-';
                $hariJadwal = $jadwalObj?->hari ?? '-';
                $jamMulai   = $jadwalObj ? \Carbon\Carbon::parse($jadwalObj->jam_mulai)->format('H:i') : '-';
                $jamSelesai = $jadwalObj ? \Carbon\Carbon::parse($jadwalObj->jam_selesai)->format('H:i') : '-';
                $totalSesi  = $sesiList->count();
            @endphp

            <div class="bg-white/60 backdrop-blur-xl rounded-2xl border border-white/50 shadow-[0_4px_20px_rgb(0,0,0,0.04)] overflow-hidden">
                {{-- Header Kitab --}}
                <div class="bg-hijau-tua/5 border-b border-hijau-tua/10 px-5 py-4 flex flex-col sm:flex-row sm:items-center gap-3">
                    <div class="flex items-center gap-3 flex-1">
                        <div class="w-10 h-10 rounded-xl bg-hijau-tua/10 flex items-center justify-center text-hijau-tua shrink-0">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-hijau-tua">{{ $namaKitab }}</h3>
                            <div class="flex flex-wrap items-center gap-2 mt-0.5 text-xs text-gray-500">
                                <span><i class="fas fa-chalkboard-teacher mr-1"></i>{{ $namaUstadz }}</span>
                                <span class="text-gray-300">·</span>
                                <span><i class="fas fa-calendar-week mr-1"></i>{{ $hariJadwal }}</span>
                                <span class="text-gray-300">·</span>
                                <span><i class="fas fa-clock mr-1"></i>{{ $jamMulai }}–{{ $jamSelesai }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2 shrink-0">
                        <span class="text-xs font-semibold bg-hijau-tua/10 text-hijau-tua px-3 py-1 rounded-full">
                            {{ $totalSesi }} sesi
                        </span>
                    </div>
                </div>

                @if($sesiList->isEmpty())
                <div class="py-8 text-center text-gray-400 text-sm">Belum ada sesi presensi bulan ini.</div>
                @else
                {{-- Desktop Tabel Matriks: Baris = Santri, Kolom = Tanggal sesi --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full text-xs">
                        <thead class="bg-hijau-tua text-white shadow-sm">
                            <tr>
                                {{-- Kolom nama santri --}}
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider sticky left-0 z-20 bg-hijau-tua min-w-[200px] border-r border-emerald-700/50 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">
                                    Nama Santri
                                </th>
                                {{-- Kolom per tanggal sesi --}}
                                @foreach($sesiList as $sesi)
                                <th class="px-3 py-3 text-center text-xs font-semibold uppercase tracking-wider min-w-[80px] border-r border-emerald-700/20">
                                    <div>{{ \Carbon\Carbon::parse($sesi['tanggal'])->format('d') }}</div>
                                    <div class="text-[10px] font-normal text-emerald-100 mt-0.5">{{ \Carbon\Carbon::parse($sesi['tanggal'])->translatedFormat('M') }}</div>
                                </th>
                                @endforeach
                                {{-- Kolom rekap --}}
                                <th class="px-3 py-3 text-center text-xs font-semibold uppercase tracking-wider min-w-[60px] border-l border-emerald-700/50 bg-emerald-800/80">H</th>
                                <th class="px-3 py-3 text-center text-xs font-semibold uppercase tracking-wider min-w-[60px] bg-emerald-800/80">I</th>
                                <th class="px-3 py-3 text-center text-xs font-semibold uppercase tracking-wider min-w-[60px] bg-emerald-800/80">A</th>
                                <th class="px-3 py-3 text-center text-xs font-semibold uppercase tracking-wider min-w-[70px] bg-emerald-800/80">%</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($semuaSantri as $s)
                            @php
                                $isSaya = isset($santriSaya) && $santriSaya->id === $s->id;
                                $hadirCount = 0;
                                $izinCount  = 0;
                                $alpaCount  = 0;
                                $totalCount = 0;
                            @endphp
                            <tr class="hover:bg-gray-50/60 transition-colors {{ $isSaya ? 'bg-emerald-50/50' : '' }}">
                                {{-- Nama Santri --}}
                                <td class="px-4 py-2.5 sticky left-0 z-10 bg-white {{ $isSaya ? 'bg-emerald-50/80' : '' }} border-r border-gray-100">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full {{ $isSaya ? 'bg-hijau-tua text-white' : 'bg-gray-100 text-gray-500' }} flex items-center justify-center font-bold text-[10px] shrink-0">
                                            {{ strtoupper(substr($s->user->name ?? 'S', 0, 1)) }}
                                        </div>
                                        <span class="font-medium text-gray-800 text-xs whitespace-nowrap">
                                            {{ $s->user->name ?? '-' }}
                                            @if($isSaya)<span class="text-[9px] text-hijau-tua ml-1">(Saya)</span>@endif
                                        </span>
                                    </div>
                                </td>

                                {{-- Sel per sesi/tanggal --}}
                                @foreach($sesiList as $sesi)
                                @php
                                    $detail = $sesi['details'][$s->id] ?? null;
                                    $st = strtolower($detail?->status_kehadiran ?? '');
                                    if ($st === 'hadir') { $hadirCount++; $totalCount++; }
                                    elseif (in_array($st, ['izin','sakit'])) { $izinCount++; $totalCount++; }
                                    elseif (in_array($st, ['alpha','alpa'])) { $alpaCount++; $totalCount++; }
                                    elseif ($detail) { $totalCount++; }

                                    $dotMap = [
                                        'hadir' => ['bg'=>'bg-green-500','label'=>'H','text'=>'text-white'],
                                        'izin'  => ['bg'=>'bg-blue-400','label'=>'I','text'=>'text-white'],
                                        'sakit' => ['bg'=>'bg-yellow-400','label'=>'S','text'=>'text-white'],
                                        'alpha' => ['bg'=>'bg-red-400','label'=>'A','text'=>'text-white'],
                                        'alpa'  => ['bg'=>'bg-red-400','label'=>'A','text'=>'text-white'],
                                    ];
                                    $dot = $dotMap[$st] ?? null;
                                @endphp
                                <td class="px-3 py-2.5 text-center">
                                    @if($dot)
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-[10px] font-bold {{ $dot['bg'] }} {{ $dot['text'] }}">
                                            {{ $dot['label'] }}
                                        </span>
                                    @else
                                        <span class="inline-block w-4 h-4 rounded-full bg-gray-100 mx-auto"></span>
                                    @endif
                                </td>
                                @endforeach

                                {{-- Rekap --}}
                                @php
                                    $pct = $totalCount > 0 ? round(($hadirCount / $totalCount) * 100) : 0;
                                @endphp
                                <td class="px-3 py-2.5 text-center font-bold text-green-700 border-l border-gray-100">{{ $hadirCount }}</td>
                                <td class="px-3 py-2.5 text-center font-bold text-blue-600">{{ $izinCount }}</td>
                                <td class="px-3 py-2.5 text-center font-bold text-red-500">{{ $alpaCount }}</td>
                                <td class="px-3 py-2.5 text-center">
                                    <span class="text-xs font-bold {{ $pct >= 75 ? 'text-hijau-tua' : ($pct >= 50 ? 'text-yellow-600' : 'text-red-500') }}">
                                        {{ $pct }}%
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        {{-- Footer: total per sesi --}}
                        <tfoot>
                            <tr class="border-t-2 border-gray-200 bg-gray-50/80 font-semibold text-xs text-gray-600">
                                <td class="px-4 py-2.5 sticky left-0 bg-gray-50 border-r border-gray-100">Total Hadir</td>
                                @foreach($sesiList as $sesi)
                                @php
                                    $totalHadirSesi = collect($sesi['details'])->filter(fn($d) => strtolower($d->status_kehadiran) === 'hadir')->count();
                                    $totalSantriSesi = collect($sesi['details'])->count();
                                @endphp
                                <td class="px-3 py-2.5 text-center">
                                    <span class="font-bold text-hijau-tua">{{ $totalHadirSesi }}</span>
                                    <span class="text-gray-400">/{{ $totalSantriSesi }}</span>
                                </td>
                                @endforeach
                                <td colspan="4" class="px-3 py-2.5 text-center border-l border-gray-100 text-gray-400">–</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Mobile List View (Mirip Rekap Admin/Ustadz) --}}
                <div class="md:hidden flex flex-col gap-3 p-4 bg-gray-50/30">
                    @foreach($semuaSantri as $s)
                    @php
                        $isSaya = isset($santriSaya) && $santriSaya->id === $s->id;
                        $hadirCount = 0;
                        $izinCount  = 0;
                        $alpaCount  = 0;
                        $totalCount = 0;
                        foreach($sesiList as $sesi) {
                            $detail = $sesi['details'][$s->id] ?? null;
                            $st = strtolower($detail?->status_kehadiran ?? '');
                            if ($st === 'hadir') { $hadirCount++; $totalCount++; }
                            elseif (in_array($st, ['izin','sakit'])) { $izinCount++; $totalCount++; }
                            elseif (in_array($st, ['alpha','alpa'])) { $alpaCount++; $totalCount++; }
                            elseif ($detail) { $totalCount++; }
                        }
                        $pct = $totalCount > 0 ? round(($hadirCount / $totalCount) * 100) : 0;
                    @endphp
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 relative overflow-hidden {{ $isSaya ? 'ring-1 ring-hijau-tua/50 bg-green-50/10' : '' }}">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-full {{ $isSaya ? 'bg-hijau-tua text-white' : 'bg-gray-100 text-gray-500' }} flex items-center justify-center font-bold text-xs shrink-0">
                                    {{ strtoupper(substr($s->user->name ?? 'S', 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-[13px] font-bold text-gray-800 leading-tight truncate">
                                        {{ $s->user->name ?? '-' }}
                                        @if($isSaya)<span class="inline-block px-1 bg-hijau-tua/10 text-hijau-tua text-[9px] rounded ml-1 align-middle">(Saya)</span>@endif
                                    </h4>
                                    
                                    {{-- Stats inline --}}
                                    <div class="flex items-center gap-1.5 mt-0.5 text-[9px] sm:text-[10px]">
                                        <span class="text-green-600 font-bold">H:{{ $hadirCount }}</span>
                                        <span class="text-gray-300">|</span>
                                        <span class="text-blue-600 font-bold">I/S:{{ $izinCount }}</span>
                                        <span class="text-gray-300">|</span>
                                        <span class="text-red-500 font-bold">A:{{ $alpaCount }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Badge % -->
                            @php
                                $badgeClass = $pct >= 75 ? 'bg-green-50 text-green-600 border-green-100' : ($pct >= 50 ? 'bg-yellow-50 text-yellow-600 border-yellow-100' : 'bg-red-50 text-red-600 border-red-100');
                            @endphp
                            <div class="shrink-0 px-2 py-1 font-bold text-[10px] rounded border {{ $badgeClass }} whitespace-nowrap">
                                {{ $pct }}%
                            </div>
                        </div>

                        {{-- Detail Tanggal (Horizontal Scroll) --}}
                        <div class="mt-2.5 pt-2 border-t border-gray-100">
                            <div class="flex gap-2 overflow-x-auto pb-1" style="scrollbar-width: none;">
                                @foreach($sesiList as $sesi)
                                @php
                                    $detail = $sesi['details'][$s->id] ?? null;
                                    $st = strtolower($detail?->status_kehadiran ?? '');
                                    $dotMap = [
                                        'hadir' => ['bg'=>'bg-green-500','label'=>'H','text'=>'text-white'],
                                        'izin'  => ['bg'=>'bg-blue-400','label'=>'I','text'=>'text-white'],
                                        'sakit' => ['bg'=>'bg-yellow-400','label'=>'S','text'=>'text-white'],
                                        'alpha' => ['bg'=>'bg-red-400','label'=>'A','text'=>'text-white'],
                                        'alpa'  => ['bg'=>'bg-red-400','label'=>'A','text'=>'text-white'],
                                    ];
                                    $dot = $dotMap[$st] ?? null;
                                @endphp
                                <div class="flex flex-col items-center gap-0.5 min-w-[24px] shrink-0">
                                    <span class="text-[9px] text-gray-400 font-medium">{{ \Carbon\Carbon::parse($sesi['tanggal'])->format('d') }}</span>
                                    @if($dot)
                                        <span class="flex items-center justify-center w-5 h-5 rounded-full text-[9px] font-bold {{ $dot['bg'] }} {{ $dot['text'] }}">
                                            {{ $dot['label'] }}
                                        </span>
                                    @else
                                        <span class="flex items-center justify-center w-5 h-5 rounded-full bg-gray-50 text-gray-300 text-[9px] font-bold border border-gray-100">
                                            -
                                        </span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Legenda --}}
                <div class="px-5 py-3 border-t border-gray-100 flex flex-wrap gap-4 text-xs text-gray-500">
                    <span class="flex items-center gap-1.5"><span class="w-4 h-4 rounded-full bg-green-500 inline-block"></span>H = Hadir</span>
                    <span class="flex items-center gap-1.5"><span class="w-4 h-4 rounded-full bg-blue-400 inline-block"></span>I = Izin / Sakit</span>
                    <span class="flex items-center gap-1.5"><span class="w-4 h-4 rounded-full bg-red-400 inline-block"></span>A = Alpha</span>
                    <span class="flex items-center gap-1.5"><span class="w-4 h-4 rounded-full bg-gray-100 border border-gray-200 inline-block"></span>= Tidak tercatat</span>
                </div>
                @endif
            </div>
            @endforeach
        @endif
        {{-- Mobile Filter Modal --}}
        <div x-show="showFilterModal" x-effect="document.body.style.overflow = showFilterModal ? 'hidden' : ''" class="md:hidden fixed inset-0 z-50 flex flex-col justify-end bg-black/50" x-transition.opacity style="display:none">
            <div @click.away="showFilterModal = false" class="bg-white w-full rounded-t-2xl shadow-xl overflow-hidden flex flex-col max-h-[85vh]"
                 x-show="showFilterModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full">
                <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center flex-shrink-0">
                    <h3 class="text-sm font-bold text-gray-800">Filter Presensi</h3>
                    <button type="button" @click="showFilterModal = false" class="text-gray-400 bg-gray-50 rounded-full w-8 h-8 flex items-center justify-center hover:bg-gray-100 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-5 overflow-y-auto">
                    <form method="GET" action="{{ route('santri.presensi.semua') }}" class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-2">Pilih Kitab / Pelajaran</label>
                            <x-custom-select name="jadwal_id" :options="$jadwalOptions" :selected="request('jadwal_id', $jadwalDipilih ?? '')" placeholder="Semua Kitab" />
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Bulan</label>
                                <x-custom-select name="bulan" :options="$bulanOptions" :selected="request('bulan', $bulan ?? '')" placeholder="Semua Bulan" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Tahun</label>
                                <x-custom-select name="tahun" :options="$tahunOptions" :selected="request('tahun', $tahun ?? '')" placeholder="Semua Tahun" />
                            </div>
                        </div>

                        <div class="flex gap-2 pt-2">
                            <button type="button" @click="window.location.href='{{ route('santri.presensi.semua') }}'" class="flex-1 py-2.5 text-xs font-semibold text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">Reset</button>
                            <button type="submit" @click="showFilterModal = false" class="flex-1 py-2.5 text-xs font-semibold text-white bg-[#187a41] hover:bg-hijau-muda rounded-xl transition-colors">Tampilkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

