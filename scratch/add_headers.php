<?php

$pages = [
    'santri' => ['title' => 'Data Santri', 'desc' => 'Kelola seluruh data santri Madin'],
    'ustadz' => ['title' => 'Data Ustadz', 'desc' => 'Kelola data asatidz dan pengampu'],
    'jadwal' => ['title' => 'Data Jadwal', 'desc' => 'Kelola jadwal pengajian majelis'],
    'kitab' => ['title' => 'Data Kitab', 'desc' => 'Kelola daftar kitab yang dipelajari'],
    'majelis' => ['title' => 'Data Majelis', 'desc' => 'Kelola daftar majelis dan tingkatannya'],
    'pengguna' => ['title' => 'Manajemen Pengguna', 'desc' => 'Kelola akses dan role pengguna sistem'],
    'presensi' => ['title' => 'Rekap Presensi', 'desc' => 'Kelola data riwayat presensi santri']
];

foreach ($pages as $slug => $data) {
    $path = "d:/PRESENSI MADIN/resources/views/admin/{$slug}/index.blade.php";
    if (!file_exists($path)) {
        echo "File $path not found.\n";
        continue;
    }
    
    $content = file_get_contents($path);
    
    if (strpos($content, '<h2 class="text-lg font-semibold text-gray-800">' . $data['title'] . '</h2>') !== false) {
        echo "Header already exists in $slug.\n";
        continue;
    }

    $headerHtml = <<<HTML
        {{-- Header Desktop --}}
        <div class="hidden md:flex bg-white/70 backdrop-blur-xl rounded-2xl border border-white/50 shadow-sm p-5 flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">{$data['title']}</h2>
                <p class="text-sm text-gray-500 mt-0.5">{$data['desc']}</p>
            </div>
        </div>

HTML;

    // We look for where to inject. Usually before `<div class="hidden md:flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 bg-white/60 p-4 rounded-xl shadow-sm border border-gray-100">`
    // Or just `class="hidden md:flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 bg-white/60 p-4 rounded-xl shadow-sm border border-gray-100"`
    
    $search = '<div class="hidden md:flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 bg-white/60 p-4 rounded-xl shadow-sm border border-gray-100">';
    $search2 = '<div class="hidden md:flex items-center gap-3 bg-white/60 p-4 rounded-xl shadow-sm border border-gray-100">';
    $search3 = '{{-- Top section: Search and Filter Desktop --}}';
    $search4 = '{{-- Filter Desktop --}}';
    $search5 = '{{-- Desktop Filter --}}';
    
    $inserted = false;
    
    if (strpos($content, $search3) !== false) {
        $content = str_replace($search3, $headerHtml . "\n        " . $search3, $content);
        $inserted = true;
    } elseif (strpos($content, $search) !== false) {
        $content = str_replace($search, $headerHtml . "\n        " . $search, $content);
        $inserted = true;
    } elseif (strpos($content, $search2) !== false) {
        $content = str_replace($search2, $headerHtml . "\n        " . $search2, $content);
        $inserted = true;
    } else {
        // Fallback: Just insert after `<x-alert />`
        $alertStr = '<x-alert />';
        if (strpos($content, $alertStr) !== false) {
            $content = str_replace($alertStr, $alertStr . "\n\n" . $headerHtml, $content);
            $inserted = true;
        }
    }
    
    if ($inserted) {
        file_put_contents($path, $content);
        echo "Added header to $slug.\n";
    } else {
        echo "Failed to add header to $slug.\n";
    }
}
