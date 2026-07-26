<?php

// 1. Update Route
$routeFile = 'd:\PRESENSI MADIN\routes\web.php';
$routeContent = file_get_contents($routeFile);
if (strpos($routeContent, 'kitab/bulk-action') === false) {
    $routeContent = str_replace(
        "Route::resource('kitab', Admin\KitabController::class);",
        "Route::post('kitab/bulk-action', [Admin\KitabController::class, 'bulkAction'])->name('kitab.bulk-action');\n    Route::resource('kitab', Admin\KitabController::class);",
        $routeContent
    );
    file_put_contents($routeFile, $routeContent);
}

// 2. Add bulkAction to KitabController
$controllerFile = 'd:\PRESENSI MADIN\app\Http\Controllers\Admin\KitabController.php';
$controllerContent = file_get_contents($controllerFile);
if (strpos($controllerContent, 'bulkAction') === false) {
    $bulkAction = <<<'EOD'
    public function bulkAction(Request $request)
    {
        $ids = $request->input('ids', []);
        $action = $request->input('action');

        if (empty($ids)) {
            return redirect()->route('admin.kitab.index')->with('error', 'Tidak ada kitab yang dipilih.');
        }

        $kitabs = \App\Models\Kitab::whereIn('id', $ids)->get();
        $count  = $kitabs->count();

        if ($action === 'delete') {
            foreach ($kitabs as $k) {
                $k->delete();
            }
            return redirect()->route('admin.kitab.index')->with('success', "{$count} data kitab berhasil dihapus.");
        }

        return redirect()->route('admin.kitab.index')->with('error', 'Aksi tidak valid.');
    }
}
EOD;
    $controllerContent = preg_replace('/\}\s*$/', "\n$bulkAction", $controllerContent);
    file_put_contents($controllerFile, $controllerContent);
}

// 3. Update Kitab Index View
$viewFile = 'd:\PRESENSI MADIN\resources\views\admin\kitab\index.blade.php';
$viewContent = file_get_contents($viewFile);

if (strpos($viewContent, 'x-data="{') !== false && strpos($viewContent, 'selectedIds') === false) {
    // Modify existing x-data
    $oldXData = 'x-data="{ showFilterModal: false }"';
    $newXData = <<<'EOD'
x-data="{ 
        showFilterModal: false,
        selectedIds: [],
        selectAllPages: false,
        isPageAllChecked: false,
        allIds: {{ json_encode($kitabs->pluck('id')) }},
        pageIds: {{ json_encode($kitabs->pluck('id')) }},
        
        togglePageAll(checked) {
            this.isPageAllChecked = checked;
            if (checked) {
                this.pageIds.forEach(id => {
                    if (!this.selectedIds.includes(id)) this.selectedIds.push(id);
                });
            } else {
                this.selectedIds = this.selectedIds.filter(id => !this.pageIds.includes(id));
                this.selectAllPages = false;
            }
        },
        selectEveryPage() {
            this.selectedIds = [...this.allIds];
            this.selectAllPages = true;
            this.isPageAllChecked = true;
        },
        clearAll() {
            this.selectedIds = [];
            this.selectAllPages = false;
            this.isPageAllChecked = false;
        }
    }"
EOD;
    $viewContent = str_replace($oldXData, $newXData, $viewContent);
    
    $headerCheckbox = <<<'EOD'
                        <th class="px-4 py-3 text-left w-10">
                            <input type="checkbox"
                                   :checked="isPageAllChecked"
                                   @change="togglePageAll($event.target.checked)"
                                   class="rounded border-gray-300 text-hijau-muda focus:ring-hijau-muda cursor-pointer"
                                   title="Pilih semua di halaman ini">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">No</th>
