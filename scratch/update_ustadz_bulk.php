<?php

// 1. Update Route
$routeFile = 'd:\PRESENSI MADIN\routes\web.php';
$routeContent = file_get_contents($routeFile);
if (strpos($routeContent, 'ustadz/bulk-action') === false) {
    $routeContent = str_replace(
        "Route::resource('ustadz', Admin\UstadzController::class);",
        "Route::post('ustadz/bulk-action', [Admin\UstadzController::class, 'bulkAction'])->name('ustadz.bulk-action');\n    Route::resource('ustadz', Admin\UstadzController::class);",
        $routeContent
    );
    file_put_contents($routeFile, $routeContent);
}

// 2. Add bulkAction to UstadzController
$controllerFile = 'd:\PRESENSI MADIN\app\Http\Controllers\Admin\UstadzController.php';
$controllerContent = file_get_contents($controllerFile);
if (strpos($controllerContent, 'bulkAction') === false) {
    $bulkAction = <<<'EOD'
    public function bulkAction(Request $request)
    {
        $ids = $request->input('ids', []);
        $action = $request->input('action');

        if (empty($ids)) {
            return redirect()->route('admin.ustadz.index')->with('error', 'Tidak ada ustadz yang dipilih.');
        }

        $ustadzs = Ustadz::with('user')->whereIn('id', $ids)->get();
        $count   = $ustadzs->count();

        if ($action === 'delete') {
            foreach ($ustadzs as $ustadz) {
                if ($ustadz->user?->foto_profil) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($ustadz->user->foto_profil);
                }
                $ustadz->user?->delete();
            }
            return redirect()->route('admin.ustadz.index')->with('success', "{$count} data ustadz berhasil dihapus.");
        } elseif ($action === 'active') {
            foreach ($ustadzs as $ustadz) {
                if ($ustadz->user) {
                    $ustadz->user->update(['status_aktif' => true]);
                }
            }
            return redirect()->route('admin.ustadz.index')->with('success', "{$count} ustadz berhasil diaktifkan.");
        } elseif ($action === 'inactive') {
            foreach ($ustadzs as $ustadz) {
                if ($ustadz->user) {
                    $ustadz->user->update(['status_aktif' => false]);
                }
            }
            return redirect()->route('admin.ustadz.index')->with('success', "{$count} ustadz berhasil dinonaktifkan.");
        }

        return redirect()->route('admin.ustadz.index')->with('error', 'Aksi tidak valid.');
    }
}
EOD;
    $controllerContent = preg_replace('/\}\s*$/', "\n$bulkAction", $controllerContent);
    file_put_contents($controllerFile, $controllerContent);
}

// 3. Update Ustadz Index View
$viewFile = 'd:\PRESENSI MADIN\resources\views\admin\ustadz\index.blade.php';
$viewContent = file_get_contents($viewFile);

if (strpos($viewContent, 'x-data="{') === false) {
    $alpineWrapper = <<<'EOD'
<x-app-layout>
    <div x-data="{
        selectedIds: [],
        selectAllPages: false,
        isPageAllChecked: false,
        allIds: {{ json_encode($ustadzs->pluck('id')) }},
        pageIds: {{ json_encode($ustadzs->pluck('id')) }},
        
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
    }" class="px-2 sm:px-6 lg:px-8 py-4 sm:py-8 w-full max-w-7xl mx-auto space-y-4 sm:space-y-6">
