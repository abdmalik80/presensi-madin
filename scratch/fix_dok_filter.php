<?php

// 1. UPDATE ADMIN DOKUMENTASI
$admin_top = <<<'EOD'
        {{-- Filter Bar Desktop --}}
        <div class="hidden md:block bg-white/60 backdrop-blur-xl rounded-2xl border border-white/50 shadow-sm p-4 relative z-40">
            <form method="GET" action="{{ route('admin.dokumentasi.index') }}">
                <div class="grid grid-cols-[1.5fr_1.5fr_1.2fr_auto] gap-3 items-end">
                    <div class="min-w-0">
                        <label class="block text-xs font-semibold text-gray-600 mb-1 truncate">Majelis</label>
                        <x-custom-select name="majelis_id" :options="$majelisOptions" :selected="request('majelis_id')" placeholder="Semua Majelis" width="w-full" />
                    </div>
                    <div class="min-w-0">
                        <label class="block text-xs font-semibold text-gray-600 mb-1 truncate">Ustadz</label>
                        <x-custom-select name="pengampu_id" :options="$ustadzOptions" :selected="request('pengampu_id')" placeholder="Semua Ustadz" width="w-full" />
                    </div>
                    <div class="min-w-0">
                        <label class="block text-xs font-semibold text-gray-600 mb-1 truncate">Tipe Berkas</label>
                        <x-custom-select name="tipe" :options="['materi'=>'Materi','kegiatan'=>'Kegiatan']" :selected="request('tipe')" placeholder="Semua Tipe" width="w-full" />
                    </div>
                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        <button type="submit" class="bg-hijau-tua text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-hijau-muda transition-all shadow-sm flex items-center justify-center gap-1.5 h-[38px]">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        @if(request()->hasAny(['majelis_id','pengampu_id','jadwal_id','tipe']))
                        <a href="{{ route('admin.dokumentasi.index') }}" class="bg-white border border-gray-200 text-gray-600 px-3 py-2 rounded-lg text-sm font-semibold hover:bg-gray-50 transition-all shadow-sm flex items-center justify-center gap-1.5 h-[38px]" title="Reset">
                            <i class="fas fa-times"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
EOD;

$target_admin = file_get_contents('d:\PRESENSI MADIN\resources\views\admin\dokumentasi\index.blade.php');
$target_admin = preg_replace('/\{\{-- Filter Bar Desktop --\}\}.*?<\/form>\s*<\/div>/s', $admin_top, $target_admin);
file_put_contents('d:\PRESENSI MADIN\resources\views\admin\dokumentasi\index.blade.php', $target_admin);


// 2. UPDATE USTADZ DOKUMENTASI
$ustadz_top = <<<'EOD'
        {{-- Desktop Filter Bar --}}
        <div class="hidden md:block bg-white/60 backdrop-blur-xl rounded-2xl border border-white/50 shadow-sm p-4 relative z-40">
            <form method="GET" action="{{ route('ustadz.dokumentasi.index') }}">
                <div class="grid grid-cols-[2fr_1fr_1fr_1fr_auto] gap-3 items-end">
                    <div class="min-w-0">
                        <label class="block text-xs font-semibold text-gray-600 mb-1 truncate">Filter Majelis</label>
                        <x-custom-select name="jadwal_id" :options="$jadwalOptions" :selected="request('jadwal_id')" placeholder="Semua Majelis" width="w-full" />
                    </div>
                    <div class="min-w-0">
                        <label class="block text-xs font-semibold text-gray-600 mb-1 truncate">Tipe Berkas</label>
                        <x-custom-select name="tipe" :options="$tipeOptions" :selected="request('tipe')" placeholder="Semua Tipe" width="w-full" />
                    </div>
                    <div class="min-w-0">
                        <label class="block text-xs font-semibold text-gray-600 mb-1 truncate">Bulan</label>
                        <x-custom-select name="bulan" :options="$bulanOptions" :selected="request('bulan')" placeholder="Semua Bulan" width="w-full" />
                    </div>
                    <div class="min-w-0">
                        <label class="block text-xs font-semibold text-gray-600 mb-1 truncate">Tahun</label>
                        <x-custom-select name="tahun" :options="$tahunOptions" :selected="request('tahun')" placeholder="Semua Tahun" width="w-full" />
                    </div>
                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        <button type="submit" class="bg-hijau-tua text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-hijau-muda transition-all shadow-sm flex items-center justify-center gap-1.5 h-[38px]">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        @if(request()->hasAny(['jadwal_id','tipe','bulan','tahun']))
                        <a href="{{ route('ustadz.dokumentasi.index') }}" class="bg-white border border-gray-200 text-gray-600 px-3 py-2 rounded-lg text-sm font-semibold hover:bg-gray-50 transition-all shadow-sm flex items-center justify-center gap-1.5 h-[38px]" title="Reset">
                            <i class="fas fa-times"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
EOD;

$target_ustadz = file_get_contents('d:\PRESENSI MADIN\resources\views\ustadz\dokumentasi\index.blade.php');
// Notice ustadz uses "Desktop Filter Bar" instead of "Filter Bar Desktop"
$target_ustadz = preg_replace('/\{\{-- Desktop Filter Bar --\}\}.*?<\/form>\s*<\/div>/s', $ustadz_top, $target_ustadz);
file_put_contents('d:\PRESENSI MADIN\resources\views\ustadz\dokumentasi\index.blade.php', $target_ustadz);

echo "Done.";