EOD;
    $viewContent = preg_replace('/<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">No<\/th>/', $headerCheckbox, $viewContent);

    $bodyRow = <<<'EOD'
                    <tr class="group hover:bg-gray-100 transition-colors"
                        :class="selectedIds.includes({{ $kitab->id }}) ? 'bg-red-50/70' : ''">
                        <td class="px-4 py-3 w-10 transition-opacity duration-200"
                            :class="selectedIds.includes({{ $kitab->id }}) || selectedIds.length > 0 ? 'opacity-100' : 'opacity-0 group-hover:opacity-100 focus-within:opacity-100'">
                            <input type="checkbox"
                                   :checked="selectedIds.includes({{ $kitab->id }})"
                                   @change="
                                       if ($event.target.checked) {
                                           if (!selectedIds.includes({{ $kitab->id }})) selectedIds.push({{ $kitab->id }});
                                       } else {
                                           selectedIds = selectedIds.filter(id => id !== {{ $kitab->id }});
                                           this.selectAllPages = false;
                                       }
                                   "
                                   class="rounded border-gray-300 text-red-600 focus:ring-red-400 cursor-pointer w-4 h-4">
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $loop->iteration + ($kitabs->currentPage()-1)*$kitabs->perPage() }}</td>
EOD;
    $viewContent = preg_replace('/<tr class="hover:bg-gray-50\/60 transition-colors">\s*<td class="px-4 py-3 text-sm text-gray-600">\{\{ \$loop->iteration \+ \(\$kitabs->currentPage\(\)-1\)\*\$kitabs->perPage\(\) \}\}<\/td>/s', $bodyRow, $viewContent);

    $bulkActionBanner = <<<'EOD'
        {{-- Bulk Actions Banner --}}
        <div x-show="selectedIds.length > 0" x-transition.opacity
             class="mb-4 bg-red-50 border border-red-100 rounded-xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-red-700">
                    <i class="fas fa-check-square mr-1"></i>
                    <span x-text="selectedIds.length"></span>
                    <span x-text="selectAllPages ? ' kitab (semua data) dipilih' : ' kitab dipilih'"></span>
                </p>
                <p x-show="isPageAllChecked && !selectAllPages && allIds.length > pageIds.length"
                   class="text-xs text-red-600 mt-0.5">
                    Semua {{ $kitabs->count() }} kitab di halaman ini dipilih.
                    <button type="button" @click="selectEveryPage()"
                            class="font-bold underline hover:text-red-800 ml-1">
                        Pilih semua {{ count($allIds) }} kitab?
                    </button>
                </p>
                <p x-show="selectAllPages" class="text-xs text-red-600 mt-0.5">
                    Semua {{ count($allIds) }} kitab dipilih.
                    <button type="button" @click="clearAll()" class="font-bold underline hover:text-red-800 ml-1">Batalkan pilihan</button>
                </p>
            </div>
            
            <form method="POST" action="{{ route('admin.kitab.bulk-action') }}" id="bulkActionFormKitab">
                @csrf
                <input type="hidden" name="action" id="bulkActionTypeKitab" value="">
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>

                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" @click="if(confirm('Yakin hapus ' + selectedIds.length + ' kitab? Tindakan ini tidak dapat dibatalkan!')) { document.getElementById('bulkActionTypeKitab').value='delete'; document.getElementById('bulkActionFormKitab').submit(); }"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-600 text-white text-xs font-semibold hover:bg-red-700 transition-colors shadow-sm">
                        <i class="fas fa-trash"></i> Hapus Terpilih
                    </button>
                </div>
            </form>
        </div>
EOD;
    $viewContent = preg_replace('/<div class="hidden md:block bg-white\/60 backdrop-blur-xl rounded-2xl shadow-\[0_8px_30px_rgb\(0,0,0,0\.04\)\] border border-white\/50 overflow-hidden">/', $bulkActionBanner . "\n\n        <div class=\"hidden md:block bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 overflow-hidden\">", $viewContent);

    // Mobile
    $oldMobileCard = <<<'EOD'
            <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 border-b border-gray-100 last:border-0 hover:bg-gray-50/50 transition-colors gap-3">
EOD;
    $newMobileCard = <<<'EOD'
            <div x-data="{ timer: null }"
                 @touchstart="timer = setTimeout(() => { if (!selectedIds.includes({{ $kitab->id }})) { selectedIds.push({{ $kitab->id }}); if (navigator.vibrate) navigator.vibrate(50); } }, 500)"
                 @touchend="clearTimeout(timer)"
                 @touchmove="clearTimeout(timer)"
                 :class="selectedIds.includes({{ $kitab->id }}) ? 'bg-red-50/50' : 'bg-white'"
                 class="flex flex-col sm:flex-row sm:items-center justify-between p-4 border-b border-gray-100 last:border-0 hover:bg-gray-50/50 transition-colors gap-3 relative">
                 
                 <div class="absolute top-2 right-2 z-10"
                       x-show="selectedIds.length > 0 || isPageAllChecked">
                      <input type="checkbox"
                             :checked="selectedIds.includes({{ $kitab->id }})"
                             @change="
                                 if ($event.target.checked) {
                                     if (!selectedIds.includes({{ $kitab->id }})) selectedIds.push({{ $kitab->id }});
                                 } else {
                                     selectedIds = selectedIds.filter(id => id !== {{ $kitab->id }});
                                     selectAllPages = false;
                                 }
                             "
                             class="rounded border-gray-300 text-red-600 focus:ring-red-400 cursor-pointer w-4 h-4">
                  </div>
EOD;
    $viewContent = str_replace($oldMobileCard, $newMobileCard, $viewContent);
    
    file_put_contents($viewFile, $viewContent);
}
echo "Done Kitab.";