EOD;
    $viewContent = preg_replace('/<x-app-layout>\s*<div class="px-2 sm:px-6 lg:px-8 py-4 sm:py-8 w-full max-w-7xl mx-auto space-y-4 sm:space-y-6">/', $alpineWrapper, $viewContent);
    
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
                        :class="selectedIds.includes({{ $ustadz->id }}) ? 'bg-red-50/70' : ''">
                        <td class="px-4 py-3 w-10 transition-opacity duration-200"
                            :class="selectedIds.includes({{ $ustadz->id }}) || selectedIds.length > 0 ? 'opacity-100' : 'opacity-0 group-hover:opacity-100 focus-within:opacity-100'">
                            <input type="checkbox"
                                   :checked="selectedIds.includes({{ $ustadz->id }})"
                                   @change="
                                       if ($event.target.checked) {
                                           if (!selectedIds.includes({{ $ustadz->id }})) selectedIds.push({{ $ustadz->id }});
                                       } else {
                                           selectedIds = selectedIds.filter(id => id !== {{ $ustadz->id }});
                                           this.selectAllPages = false;
                                       }
                                   "
                                   class="rounded border-gray-300 text-red-600 focus:ring-red-400 cursor-pointer w-4 h-4">
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $loop->iteration + ($ustadzs->currentPage()-1)*$ustadzs->perPage() }}</td>
EOD;
    $viewContent = preg_replace('/<tr class="hover:bg-gray-50\/60 transition-colors">\s*<td class="px-4 py-3 text-sm text-gray-600">\{\{ \$loop->iteration \+ \(\$ustadzs->currentPage\(\)-1\)\*\$ustadzs->perPage\(\) \}\}<\/td>/s', $bodyRow, $viewContent);

    $bulkActionBanner = <<<'EOD'
        {{-- Bulk Actions Banner --}}
        <div x-show="selectedIds.length > 0" x-transition.opacity
             class="mb-4 bg-red-50 border border-red-100 rounded-xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-red-700">
                    <i class="fas fa-check-square mr-1"></i>
                    <span x-text="selectedIds.length"></span>
                    <span x-text="selectAllPages ? ' ustadz (semua data) dipilih' : ' ustadz dipilih'"></span>
                </p>
                <p x-show="isPageAllChecked && !selectAllPages && allIds.length > pageIds.length"
                   class="text-xs text-red-600 mt-0.5">
                    Semua {{ $ustadzs->count() }} ustadz di halaman ini dipilih.
                    <button type="button" @click="selectEveryPage()"
                            class="font-bold underline hover:text-red-800 ml-1">
                        Pilih semua {{ count($allIds) }} ustadz?
                    </button>
                </p>
                <p x-show="selectAllPages" class="text-xs text-red-600 mt-0.5">
                    Semua {{ count($allIds) }} ustadz dipilih.
                    <button type="button" @click="clearAll()" class="font-bold underline hover:text-red-800 ml-1">Batalkan pilihan</button>
                </p>
            </div>
            
            <form method="POST" action="{{ route('admin.ustadz.bulk-action') }}" id="bulkActionFormUstadz">
                @csrf
                <input type="hidden" name="action" id="bulkActionTypeUstadz" value="">
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>

                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" @click="if(confirm('Yakin set aktif ' + selectedIds.length + ' ustadz?')) { document.getElementById('bulkActionTypeUstadz').value='active'; document.getElementById('bulkActionFormUstadz').submit(); }"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-600 text-white text-xs font-semibold hover:bg-green-700 transition-colors shadow-sm">
                        <i class="fas fa-check-circle"></i> Set Aktif
                    </button>
                    <button type="button" @click="if(confirm('Yakin set non-aktif ' + selectedIds.length + ' ustadz?')) { document.getElementById('bulkActionTypeUstadz').value='inactive'; document.getElementById('bulkActionFormUstadz').submit(); }"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-orange-500 text-white text-xs font-semibold hover:bg-orange-600 transition-colors shadow-sm">
                        <i class="fas fa-times-circle"></i> Set Non-Aktif
                    </button>
                    <div class="hidden sm:block w-px h-5 bg-red-200 mx-1"></div>
                    <button type="button" @click="if(confirm('Yakin hapus ' + selectedIds.length + ' ustadz? Tindakan ini tidak dapat dibatalkan!')) { document.getElementById('bulkActionTypeUstadz').value='delete'; document.getElementById('bulkActionFormUstadz').submit(); }"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-600 text-white text-xs font-semibold hover:bg-red-700 transition-colors shadow-sm">
                        <i class="fas fa-trash"></i> Hapus Terpilih
                    </button>
                </div>
            </form>
        </div>
EOD;
    $viewContent = preg_replace('/<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hidden md:block">/', $bulkActionBanner . "\n\n        <div class=\"bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hidden md:block\">", $viewContent);

    $oldMobileCard = <<<'EOD'
            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors relative">
EOD;
    $newMobileCard = <<<'EOD'
            <div x-data="{ timer: null }"
                 @touchstart="timer = setTimeout(() => { if (!selectedIds.includes({{ $ustadz->id }})) { selectedIds.push({{ $ustadz->id }}); if (navigator.vibrate) navigator.vibrate(50); } }, 500)"
                 @touchend="clearTimeout(timer)"
                 @touchmove="clearTimeout(timer)"
                 :class="selectedIds.includes({{ $ustadz->id }}) ? 'bg-red-50/50' : 'bg-white'"
                 class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors relative px-2 rounded-lg mb-1">
                 
                 <div class="absolute top-2 right-2 z-10"
                       x-show="selectedIds.length > 0 || isPageAllChecked">
                      <input type="checkbox"
                             :checked="selectedIds.includes({{ $ustadz->id }})"
                             @change="
                                 if ($event.target.checked) {
                                     if (!selectedIds.includes({{ $ustadz->id }})) selectedIds.push({{ $ustadz->id }});
                                 } else {
                                     selectedIds = selectedIds.filter(id => id !== {{ $ustadz->id }});
                                     selectAllPages = false;
                                 }
                             "
                             class="rounded border-gray-300 text-red-600 focus:ring-red-400 cursor-pointer w-4 h-4">
                  </div>
EOD;
    $viewContent = str_replace($oldMobileCard, $newMobileCard, $viewContent);
    
    file_put_contents($viewFile, $viewContent);
}
echo "Done Ustadz.";
