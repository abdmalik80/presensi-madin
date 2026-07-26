<?php

$files = [
    'd:\PRESENSI MADIN\resources\views\admin\ustadz\index.blade.php',
    'd:\PRESENSI MADIN\resources\views\admin\pengguna\index.blade.php',
    'd:\PRESENSI MADIN\resources\views\admin\majelis\index.blade.php',
    'd:\PRESENSI MADIN\resources\views\admin\kitab\index.blade.php',
    'd:\PRESENSI MADIN\resources\views\admin\jadwal\index.blade.php',
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);
    
    // Fix 1: Pilih semua {{ count($allIds) }} ...
    $content = preg_replace('/\{\{\s*count\(\$allIds\)\s*\}\}/', '<span x-text="allIds.length"></span>', $content);
    
    file_put_contents($file, $content);
    echo "Fixed: " . basename(dirname($file)) . "\n";
}

echo "All fixed.\n";
