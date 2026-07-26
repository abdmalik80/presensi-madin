<?php

$viewPath = 'd:\PRESENSI MADIN\resources\views\admin\dokumentasi\index.blade.php';
$view = file_get_contents($viewPath);

// 1. Update Mobile Modal Form to match Desktop
$mobileForm = <<<'EOD'
                      <form method="GET" action="{{ route('admin.dokumentasi.index') }}" class="space-y-4">
                          <div>
                              <label class="block text-xs font-semibold text-gray-700 mb-2">Majelis</label>
                              <x-custom-select name="majelis_id" :options="$majelisOptions" :selected="request('majelis_id')" placeholder="Semua Majelis" width="w-full" onchange="filterUstadz(val)" />
                          </div>
                          <div>
                              <label class="block text-xs font-semibold text-gray-700 mb-2">Ustadz</label>
                              <x-custom-select name="pengampu_id" :options="$ustadzOptions" :selected="request('pengampu_id')" placeholder="Semua Ustadz" width="w-full" onchange="filterMajelis(val)" />
                          </div>
                          <div>
                              <label class="block text-xs font-semibold text-gray-700 mb-2">Tipe Berkas</label>
                              <x-custom-select name="tipe" :options="['materi'=>'Materi','kegiatan'=>'Kegiatan']" :selected="request('tipe')" placeholder="Semua Tipe" width="w-full" />
                          </div>
                          <div class="flex gap-3">
                              <div class="flex-1">
                                  <label class="block text-xs font-semibold text-gray-700 mb-2">Bulan</label>
                                  <x-custom-select name="bulan" :options="$bulanOptions" :selected="request('bulan')" placeholder="Semua Bulan" width="w-full" />
                              </div>
                              <div class="flex-1">
                                  <label class="block text-xs font-semibold text-gray-700 mb-2">Tahun</label>
                                  <x-custom-select name="tahun" :options="$tahunOptions" :selected="request('tahun')" placeholder="Semua Tahun" width="w-full" />
                              </div>
                          </div>
                          <div class="flex gap-2 pt-2">
                              <button type="button" @click="window.location.href='{{ route('admin.dokumentasi.index') }}'" class="flex-1 py-2.5 text-xs font-semibold text-gray-600 bg-gray-100 rounded-xl">Reset</button>
                              <button type="submit" @click="showFilterModal = false" class="flex-1 py-2.5 text-xs font-semibold text-white bg-[#187a41] rounded-xl">Terapkan</button>
                          </div>
                      </form>
EOD;

$view = preg_replace('/<form method="GET" action="\{\{ route\(\'admin\.dokumentasi\.index\'\) \}\}" class="space-y-4">.*?<\/form>/s', $mobileForm, $view);

// 2. Fix JS logic to target all wrappers (Desktop & Mobile)
$scriptCode = <<<'EOD'
    <script>
        document.addEventListener('alpine:init', () => {
            window.majelisUstadzMap = @json($majelisUstadzMap ?? []);
            window.ustadzMajelisMap = @json($ustadzMajelisMap ?? []);
            
            window.filterUstadz = function(majelisId) {
                setTimeout(() => {
                    const ustadzInputs = document.querySelectorAll('input[name="pengampu_id"]');
                    ustadzInputs.forEach(input => {
                        const ustadzWrapper = input.closest('[x-data]');
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
                    });
                }, 50);
            };

            window.filterMajelis = function(ustadzId) {
                setTimeout(() => {
                    const majelisInputs = document.querySelectorAll('input[name="majelis_id"]');
                    majelisInputs.forEach(input => {
                        const majelisWrapper = input.closest('[x-data]');
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
                    });
                }, 50);
            };

            // Run once on load to filter initial state
            setTimeout(() => {
                const initialMajelisInput = document.querySelector('input[name="majelis_id"]');
                const initialUstadzInput = document.querySelector('input[name="pengampu_id"]');
                if(initialMajelisInput && initialMajelisInput.value) window.filterUstadz(initialMajelisInput.value);
                if(initialUstadzInput && initialUstadzInput.value) window.filterMajelis(initialUstadzInput.value);
            }, 100);
        });
    </script>
</x-app-layout>
EOD;

$view = preg_replace('/<script>.*?<\/x-app-layout>/s', $scriptCode, $view);

file_put_contents($viewPath, $view);

echo "Done.";
