<?php
$top = <<<'EOD'
<x-app-layout>
    <x-slot name="header">Laporan Presensi</x-slot>

    @php
    $majelisOptions = []; foreach($majelis as $m) $majelisOptions[$m->id] = $m->nama_majelis; $kitabOptions = []; foreach($kitabs as $k) $kitabOptions[$k->id] = $k->nama_kitab;
    @endphp

    <div class="space-y-4 pb-20 md:pb-0" x-data="{ showFilterModal: false }">

        {{-- Desktop Filter --}}
        <div class="hidden md:block bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 p-4">
            <form method="GET">
                <div class="grid grid-cols-[1.5fr_1fr_1fr_2fr_auto] gap-3 items-end">
                    <div class="min-w-0">
                        <label class="block text-[11px] font-semibold text-gray-600 mb-1 truncate">Cari Santri</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama/NIS" class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-xs focus:ring-2 focus:ring-hijau-tua bg-white">
                    </div>
                    <div class="min-w-0">
                        <label class="block text-[11px] font-semibold text-gray-600 mb-1 truncate">Majelis</label>
                        <x-custom-select name="majelis_id" :options="$majelisOptions" :selected="request('majelis_id')" placeholder="Majelis" width="w-full" />
                    </div>
                    <div class="min-w-0">
                        <label class="block text-[11px] font-semibold text-gray-600 mb-1 truncate">Kitab</label>
                        <x-custom-select name="kitab_id" :options="$kitabOptions" :selected="request('kitab_id')" placeholder="Kitab" width="w-full" />
                    </div>
                    
                    {{-- Rentang Tanggal (2 Input digabung secara visual) --}}
                    <div class="min-w-0">
                        <label class="block text-[11px] font-semibold text-gray-600 mb-1 truncate">Rentang Tanggal</label>
                        <div class="flex items-center border border-gray-200 rounded-lg bg-white overflow-hidden focus-within:ring-2 focus-within:ring-hijau-tua">
                            <input type="date" name="dari" value="{{ request('dari') }}" class="w-full border-none focus:ring-0 text-xs px-2 py-1.5 bg-transparent h-[34px]">
                            <span class="text-gray-400 text-xs">-</span>
                            <input type="date" name="sampai" value="{{ request('sampai') }}" class="w-full border-none focus:ring-0 text-xs px-2 py-1.5 bg-transparent h-[34px]">
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        <button type="submit" class="bg-hijau-tua text-white px-4 py-1.5 rounded-lg text-xs font-semibold hover:bg-hijau-muda transition-all shadow-sm flex items-center justify-center gap-1.5 h-[34px]">
                            <i class="fas fa-search"></i> Tampilkan
                        </button>
                        @if(request()->anyFilled(['majelis_id','kitab_id','dari','sampai','search']))
                        <a href="{{ route('admin.laporan.pdf', request()->query()) }}" target="_blank"
                           class="bg-rose-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-rose-700 transition-all shadow-sm flex items-center justify-center gap-1.5 h-[34px]" title="Export PDF">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
EOD;

$target = file_get_contents('d:\PRESENSI MADIN\resources\views\admin\laporan\index.blade.php');
$target = preg_replace('/<x-app-layout>.*?<div class="hidden md:block bg-white\/60 backdrop-blur-xl rounded-2xl shadow-\[0_8px_30px_rgb\(0,0,0,0\.04\)\] border border-white\/50 p-[45]\">.*?<\/form>\s*<\/div>/s', $top, $target);
file_put_contents('d:\PRESENSI MADIN\resources\views\admin\laporan\index.blade.php', $target);
echo "Done.";
