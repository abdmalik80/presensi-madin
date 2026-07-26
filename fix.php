<?php
$file = 'resources/views/santri/dokumentasi/index.blade.php';
$content = file_get_contents($file);
$fix = file_get_contents('fix.txt');

$start = strpos($content, '                {{-- Filter Tipe --}}');
$end = strpos($content, '            @endforelse');
$end = strpos($content, '</div>', $end) + 6;

$chunk_to_replace = substr($content, $start, $end - $start);

$fix_start = strpos($fix, '                {{-- Filter Bulan --}}');
$fix_chunk = substr($fix, $fix_start);

$new_content = str_replace($chunk_to_replace, '                {{-- Filter Tipe --}}
                <div class="flex-1 min-w-[130px]">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tipe Berkas</label>
                    <x-custom-select name="tipe" :options="$tipeOptions" :selected="request(\'tipe\')" placeholder="Semua Tipe" />
                </div>
' . $fix_chunk, $content);

file_put_contents($file, $new_content);
echo "Fixed!\n";
