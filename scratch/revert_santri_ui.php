<?php

$file = 'd:\PRESENSI MADIN\resources\views\admin\santri\index.blade.php';
$content = file_get_contents($file);

// 1. Revert Dual Buttons back to simple Tambah/Import
$dualButtons = <<<'EOD'
        <div class="hidden md:flex items-center gap-2">
            <div class="flex items-center gap-1 bg-white border border-gray-200 p-1 rounded-lg mr-2 shadow-sm">
                <button type="button" @click="togglePageAll(true)" class="px-3 py-1 text-sm font-medium rounded-md hover:bg-gray-100 text-gray-700 transition-colors" title="Pilih semua di halaman ini">
                    <i class="fas fa-check-double text-hijau-tua mr-1"></i> Pilih Semua
                </button>
                <button type="button" @click="clearAll()" class="px-3 py-1 text-sm font-medium rounded-md hover:bg-gray-100 text-gray-700 transition-colors" title="Batalkan pilihan">
                    <i class="fas fa-ban text-red-500 mr-1"></i> Batal
                </button>
            </div>
            <button @click="showImportModal = true" type="button" class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-500 transition-colors shadow">
                <i class="fas fa-file-import"></i> Import
            </button>
            <a href="{{ route('admin.santri.create') }}" class="inline-flex items-center gap-2 bg-[#187a41] text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-green-700 transition-colors shadow">
                <i class="fas fa-plus"></i> Tambah
            </a>
        </div>
EOD;
$revertedActions = <<<'EOD'
        <div class="hidden md:flex items-center gap-2">
            <button @click="showImportModal = true" type="button" class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-500 transition-colors shadow">
                <i class="fas fa-file-import"></i> Import
            </button>
            <a href="{{ route('admin.santri.create') }}" class="inline-flex items-center gap-2 bg-[#187a41] text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-green-700 transition-colors shadow">
                <i class="fas fa-plus"></i> Tambah
            </a>
        </div>
EOD;
if(strpos($content, $dualButtons) !== false) {
    $content = str_replace($dualButtons, $revertedActions, $content);
}

// 2. Re-add Table Header Checkbox
// Find: <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">No</th>
$noTh = '<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">No</th>';
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
if(!str_contains($content, 'togglePageAll($event.target.checked)')) {
    $content = str_replace($noTh, $headerCheckbox, $content);
}

// 3. Fix TR and add Checkbox
// We previously changed tr to have @click="if...". We replace it with group class and no click handler.
$oldTr = <<<'EOD'
                    <tr @click="
                            if (['A', 'BUTTON', 'I'].includes($event.target.tagName)) return;
                            if (selectedIds.includes({{ $santri->id }})) {
                                selectedIds = selectedIds.filter(id => id !== {{ $santri->id }});
                                selectAllPages = false;
                            } else {
                                selectedIds.push({{ $santri->id }});
                            }
                        "
                        class="hover:bg-gray-100 transition-colors cursor-pointer" 
                        :class="selectedIds.includes({{ $santri->id }}) ? 'bg-red-50/70 border-l-4 border-red-500' : 'border-l-4 border-transparent'">
                          
                          <td class="px-4 py-3 text-sm text-gray-600">{{ $loop->iteration + ($santris->currentPage()-1)*$santris->perPage() }}</td>
EOD;

$newTr = <<<'EOD'
                    <tr class="group hover:bg-gray-100 transition-colors" 
                        :class="selectedIds.includes({{ $santri->id }}) ? 'bg-red-50/70 border-l-4 border-red-500' : 'border-l-4 border-transparent'">
                        <td class="px-4 py-3 w-10 transition-opacity duration-200"
                            :class="selectedIds.includes({{ $santri->id }}) || selectedIds.length > 0 ? 'opacity-100' : 'opacity-0 group-hover:opacity-100 focus-within:opacity-100'">
                            <input type="checkbox"
                                   :checked="selectedIds.includes({{ $santri->id }})"
                                   @change="
                                       if ($event.target.checked) {
                                           if (!selectedIds.includes({{ $santri->id }})) selectedIds.push({{ $santri->id }});
                                       } else {
                                           selectedIds = selectedIds.filter(id => id !== {{ $santri->id }});
                                           selectAllPages = false;
                                       }
                                   "
                                   class="cb-santri rounded border-gray-300 text-red-600 focus:ring-red-400 cursor-pointer w-4 h-4">
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $loop->iteration + ($santris->currentPage()-1)*$santris->perPage() }}</td>
EOD;

if(strpos($content, "@click=\"\n                            if (['A', 'BUTTON', 'I']") !== false) {
    // Since str_replace with huge multiline blocks might fail due to exact whitespace, let's use regex
    $content = preg_replace('/<tr @click=".*?<td class="px-4 py-3 text-sm text-gray-600">\{\{ \$loop->iteration/s', $newTr, $content);
}

// 4. Update Mobile Card for Long Press
// Find: <div x-data="{ open: false }" 
//                   class="bg-white/80 backdrop-blur-sm rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all relative"
$oldMobileCard = <<<'EOD'
              <div x-data="{ open: false }" 
                   class="bg-white/80 backdrop-blur-sm rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all relative"
EOD;
$newMobileCard = <<<'EOD'
              <div x-data="{ open: false, timer: null }" 
                   @touchstart="timer = setTimeout(() => { if (!selectedIds.includes({{ $santri->id }})) { selectedIds.push({{ $santri->id }}); if (navigator.vibrate) navigator.vibrate(50); } }, 500)"
                   @touchend="clearTimeout(timer)"
                   @touchmove="clearTimeout(timer)"
                   class="bg-white/80 backdrop-blur-sm rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all relative"
EOD;

if(strpos($content, '@touchstart="timer') === false) {
    // Normalise newlines and spaces just in case
    $content = preg_replace('/<div x-data="\{ open: false \}"\s+class="bg-white\/80 backdrop-blur-sm/s', "<div x-data=\"{ open: false, timer: null }\"\n                   @touchstart=\"timer = setTimeout(() => { if (!selectedIds.includes({{ \$santri->id }})) { selectedIds.push({{ \$santri->id }}); if (navigator.vibrate) navigator.vibrate(50); } }, 500)\"\n                   @touchend=\"clearTimeout(timer)\"\n                   @touchmove=\"clearTimeout(timer)\"\n                   class=\"bg-white/80 backdrop-blur-sm", $content);
}

file_put_contents($file, $content);
echo "Done.";
