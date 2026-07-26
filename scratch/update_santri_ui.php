<?php

$file = 'd:\PRESENSI MADIN\resources\views\admin\santri\index.blade.php';
$content = file_get_contents($file);

// 1. Remove table header checkbox
$headerCheckbox = <<<'EOD'
                        <th class="px-4 py-3 text-left w-10">
                            <input type="checkbox"
                                   :checked="isPageAllChecked"
                                   @change="togglePageAll($event.target.checked)"
                                   class="rounded border-white/50 text-hijau-muda focus:ring-0 cursor-pointer"
                                   title="Pilih semua di halaman ini">
                        </th>
EOD;
$content = str_replace($headerCheckbox, '', $content);

// 2. Remove body checkbox
$bodyCheckboxRegex = '/<td class="px-4 py-3">\s*<input type="number".*?class="cb-santri.*?<\/td>/s';
$content = preg_replace($bodyCheckboxRegex, '', $content);

// 3. Update tr to be clickable
// Current tr: <tr class="hover:bg-gray-50/60 transition-colors" :class="selectedIds.includes({{ $santri->id }}) ? 'bg-red-50/40' : ''">
$oldTr = '<tr class="hover:bg-gray-50/60 transition-colors" :class="selectedIds.includes({{ $santri->id }}) ? \'bg-red-50/40\' : \'\'">';
$newTr = <<<'EOD'
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
EOD;
$content = str_replace($oldTr, $newTr, $content);

// 4. Update action buttons to have @click.stop to prevent row click, actually the tag check covers it.
// We just added `if (['A', 'BUTTON', 'I'].includes($event.target.tagName)) return;` so it's safe!

// 5. Add the dual buttons "Pilih Semua" and "Batalkan Pilihan" above the table.
// Where to add them? Let's add them right before the bulk delete form / banner.
// Let's find the bulk delete banner area.
/*
        <div x-show="selectedIds.length > 0" x-transition.opacity
             class="mb-4 bg-red-50 border border-red-100 rounded-xl p-4 flex items-center justify-between">
*/
// Wait! If they are hidden when selectedIds.length == 0, we can't select all!
// The "Pilih Semua" button should be visible always.
// Let's add the buttons next to "Tambah" and "Import" buttons for Desktop!
$oldAdminActions = <<<'EOD'
        <div class="hidden md:flex items-center gap-2">
            <button @click="showImportModal = true" type="button" class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-500 transition-colors shadow">
                <i class="fas fa-file-import"></i> Import
            </button>
            <a href="{{ route('admin.santri.create') }}" class="inline-flex items-center gap-2 bg-[#187a41] text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-green-700 transition-colors shadow">
                <i class="fas fa-plus"></i> Tambah
            </a>
        </div>
EOD;
$newAdminActions = <<<'EOD'
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
$content = str_replace($oldAdminActions, $newAdminActions, $content);

file_put_contents($file, $content);

echo "Done.";
