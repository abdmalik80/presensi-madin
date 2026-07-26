<?php

$file = 'd:\PRESENSI MADIN\resources\views\admin\santri\index.blade.php';
$content = file_get_contents($file);

// The exact string to remove (from line 305 to 352)
$bannerRegex = '/\{\{-- Bulk Action Toolbar \(Shared\) --\}\}.*?<\/form>\s*<\/div>/s';
if (preg_match($bannerRegex, $content, $matches)) {
    $bannerContent = $matches[0];
    
    // Remove the banner from its current location
    $content = preg_replace($bannerRegex, '', $content);
    
    // Format the banner to match the other pages (e.g. style="display:none" -> style="display: none;", x-transition -> x-transition.opacity, mt-4 -> mb-4)
    $bannerContent = str_replace('flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-4 py-3 rounded-xl border border-red-200 bg-red-50 mt-4', 'mb-4 bg-red-50 border border-red-100 rounded-xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4', $bannerContent);
    $bannerContent = str_replace('x-transition ', 'x-transition.opacity ', $bannerContent);
    
    // Inject it before <div class="hidden md:block bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 overflow-hidden">
    $targetString = '<div class="hidden md:block bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 overflow-hidden">';
    
    if (strpos($content, $targetString) !== false) {
        $content = str_replace($targetString, $bannerContent . "\n" . $targetString, $content);
        file_put_contents($file, $content);
        echo "Santri banner moved successfully.\n";
    } else {
        echo "Target string not found!\n";
    }
} else {
    echo "Banner regex not matched!\n";
}
