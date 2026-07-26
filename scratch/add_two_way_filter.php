<?php

// 1. Update Controller
$controllerPath = 'd:\PRESENSI MADIN\app\Http\Controllers\Admin\DokumentasiController.php';
$controller = file_get_contents($controllerPath);

$mapCode = <<<'EOD'
        $majelisUstadzMap = [];
        $ustadzMajelisMap = [];
        foreach($jadwals as $j) {
            if ($j->ustadz_id) {
                $majelisUstadzMap[$j->majelis_id][] = $j->ustadz_id;
                $ustadzMajelisMap[$j->ustadz_id][] = $j->majelis_id;
            }
        }
        foreach($majelisUstadzMap as $mId => $uIds) {
            $majelisUstadzMap[$mId] = array_values(array_unique($uIds));
        }
        foreach($ustadzMajelisMap as $uId => $mIds) {
            $ustadzMajelisMap[$uId] = array_values(array_unique($mIds));
        }

        return view('admin.dokumentasi.index', compact('dokumentasis', 'jadwals', 'majeliss', 'ustadzs', 'tahunList', 'majelisUstadzMap', 'ustadzMajelisMap'));
EOD;

$controller = preg_replace("/\\\$majelisUstadzMap = \[\];.*?return view\('admin\.dokumentasi\.index'.*?\);/s", $mapCode, $controller);
file_put_contents($controllerPath, $controller);

// 2. Update View
$viewPath = 'd:\PRESENSI MADIN\resources\views\admin\dokumentasi\index.blade.php';
$view = file_get_contents($viewPath);

$view = str_replace(
    '<x-custom-select name="pengampu_id" :options="$ustadzOptions" :selected="request(\'pengampu_id\')" placeholder="Semua Ustadz" width="w-full" />',
    '<x-custom-select name="pengampu_id" :options="$ustadzOptions" :selected="request(\'pengampu_id\')" placeholder="Semua Ustadz" width="w-full" onchange="filterMajelis(val)" />',
    $view
);

$scriptCode = <<<'EOD'
    <script>
        document.addEventListener('alpine:init', () => {
            window.majelisUstadzMap = @json($majelisUstadzMap ?? []);
            window.ustadzMajelisMap = @json($ustadzMajelisMap ?? []);
            
            window.filterUstadz = function(majelisId) {
                setTimeout(() => {
                    const ustadzWrapper = document.querySelector('input[name="pengampu_id"]').closest('[x-data]');
                    if (!ustadzWrapper) return;
                    
                    const buttons = ustadzWrapper.querySelectorAll('button[data-val]');
                    let validUstadzIds = majelisId ? (window.majelisUstadzMap[majelisId] || []) : null;

                    buttons.forEach(btn => {
                        const val = btn.getAttribute('data-val');
                        if (val === "") {
                            btn.style.display = 'block';
                            return;
                        }
                        
                        if (validUstadzIds === null) {
                            btn.style.display = 'block';
                        } else {
                            if (validUstadzIds.includes(parseInt(val))) {
                                btn.style.display = 'block';
                            } else {
                                btn.style.display = 'none';
                            }
                        }
                    });

                    // Reset selected ustadz if they are no longer valid
                    if (window.Alpine) {
                        const alpineData = Alpine.$data(ustadzWrapper);
                        const currentUstadz = alpineData.value;
                        if (currentUstadz && validUstadzIds !== null && !validUstadzIds.includes(parseInt(currentUstadz))) {
                            alpineData.value = '';
                        }
                    }
                }, 50);
            };

            window.filterMajelis = function(ustadzId) {
                setTimeout(() => {
                    const majelisWrapper = document.querySelector('input[name="majelis_id"]').closest('[x-data]');
                    if (!majelisWrapper) return;
                    
                    const buttons = majelisWrapper.querySelectorAll('button[data-val]');
                    let validMajelisIds = ustadzId ? (window.ustadzMajelisMap[ustadzId] || []) : null;

                    buttons.forEach(btn => {
                        const val = btn.getAttribute('data-val');
                        if (val === "") {
                            btn.style.display = 'block';
                            return;
                        }
                        
                        if (validMajelisIds === null) {
                            btn.style.display = 'block';
                        } else {
                            if (validMajelisIds.includes(parseInt(val))) {
                                btn.style.display = 'block';
                            } else {
                                btn.style.display = 'none';
                            }
                        }
                    });

                    // Reset selected majelis if they are no longer valid
                    if (window.Alpine) {
                        const alpineData = Alpine.$data(majelisWrapper);
                        const currentMajelis = alpineData.value;
                        if (currentMajelis && validMajelisIds !== null && !validMajelisIds.includes(parseInt(currentMajelis))) {
                            alpineData.value = '';
                        }
                    }
                }, 50);
            };

            // Run once on load to filter initial state
            setTimeout(() => {
                const initialMajelis = document.querySelector('input[name="majelis_id"]').value;
                const initialUstadz = document.querySelector('input[name="pengampu_id"]').value;
                if(initialMajelis) window.filterUstadz(initialMajelis);
                if(initialUstadz) window.filterMajelis(initialUstadz);
            }, 100);
        });
    </script>
</x-app-layout>
EOD;

$view = preg_replace('/<script>.*?<\/x-app-layout>/s', $scriptCode, $view);
file_put_contents($viewPath, $view);

echo "Done.";
