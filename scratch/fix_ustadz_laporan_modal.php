<?php
$file = 'd:/PRESENSI MADIN/resources/views/ustadz/laporan/index.blade.php';
$content = file_get_contents($file);

$pos = strpos($content, '{{-- Mobile Filter Modal --}}');
if ($pos !== false) {
    $content = substr($content, 0, $pos);
    $modalContent = '{{-- Mobile Filter Modal --}}
        <div x-show="showFilterModal" class="md:hidden fixed inset-0 z-50 flex flex-col justify-end bg-black/50" x-transition.opacity style="display:none">
            <div @click.away="showFilterModal = false" class="bg-white w-full rounded-t-2xl shadow-xl overflow-hidden flex flex-col max-h-[85vh]"
                 x-show="showFilterModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full">
                <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center flex-shrink-0">
                    <h3 class="text-sm font-bold text-gray-800">Filter Laporan</h3>
                    <button @click="showFilterModal = false" class="text-gray-400 bg-gray-50 rounded-full w-8 h-8 flex items-center justify-center"><i class="fas fa-times"></i></button>
                </div>
                <div class="p-5 overflow-y-auto">
                    <form method="GET" action="{{ route(\'ustadz.laporan.index\') }}" class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-2">Cari Santri</label>
                            <input type="text" name="search" value="{{ request(\'search\') }}" placeholder="Nama / NIS" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-hijau-tua bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-2">Majelis</label>
                            <select name="majelis_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-hijau-tua bg-white">
                                <option value="">Semua Majelis Saya</option>
                                @foreach($majelisOptions as $val => $text)
                                    <option value="{{ $val }}" {{ request(\'majelis_id\') == $val ? \'selected\' : \'\' }}>{!! $text !!}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Dari Tanggal</label>
                                <input type="date" name="dari" value="{{ request(\'dari\') }}" class="w-full border border-gray-200 rounded-lg px-2 py-2 text-[10px] focus:ring-2 focus:ring-hijau-tua">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Sampai Tanggal</label>
                                <input type="date" name="sampai" value="{{ request(\'sampai\') }}" class="w-full border border-gray-200 rounded-lg px-2 py-2 text-[10px] focus:ring-2 focus:ring-hijau-tua">
                            </div>
                        </div>
                        <div class="flex gap-2 pt-2">
                            <button type="button" @click="window.location.href=\'{{ route(\'ustadz.laporan.index\') }}\'" class="flex-1 py-2.5 text-xs font-semibold text-gray-600 bg-gray-100 rounded-xl">Reset</button>
                            <button type="submit" @click="showFilterModal = false" class="flex-1 py-2.5 text-xs font-semibold text-white bg-[#187a41] rounded-xl">Terapkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
';
    file_put_contents($file, $content . $modalContent);
    echo "Fixed ustadz laporan.\n";
}
