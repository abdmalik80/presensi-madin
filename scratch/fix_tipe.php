<?php

// Fix Admin View
$adminView = 'd:\PRESENSI MADIN\resources\views\admin\dokumentasi\index.blade.php';
$adminContent = file_get_contents($adminView);

if (!str_contains($adminContent, '$tipeOptions')) {
    $phpBlock = <<<'EOD'
    @php
    $tipeOptions = [
        'image' => '<i class="far fa-image text-blue-500 w-4 text-center mr-1"></i> Gambar',
        'pdf'   => '<i class="far fa-file-pdf text-red-500 w-4 text-center mr-1"></i> PDF',
        'doc'   => '<i class="far fa-file-word text-blue-600 w-4 text-center mr-1"></i> Word',
        'xls'   => '<i class="far fa-file-excel text-green-500 w-4 text-center mr-1"></i> Excel',
        'ppt'   => '<i class="far fa-file-powerpoint text-orange-500 w-4 text-center mr-1"></i> PowerPoint',
        'other' => '<i class="far fa-file text-gray-500 w-4 text-center mr-1"></i> Lainnya',
    ];
EOD;
    $adminContent = preg_replace('/@php/', $phpBlock, $adminContent, 1);
}

$adminContent = str_replace(
    '<x-custom-select name="tipe" :options="[\'materi\'=>\'Materi\',\'kegiatan\'=>\'Kegiatan\']" :selected="request(\'tipe\')" placeholder="Semua Tipe" width="w-full" />',
    '<x-custom-select name="tipe" :options="$tipeOptions" :selected="request(\'tipe\')" placeholder="Semua Tipe" width="w-full" />',
    $adminContent
);

file_put_contents($adminView, $adminContent);

// Fix Ustadz View
$ustadzView = 'd:\PRESENSI MADIN\resources\views\ustadz\dokumentasi\index.blade.php';
$ustadzContent = file_get_contents($ustadzView);

// Replace the weird emoji strings with FontAwesome
$oldTipe = <<<'EOD'
    $tipeOptions = [
        'image' => 'dY-,? Gambar',
        'pdf'   => 'dY", PDF',
        'doc'   => 'dY"? Word',
        'xls'   => 'dY"S Excel',
        'ppt'   => 'dY",? PowerPoint',
        'other' => 'dY"? Lainnya',
    ];
EOD;

// if it's there
$ustadzContent = preg_replace('/\$tipeOptions\s*=\s*\[.*?\];/s', <<<'EOD'
$tipeOptions = [
        'image' => '<i class="far fa-image text-blue-500 w-4 text-center mr-1"></i> Gambar',
        'pdf'   => '<i class="far fa-file-pdf text-red-500 w-4 text-center mr-1"></i> PDF',
        'doc'   => '<i class="far fa-file-word text-blue-600 w-4 text-center mr-1"></i> Word',
        'xls'   => '<i class="far fa-file-excel text-green-500 w-4 text-center mr-1"></i> Excel',
        'ppt'   => '<i class="far fa-file-powerpoint text-orange-500 w-4 text-center mr-1"></i> PowerPoint',
        'other' => '<i class="far fa-file text-gray-500 w-4 text-center mr-1"></i> Lainnya',
    ];
EOD, $ustadzContent);

file_put_contents($ustadzView, $ustadzContent);

echo "Done.";
