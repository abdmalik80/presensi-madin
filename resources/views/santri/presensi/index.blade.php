<x-app-layout>
    <x-slot name="header">Presensi</x-slot>

    @php
    $jadwalOptions = []; foreach($jadwals as $j) $jadwalOptions[$j->id] = ($j->kitab->nama_kitab ?? '-') . ' (' . $j->hari . ')'; $bulanOptions = []; foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bln) $bulanOptions[$i+1] = $bln; $tahunOptions = []; $thnSkrg = date('Y'); for($t = $thnSkrg - 2; $t <= $thnSkrg + 1; $t++) $tahunOptions[$t] = $t;
    @endphp


    <div class="space-y-5 pb-8 md:pb-0" x-data="{ showFilterModal: false }">
        {{-- Tab Navigasi --}}
        <div class="flex gap-1.5 bg-white/60 backdrop-blur-xl rounded-2xl p-1.5 border border-white/50 shadow-[0_4px_20px_rgb(0,0,0,0.04)] w-full md:w-fit">
            <a href="{{ route('santri.presensi.index') }}"
               class="flex-1 md:flex-none flex justify-center items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold bg-hijau-tua text-white shadow-sm transition-all">
                <i class="fas fa-user text-[10px]"></i> Presensi Saya
            </a>
            <a href="{{ route('santri.presensi.semua') }}"
               class="flex-1 md:flex-none flex justify-center items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-gray-500 hover:text-hijau-tua hover:bg-hijau-tua/5 transition-all">
                <i class="fas fa-users text-[10px]"></i> Semua Santri
            </a>
        </div>

        {{-- Mobile Header & Filter Button --}}
        <div class="md:hidden flex items-center justify-between bg-white p-3 rounded-xl shadow-sm border border-gray-100 sticky top-0 z-30">
            <div>
                <h2 class="text-sm font-bold text-gray-800">Presensi Saya</h2>
                <p class="text-[10px] text-gray-500">
                    Filter Riwayat Kehadiran
                </p>
            </div>
            <button type="button" @click="showFilterModal = true" class="p-2 border border-[#187a41] bg-green-50 text-[#187a41] rounded-lg flex items-center justify-center w-10 h-10">
                <i class="fas fa-filter"></i>
            </button>
        </div>


        {{-- Summary Cards --}}
        @php
            $hadir = $details->where('status_kehadiran', 'Hadir')->count();
            $izin  = $details->where('status_kehadiran', 'Izin')->count();
            $sakit = $details->where('status_kehadiran', 'Sakit')->count();
            $alpa  = $details->where('status_kehadiran', 'Alpha')->count();
            $total = $details->count();
            $pct   = $total > 0 ? round($hadir/$total*100) : 0;
        @endphp

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="bg-green-50 border border-green-100 rounded-xl p-4 text-center transition hover:shadow-md hover:-translate-y-0.5">
                <p class="text-3xl font-bold text-green-700">{{ $hadir }}</p>
                <p class="text-[10px] text-green-600 font-semibold mt-1">Hadir</p>
            </div>
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-center transition hover:shadow-md hover:-translate-y-0.5">
                <p class="text-3xl font-bold text-blue-700">{{ $izin }}</p>
                <p class="text-[10px] text-blue-600 font-semibold mt-1">Izin</p>
            </div>
            <div class="bg-yellow-50 border border-yellow-100 rounded-xl p-4 text-center transition hover:shadow-md hover:-translate-y-0.5">
                <p class="text-3xl font-bold text-yellow-700">{{ $sakit }}</p>
                <p class="text-[10px] text-yellow-600 font-semibold mt-1">Sakit</p>
            </div>
            <div class="bg-red-50 border border-red-100 rounded-xl p-4 text-center transition hover:shadow-md hover:-translate-y-0.5">
                <p class="text-3xl font-bold text-red-700">{{ $alpa }}</p>
                <p class="text-[10px] text-red-600 font-semibold mt-1">Alpa</p>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-600">Tingkat Kehadiran</span>
                <span class="text-sm font-bold {{ $pct >= 75 ? 'text-green-700' : 'text-red-600' }}">{{ $pct }}%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2.5">
                <div class="h-2.5 rounded-full transition-all duration-700 {{ $pct >= 75 ? 'bg-gradient-to-r from-hijau-tua to-hijau-muda' : 'bg-red-400' }}" style="width: {{ $pct }}%"></div>
            </div>
            <div class="flex justify-between mt-2">
                <span class="text-[10px] text-gray-400">{{ $total }} total pertemuan</span>
                <span class="text-[10px] text-gray-400">{{ $hadir }} hadir</span>
            </div>
        </div>

        {{-- Filter Desktop --}}
        <div class="hidden md:block bg-white/60 backdrop-blur-xl rounded-2xl border border-white/50 shadow-sm p-4 relative z-40">
            <form method="GET" action="{{ route('santri.presensi.index') }}" class="flex flex-wrap gap-3 items-end">
                {{-- Pilih Jadwal/Kitab --}}
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">
                        <i class="fas fa-book-open mr-1 text-hijau-tua"></i>Filter Kitab / Pelajaran
                    </label>
                    <x-custom-select name="jadwal_id" id="jadwal_id" :options="$jadwalOptions" :selected="request('jadwal_id', $jadwalDipilih ?? '')" placeholder="— Semua Kitab —" width="w-full" />
                </div>

                {{-- Bulan --}}
                <div class="min-w-[130px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">
                        <i class="fas fa-calendar mr-1 text-hijau-tua"></i>Bulan
                    </label>
                    <x-custom-select name="bulan" id="bulan" :options="$bulanOptions" :selected="request('bulan', $bulan ?? '')" placeholder="Bulan" width="w-full" />
                </div>

                {{-- Tahun --}}
                <div class="min-w-[100px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">
                        <i class="fas fa-calendar-alt mr-1 text-hijau-tua"></i>Tahun
                    </label>
                    <x-custom-select name="tahun" id="tahun" :options="$tahunOptions" :selected="request('tahun', $tahun ?? '')" placeholder="Tahun" width="w-full" />
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                            class="flex items-center justify-center gap-2 px-5 py-2.5 bg-hijau-tua text-white text-sm font-semibold rounded-xl hover:bg-green-700 transition shadow-sm h-[42px]">
                        <i class="fas fa-filter text-xs"></i> Tampilkan
                    </button>
                    @if(request()->hasAny(['jadwal_id','bulan','tahun']))
                    <a href="{{ route('santri.presensi.index') }}" class="flex items-center justify-center gap-2 px-5 py-2.5 bg-white border border-gray-200 text-gray-600 text-sm font-semibold rounded-xl hover:bg-gray-50 hover:text-hijau-tua transition shadow-sm h-[42px]">
                        <i class="fas fa-undo text-xs"></i> Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 overflow-hidden">
            {{-- Table Header --}}
            <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-hijau-tua/10 flex items-center justify-center text-hijau-tua">
                    <i class="fas fa-history text-xs"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-700">Riwayat Presensi</h3>
                    <p class="text-[10px] text-gray-400">Detail semua catatan kehadiran</p>
                </div>
            </div>

            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-hijau-tua to-green-700 text-white">
                            <th class="px-5 py-3.5 text-left text-[10px] font-semibold uppercase tracking-wider">No</th>
                            <th class="px-5 py-3.5 text-left text-[10px] font-semibold uppercase tracking-wider">Tanggal</th>
                            <th class="px-5 py-3.5 text-left text-[10px] font-semibold uppercase tracking-wider">Majelis</th>
                            <th class="px-5 py-3.5 text-left text-[10px] font-semibold uppercase tracking-wider">Kitab</th>
                            <th class="px-5 py-3.5 text-left text-[10px] font-semibold uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($details as $i => $detail)
                        <tr class="hover:bg-green-50/30 transition-colors">
                            <td class="px-5 py-3.5 text-sm text-gray-500">{{ $i + 1 }}</td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                                        <i class="fas fa-calendar-day text-gray-400 text-[9px]"></i>
                                    </div>
                                    <span class="text-sm text-gray-800">{{ \Carbon\Carbon::parse($detail->presensi->tanggal)->translatedFormat('d M Y') }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-sm text-gray-600">{{ $detail->presensi->jadwal->majelis->nama_majelis ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-sm text-gray-600">{{ $detail->presensi->jadwal->kitab->nama_kitab ?? '-' }}</td>
                            <td class="px-5 py-3.5">
                                @php
                                    $statusStyle = [
                                        'Hadir' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'icon' => 'fa-check-circle'],
                                        'Izin'  => ['bg' => 'bg-blue-100',  'text' => 'text-blue-700',  'icon' => 'fa-file-alt'],
                                        'Sakit' => ['bg' => 'bg-yellow-100','text' => 'text-yellow-700','icon' => 'fa-heartbeat'],
                                        'Alpha' => ['bg' => 'bg-red-100',   'text' => 'text-red-700',   'icon' => 'fa-times-circle'],
                                    ];
                                    $sc = $statusStyle[$detail->status_kehadiran] ?? $statusStyle['Alpha'];
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] rounded-full font-semibold {{ $sc['bg'] }} {{ $sc['text'] }}">
                                    <i class="fas {{ $sc['icon'] }} text-[8px]"></i>
                                    {{ $detail->status_kehadiran }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mb-4">
                                        <i class="fas fa-history text-gray-300 text-2xl"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-500">Belum ada riwayat presensi</p>
                                    <p class="text-xs text-gray-400 mt-1">Data kehadiran akan muncul di sini</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile List View --}}
            <div class="md:hidden divide-y divide-gray-100">
                @forelse($details as $i => $detail)
                @php
                    $statusStyle = [
                        'Hadir' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'ring' => 'ring-green-200', 'left' => 'bg-green-500', 'icon' => 'fa-check-circle'],
                        'Izin'  => ['bg' => 'bg-blue-100',  'text' => 'text-blue-700',  'ring' => 'ring-blue-200',  'left' => 'bg-blue-500',  'icon' => 'fa-file-alt'],
                        'Sakit' => ['bg' => 'bg-yellow-100','text' => 'text-yellow-700','ring' => 'ring-yellow-200','left' => 'bg-yellow-400','icon' => 'fa-heartbeat'],
                        'Alpha' => ['bg' => 'bg-red-100',   'text' => 'text-red-700',   'ring' => 'ring-red-200',   'left' => 'bg-red-500',   'icon' => 'fa-times-circle'],
                    ];
                    $sc = $statusStyle[$detail->status_kehadiran] ?? $statusStyle['Alpha'];
                @endphp
                <div class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50/70 transition-colors relative">
                    {{-- Left accent bar --}}
                    <div class="w-1 self-stretch rounded-full {{ $sc['left'] }} flex-shrink-0"></div>

                    {{-- Nomor --}}
                    <span class="text-[10px] font-bold text-gray-300 w-4 text-center flex-shrink-0">{{ $i + 1 }}</span>

                    {{-- Main Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-xs font-bold text-gray-800 truncate">
                                {{ \Carbon\Carbon::parse($detail->presensi->tanggal)->translatedFormat('d M Y') }}
                            </p>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-semibold rounded-full flex-shrink-0 {{ $sc['bg'] }} {{ $sc['text'] }} ring-1 {{ $sc['ring'] }}">
                                <i class="fas {{ $sc['icon'] }} text-[8px]"></i>
                                {{ $detail->status_kehadiran }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-[10px] text-gray-500 truncate flex items-center gap-1">
                                <i class="fas fa-book-open text-gray-300 text-[8px]"></i>
                                {{ $detail->presensi->jadwal->kitab->nama_kitab ?? '-' }}
                            </span>
                            <span class="text-gray-300 text-[10px]">·</span>
                            <span class="text-[10px] text-gray-400 truncate">
                                {{ $detail->presensi->jadwal->majelis->nama_majelis ?? '-' }}
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="py-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                        <i class="fas fa-history text-gray-300 text-2xl"></i>
                    </div>
                    <p class="text-sm font-semibold text-gray-500">Belum ada riwayat presensi</p>
                    <p class="text-[10px] text-gray-400 mt-1">Data kehadiran Anda akan muncul di sini</p>
                </div>
                @endforelse
            </div>
        </div>
        
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
                    <form method="GET" action="{{ route('santri.presensi.index') }}" class="space-y-4">
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
                            <button type="button" @click="window.location.href='{{ route('santri.presensi.index') }}'" class="flex-1 py-2.5 text-xs font-semibold text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">Reset</button>
                            <button type="submit" @click="showFilterModal = false" class="flex-1 py-2.5 text-xs font-semibold text-white bg-[#187a41] hover:bg-hijau-muda rounded-xl transition-colors">Tampilkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

