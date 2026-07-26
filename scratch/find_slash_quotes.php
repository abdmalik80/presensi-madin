<?php
foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator('d:/PRESENSI MADIN/resources/views')) as $f) {
    if(strpos($f->getPathname(), '.blade.php') !== false) {
        $c = file_get_contents($f->getPathname());
        if(strpos($c, '\\\'') !== false) {
            echo "Found backslash quote in: " . $f->getPathname() . "\n";
        }
    }
}
