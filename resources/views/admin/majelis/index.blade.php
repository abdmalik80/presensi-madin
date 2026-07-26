<x-app-layout>
    <x-slot name="header">Data Majelis</x-slot>

    <div class="space-y-4 pb-8 md:pb-0" x-data="{
        showFilterModal: false,
        selectedIds: [],
        selectAllPages: false,
        isPageAllChecked: false,
        allIds: {{ json_encode($majelis->pluck('id')) }},
        pageIds: {{ json_encode($majelis->pluck('id')) }},
        
        togglePageAll(checked) {
            this.isPageAllChecked = checked;
            if (checked) {
                this.pageIds.forEach(id => {
                    if (!this.selectedIds.includes(id)) this.selectedIds.push(id);
                });
            } else {
                this.selectedIds = this.selectedIds.filter(id => !this.pageIds.includes(id));
                this.selectAllPages = false;
            }
        },
        selectEveryPage() {
            this.selectedIds = [...this.allIds];
            this.selectAllPages = true;
            this.isPageAllChecked = true;
        },
        clearAll() {
            this.selectedIds = [];
            this.selectAllPages = false;
            this.isPageAllChecked = false;
        }
    }">
        <x-alert />

        {{-- DESKTOP VIEW --}}
        {{-- Header Bar Desktop --}}
        <div class="hidden md:flex flex-col sm:flex-row items-start sm:items-end justify-between gap-3 mb-4">
            <form method="GET" class="flex items-end gap-2">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1 pl-1">Cari Majelis</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari majelis/koordinator..."
                        class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent bg-white/80 w-64 h-[38px]">
                </div>
                <button type="submit" class="bg-hijau-tua text-white px-4 py-2 rounded-lg text-sm hover:bg-hijau-muda transition-colors h-[38px] flex items-center gap-1.5">
                    <i class="fas fa-search"></i> Tampilkan
                </button>
            </form>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.majelis.create') }}"
                   class="inline-flex items-center gap-2 bg-hijau-tua text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-hijau-muda transition-colors shadow-sm h-[38px]">
                    <i class="fas fa-plus"></i> Tambah Majelis
                </a>
            </div>
        </div>

                {{-- Bulk Actions Banner --}}
        <div x-show="selectedIds.length > 0" style="display: none;" x-transition.opacity
             class="mb-4 bg-red-50 border border-red-100 rounded-xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-red-700">
                    <i class="fas fa-check-square mr-1"></i>
                    <span x-text="selectedIds.length"></span>
                    <span x-text="selectAllPages ? ' majelis (semua data) dipilih' : ' majelis dipilih'"></span>
                </p>
                <p x-show="isPageAllChecked && !selectAllPages && allIds.length > pageIds.length"
                   class="text-xs text-red-600 mt-0.5">
                    Semua {{ $majelis->count() }} majelis di halaman ini dipilih.
                    <button type="button" @click="selectEveryPage()"
                            class="font-bold underline hover:text-red-800 ml-1">
                        Pilih semua <span x-text="allIds.length"></span> majelis?
                    </button>
                </p>
                <p x-show="selectAllPages" class="text-xs text-red-600 mt-0.5">
                    Semua <span x-text="allIds.length"></span> majelis dipilih.
                    <button type="button" @click="clearAll()" class="font-bold underline hover:text-red-800 ml-1">Batalkan pilihan</button>
                </p>
            </div>
            
            <form method="POST" action="{{ route('admin.majelis.bulk-action') }}" id="bulkActionFormMajelis">
                @csrf
                <input type="hidden" name="action" id="bulkActionTypeMajelis" value="">
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>

                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" @click="if(confirm('Yakin hapus ' + selectedIds.length + ' majelis? Tindakan ini tidak dapat dibatalkan!')) { document.getElementById('bulkActionTypeMajelis').value='delete'; document.getElementById('bulkActionFormMajelis').submit(); }"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-600 text-white text-xs font-semibold hover:bg-red-700 transition-colors shadow-sm">
                        <i class="fas fa-trash"></i> Hapus Terpilih
                    </button>
                </div>
            </form>
        </div>

        <div class="hidden md:block bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-hijau-tua text-white">
                    <tr>
                                                <th class="px-4 py-3 text-left w-10">
                            <input type="checkbox"
                                   :checked="isPageAllChecked"
                                   @change="togglePageAll($event.target.checked)"
                                   class="rounded border-gray-300 text-hijau-muda focus:ring-hijau-muda cursor-pointer"
                                   title="Pilih semua di halaman ini">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Nama Majelis</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Tempat</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Koordinator</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Jumlah Santri</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($majelis as $m)
                                        <tr class="group hover:bg-gray-100 transition-colors"
                        :class="selectedIds.includes({{ $m->id }}) ? 'bg-red-50/70' : ''">
                        <td class="px-4 py-3 w-10 transition-opacity duration-200"
                            :class="selectedIds.includes({{ $m->id }}) || selectedIds.length > 0 ? 'opacity-100' : 'opacity-0 group-hover:opacity-100 focus-within:opacity-100'">
                            <input type="checkbox"
                                   :checked="selectedIds.includes({{ $m->id }})"
                                   @change="
                                       if ($event.target.checked) {
                                           if (!selectedIds.includes({{ $m->id }})) selectedIds.push({{ $m->id }});
                                       } else {
                                           selectedIds = selectedIds.filter(id => id !== {{ $m->id }});
                                           this.selectAllPages = false;
                                       }
                                   "
                                   class="rounded border-gray-300 text-red-600 focus:ring-red-400 cursor-pointer w-4 h-4">
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $loop->iteration + ($majelis->currentPage()-1)*$majelis->perPage() }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $m->nama_majelis }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $m->tempat ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $m->koordinator ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            <a href="{{ route('admin.majelis.show', $m) }}"
                               class="inline-flex items-center gap-1.5 font-semibold text-hijau-tua hover:text-hijau-muda transition-colors group">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-hijau-tua/10 group-hover:bg-hijau-tua/20 text-xs font-bold transition-colors">
                                    {{ $m->santris_count }}
                                </span>
                                <span class="text-xs">santri</span>
                                <i class="fas fa-arrow-right text-[9px] opacity-0 group-hover:opacity-100 transition-opacity"></i>
                            </a>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1">
                                <a href="{{ route('admin.majelis.show', $m) }}" class="p-1.5 rounded-lg text-hijau-tua hover:bg-hijau-tua/10 transition-colors" title="Lihat Santri"><i class="fas fa-eye text-sm"></i></a>
                                <a href="{{ route('admin.majelis.edit', $m) }}" class="p-1.5 rounded-lg text-biru hover:bg-biru/10 transition-colors" title="Edit"><i class="fas fa-edit text-sm"></i></a>
                                <form method="POST" action="{{ route('admin.majelis.destroy', $m) }}" class="inline"
                                      onsubmit="return confirm('Yakin hapus majelis ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-merah hover:bg-red-50 transition-colors"><i class="fas fa-trash text-sm"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-gray-400">
                            <i class="fas fa-mosque text-4xl mb-3 block text-gray-200"></i>
                            Tidak ada data majelis.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @if($majelis->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-white/40">
                {{ $majelis->appends(request()->query())->links() }}
            </div>
            @endif
        </div>

        {{-- MOBILE VIEW --}}
        <div class="md:hidden flex items-center gap-2 mb-4 bg-white p-3 rounded-xl shadow-sm border border-gray-100 sticky top-0 z-30">
            <form method="GET" class="flex-1 relative flex items-center">
                <i class="fas fa-search absolute left-3 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari majelis/koordinator..."
                       class="w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent bg-gray-50/50">
            </form>
        </div>

        <div class="md:hidden flex items-center justify-between mb-3 px-1">
            <div>
                <h2 class="text-sm font-bold text-gray-800">Data Majelis</h2>
                <p class="text-[10px] text-gray-500">Total {{ $majelis->total() }} Majelis terdaftar</p>
            </div>
            <a href="{{ route('admin.majelis.create') }}"
               class="inline-flex items-center gap-1.5 bg-[#187a41] text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-green-700 transition-colors shadow-sm">
                <i class="fas fa-plus"></i> Tambah
            </a>
        </div>

        <div class="md:hidden space-y-0 bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 overflow-hidden">
            @forelse($majelis as $m)
            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-3 flex-1 pl-3">
                    <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-mosque text-[#187a41] text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0 pr-2">
                        <h4 class="text-xs font-bold text-gray-800 truncate">{{ $m->nama_majelis }}</h4>
                        <div class="text-[10px] text-gray-500 mt-0.5 flex items-center gap-1">
                            <span>{{ $m->koordinator ?? 'Koordinator: -' }}</span>
                            <span class="text-gray-300">&bull;</span>
                            <span class="font-semibold text-[#187a41]">{{ $m->santris_count }} santri</span>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col items-end justify-between self-stretch py-1 pr-3">
                    <span class="px-2 py-0.5 text-[9px] font-bold rounded-full bg-green-50 text-[#187a41] border border-green-200 tracking-wide">
                        {{ $m->tempat ?? '-' }}
                    </span>
                    <div x-data="{ open: false }" class="relative mt-1">
                        <button @click="open = !open" @click.away="open = false" class="text-gray-400 hover:text-gray-700 p-1 rounded">
                            <i class="fas fa-ellipsis-v text-xs px-1"></i>
                        </button>
                        <div x-show="open" x-transition.opacity class="absolute right-0 top-full mt-1 w-32 bg-white rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.1)] border border-gray-100 py-1.5 z-20" style="display:none">
                            <a href="{{ route('admin.majelis.show', $m) }}" class="block px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 hover:text-[#187a41]"><i class="fas fa-eye w-4"></i> Detail</a>
                            <a href="{{ route('admin.majelis.edit', $m) }}" class="block px-4 py-2 text-xs font-medium text-blue-600 hover:bg-gray-50"><i class="fas fa-edit w-4"></i> Edit</a>
                            <form method="POST" action="{{ route('admin.majelis.destroy', $m) }}" onsubmit="return confirm('Yakin hapus majelis ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="block w-full text-left px-4 py-2 text-xs font-medium text-red-600 hover:bg-gray-50"><i class="fas fa-trash w-4"></i> Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-10">
                <i class="fas fa-mosque text-4xl text-gray-200 mb-2"></i>
                <p class="text-xs text-gray-500">Belum ada data majelis</p>
            </div>
            @endforelse
        </div>

        {{-- Pagination Mobile --}}
        @if($majelis->hasPages())
        <div class="md:hidden mt-4 pb-8 px-1">
            <div class="flex items-center justify-between bg-white rounded-xl shadow-sm border border-gray-100 p-2">
                @if ($majelis->onFirstPage())
                    <span class="p-2 w-10 h-10 flex items-center justify-center rounded-lg text-gray-300"><i class="fas fa-chevron-left"></i></span>
                @else
                    <a href="{{ $majelis->appends(request()->query())->previousPageUrl() }}" class="p-2 w-10 h-10 flex items-center justify-center rounded-lg text-[#187a41] hover:bg-green-50 transition-colors"><i class="fas fa-chevron-left"></i></a>
                @endif
                <span class="text-xs font-semibold text-gray-600">Hal {{ $majelis->currentPage() }} dari {{ $majelis->lastPage() }}</span>
                @if ($majelis->hasMorePages())
                    <a href="{{ $majelis->appends(request()->query())->nextPageUrl() }}" class="p-2 w-10 h-10 flex items-center justify-center rounded-lg text-[#187a41] hover:bg-green-50 transition-colors"><i class="fas fa-chevron-right"></i></a>
                @else
                    <span class="p-2 w-10 h-10 flex items-center justify-center rounded-lg text-gray-300"><i class="fas fa-chevron-right"></i></span>
                @endif
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
