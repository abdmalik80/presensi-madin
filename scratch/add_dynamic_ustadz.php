<?php

// 1. Update Controller
$controllerPath = 'd:\PRESENSI MADIN\app\Http\Controllers\Admin\DokumentasiController.php';
$controller = file_get_contents($controllerPath);

$mapCode = <<<'EOD'
        $majelisUstadzMap = [];
        foreach($jadwals as $j) {
            if ($j->ustadz_id) {
                $majelisUstadzMap[$j->majelis_id][] = $j->ustadz_id;
            }
        }
        foreach($majelisUstadzMap as $mId => $uIds) {
            $majelisUstadzMap[$mId] = array_values(array_unique($uIds));
        }

        return view('admin.dokumentasi.index', compact('dokumentasis', 'jadwals', 'majeliss', 'ustadzs', 'tahunList', 'majelisUstadzMap'));
EOD;

$controller = preg_replace("/return view\('admin\.dokumentasi\.index', compact\('dokumentasis', 'jadwals', 'majeliss', 'ustadzs', 'tahunList'\)\);/s", $mapCode, $controller);
file_put_contents($controllerPath, $controller);

// 2. Update View
$viewPath = 'd:\PRESENSI MADIN\resources\views\admin\dokumentasi\index.blade.php';
$view = file_get_contents($viewPath);

$view = str_replace(
    '<x-custom-select name="majelis_id" :options="$majelisOptions" :selected="request(\'majelis_id\')" placeholder="Semua Majelis" width="w-full" />',
    '<x-custom-select name="majelis_id" :options="$majelisOptions" :selected="request(\'majelis_id\')" placeholder="Semua Majelis" width="w-full" onchange="filterUstadz(val)" />',
    $view
);

$scriptCode = <<<'EOD'
    <script>
        document.addEventListener('alpine:init', () => {
            window.majelisUstadzMap = @json($majelisUstadzMap ?? []);
            
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

                    // Reset selected ustadz if they are no longer valid for the selected majelis
                    if (window.Alpine) {
                        const alpineData = Alpine.$data(ustadzWrapper);
                        const currentUstadz = alpineData.value;
                        if (currentUstadz && validUstadzIds !== null && !validUstadzIds.includes(parseInt(currentUstadz))) {
                            alpineData.value = '';
                        }
                    }
                }, 50);
            };

            // Run once on load to filter initial state
            setTimeout(() => {
                const initialMajelis = document.querySelector('input[name="majelis_id"]').value;
                if(initialMajelis) window.filterUstadz(initialMajelis);
            }, 100);
        });
    </script>
</x-app-layout>
EOD;

$view = str_replace('</x-app-layout>', $scriptCode, $view);
file_put_contents($viewPath, $view);

echo "Done.";
