<?php

function fixFile($file, $modelName, $itemName, $loopVar) {
    if (!file_exists($file)) return false;
    $content = file_get_contents($file);
    
    // 1. Desktop Head
    $oldHead = '<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">No</th>';
    $newHead = '<th class="px-4 py-3 text-left w-10">
                            <input type="checkbox"
                                   :checked="isPageAllChecked"
                                   @change="togglePageAll($event.target.checked)"
                                   class="rounded border-gray-300 text-hijau-muda focus:ring-hijau-muda cursor-pointer"
                                   title="Pilih semua di halaman ini">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">No</th>';
    if (strpos($content, 'togglePageAll($event.target.checked)') === false) {
        $content = str_replace($oldHead, $newHead, $content);
    }
    
    // 2. Desktop Body
    $oldBody = '<tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $loop->iteration + ($' . $modelName . '->currentPage()-1)*$' . $modelName . '->perPage() }}</td>';
    $newBody = '<tr class="group hover:bg-gray-100 transition-colors"
                        :class="selectedIds.includes({{ $' . $loopVar . '->id }}) ? \'bg-red-50/70\' : \'\'">
                        <td class="px-4 py-3 w-10 transition-opacity duration-200"
                            :class="selectedIds.includes({{ $' . $loopVar . '->id }}) || selectedIds.length > 0 ? \'opacity-100\' : \'opacity-0 group-hover:opacity-100 focus-within:opacity-100\'">
                            <input type="checkbox"
                                   :checked="selectedIds.includes({{ $' . $loopVar . '->id }})"
                                   @change="if($event.target.checked) { selectedIds.push({{ $' . $loopVar . '->id }}) } else { selectedIds = selectedIds.filter(id => id !== {{ $' . $loopVar . '->id }}); selectAllPages = false; isPageAllChecked = false; }"
                                   class="rounded border-gray-300 text-hijau-muda focus:ring-hijau-muda cursor-pointer">
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $loop->iteration + ($' . $modelName . '->currentPage()-1)*$' . $modelName . '->perPage() }}</td>';
    $content = str_replace($oldBody, $newBody, $content);
    
    // 3. Mobile Container
    if ($modelName === 'pengguna') {
        $oldMobileContainer = '<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 relative flex flex-col gap-3">';
        $newMobileContainer = '<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 relative flex flex-col gap-3"
                 x-data="{ pressTimer: null }"
                 @touchstart="pressTimer = setTimeout(() => { if(!selectedIds.includes({{ $' . $loopVar . '->id }})) { selectedIds.push({{ $' . $loopVar . '->id }}) } }, 500)"
                 @touchend="clearTimeout(pressTimer)"
                 @touchmove="clearTimeout(pressTimer)"
                 :class="selectedIds.includes({{ $' . $loopVar . '->id }}) ? \'bg-red-50\' : \'\'">
                
                {{-- Checkbox for Mobile --}}
                <div class="absolute left-2 top-4 z-10"
                     x-show="selectedIds.length > 0"
                     x-transition.opacity>
                    <input type="checkbox"
                           :checked="selectedIds.includes({{ $' . $loopVar . '->id }})"
                           @change="if($event.target.checked) { selectedIds.push({{ $' . $loopVar . '->id }}) } else { selectedIds = selectedIds.filter(id => id !== {{ $' . $loopVar . '->id }}); selectAllPages = false; isPageAllChecked = false; }"
                           class="rounded border-gray-300 text-[#187a41] focus:ring-[#187a41] shadow-sm w-5 h-5">
                </div>';
        $content = str_replace($oldMobileContainer, $newMobileContainer, $content);

        $oldMobileInner = '<div class="flex items-start gap-3">';
        $newMobileInner = '<div class="flex items-start gap-3 transition-transform duration-300"
                     :class="selectedIds.length > 0 ? \'translate-x-8\' : \'\'">';
        $content = str_replace($oldMobileInner, $newMobileInner, $content);
    } else {
        $oldMobileContainer = '<div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors relative">';
        $newMobileContainer = '<div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors relative"
                 x-data="{ pressTimer: null }"
                 @touchstart="pressTimer = setTimeout(() => { if(!selectedIds.includes({{ $' . $loopVar . '->id }})) { selectedIds.push({{ $' . $loopVar . '->id }}) } }, 500)"
                 @touchend="clearTimeout(pressTimer)"
                 @touchmove="clearTimeout(pressTimer)"
                 :class="selectedIds.includes({{ $' . $loopVar . '->id }}) ? \'bg-red-50\' : \'\'">
                
                {{-- Checkbox for Mobile --}}
                <div class="absolute left-2 top-1/2 -translate-y-1/2 z-10"
                     x-show="selectedIds.length > 0"
                     x-transition.opacity>
                    <input type="checkbox"
                           :checked="selectedIds.includes({{ $' . $loopVar . '->id }})"
                           @change="if($event.target.checked) { selectedIds.push({{ $' . $loopVar . '->id }}) } else { selectedIds = selectedIds.filter(id => id !== {{ $' . $loopVar . '->id }}); selectAllPages = false; isPageAllChecked = false; }"
                           class="rounded border-gray-300 text-[#187a41] focus:ring-[#187a41] shadow-sm w-5 h-5">
                </div>';
        $content = str_replace($oldMobileContainer, $newMobileContainer, $content);

        $oldMobileInner = '<div class="flex items-center gap-3 flex-1 pl-3">';
        $newMobileInner = '<div class="flex items-center gap-3 flex-1 pl-3 transition-transform duration-300"
                     :class="selectedIds.length > 0 ? \'translate-x-8\' : \'\'">';
        $content = str_replace($oldMobileInner, $newMobileInner, $content);
    }
    
    file_put_contents($file, $content);
    return true;
}

fixFile('d:\PRESENSI MADIN\resources\views\admin\ustadz\index.blade.php', 'ustadzs', 'ustadz', 'ustadz');
fixFile('d:\PRESENSI MADIN\resources\views\admin\pengguna\index.blade.php', 'pengguna', 'pengguna', 'user');

echo "Checkboxes injected.\n";
