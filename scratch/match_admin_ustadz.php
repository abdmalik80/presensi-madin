<?php

// 1. Controller Update
$controller = file_get_contents('d:\PRESENSI MADIN\app\Http\Controllers\Admin\DokumentasiController.php');

// Remove dari/sampai and add bulan/tahun
$controller = preg_replace("/if \(\\\$request->filled\('dari'\)\) \{.*?if \(\\\$request->filled\('sampai'\)\) \{.*?\}/s", "", $controller);

$addon = <<<'EOD'
        if ($request->filled('tipe')) {
            $query->where('tipe_file', $request->tipe);
        }
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }
EOD;
$controller = str_replace("if (\$request->filled('tipe')) {\n            \$query->where('tipe_file', \$request->tipe);\n        }", $addon, $controller);

// Add tahunList
$tahunListCode = <<<'EOD'
        $dokumentasis = $query->latest()->paginate(12)->withQueryString();

        $tahunList = Dokumentasi::selectRaw('YEAR(tanggal) as tahun')
                        ->distinct()
                        ->orderByDesc('tahun')
                        ->pluck('tahun');

        return view('admin.dokumentasi.index', compact('dokumentasis', 'jadwals', 'majeliss', 'ustadzs', 'tahunList'));
EOD;
$controller = preg_replace("/\\\$dokumentasis = \\\$query->latest\(\)->paginate\(12\)->withQueryString\(\);\s+return view\('admin\.dokumentasi\.index', compact\('dokumentasis', 'jadwals', 'majeliss', 'ustadzs'\)\);/s", $tahunListCode, $controller);
file_put_contents('d:\PRESENSI MADIN\app\Http\Controllers\Admin\DokumentasiController.php', $controller);

// 2. View Update
$view = file_get_contents('d:\PRESENSI MADIN\resources\views\admin\dokumentasi\index.blade.php');

// Insert bulanOptions and tahunOptions at the top PHP block
if (!str_contains($view, '$bulanOptions = [];')) {
    $phpBlock = <<<'EOD'
    @php
    $majelisOptions = [];
    foreach($majeliss as $m) $majelisOptions[$m->id] = $m->nama_majelis;
    $ustadzOptions = [];
    foreach($ustadzs as $u) $ustadzOptions[$u->id] = $u->nama;
    $bulanOptions = [];
    foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bln) {
        $bulanOptions[$i+1] = $bln;
    }
    $tahunOptions = [];
    foreach($tahunList ?? [] as $th) {
        $tahunOptions[$th] = $th;
    }
    @endphp
EOD;
    $view = preg_replace('/@php.*?@endphp/s', $phpBlock, $view, 1);
}


$admin_top = <<<'EOD'
        {{-- Filter Bar Desktop --}}
        <div class="hidden md:block bg-white/60 backdrop-blur-xl rounded-2xl border border-white/50 shadow-sm p-4 relative z-40">
            <form method="GET" action="{{ route('admin.dokumentasi.index') }}">
                <div class="grid grid-cols-[1.5fr_1.5fr_1fr_1fr_1fr_auto] gap-3 items-end">
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
                        @if(request()->anyFilled(['majelis_id','pengampu_id','tipe','bulan','tahun']))
                        <a href="{{ route('admin.dokumentasi.index') }}" class="bg-white border border-gray-200 text-gray-600 px-3 py-2 rounded-lg text-sm font-semibold hover:bg-gray-50 transition-all shadow-sm flex items-center justify-center gap-1.5 h-[38px]" title="Reset">
                            <i class="fas fa-times"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
EOD;

$view = preg_replace('/\{\{-- Filter Bar Desktop --\}\}.*?<\/form>\s*<\/div>/s', $admin_top, $view);
$view = str_replace("['majelis_id','pengampu_id','jadwal_id','tipe','dari','sampai']", "['majelis_id','pengampu_id','tipe','bulan','tahun']", $view);
$view = str_replace("['majelis_id','pengampu_id','jadwal_id','tipe']", "['majelis_id','pengampu_id','tipe','bulan','tahun']", $view);
file_put_contents('d:\PRESENSI MADIN\resources\views\admin\dokumentasi\index.blade.php', $view);

echo "Done.";
