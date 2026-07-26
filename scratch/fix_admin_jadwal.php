<?php
$file = 'd:/PRESENSI MADIN/resources/views/admin/jadwal/index.blade.php';
$content = file_get_contents($file);

$pos = strpos($content, '{{-- Filter Modal Mobile --}}');
if ($pos !== false) {
    $content = substr($content, 0, $pos);
    $modalContent = '{{-- Filter Modal Mobile --}}
        <div x-show="showFilterModal" class="md:hidden fixed inset-0 z-50 flex flex-col justify-end bg-black/50" x-transition.opacity style="display:none">
            <div @click.away="showFilterModal = false" class="bg-white w-full rounded-t-2xl shadow-xl overflow-hidden"
                 x-show="showFilterModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full">
                <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-gray-800">Filter Jadwal</h3>
                    <button @click="showFilterModal = false" class="text-gray-400 bg-gray-50 rounded-full w-8 h-8 flex items-center justify-center"><i class="fas fa-times"></i></button>
                </div>
                <div class="p-5">
                    <form method="GET" action="{{ route(\'admin.jadwal.index\') }}" x-data="{ hari: \'{{ request(\'hari\') }}\', majelis_id: \'{{ request(\'majelis_id\') }}\' }">
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-gray-700 mb-2">Hari</label>
                            <div class="grid grid-cols-3 gap-2">
                                <label class="flex items-center justify-center p-2 border rounded-lg cursor-pointer" :class="hari === \'\' ? \'border-[#187a41] bg-green-50\' : \'border-gray-200\'">
                                    <input type="radio" name="hari" value="" class="hidden" x-model="hari">
                                    <span class="text-xs font-medium" :class="hari === \'\' ? \'text-[#187a41]\' : \'text-gray-600\'">Semua</span>
                                </label>
                                @foreach($hariList as $h)
                                <label class="flex items-center justify-center p-2 border rounded-lg cursor-pointer" :class="hari === \'{{ strtolower($h) }}\' ? \'border-[#187a41] bg-green-50\' : \'border-gray-200\'">
                                    <input type="radio" name="hari" value="{{ strtolower($h) }}" class="hidden" x-model="hari">
                                    <span class="text-xs font-medium" :class="hari === \'{{ strtolower($h) }}\' ? \'text-[#187a41]\' : \'text-gray-600\'">{{ $h }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-gray-700 mb-2">Majelis</label>
                            <div class="grid grid-cols-2 gap-2 max-h-36 overflow-y-auto">
                                <label class="flex items-center gap-2 p-2 border rounded-lg cursor-pointer" :class="majelis_id === \'\' ? \'border-[#187a41] bg-green-50\' : \'border-gray-200\'">
                                    <input type="radio" name="majelis_id" value="" class="hidden" x-model="majelis_id">
                                    <span class="text-xs font-medium" :class="majelis_id === \'\' ? \'text-[#187a41]\' : \'text-gray-600\'">Semua</span>
                                </label>
                                @foreach($majelis as $m)
                                <label class="flex items-center gap-2 p-2 border rounded-lg cursor-pointer" :class="majelis_id === \'{{ $m->id }}\' ? \'border-[#187a41] bg-green-50\' : \'border-gray-200\'">
                                    <input type="radio" name="majelis_id" value="{{ $m->id }}" class="hidden" x-model="majelis_id">
                                    <span class="text-xs font-medium" :class="majelis_id === \'{{ $m->id }}\' ? \'text-[#187a41]\' : \'text-gray-600\'">{{ $m->nama_majelis }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" @click="window.location.href=\'{{ route(\'admin.jadwal.index\') }}\'" class="flex-1 py-2.5 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Reset Filter</button>
                            <button type="submit" class="flex-1 py-2.5 text-xs font-semibold text-white bg-[#187a41] hover:bg-green-700 rounded-xl transition-colors shadow-sm">Terapkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
';
    file_put_contents($file, $content . $modalContent);
    echo "Fixed admin jadwal.\n";
}
