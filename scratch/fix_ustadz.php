<?php
$top = <<<'EOD'
        {{-- Desktop Filter Form --}}
        <div class="hidden md:block bg-white/70 backdrop-blur-xl rounded-2xl border border-white/60 shadow-sm p-4 relative z-40">
            <form method="GET">
                <div class="grid grid-cols-[1.5fr_1.2fr_1fr_1fr_auto] gap-3 items-end">
                    <div class="min-w-0">
                        <label class="block text-xs font-semibold text-gray-600 mb-1 truncate">Cari Santri</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / NIS"
                               class="w-full h-[38px] border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white/80 focus:ring-2 focus:ring-hijau-tua focus:border-transparent">
                    </div>
                    <div class="min-w-0">
                        <label class="block text-xs font-semibold text-gray-600 mb-1 truncate">Majelis</label>
                        <x-custom-select name="majelis_id" :options="$majelisOptions" :selected="request('majelis_id')" placeholder="Semua Majelis Saya" width="w-full" />
                    </div>
                    <div class="min-w-0">
                        <label class="block text-xs font-semibold text-gray-600 mb-1 truncate">Dari</label>
                        <input type="date" name="dari" value="{{ request('dari') }}"
                               class="w-full h-[38px] border border-gray-200 rounded-lg px-2 py-2 text-sm bg-white/80 focus:ring-2 focus:ring-hijau-tua focus:border-transparent">
                    </div>
                    <div class="min-w-0">
                        <label class="block text-xs font-semibold text-gray-600 mb-1 truncate">Sampai</label>
                        <input type="date" name="sampai" value="{{ request('sampai') }}"
                               class="w-full h-[38px] border border-gray-200 rounded-lg px-2 py-2 text-sm bg-white/80 focus:ring-2 focus:ring-hijau-tua focus:border-transparent">
                    </div>
                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        <button type="submit"
                                class="bg-hijau-tua text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-hijau-muda transition-all shadow-sm flex items-center justify-center gap-1.5 h-[38px]">
                            <i class="fas fa-search"></i> Tampilkan
                        </button>
                        @if(request()->anyFilled(['majelis_id','dari','sampai','search']))
                        <a href="{{ route('ustadz.laporan.pdf', request()->query()) }}" target="_blank"
                           class="bg-rose-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-rose-700 transition-all shadow-sm flex items-center justify-center gap-1.5 h-[38px]" title="Export PDF">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
EOD;

$target = file_get_contents('d:\PRESENSI MADIN\resources\views\ustadz\laporan\index.blade.php');
$target = preg_replace('/\{\{-- Desktop Filter Form --\}\}.*?<\/form>\s*<\/div>/s', $top, $target);
file_put_contents('d:\PRESENSI MADIN\resources\views\ustadz\laporan\index.blade.php', $target);
echo "Done.";
