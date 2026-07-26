<?php
$file = 'd:/PRESENSI MADIN/resources/views/admin/laporan/index.blade.php';
$content = file_get_contents($file);

// Find Mobile Header Section
$start = strpos($content, '{{-- Mobile Header & Filter Button --}}');
$end = strpos($content, '$cards = ['); // The start of the array

if ($start !== false && $end !== false) {
    // We need to keep {{-- Summary Cards --}} and @if(isset($summary)) and @php
    $end = strpos($content, '{{-- Summary Cards --}}');
    
    $head = substr($content, 0, $start);
    $tail = substr($content, $end);
    
    $mobileHeader = '{{-- Mobile Header & Filter Button --}}
        <div class="md:hidden flex items-center justify-between bg-white p-3 rounded-xl shadow-sm border border-gray-100 sticky top-0 z-30">
            <div>
                <h2 class="text-sm font-bold text-gray-800">Laporan Presensi</h2>
                <p class="text-[10px] text-gray-500">
                    @if(request()->anyFilled([\'majelis_id\',\'kitab_id\',\'dari\',\'sampai\',\'search\'])) Filter Aktif @else Pilih Filter @endif
                </p>
            </div>
            <div class="flex items-center gap-2">
                @if(request()->anyFilled([\'majelis_id\',\'kitab_id\',\'dari\',\'sampai\',\'search\']))
                <a href="{{ route(\'admin.laporan.pdf\', request()->query()) }}" target="_blank" class="p-2 bg-rose-50 text-rose-600 border border-rose-200 rounded-lg flex items-center justify-center w-10 h-10">
                    <i class="fas fa-file-pdf"></i>
                </a>
                @endif
                <button type="button" @click="showFilterModal = true" class="p-2 border border-[#187a41] bg-green-50 text-[#187a41] rounded-lg flex items-center justify-center w-10 h-10">
                    <i class="fas fa-filter"></i>
                </button>
            </div>
        </div>

        ';
        
    file_put_contents($file, $head . $mobileHeader . $tail);
    echo "Fixed admin laporan header.\n";
}
