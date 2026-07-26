<?php
$file = 'd:/PRESENSI MADIN/resources/views/admin/laporan/index.blade.php';
$lines = file($file);

// Find the first occurrence of {{-- Summary Cards --}}
$summaryIndex = -1;
for ($i = 70; $i < count($lines); $i++) {
    if (strpos($lines[$i], '{{-- Summary Cards --}}') !== false) {
        $summaryIndex = $i;
        break;
    }
}

if ($summaryIndex !== -1) {
    // Keep lines 0 to 70 (my fixed top half including fixed mobile header)
    $newLines = array_slice($lines, 0, 70);
    // Append the rest of the file from Summary Cards onwards
    $newLines = array_merge($newLines, array_slice($lines, $summaryIndex));
    
    file_put_contents($file, implode("", $newLines));
    echo "Cleaned duplicated content.\n";
} else {
    echo "Could not find Summary Cards.\n";
}
