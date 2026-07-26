<?php

$controller = file_get_contents('d:\PRESENSI MADIN\app\Http\Controllers\Admin\DokumentasiController.php');
if (!str_contains($controller, "if (\$request->filled('dari'))")) {
    $addon = <<<'EOD'
        if ($request->filled('tipe')) {
            $query->where('tipe_file', $request->tipe);
        }
        if ($request->filled('dari')) {
            $query->whereDate('tanggal', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('tanggal', '<=', $request->sampai);
        }
EOD;
    $controller = str_replace("if (\$request->filled('tipe')) {\n            \$query->where('tipe_file', \$request->tipe);\n        }", $addon, $controller);
    file_put_contents('d:\PRESENSI MADIN\app\Http\Controllers\Admin\DokumentasiController.php', $controller);
}

$admin_top = <<<'EOD'
        {{-- Filter Bar Desktop --}}
        <div class="hidden md:block bg-white/60 backdrop-blur-xl rounded-2xl border border-white/50 shadow-sm p-4 relative z-40">
            <form method="GET" action="{{ route('admin.dokumentasi.index') }}">
                <div class="grid grid-cols-[1.5fr_1.5fr_1.2fr_1fr_1fr_auto] gap-3 items-end">
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
                    <div class="min-w-0">
                        <label class="block text-xs font-semibold text-gray-600 mb-1 truncate">Dari</label>
                        <input type="date" name="dari" value="{{ request('dari') }}" class="w-full h-[38px] border border-gray-200 rounded-lg px-2 py-2 text-sm bg-white/80 focus:ring-2 focus:ring-hijau-tua focus:border-transparent">
                    </div>
                    <div class="min-w-0">
                        <label class="block text-xs font-semibold text-gray-600 mb-1 truncate">Sampai</label>
                        <input type="date" name="sampai" value="{{ request('sampai') }}" class="w-full h-[38px] border border-gray-200 rounded-lg px-2 py-2 text-sm bg-white/80 focus:ring-2 focus:ring-hijau-tua focus:border-transparent">
                    </div>
                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        <button type="submit" class="bg-hijau-tua text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-hijau-muda transition-all shadow-sm flex items-center justify-center gap-1.5 h-[38px]">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        @if(request()->anyFilled(['majelis_id','pengampu_id','jadwal_id','tipe','dari','sampai']))
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
// Update anyFilled in mobile view to include dari, sampai
$target_admin = str_replace("['majelis_id','pengampu_id','jadwal_id','tipe']", "['majelis_id','pengampu_id','jadwal_id','tipe','dari','sampai']", $target_admin);
file_put_contents('d:\PRESENSI MADIN\resources\views\admin\dokumentasi\index.blade.php', $target_admin);

echo "Done.";
