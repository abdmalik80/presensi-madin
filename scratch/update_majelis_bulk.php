<?php

// 1. Update Route
$routeFile = 'd:\PRESENSI MADIN\routes\web.php';
$routeContent = file_get_contents($routeFile);
if (strpos($routeContent, 'majelis/bulk-action') === false) {
    $routeContent = str_replace(
        "Route::resource('majelis', Admin\MajelisController::class);",
        "Route::post('majelis/bulk-action', [Admin\MajelisController::class, 'bulkAction'])->name('majelis.bulk-action');\n    Route::resource('majelis', Admin\MajelisController::class);",
        $routeContent
    );
    file_put_contents($routeFile, $routeContent);
}

// 2. Add bulkAction to MajelisController
$controllerFile = 'd:\PRESENSI MADIN\app\Http\Controllers\Admin\MajelisController.php';
$controllerContent = file_get_contents($controllerFile);
if (strpos($controllerContent, 'bulkAction') === false) {
    $bulkAction = <<<'EOD'
    public function bulkAction(Request $request)
    {
        $ids = $request->input('ids', []);
        $action = $request->input('action');

        if (empty($ids)) {
            return redirect()->route('admin.majelis.index')->with('error', 'Tidak ada majelis yang dipilih.');
        }

        $majelis = \App\Models\Majelis::whereIn('id', $ids)->get();
        $count   = $majelis->count();

        if ($action === 'delete') {
            foreach ($majelis as $m) {
                $m->delete();
            }
            return redirect()->route('admin.majelis.index')->with('success', "{$count} data majelis berhasil dihapus.");
        }

        return redirect()->route('admin.majelis.index')->with('error', 'Aksi tidak valid.');
    }
}
EOD;
    $controllerContent = preg_replace('/\}\s*$/', "\n$bulkAction", $controllerContent);
    file_put_contents($controllerFile, $controllerContent);
}

// 3. Update Majelis Index View
$viewFile = 'd:\PRESENSI MADIN\resources\views\admin\majelis\index.blade.php';
$viewContent = file_get_contents($viewFile);

if (strpos($viewContent, 'x-data="{ selectedIds') === false) {
    // Add Alpine logic wrapping the main layout
    $alpineWrapper = <<<'EOD'
<x-app-layout>
    <x-slot name="header">Data Majelis</x-slot>

    <div class="space-y-4 pb-20 md:pb-0" x-data="{
        showFilterModal: false,
        selectedIds: [],
        selectAllPages: false,
        isPageAllChecked: false,
        allIds: {{ json_encode($majelis->pluck('id')) }},
        pageIds: {{ json_encode($majelis->pluck('id')) }},
        
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
    }">
EOD;
    $viewContent = preg_replace('/<x-app-layout>\s*<x-slot name="header">Data Majelis<\/x-slot>\s*<div class="space-y-4 pb-20 md:pb-0" x-data="\{ showFilterModal: false \}">/', $alpineWrapper, $viewContent);
    
    // Header Checkbox
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

    // Body Row
    $bodyRow = <<<'EOD'
                    <tr class="group hover:bg-gray-100 transition-colors"
                        :class="selectedIds.includes({{ $m->id }}) ? 'bg-red-50/70' : ''">
                        <td class="px-4 py-3 w-10 transition-opacity duration-200"
                            :class="selectedIds.includes({{ $m->id }}) || selectedIds.length > 0 ? 'opacity-100' : 'opacity-0 group-hover:opacity-100 focus-within:opacity-100'">
                            <input type="checkbox"
                                   :checked="selectedIds.includes({{ $m->id }})"
                                   @change="
                                       if ($event.target.checked) {
                                           if (!selectedIds.includes({{ $m->id }})) selectedIds.push({{ $m->id }});
                                       } else {
                                           selectedIds = selectedIds.filter(id => id !== {{ $m->id }});
                                           this.selectAllPages = false;
                                       }
                                   "
                                   class="rounded border-gray-300 text-red-600 focus:ring-red-400 cursor-pointer w-4 h-4">
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $loop->iteration + ($majelis->currentPage()-1)*$majelis->perPage() }}</td>
EOD;
    $viewContent = preg_replace('/<tr class="hover:bg-gray-50\/60 transition-colors">\s*<td class="px-4 py-3 text-sm text-gray-600">\{\{ \$loop->iteration \+ \(\$majelis->currentPage\(\)-1\)\*\$majelis->perPage\(\) \}\}<\/td>/s', $bodyRow, $viewContent);

    // Banner Bulk Action
    $bulkActionBanner = <<<'EOD'
        {{-- Bulk Actions Banner --}}
        <div x-show="selectedIds.length > 0" x-transition.opacity
             class="mb-4 bg-red-50 border border-red-100 rounded-xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-red-700">
                    <i class="fas fa-check-square mr-1"></i>
                    <span x-text="selectedIds.length"></span>
                    <span x-text="selectAllPages ? ' majelis (semua data) dipilih' : ' majelis dipilih'"></span>
                </p>
                <p x-show="isPageAllChecked && !selectAllPages && allIds.length > pageIds.length"
                   class="text-xs text-red-600 mt-0.5">
                    Semua {{ $majelis->count() }} majelis di halaman ini dipilih.
                    <button type="button" @click="selectEveryPage()"
                            class="font-bold underline hover:text-red-800 ml-1">
                        Pilih semua {{ count($allIds) }} majelis?
                    </button>
                </p>
                <p x-show="selectAllPages" class="text-xs text-red-600 mt-0.5">
                    Semua {{ count($allIds) }} majelis dipilih.
                    <button type="button" @click="clearAll()" class="font-bold underline hover:text-red-800 ml-1">Batalkan pilihan</button>
                </p>
            </div>
            
            <form method="POST" action="{{ route('admin.majelis.bulk-action') }}" id="bulkActionFormMajelis">
                @csrf
                <input type="hidden" name="action" id="bulkActionTypeMajelis" value="">
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>

                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" @click="if(confirm('Yakin hapus ' + selectedIds.length + ' majelis? Tindakan ini tidak dapat dibatalkan!')) { document.getElementById('bulkActionTypeMajelis').value='delete'; document.getElementById('bulkActionFormMajelis').submit(); }"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-600 text-white text-xs font-semibold hover:bg-red-700 transition-colors shadow-sm">
                        <i class="fas fa-trash"></i> Hapus Terpilih
                    </button>
                </div>
            </form>
        </div>
EOD;
    $viewContent = preg_replace('/<div class="hidden md:block bg-white\/60 backdrop-blur-xl rounded-2xl shadow-\[0_8px_30px_rgb\(0,0,0,0\.04\)\] border border-white\/50 overflow-hidden">/', $bulkActionBanner . "\n\n        <div class=\"hidden md:block bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 overflow-hidden\">", $viewContent);

    // Mobile Card (I assume there's a mobile card for Majelis similar to Santri)
    // Wait, let's just make it a generic replace for mobile. But I didn't see the mobile card in Majelis in the truncated output.
    // Let me check if there's a mobile list in majelis. Yes, there must be.
    // Let me just replace the Desktop part first, and write the file. I will check mobile part afterwards.
    
    file_put_contents($viewFile, $viewContent);
}
echo "Done Majelis.";
