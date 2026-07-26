<x-app-layout>
    @php
    $jadwalOptions = [];
    foreach($jadwals as $j) {
        $jadwalOptions[$j->id] = ($j->majelis->nama_majelis ?? '') . ' - ' . $j->hari;
    }
    @endphp
    <x-slot name="header">Majelis & Presensi</x-slot>

    <div class="space-y-5 pb-8 md:pb-0" x-data="{ showFilterModal: false }">
        <x-alert />

        {{-- Jadwal Majelis Section --}}
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Daftar Majelis Mengajar</h2>
                    <p class="text-xs text-gray-500">Daftar semua majelis dan kitab yang Anda ampu</p>
                </div>
            </div>

            @if($jadwals->isEmpty())
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 text-center">
                    <div class="w-12 h-12 bg-amber-100 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-calendar-times text-xl"></i>
                    </div>
                    <p class="text-sm font-semibold text-amber-700">Belum ada Jadwal Majelis yang ditugaskan kepada Anda.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($jadwals as $j)
                    @php
                        $hariMap = ['Sunday'=>'Ahad','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
                        $hariIni = $hariMap[now()->format('l')] ?? '';
                        $isToday = $j->hari === $hariIni;
                        $presensiHariIni = $j->presensis->first();
                        $isOpen = $presensiHariIni && $presensiHariIni->status === 'dibuka';
                    @endphp
                        <div class="bg-white/80 backdrop-blur-md rounded-2xl border {{ $isToday ? 'border-hijau-tua ring-2 ring-hijau-tua/20 shadow-lg' : 'border-green-100 shadow-sm hover:shadow-md' }} p-4 sm:p-5 transition-all group relative overflow-hidden flex flex-col h-full">
                            <!-- Background decoration -->
                            <div class="absolute -right-6 -top-6 w-24 h-24 bg-green-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
                            
                            <div class="relative z-10 flex flex-col h-full">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 text-hijau-tua rounded-xl flex items-center justify-center font-bold shadow-sm">
                                        <i class="fas fa-mosque text-base sm:text-lg"></i>
                                    </div>
                                    <div class="flex flex-col items-end gap-1.5">
                                        <span class="px-3 py-1 flex items-center gap-1.5 {{ $isToday ? 'bg-orange-500 animate-pulse' : 'bg-hijau-tua' }} text-white text-[10px] font-bold uppercase tracking-wider rounded-lg shadow-sm">
                                            @if($isToday) <span class="w-1.5 h-1.5 bg-white rounded-full"></span> @endif
                                            {{ $j->hari }} {{ $isToday ? '• Hari Ini' : '' }}
                                        </span>
                                        <span class="px-2.5 py-1 bg-white border border-gray-100 text-gray-600 text-[10px] font-bold uppercase tracking-wider rounded-lg">
                                            <i class="fas fa-clock mr-1 text-gray-400"></i>{{ $j->jam_mulai ? substr($j->jam_mulai, 0, 5) : 'TBA' }} - {{ $j->jam_selesai ? substr($j->jam_selesai, 0, 5) : 'TBA' }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="flex-grow">
                                    <h3 class="text-base sm:text-lg font-bold text-gray-800 mb-1 group-hover:text-hijau-tua transition-colors">
                                        {{ $j->majelis->nama_majelis ?? 'Majelis Tidak Diketahui' }}
                                    </h3>
                                    <p class="text-xs sm:text-sm text-gray-500 flex items-center gap-1.5 mb-3 sm:mb-4">
                                        <i class="fas fa-book-open text-gray-400"></i>
                                        Kitab {{ $j->kitab->nama_kitab ?? '-' }}
                                    </p>
                                    
                                    {{-- Status absen santri --}}
                                    <div class="mb-4">
                                        @if($isOpen)
                                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold bg-green-100 text-green-700 px-3 py-1.5 rounded-full border border-green-200">
                                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span> Presensi Santri: Dibuka
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold bg-gray-100 text-gray-500 px-3 py-1.5 rounded-full border border-gray-200">
                                                <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span> Presensi Santri: Ditutup
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="flex flex-col gap-2 mt-auto">
                                    <div class="grid grid-cols-2 gap-2">
                                        <a href="{{ route('ustadz.jadwal.show', $j->id) }}" 
                                           class="flex items-center justify-center gap-1.5 w-full py-2.5 bg-white border border-gray-200 text-gray-600 text-center text-xs font-semibold rounded-xl hover:bg-gray-50 hover:text-hijau-tua hover:border-hijau-tua/30 transition-all">
                                            <i class="fas fa-folder-open"></i> Detail
                                        </a>
                                        <a href="{{ route('ustadz.presensi.create', ['jadwal_id' => $j->id]) }}" 
                                           class="flex items-center justify-center gap-1.5 w-full py-2.5 bg-hijau-tua hover:bg-hijau-muda text-white text-center text-xs font-semibold rounded-xl transition-all shadow-md shadow-hijau-tua/20 hover:-translate-y-0.5">
                                            <i class="fas fa-edit"></i> Presensi
                                        </a>
                                    </div>
                                    @if($isOpen)
                                        <form method="POST" action="{{ route('ustadz.presensi.toggle') }}">
                                            @csrf
                                            <input type="hidden" name="jadwal_id" value="{{ $j->id }}">
                                            <button type="submit" onclick="return confirm('Tutup presensi santri sekarang?')"
                                                    class="w-full py-2.5 text-xs font-semibold rounded-xl transition-all border flex items-center justify-center gap-1.5 bg-red-50 border-red-200 text-red-600 hover:bg-red-100">
                                                <i class="fas fa-lock"></i> Tutup Presensi Santri
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" onclick="openModalBukaAbsen('{{ $j->id }}', '{{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }}', '{{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }}')"
                                                class="w-full py-2.5 text-xs font-semibold rounded-xl transition-all border flex items-center justify-center gap-1.5 bg-blue-50 border-blue-200 text-blue-600 hover:bg-blue-100">
                                            <i class="fas fa-lock-open"></i> Buka Presensi Santri
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                @endforeach
                </div>
            @endif
        </div>

        {{-- Filter + Header Riwayat --}}
        <div class="bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 overflow-hidden">
            <div class="px-4 py-3 sm:px-6 sm:py-4 border-b border-gray-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 sm:gap-3">
                <div class="flex items-center justify-between w-full sm:w-auto">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-hijau-tua/10 flex items-center justify-center text-hijau-tua">
                            <i class="fas fa-clipboard-list text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-700">Riwayat Presensi</h3>
                            <p class="text-[10px] text-gray-400">{{ $presensis->total() }} total catatan</p>
                        </div>
                    </div>
                    <div class="sm:hidden flex items-center gap-2">
                        @if(request()->anyFilled(['dari','sampai','jadwal_id']))
                        <a href="{{ route('ustadz.presensi.index') }}" class="px-2 py-1 text-[10px] font-semibold text-red-600 bg-red-50 border border-red-200 rounded-full">
                            <i class="fas fa-times mr-0.5"></i> Reset
                        </a>
                        @endif
                        <button @click="showFilterModal = true" class="p-1.5 rounded-lg border border-gray-200 text-gray-500 bg-white hover:bg-gray-50 flex items-center justify-center h-8 w-8 transition-colors">
                            <i class="fas fa-filter text-[10px]"></i>
                        </button>
                        <a href="{{ route('ustadz.presensi.create') }}"
                           class="inline-flex items-center gap-1.5 text-xs font-semibold bg-hijau-tua/10 text-hijau-tua border border-hijau-tua/20 px-3 py-1.5 rounded-lg hover:bg-hijau-tua hover:text-white transition-all active:scale-95">
                            <i class="fas fa-plus text-[10px]"></i>
                        </a>
                    </div>
                </div>
                
                {{-- Desktop Filter Form --}}
                <div class="hidden sm:flex flex-wrap items-center gap-2">
                    <form method="GET" action="{{ route('ustadz.presensi.index') }}" class="flex flex-wrap gap-2 items-center" 
                          x-data="{ mode: '{{ request('jadwal_id') ? 'jadwal' : 'tanggal' }}' }">
                        
                        <!-- Toggle Mode Pencarian -->
                        <div class="flex bg-gray-100/80 p-1 rounded-lg border border-gray-200 items-center h-[36px]">
                            <button type="button" @click="mode = 'tanggal'" 
                                    :class="mode === 'tanggal' ? 'bg-white shadow-sm text-[#187a41] font-bold border border-gray-100' : 'text-gray-500 hover:text-gray-700'"
                                    class="px-3 py-1 text-xs rounded-md transition-all h-full flex items-center">
                                <i class="fas fa-calendar-day mr-1.5"></i> Tanggal
                            </button>
                            <button type="button" @click="mode = 'jadwal'" 
                                    :class="mode === 'jadwal' ? 'bg-white shadow-sm text-[#187a41] font-bold border border-gray-100' : 'text-gray-500 hover:text-gray-700'"
                                    class="px-3 py-1 text-xs rounded-md transition-all h-full flex items-center">
                                <i class="fas fa-mosque mr-1.5"></i> Majelis
                            </button>
                        </div>

                        <!-- Input Tanggal -->
                        <div x-show="mode === 'tanggal'" style="display: {{ request('jadwal_id') ? 'none' : 'flex' }}; gap: 0.5rem;" class="items-center">
                            <input type="date" name="dari" value="{{ request('dari') }}" :disabled="mode !== 'tanggal'"
                                class="border border-gray-200 rounded-lg px-2 py-1.5 text-xs focus:ring-2 focus:ring-hijau-tua bg-white/80 h-[36px]">
                            <span class="text-xs text-gray-500">-</span>
                            <input type="date" name="sampai" value="{{ request('sampai') }}" :disabled="mode !== 'tanggal'"
                                class="border border-gray-200 rounded-lg px-2 py-1.5 text-xs focus:ring-2 focus:ring-hijau-tua bg-white/80 h-[36px]">
                        </div>

                        <!-- Input Jadwal -->
                        <div x-show="mode === 'jadwal'" style="display: {{ request('jadwal_id') ? 'block' : 'none' }};">
                            <x-custom-select name="jadwal_id" :options="$jadwalOptions" :selected="request('jadwal_id')" placeholder="Semua Jadwal" />
                        </div>

                        <button type="submit" class="bg-hijau-tua text-white px-4 py-1.5 rounded-lg text-sm hover:bg-hijau-muda transition-colors flex items-center gap-1 h-[36px]">
                            <i class="fas fa-search text-xs"></i> Cari
                        </button>
                        
                        @if(request()->anyFilled(['dari','sampai','jadwal_id']))
                        <a href="{{ route('ustadz.presensi.index') }}" class="px-3 py-1.5 rounded-lg text-sm border border-gray-200 text-gray-500 hover:bg-gray-50 transition-colors h-[36px] flex items-center">
                            <i class="fas fa-times text-xs mr-1"></i> Reset
                        </a>
                        @endif
                    </form>
                    <div class="h-6 w-px bg-gray-200 mx-1"></div>
                    <a href="{{ route('ustadz.presensi.create') }}"
                       class="inline-flex items-center gap-2 bg-hijau-tua/10 text-hijau-tua border border-hijau-tua/20 px-4 py-1.5 h-[36px] rounded-lg text-sm font-semibold hover:bg-hijau-tua hover:text-white transition-all shadow-sm">
                        <i class="fas fa-calendar-plus text-xs"></i> Rekam Manual
                    </a>
                </div>
            </div>

            {{-- Table Desktop --}}
            <div class="overflow-x-auto hidden md:block" style="position: relative; z-index: 10;">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-hijau-tua to-green-700 text-white">
                            <th class="px-5 py-3.5 text-left text-[10px] font-semibold uppercase tracking-wider">No</th>
                            <th class="px-5 py-3.5 text-left text-[10px] font-semibold uppercase tracking-wider">Tanggal</th>
                            <th class="px-5 py-3.5 text-left text-[10px] font-semibold uppercase tracking-wider">Majelis</th>
                            <th class="px-5 py-3.5 text-left text-[10px] font-semibold uppercase tracking-wider">Kitab</th>
                            <th class="px-5 py-3.5 text-left text-[10px] font-semibold uppercase tracking-wider">Hadir / Total</th>
                            <th class="px-5 py-3.5 text-center text-[10px] font-semibold uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($presensis as $p)
                        @php
                            $hadirCount = $p->details->where('status_kehadiran','Hadir')->count();
                            $totalCount = $p->details->count();
                            $pctHadir = $totalCount > 0 ? round(($hadirCount/$totalCount)*100) : 0;
                        @endphp
                        <tr class="hover:bg-green-50/30 transition-colors">
                            <td class="px-5 py-3.5 text-sm text-gray-500">{{ $loop->iteration + ($presensis->currentPage()-1)*$presensis->perPage() }}</td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-hijau-tua/5 flex items-center justify-center text-hijau-tua shrink-0">
                                        <i class="fas fa-calendar-day text-[10px]"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-800">{{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d M Y') }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-sm text-gray-600">{{ $p->jadwal->majelis->nama_majelis ?? '-' }}</td>
                            <td class="px-5 py-3.5">
                                <span class="text-sm text-gray-600">{{ $p->jadwal->kitab->nama_kitab ?? '-' }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 max-w-[80px]">
                                        <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full {{ $pctHadir >= 75 ? 'bg-green-500' : ($pctHadir >= 50 ? 'bg-yellow-500' : 'bg-red-400') }}" style="width: {{ $pctHadir }}%"></div>
                                        </div>
                                    </div>
                                    <span class="text-xs font-bold {{ $pctHadir >= 75 ? 'text-green-700' : ($pctHadir >= 50 ? 'text-yellow-700' : 'text-red-600') }}">{{ $hadirCount }}/{{ $totalCount }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <a href="{{ route('ustadz.presensi.show', $p) }}"
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-hijau-tua bg-hijau-tua/5 hover:bg-hijau-tua/15 transition-colors">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mb-4">
                                        <i class="fas fa-clipboard text-gray-300 text-2xl"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-500">Belum ada riwayat presensi</p>
                                    <p class="text-xs text-gray-400 mt-1">Mulai dengan merekam presensi pertama</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile List --}}
            <div class="md:hidden space-y-0 bg-white/60 backdrop-blur-xl">
                @forelse($presensis as $p)
                @php
                    $hadirCount = $p->details->where('status_kehadiran','Hadir')->count();
                    $totalCount = $p->details->count();
                    $pctHadir = $totalCount > 0 ? round(($hadirCount/$totalCount)*100) : 0;
                @endphp
                <div class="p-4 border-b border-gray-100 last:border-0 hover:bg-gray-50/50 transition-colors">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-hijau-tua/10 flex items-center justify-center text-hijau-tua shrink-0 border border-hijau-tua/20">
                                <i class="fas fa-calendar-day text-xs"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-gray-800">{{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d M Y') }}</h4>
                                <p class="text-[10px] text-gray-500 mt-0.5">{{ $p->jadwal->majelis->nama_majelis ?? '-' }}</p>
                            </div>
                        </div>
                        <a href="{{ route('ustadz.presensi.show', $p) }}"
                           class="w-8 h-8 rounded-lg text-hijau-tua bg-hijau-tua/5 border border-hijau-tua/10 flex items-center justify-center hover:bg-hijau-tua/10 transition-colors">
                            <i class="fas fa-chevron-right text-[10px]"></i>
                        </a>
                    </div>
                    
                    <div class="pl-12">
                        <p class="text-[10px] text-gray-600 mb-2"><i class="fas fa-book-open text-gray-400 w-3"></i> {{ $p->jadwal->kitab->nama_kitab ?? '-' }}</p>
                        
                        <div class="flex items-center gap-2">
                            <div class="flex-1">
                                <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full {{ $pctHadir >= 75 ? 'bg-green-500' : ($pctHadir >= 50 ? 'bg-yellow-500' : 'bg-red-400') }}" style="width: {{ $pctHadir }}%"></div>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold {{ $pctHadir >= 75 ? 'text-green-700' : ($pctHadir >= 50 ? 'text-yellow-700' : 'text-red-600') }}">{{ $hadirCount }}/{{ $totalCount }} Hadir</span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="py-10 text-center">
                    <div class="w-12 h-12 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-clipboard text-gray-300 text-xl"></i>
                    </div>
                    <p class="text-xs font-semibold text-gray-500">Belum ada riwayat presensi</p>
                </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($presensis->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
                {{ $presensis->links() }}
            </div>
            @endif
        </div>

        {{-- Mobile Filter Modal --}}
        <div x-show="showFilterModal" x-effect="document.body.style.overflow = showFilterModal ? 'hidden' : ''" class="md:hidden fixed inset-0 z-50 flex flex-col justify-end bg-black/50" x-transition.opacity style="display:none">
            <div @click.away="showFilterModal = false" class="bg-white w-full rounded-t-2xl shadow-xl flex flex-col max-h-[85vh]"
                 x-show="showFilterModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full">
                <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center flex-shrink-0">
                    <h3 class="text-sm font-bold text-gray-800">Filter Riwayat Presensi</h3>
                    <button @click="showFilterModal = false" class="text-gray-400 bg-gray-50 rounded-full w-8 h-8 flex items-center justify-center"><i class="fas fa-times"></i></button>
                </div>
                <div class="p-5 overflow-visible">
                    <form method="GET" action="{{ route('ustadz.presensi.index') }}" class="space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-2">Dari Tanggal</label>
                                <input type="date" name="dari" value="{{ request('dari') }}" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-hijau-tua bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-2">Sampai Tanggal</label>
                                <input type="date" name="sampai" value="{{ request('sampai') }}" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-hijau-tua bg-white">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-2">Pilih Jadwal/Majelis</label>
                            <x-custom-select name="jadwal_id" :options="$jadwalOptions" :selected="request('jadwal_id')" placeholder="Semua Jadwal" width="w-full" />
                        </div>

                        <div class="flex gap-2 pt-2">
                            <button type="button" @click="window.location.href='{{ route('ustadz.presensi.index') }}'" class="flex-1 py-2.5 text-xs font-semibold text-gray-600 bg-gray-100 rounded-xl">Reset</button>
                            <button type="submit" @click="showFilterModal = false" class="flex-1 py-2.5 text-xs font-semibold text-white bg-[#187a41] rounded-xl">Terapkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('modals')
    <!-- Modal Buka Presensi -->
    <div id="modalBukaAbsen" class="fixed inset-0 hidden bg-gray-900/60 backdrop-blur-sm items-center justify-center p-4 transition-all" style="z-index: 9999;">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="modalBukaAbsenContent">
            
            {{-- Modal Header --}}
            <div class="relative bg-gradient-to-br from-hijau-tua to-emerald-700 p-6 text-white overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                <div class="relative z-10 flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner">
                            <i class="fas fa-fingerprint text-2xl text-emerald-100"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold leading-tight">Buka Presensi Santri</h3>
                            <p class="text-emerald-100 text-xs mt-1">Tentukan batas waktu presensi mandiri.</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeModalBukaAbsen()" class="w-8 h-8 flex items-center justify-center rounded-full bg-black/10 hover:bg-black/20 text-white transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <form action="{{ route('ustadz.presensi.toggle') }}" method="POST">
                @csrf
                <input type="hidden" name="jadwal_id" id="buka_jadwal_id">
                
                <div class="p-6 space-y-5">
                    
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex items-start gap-3">
                        <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                        <p class="text-[11px] text-blue-700 leading-relaxed">
                            Secara default, jam di bawah disi sesuai dengan jam majelis Anda. Anda bebas merubahnya untuk memberi kelonggaran waktu bagi santri.
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="flex items-center gap-1.5 text-xs font-semibold text-gray-700 mb-2">
                                <i class="fas fa-hourglass-start text-emerald-500"></i> Jam Buka
                            </label>
                            <input type="time" name="jam_buka" id="input_jam_buka" required 
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all font-semibold text-gray-700">
                        </div>
                        <div>
                            <label class="flex items-center gap-1.5 text-xs font-semibold text-gray-700 mb-2">
                                <i class="fas fa-hourglass-end text-rose-500"></i> Jam Tutup
                            </label>
                            <input type="time" name="jam_tutup" id="input_jam_tutup" required 
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all font-semibold text-gray-700">
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3 border-t border-gray-100">
                    <button type="button" onclick="closeModalBukaAbsen()" 
                            class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 rounded-xl transition-all shadow-sm">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-6 py-2.5 text-sm font-bold text-white rounded-xl transition-all shadow-lg shadow-emerald-600/20 hover:shadow-emerald-600/40 hover:-translate-y-0.5 flex items-center gap-2"
                            style="background: linear-gradient(135deg, #10b981 0%, #047857 100%);">
                        <i class="fas fa-check-circle"></i> Buka Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endpush

    @push('scripts')
    <script>
        function openModalBukaAbsen(jadwalId, jamMulai, jamSelesai) {
            document.getElementById('buka_jadwal_id').value = jadwalId;
            document.getElementById('input_jam_buka').value = jamMulai;
            document.getElementById('input_jam_tutup').value = jamSelesai;
            
            const modal = document.getElementById('modalBukaAbsen');
            const content = document.getElementById('modalBukaAbsenContent');
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }
        
        function closeModalBukaAbsen() {
            const modal = document.getElementById('modalBukaAbsen');
            const content = document.getElementById('modalBukaAbsenContent');
            
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 300);
        }
    </script>
    @endpush
</x-app-layout>

