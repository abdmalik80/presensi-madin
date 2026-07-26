<x-app-layout>
    <x-slot name="header">Detail Presensi</x-slot>

    <div class="max-w-3xl mx-auto space-y-5">

        {{-- Header Info Card --}}
        <div class="relative overflow-hidden bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50">
            {{-- Gradient accent bar --}}
            <div class="h-1 bg-gradient-to-r from-hijau-tua via-hijau-muda to-green-400"></div>

            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-hijau-tua/10 flex items-center justify-center text-hijau-tua shrink-0">
                            <i class="fas fa-calendar-day text-sm"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold">Tanggal</p>
                            <p class="text-sm font-bold text-gray-800">{{ \Carbon\Carbon::parse($presensi->tanggal)->translatedFormat('d M Y') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 shrink-0">
                            <i class="fas fa-mosque text-sm"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold">Majelis</p>
                            <p class="text-sm font-bold text-gray-800">{{ $presensi->jadwal->majelis->nama_majelis ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-500 shrink-0">
                            <i class="fas fa-book-open text-sm"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold">Kitab</p>
                            <p class="text-sm font-bold text-gray-800">{{ $presensi->jadwal->kitab->nama_kitab ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Status Summary --}}
        @php
            $hadirCount = $presensi->details->where('status_kehadiran','Hadir')->count();
            $izinCount  = $presensi->details->where('status_kehadiran','Izin')->count();
            $sakitCount = $presensi->details->where('status_kehadiran','Sakit')->count();
            $alpaCount  = $presensi->details->where('status_kehadiran','Alpha')->count();
            $totalCount = $presensi->details->count();
            $pctHadir   = $totalCount > 0 ? round(($hadirCount/$totalCount)*100) : 0;
        @endphp

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="bg-green-50 border border-green-100 rounded-xl p-3.5 text-center transition hover:shadow-md hover:-translate-y-0.5">
                <p class="text-2xl font-bold text-green-700">{{ $hadirCount }}</p>
                <p class="text-[10px] text-green-600 font-semibold mt-0.5">Hadir</p>
            </div>
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-3.5 text-center transition hover:shadow-md hover:-translate-y-0.5">
                <p class="text-2xl font-bold text-blue-700">{{ $izinCount }}</p>
                <p class="text-[10px] text-blue-600 font-semibold mt-0.5">Izin</p>
            </div>
            <div class="bg-yellow-50 border border-yellow-100 rounded-xl p-3.5 text-center transition hover:shadow-md hover:-translate-y-0.5">
                <p class="text-2xl font-bold text-yellow-700">{{ $sakitCount }}</p>
                <p class="text-[10px] text-yellow-600 font-semibold mt-0.5">Sakit</p>
            </div>
            <div class="bg-red-50 border border-red-100 rounded-xl p-3.5 text-center transition hover:shadow-md hover:-translate-y-0.5">
                <p class="text-2xl font-bold text-red-700">{{ $alpaCount }}</p>
                <p class="text-[10px] text-red-600 font-semibold mt-0.5">Alpha</p>
            </div>
        </div>

        {{-- Attendance Progress --}}
        <div class="bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-600">Tingkat Kehadiran</span>
                <span class="text-sm font-bold {{ $pctHadir >= 75 ? 'text-green-700' : 'text-red-600' }}">{{ $pctHadir }}%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2">
                <div class="h-2 rounded-full transition-all duration-700 {{ $pctHadir >= 75 ? 'bg-gradient-to-r from-hijau-tua to-hijau-muda' : 'bg-red-400' }}" style="width: {{ $pctHadir }}%"></div>
            </div>
        </div>

        {{-- Santri Detail Table --}}
        <div class="bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-hijau-tua/10 flex items-center justify-center text-hijau-tua">
                    <i class="fas fa-users text-xs"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-700">Detail Santri</h3>
                    <p class="text-[10px] text-gray-400">{{ $totalCount }} santri tercatat</p>
                </div>
            </div>

            <div class="divide-y divide-gray-50">
                @foreach($presensi->details as $i => $detail)
                @php
                    $statusColors = [
                        'Hadir' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'icon' => 'fa-check-circle'],
                        'Izin'  => ['bg' => 'bg-blue-100',  'text' => 'text-blue-700',  'icon' => 'fa-file-alt'],
                        'Sakit' => ['bg' => 'bg-yellow-100','text' => 'text-yellow-700','icon' => 'fa-heartbeat'],
                        'Alpha' => ['bg' => 'bg-red-100',   'text' => 'text-red-700',   'icon' => 'fa-times-circle'],
                    ];
                    $sc = $statusColors[$detail->status_kehadiran] ?? $statusColors['Alpha'];
                @endphp
                <div class="flex items-center justify-between px-5 py-3 hover:bg-gray-50/50 transition-colors">
                    <div class="flex items-center gap-3">
                        <span class="w-7 h-7 rounded-lg bg-gray-100 flex items-center justify-center text-[10px] font-bold text-gray-500">{{ $i + 1 }}</span>
                        <p class="text-sm font-medium text-gray-800">{{ $detail->santri->user->name ?? '-' }}</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[11px] rounded-full font-semibold {{ $sc['bg'] }} {{ $sc['text'] }}">
                        <i class="fas {{ $sc['icon'] }} text-[9px]"></i>
                        {{ $detail->status_kehadiran }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Back Button --}}
        <div>
            <a href="{{ route('ustadz.presensi.index') }}"
               class="inline-flex items-center gap-2 border border-gray-200 text-gray-600 px-5 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50 transition-colors active:scale-95">
                <i class="fas fa-arrow-left text-xs"></i> Kembali
            </a>
        </div>
    </div>
</x-app-layout>
