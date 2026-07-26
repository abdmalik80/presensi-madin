<?php
$files = [
    'd:/PRESENSI MADIN/resources/views/admin/laporan/index.blade.php',
    'd:/PRESENSI MADIN/resources/views/ustadz/laporan/index.blade.php',
    'd:/PRESENSI MADIN/resources/views/admin/presensi/index.blade.php',
    'd:/PRESENSI MADIN/resources/views/ustadz/presensi/index.blade.php'
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);

    // Fix modal overflow issues
    $content = str_replace('shadow-xl overflow-hidden flex flex-col', 'shadow-xl flex flex-col', $content);
    $content = str_replace('shadow-xl overflow-hidden', 'shadow-xl', $content); // generic
    
    // In ustadz laporan, it has overflow-hidden flex flex-col
    $content = str_replace('overflow-y-auto', 'overflow-visible', $content);

    // Replace <select> back to <x-custom-select> for Majelis (Mobile)
    $pattern = '/<select name="majelis_id"[\s\S]*?<\/select>/';
    if (strpos($file, 'ustadz') !== false) {
        $replacement = '<x-custom-select name="majelis_id" :options="$majelisOptions" :selected="request(\'majelis_id\')" placeholder="Semua Majelis Saya" width="w-full" />';
    } else {
        $replacement = '<x-custom-select name="majelis_id" :options="$majelisOptions" :selected="request(\'majelis_id\')" placeholder="Semua Majelis" width="w-full" />';
    }
    $content = preg_replace($pattern, $replacement, $content);

    // Replace <select> back to <x-custom-select> for Kitab
    $patternKitab = '/<select name="kitab_id"[\s\S]*?<\/select>/';
    $replacementKitab = '<x-custom-select name="kitab_id" :options="$kitabOptions" :selected="request(\'kitab_id\')" placeholder="Semua Kitab" width="w-full" />';
    $content = preg_replace($patternKitab, $replacementKitab, $content);
    
    // Replace <select> back to <x-custom-select> for Jadwal (Presensi)
    $patternJadwal = '/<select name="jadwal_id"[\s\S]*?<\/select>/';
    $replacementJadwal = '<x-custom-select name="jadwal_id" :options="$jadwalOptions" :selected="request(\'jadwal_id\')" placeholder="Semua Jadwal" width="w-full" />';
    $content = preg_replace($patternJadwal, $replacementJadwal, $content);

    file_put_contents($file, $content);
    echo "Fixed mobile filter in " . basename(dirname($file)) . "/" . basename($file) . "\n";
}
