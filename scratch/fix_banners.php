<?php

$ustadzFile = 'd:\PRESENSI MADIN\resources\views\admin\ustadz\index.blade.php';
$ustadzContent = file_get_contents($ustadzFile);

$ustadzBanner = <<<'EOD'
        {{-- Bulk Actions Banner --}}
        <div x-show="selectedIds.length > 0" x-transition.opacity
             class="mb-4 bg-red-50 border border-red-100 rounded-xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-red-700">
                    <i class="fas fa-check-square mr-1"></i>
                    <span x-text="selectedIds.length"></span>
                    <span x-text="selectAllPages ? ' ustadz (semua data) dipilih' : ' ustadz dipilih'"></span>
                </p>
                <p x-show="isPageAllChecked && !selectAllPages && allIds.length > pageIds.length"
                   class="text-xs text-red-600 mt-0.5">
                    Semua {{ $ustadzs->count() }} ustadz di halaman ini dipilih.
                    <button type="button" @click="selectEveryPage()"
                            class="font-bold underline hover:text-red-800 ml-1">
                        Pilih semua <span x-text="allIds.length"></span> ustadz?
                    </button>
                </p>
                <p x-show="selectAllPages" class="text-xs text-red-600 mt-0.5">
                    Semua <span x-text="allIds.length"></span> ustadz dipilih.
                    <button type="button" @click="clearAll()" class="font-bold underline hover:text-red-800 ml-1">Batalkan pilihan</button>
                </p>
            </div>
            
            <form method="POST" action="{{ route('admin.ustadz.bulk-action') }}" id="bulkActionFormUstadz">
                @csrf
                <input type="hidden" name="action" id="bulkActionTypeUstadz" value="">
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                <div class="flex gap-2">
                    <button type="button" onclick="document.getElementById('bulkActionTypeUstadz').value='set_aktif'; document.getElementById('bulkActionFormUstadz').submit();"
                            class="bg-[#187a41] hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold shadow-sm transition-colors">
                        <i class="fas fa-check-circle mr-1"></i> Set Aktif
                    </button>
                    <button type="button" onclick="document.getElementById('bulkActionTypeUstadz').value='set_nonaktif'; document.getElementById('bulkActionFormUstadz').submit();"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold shadow-sm transition-colors">
                        <i class="fas fa-times-circle mr-1"></i> Set Non-Aktif
                    </button>
                    <button type="button" onclick="if(confirm('Yakin ingin menghapus data yang dipilih?')) { document.getElementById('bulkActionTypeUstadz').value='delete'; document.getElementById('bulkActionFormUstadz').submit(); }"
                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold shadow-sm transition-colors">
                        <i class="fas fa-trash-alt mr-1"></i> Hapus
                    </button>
                </div>
            </form>
        </div>

EOD;

if (strpos($ustadzContent, 'bulkActionFormUstadz') === false) {
    $ustadzContent = preg_replace('/<div class="hidden md:block bg-white\/60 backdrop-blur-xl rounded-2xl shadow-\[0_8px_30px_rgb\(0,0,0,0\.04\)\] border border-white\/50 overflow-hidden">/', $ustadzBanner . "$0", $ustadzContent);
    file_put_contents($ustadzFile, $ustadzContent);
    echo "Ustadz fixed\n";
}

$penggunaFile = 'd:\PRESENSI MADIN\resources\views\admin\pengguna\index.blade.php';
$penggunaContent = file_get_contents($penggunaFile);

$penggunaBanner = <<<'EOD'
        {{-- Bulk Actions Banner --}}
        <div x-show="selectedIds.length > 0" x-transition.opacity
             class="mb-4 bg-red-50 border border-red-100 rounded-xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-red-700">
                    <i class="fas fa-check-square mr-1"></i>
                    <span x-text="selectedIds.length"></span>
                    <span x-text="selectAllPages ? ' pengguna (semua data) dipilih' : ' pengguna dipilih'"></span>
                </p>
                <p x-show="isPageAllChecked && !selectAllPages && allIds.length > pageIds.length"
                   class="text-xs text-red-600 mt-0.5">
                    Semua {{ $pengguna->count() }} pengguna di halaman ini dipilih.
                    <button type="button" @click="selectEveryPage()"
                            class="font-bold underline hover:text-red-800 ml-1">
                        Pilih semua <span x-text="allIds.length"></span> pengguna?
                    </button>
                </p>
                <p x-show="selectAllPages" class="text-xs text-red-600 mt-0.5">
                    Semua <span x-text="allIds.length"></span> pengguna dipilih.
                    <button type="button" @click="clearAll()" class="font-bold underline hover:text-red-800 ml-1">Batalkan pilihan</button>
                </p>
            </div>
            
            <form method="POST" action="{{ route('admin.pengguna.bulk-action') }}" id="bulkActionFormPengguna">
                @csrf
                <input type="hidden" name="action" id="bulkActionTypePengguna" value="">
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                <div class="flex gap-2">
                    <button type="button" onclick="document.getElementById('bulkActionTypePengguna').value='set_aktif'; document.getElementById('bulkActionFormPengguna').submit();"
                            class="bg-[#187a41] hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold shadow-sm transition-colors">
                        <i class="fas fa-check-circle mr-1"></i> Set Aktif
                    </button>
                    <button type="button" onclick="document.getElementById('bulkActionTypePengguna').value='set_nonaktif'; document.getElementById('bulkActionFormPengguna').submit();"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold shadow-sm transition-colors">
                        <i class="fas fa-times-circle mr-1"></i> Set Non-Aktif
                    </button>
                    <button type="button" onclick="if(confirm('Yakin ingin menghapus data yang dipilih?')) { document.getElementById('bulkActionTypePengguna').value='delete'; document.getElementById('bulkActionFormPengguna').submit(); }"
                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold shadow-sm transition-colors">
                        <i class="fas fa-trash-alt mr-1"></i> Hapus
                    </button>
                </div>
            </form>
        </div>

EOD;

if (strpos($penggunaContent, 'bulkActionFormPengguna') === false) {
    $penggunaContent = preg_replace('/<div class="hidden md:block bg-white\/60 backdrop-blur-xl rounded-2xl shadow-\[0_8px_30px_rgb\(0,0,0,0\.04\)\]\s*border border-white\/50 overflow-hidden" style="position: relative; z-index: 10;">/', $penggunaBanner . "$0", $penggunaContent);
    file_put_contents($penggunaFile, $penggunaContent);
    echo "Pengguna fixed\n";
}

echo "Done injecting banners.\n";
