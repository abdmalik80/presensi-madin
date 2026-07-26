<?php
function replaceCustomSelectInModal($file, $name, $optionsVar, $placeholder) {
    if (!file_exists($file)) return;
    $content = file_get_contents($file);
    
    // We only want to replace the one inside the mobile modal.
    // So we'll look for the `<x-custom-select name="$name" ... />` that occurs AFTER `<div class="p-5 overflow-y-auto">` or inside `showFilterModal`
    
    // Split the content at `showFilterModal` to only affect the modal part
    $parts = explode('showFilterModal = false"', $content);
    if (count($parts) < 2) return;
    
    $modalContent = $parts[count($parts) - 1]; // The part after the modal close button
    
    $pattern = '/<x-custom-select\s+name="' . $name . '"\s+:options="\$' . $optionsVar . '"\s+:selected="request\(\'' . $name . '\'\)"\s+placeholder="' . $placeholder . '"\s*\/>/s';
    
    $replacement = '<select name="' . $name . '" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-hijau-tua bg-white">
                                  <option value="">' . $placeholder . '</option>
                                  @foreach($' . $optionsVar . ' as $val => $text)
                                      <option value="{{ $val }}" {{ request(\'' . $name . '\') == $val ? \'selected\' : \'\' }}>{!! $text !!}</option>
                                  @endforeach
                              </select>';
                              
    $newModalContent = preg_replace($pattern, $replacement, $modalContent);
    
    if ($newModalContent !== $modalContent) {
        $parts[count($parts) - 1] = $newModalContent;
        $content = implode('showFilterModal = false"', $parts);
        file_put_contents($file, $content);
        echo "Replaced $name in $file\n";
    }
}

// 1. Ustadz Presensi Index
replaceCustomSelectInModal('d:/PRESENSI MADIN/resources/views/ustadz/presensi/index.blade.php', 'jadwal_id', 'jadwalOptions', 'Semua Jadwal');

// 2. Admin Presensi Index
replaceCustomSelectInModal('d:/PRESENSI MADIN/resources/views/admin/presensi/index.blade.php', 'jadwal_id', 'jadwalOptions', 'Semua Jadwal');

// 3. Admin Laporan Index
replaceCustomSelectInModal('d:/PRESENSI MADIN/resources/views/admin/laporan/index.blade.php', 'majelis_id', 'majelisOptions', 'Semua Majelis');
replaceCustomSelectInModal('d:/PRESENSI MADIN/resources/views/admin/laporan/index.blade.php', 'kitab_id', 'kitabOptions', 'Semua Kitab');

// 4. Ustadz Laporan Index
replaceCustomSelectInModal('d:/PRESENSI MADIN/resources/views/ustadz/laporan/index.blade.php', 'majelis_id', 'majelisOptions', 'Semua Majelis Saya');

