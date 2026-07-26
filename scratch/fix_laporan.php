<?php
$top = file_get_contents('d:\PRESENSI MADIN\scratch\top_laporan.php');
$target = file_get_contents('d:\PRESENSI MADIN\resources\views\admin\laporan\index.blade.php');
$target = preg_replace('/<x-app-layout>.*?<p class="text-\[10px\] text-gray-500">/s', $top . "\n                    @if(request()->anyFilled(['majelis_id','kitab_id','dari','sampai','search'])) Filter Aktif @else Pilih Filter @endif\n                </p>", $target);
file_put_contents('d:\PRESENSI MADIN\resources\views\admin\laporan\index.blade.php', $target);
echo "Done.";
