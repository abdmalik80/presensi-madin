<x-app-layout>
    <x-slot name="header">Rekap Presensi Majelis</x-slot>

    @php
    $majelisOptions = []; 
    foreach($majelisList as $m) {
        $majelisOptions[$m->id] = $m->nama_majelis; 
    }
    @endphp

    <div class="space-y-4 pb-4 md:pb-0" x-data="{ showFilterModal: false }">

        {{-- Summary Cards --}}
        @if(isset($summary))
        <div class="grid gap-2 md:gap-4" style="grid-template-columns: repeat(5, minmax(0, 1fr)); position: relative; z-index: 10;" style="position: relative; z-index: 10;">
            @php
                $cards = [
                    ['label'=>'Total Data','value'=>$summary['total'],'color'=>'purple','icon'=>'fa-users'],
                    ['label'=>'Hadir','value'=>$summary['hadir'],'color'=>'green','icon'=>'fa-check-circle'],
                    ['label'=>'Izin','value'=>$summary['izin'],'color'=>'blue','icon'=>'fa-envelope'],
                    ['label'=>'Sakit','value'=>$summary['sakit'],'color'=>'yellow','icon'=>'fa-heartbeat'],
                    ['label'=>'Alpa','value'=>$summary['alpa'],'color'=>'red','icon'=>'fa-times-circle'],
                ];
            @endphp
            @foreach($cards as $card)
            <div class="bg-white/70 backdrop-blur-xl rounded-2xl border border-white/50 shadow-sm p-2 md:p-4 flex flex-col items-center justify-center text-center">
                <div class="w-6 h-6 md:w-10 md:h-10 rounded-full bg-{{ $card['color'] }}-100 flex items-center justify-center mb-1 md:mb-2">
                    <i class="fas {{ $card['icon'] }} text-{{ $card['color'] }}-600 text-[10px] md:text-sm"></i>
                </div>
                <p class="text-xs md:text-2xl font-bold text-gray-800 leading-none mb-0.5">{{ $card['value'] }}</p>
                <p class="text-[8px] md:text-xs text-gray-500 leading-tight">{{ $card['label'] }}</p>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Desktop Filter --}}
        <div class="hidden md:block bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 p-4 relative z-40">
            <form method="GET">
                <div class="grid grid-cols-[1.5fr_1.2fr_1fr_1fr_auto] gap-3 items-end">
                    <div class="min-w-0">
                        <label class="block text-xs font-semibold text-gray-600 mb-1 truncate">Cari Santri</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / NIS" class="w-full h-[38px] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-hijau-tua bg-white">
                    </div>
                    <div class="min-w-0">
                        <label class="block text-xs font-semibold text-gray-600 mb-1 truncate">Majelis</label>
                        <x-custom-select name="majelis_id" :options="$majelisOptions" :selected="request('majelis_id')" placeholder="Semua Majelis Saya" width="w-full" />
                    </div>

                    
                    <div class="min-w-0">
                        <label class="block text-xs font-semibold text-gray-600 mb-1 truncate">Dari</label>
                        <input type="date" name="dari" value="{{ request('dari') }}" class="w-full h-[38px] border border-gray-200 rounded-lg px-2 py-2 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent bg-white">
                    </div>
                    <div class="min-w-0">
                        <label class="block text-xs font-semibold text-gray-600 mb-1 truncate">Sampai</label>
                        <input type="date" name="sampai" value="{{ request('sampai') }}" class="w-full h-[38px] border border-gray-200 rounded-lg px-2 py-2 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent bg-white">
                    </div>

                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        <button type="submit" class="bg-hijau-tua text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-hijau-muda transition-all shadow-sm flex items-center justify-center gap-1.5 h-[38px]">
                            <i class="fas fa-search"></i> Tampilkan
                        </button>
                        @if(request()->anyFilled(['majelis_id','dari','sampai','search']))
                        <a href="{{ route('ustadz.laporan.pdf', request()->query()) }}" target="_blank"
                           class="bg-rose-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-rose-700 transition-all shadow-sm flex items-center justify-center gap-2 h-[38px]">
                            <i class="fas fa-file-pdf"></i> Cetak PDF
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        {{-- Mobile Header & Filter Button --}}
        <div class="md:hidden flex items-center justify-between bg-white p-3 rounded-xl shadow-sm border border-gray-100 sticky top-0 z-30">
            <div>
                <h2 class="text-sm font-bold text-gray-800">Laporan Presensi</h2>
                <p class="text-[10px] text-gray-500">
                    @if(request()->anyFilled(['majelis_id','dari','sampai','search'])) Filter Aktif @else Pilih Filter @endif
                </p>
            </div>
            <div class="flex items-center gap-2">
                @if(request()->anyFilled(['majelis_id','dari','sampai','search']))
                <a href="{{ route('ustadz.laporan.pdf', request()->query()) }}" target="_blank" class="px-3 py-2 bg-rose-50 text-rose-600 border border-rose-200 rounded-lg flex items-center justify-center gap-2 h-10 text-xs font-bold">
                    <i class="fas fa-file-pdf"></i> Cetak PDF
                </a>
                @endif
                <button type="button" @click="showFilterModal = true" class="p-2 border border-[#187a41] bg-green-50 text-[#187a41] rounded-lg flex items-center justify-center w-10 h-10">
                    <i class="fas fa-filter"></i>
                </button>
            </div>
        </div>

        {{-- Desktop Table --}}
        <div class="hidden md:block bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-hijau-tua text-white">
                    <tr>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">No</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">Nama Santri</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">Majelis</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wider">Hadir</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wider">Izin</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wider">Sakit</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wider">Alpa</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wider">%</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white/40">
                    @forelse($laporan as $i => $row)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-3 text-sm text-gray-600">{{ $i + 1 }}</td>
                        <td class="px-5 py-3">
                            <div class="text-sm font-bold text-gray-800">{{ $row['nama'] }}</div>
                            <div class="text-xs text-gray-500">{{ $row['nis'] }}</div>
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-600">{{ $row['majelis'] }}</td>
                        <td class="px-5 py-3 text-center"><span class="inline-flex items-center justify-center w-6 h-6 rounded bg-green-50 text-green-700 font-bold text-xs">{{ $row['hadir'] }}</span></td>
                        <td class="px-5 py-3 text-center"><span class="inline-flex items-center justify-center w-6 h-6 rounded bg-blue-50 text-blue-700 font-bold text-xs">{{ $row['izin'] }}</span></td>
                        <td class="px-5 py-3 text-center"><span class="inline-flex items-center justify-center w-6 h-6 rounded bg-yellow-50 text-yellow-700 font-bold text-xs">{{ $row['sakit'] }}</span></td>
                        <td class="px-5 py-3 text-center"><span class="inline-flex items-center justify-center w-6 h-6 rounded bg-red-50 text-red-700 font-bold text-xs">{{ $row['alpa'] }}</span></td>
                        <td class="px-5 py-3 text-center">
                            @php $pct = $row['total'] > 0 ? round($row['hadir']/$row['total']*100) : 0; @endphp
                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-bold {{ $pct >= 75 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $pct }}%
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center text-gray-400">
                            <i class="fas fa-file-alt text-4xl mb-3 block text-gray-200"></i>
                            Gunakan filter di atas untuk menampilkan data laporan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile List --}}
        @if(isset($summary))
        <div class="md:hidden space-y-0 bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 overflow-hidden">
            @forelse($laporan as $i => $row)
            <div class="p-3 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2 overflow-hidden">
                        <div class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0 text-[10px] text-gray-500 font-bold">
                            {{ $i + 1 }}
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-xs font-bold text-gray-800 truncate">{{ $row['nama'] }}</h4>
                            <p class="text-[9px] text-gray-500">{{ $row['nis'] }} • {{ $row['majelis'] }}</p>
                        </div>
                    </div>
                    @php $pct = $row['total'] > 0 ? round($row['hadir']/$row['total']*100) : 0; @endphp
                    <span class="px-2 py-1 text-[10px] rounded-full font-semibold flex-shrink-0 {{ $pct >= 75 ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200' }}">
                        {{ $pct }}%
                    </span>
                </div>
                
                <div class="grid grid-cols-4 gap-1 mt-2">
                    <div class="bg-green-50 border border-green-100 rounded p-1 text-center">
                        <p class="text-[8px] text-green-600 uppercase mb-0.5">Hadir</p>
                        <p class="text-[11px] font-bold text-green-700">{{ $row['hadir'] }}</p>
                    </div>
                    <div class="bg-blue-50 border border-blue-100 rounded p-1 text-center">
                        <p class="text-[8px] text-blue-600 uppercase mb-0.5">Izin</p>
                        <p class="text-[11px] font-bold text-blue-700">{{ $row['izin'] }}</p>
                    </div>
                    <div class="bg-yellow-50 border border-yellow-100 rounded p-1 text-center">
                        <p class="text-[8px] text-yellow-600 uppercase mb-0.5">Sakit</p>
                        <p class="text-[11px] font-bold text-yellow-700">{{ $row['sakit'] }}</p>
                    </div>
                    <div class="bg-red-50 border border-red-100 rounded p-1 text-center">
                        <p class="text-[8px] text-red-600 uppercase mb-0.5">Alpa</p>
                        <p class="text-[11px] font-bold text-red-700">{{ $row['alpa'] }}</p>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-10">
                <i class="fas fa-file-alt text-4xl text-gray-200 mb-2"></i>
                <p class="text-xs text-gray-500">Gunakan filter untuk menampilkan data</p>
            </div>
            @endforelse
        </div>
        @else
        <div class="md:hidden text-center py-12 bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-filter text-2xl text-gray-300"></i>
            </div>
            <p class="text-sm font-semibold text-gray-600">Pilih filter terlebih dahulu</p>
            <p class="text-[10px] text-gray-400 mt-1">Tap icon filter di pojok kanan atas</p>
            <button @click="showFilterModal = true" class="mt-4 px-4 py-2 bg-[#187a41] text-white text-xs font-semibold rounded-lg">
                Tampilkan Filter
            </button>
        </div>
        @endif

        {{-- Mobile Filter Modal --}}
        <div x-show="showFilterModal" x-effect="document.body.style.overflow = showFilterModal ? 'hidden' : ''" class="md:hidden fixed inset-0 z-50 flex flex-col justify-end bg-black/50" x-transition.opacity style="display:none">
            <div @click.away="showFilterModal = false" class="bg-white w-full rounded-t-2xl shadow-xl flex flex-col max-h-[85vh]"
                 x-show="showFilterModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full">
                <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center flex-shrink-0">
                    <h3 class="text-sm font-bold text-gray-800">Filter Laporan</h3>
                    <button @click="showFilterModal = false" class="text-gray-400 bg-gray-50 rounded-full w-8 h-8 flex items-center justify-center"><i class="fas fa-times"></i></button>
                </div>
                <div class="p-5 overflow-visible">
                    <form method="GET" action="{{ route('ustadz.laporan.index') }}" class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-2">Cari Santri</label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / NIS" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-hijau-tua bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-2">Majelis</label>
                            <x-custom-select name="majelis_id" :options="$majelisOptions" :selected="request('majelis_id')" placeholder="Semua Majelis Saya" width="w-full" />
                        </div>


                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Dari Tanggal</label>
                                <input type="date" name="dari" value="{{ request('dari') }}" class="w-full border border-gray-200 rounded-lg px-2 py-2 text-[10px] focus:ring-2 focus:ring-hijau-tua">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Sampai Tanggal</label>
                                <input type="date" name="sampai" value="{{ request('sampai') }}" class="w-full border border-gray-200 rounded-lg px-2 py-2 text-[10px] focus:ring-2 focus:ring-hijau-tua">
                            </div>
                        </div>

                        <div class="flex gap-2 pt-2">
                            <button type="button" @click="window.location.href='{{ route('ustadz.laporan.index') }}'" class="flex-1 py-2.5 text-xs font-semibold text-gray-600 bg-gray-100 rounded-xl">Reset</button>
                            <button type="submit" @click="showFilterModal = false" class="flex-1 py-2.5 text-xs font-semibold text-white bg-[#187a41] rounded-xl">Terapkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

