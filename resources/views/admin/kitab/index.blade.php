<x-app-layout>
    @php
    $bidangOptions = [];
    foreach($bidangIlmuList as $k) $bidangOptions[$k] = $k;
    @endphp
    <x-slot name="header">Data Kitab</x-slot>

    <div class="space-y-4 pb-8 md:pb-0" x-data="{ 
        showFilterModal: false,
        selectedIds: [],
        selectAllPages: false,
        isPageAllChecked: false,
        allIds: {{ json_encode($kitabs->pluck('id')) }},
        pageIds: {{ json_encode($kitabs->pluck('id')) }},
        
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

        {{-- Header Bar Desktop --}}
        <div class="hidden md:flex flex-col sm:flex-row items-start sm:items-end justify-between gap-3 mb-4">
            <form method="GET" class="flex items-end gap-2">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1 pl-1">Cari Kitab</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama kitab..."
                        class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent bg-white/80 w-48 h-[38px]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1 pl-1">Bidang Ilmu</label>
                    <x-custom-select name="bidang_ilmu" :options="$bidangOptions" :selected="request('bidang_ilmu')" placeholder="Semua Bidang" />
                </div>
                <button type="submit" class="bg-hijau-tua text-white px-4 py-2 rounded-lg text-sm hover:bg-hijau-muda transition-colors h-[38px] flex items-center gap-1.5">
                    <i class="fas fa-search"></i> Tampilkan
                </button>
            </form>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.kitab.create') }}"
                   class="inline-flex items-center gap-2 bg-hijau-tua text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-hijau-muda transition-colors shadow-sm h-[38px]">
                    <i class="fas fa-plus"></i> Tambah Kitab
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
                    <span x-text="selectAllPages ? ' kitab (semua data) dipilih' : ' kitab dipilih'"></span>
                </p>
                <p x-show="isPageAllChecked && !selectAllPages && allIds.length > pageIds.length"
                   class="text-xs text-red-600 mt-0.5">
                    Semua {{ $kitabs->count() }} kitab di halaman ini dipilih.
                    <button type="button" @click="selectEveryPage()"
                            class="font-bold underline hover:text-red-800 ml-1">
                        Pilih semua <span x-text="allIds.length"></span> kitab?
                    </button>
                </p>
                <p x-show="selectAllPages" class="text-xs text-red-600 mt-0.5">
                    Semua <span x-text="allIds.length"></span> kitab dipilih.
                    <button type="button" @click="clearAll()" class="font-bold underline hover:text-red-800 ml-1">Batalkan pilihan</button>
                </p>
            </div>
            
            <form method="POST" action="{{ route('admin.kitab.bulk-action') }}" id="bulkActionFormKitab">
                @csrf
                <input type="hidden" name="action" id="bulkActionTypeKitab" value="">
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>

                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" @click="if(confirm('Yakin hapus ' + selectedIds.length + ' kitab? Tindakan ini tidak dapat dibatalkan!')) { document.getElementById('bulkActionTypeKitab').value='delete'; document.getElementById('bulkActionFormKitab').submit(); }"
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
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Nama Kitab</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Bidang Ilmu</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Pengarang</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($kitabs as $kitab)
                                        <tr class="group hover:bg-gray-100 transition-colors"
                        :class="selectedIds.includes({{ $kitab->id }}) ? 'bg-red-50/70' : ''">
                        <td class="px-4 py-3 w-10 transition-opacity duration-200"
                            :class="selectedIds.includes({{ $kitab->id }}) || selectedIds.length > 0 ? 'opacity-100' : 'opacity-0 group-hover:opacity-100 focus-within:opacity-100'">
                            <input type="checkbox"
                                   :checked="selectedIds.includes({{ $kitab->id }})"
                                   @change="
                                       if ($event.target.checked) {
                                           if (!selectedIds.includes({{ $kitab->id }})) selectedIds.push({{ $kitab->id }});
                                       } else {
                                           selectedIds = selectedIds.filter(id => id !== {{ $kitab->id }});
                                           this.selectAllPages = false;
                                       }
                                   "
                                   class="rounded border-gray-300 text-red-600 focus:ring-red-400 cursor-pointer w-4 h-4">
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $loop->iteration + ($kitabs->currentPage()-1)*$kitabs->perPage() }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $kitab->nama_kitab }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $kitab->bidang_ilmu ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ Str::limit($kitab->pengarang, 50) ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1">
                                <a href="{{ route('admin.kitab.show', $kitab) }}" class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors" title="Detail"><i class="fas fa-eye text-sm"></i></a>
                                <a href="{{ route('admin.kitab.edit', $kitab) }}" class="p-1.5 rounded-lg text-biru hover:bg-biru/10 transition-colors" title="Edit"><i class="fas fa-edit text-sm"></i></a>
                                <form method="POST" action="{{ route('admin.kitab.destroy', $kitab) }}" class="inline"
                                      onsubmit="return confirm('Yakin hapus kitab ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-merah hover:bg-red-50 transition-colors"><i class="fas fa-trash text-sm"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center text-gray-400">
                            <i class="fas fa-book text-4xl mb-3 block text-gray-200"></i>
                            Tidak ada data kitab.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @if($kitabs->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-white/40">
                {{ $kitabs->appends(request()->query())->links() }}
            </div>
            @endif
        </div>

        {{-- MOBILE VIEW --}}
        <div class="md:hidden flex items-center gap-2 mb-4 bg-white p-3 rounded-xl shadow-sm border border-gray-100 sticky top-0 z-30">
            <form method="GET" class="flex-1 relative flex items-center">
                <i class="fas fa-search absolute left-3 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama kitab..."
                       class="w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent bg-gray-50/50">
                <input type="hidden" name="bidang_ilmu" value="{{ request('bidang_ilmu') }}">
            </form>
            <button type="button" @click="showFilterModal = true" class="p-2 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 flex-shrink-0 flex items-center justify-center w-10 h-10 bg-white">
                <i class="fas fa-sliders-h"></i>
            </button>
        </div>

        <div class="md:hidden flex items-center justify-between mb-3 px-1">
            <div>
                <h2 class="text-sm font-bold text-gray-800">Data Kitab</h2>
                <p class="text-[10px] text-gray-500">Total {{ $kitabs->total() }} Kitab terdaftar</p>
            </div>
            <a href="{{ route('admin.kitab.create') }}"
               class="inline-flex items-center gap-1.5 bg-[#187a41] text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-green-700 transition-colors shadow-sm">
                <i class="fas fa-plus"></i> Tambah
            </a>
        </div>

        <div class="md:hidden space-y-0 bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 overflow-hidden">
            @forelse($kitabs as $kitab)
            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-3 flex-1 pl-3">
                    <div class="w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-book text-amber-600 text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0 pr-2">
                        <h4 class="text-xs font-bold text-gray-800 truncate">{{ $kitab->nama_kitab }}</h4>
                        <div class="text-[10px] text-gray-500 mt-0.5 flex items-center gap-1 flex-wrap">
                            <span>{{ $kitab->pengarang ?? '-' }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col items-end justify-between self-stretch py-1 pr-3">
                    @if($kitab->bidang_ilmu)
                    <span class="px-2 py-0.5 text-[9px] font-bold rounded-full bg-amber-50 text-amber-700 border border-amber-200 tracking-wide whitespace-nowrap">
                        {{ $kitab->bidang_ilmu }}
                    </span>
                    @else
                    <span class="px-2 py-0.5 text-[9px] font-bold rounded-full bg-gray-100 text-gray-500 tracking-wide">-</span>
                    @endif
                    <div x-data="{ open: false }" class="relative mt-1">
                        <button @click="open = !open" @click.away="open = false" class="text-gray-400 hover:text-gray-700 p-1 rounded">
                            <i class="fas fa-ellipsis-v text-xs px-1"></i>
                        </button>
                        <div x-show="open" x-transition.opacity class="absolute right-0 top-full mt-1 w-32 bg-white rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.1)] border border-gray-100 py-1.5 z-20" style="display:none">
                            <a href="{{ route('admin.kitab.show', $kitab) }}" class="block px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 hover:text-[#187a41]"><i class="fas fa-eye w-4"></i> Detail</a>
                            <a href="{{ route('admin.kitab.edit', $kitab) }}" class="block px-4 py-2 text-xs font-medium text-blue-600 hover:bg-gray-50"><i class="fas fa-edit w-4"></i> Edit</a>
                            <form method="POST" action="{{ route('admin.kitab.destroy', $kitab) }}" onsubmit="return confirm('Yakin hapus kitab ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="block w-full text-left px-4 py-2 text-xs font-medium text-red-600 hover:bg-gray-50"><i class="fas fa-trash w-4"></i> Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-10">
                <i class="fas fa-book text-4xl text-gray-200 mb-2"></i>
                <p class="text-xs text-gray-500">Belum ada data kitab</p>
            </div>
            @endforelse
        </div>

        {{-- Pagination Mobile --}}
        @if($kitabs->hasPages())
        <div class="md:hidden mt-4 pb-8 px-1">
            <div class="flex items-center justify-between bg-white rounded-xl shadow-sm border border-gray-100 p-2">
                @if ($kitabs->onFirstPage())
                    <span class="p-2 w-10 h-10 flex items-center justify-center rounded-lg text-gray-300"><i class="fas fa-chevron-left"></i></span>
                @else
                    <a href="{{ $kitabs->appends(request()->query())->previousPageUrl() }}" class="p-2 w-10 h-10 flex items-center justify-center rounded-lg text-[#187a41] hover:bg-green-50 transition-colors"><i class="fas fa-chevron-left"></i></a>
                @endif
                <span class="text-xs font-semibold text-gray-600">Hal {{ $kitabs->currentPage() }} dari {{ $kitabs->lastPage() }}</span>
                @if ($kitabs->hasMorePages())
                    <a href="{{ $kitabs->appends(request()->query())->nextPageUrl() }}" class="p-2 w-10 h-10 flex items-center justify-center rounded-lg text-[#187a41] hover:bg-green-50 transition-colors"><i class="fas fa-chevron-right"></i></a>
                @else
                    <span class="p-2 w-10 h-10 flex items-center justify-center rounded-lg text-gray-300"><i class="fas fa-chevron-right"></i></span>
                @endif
            </div>
        </div>
        @endif

        {{-- Filter Modal Mobile --}}
        <div x-show="showFilterModal" x-effect="document.body.style.overflow = showFilterModal ? 'hidden' : ''" class="md:hidden fixed inset-0 z-50 flex flex-col justify-end bg-black/50" x-transition.opacity style="display:none">
            <div @click.away="showFilterModal = false" class="bg-white w-full rounded-t-2xl shadow-xl overflow-hidden"
                 x-show="showFilterModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full">
                <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-gray-800">Filter Bidang Ilmu</h3>
                    <button @click="showFilterModal = false" class="text-gray-400 bg-gray-50 rounded-full w-8 h-8 flex items-center justify-center"><i class="fas fa-times"></i></button>
                </div>
                <div class="p-5">
                    <form method="GET" action="{{ route('admin.kitab.index') }}">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <div class="grid grid-cols-2 gap-2 max-h-48 overflow-y-auto mb-4">
                            <label class="flex items-center gap-2 p-2 border rounded-lg cursor-pointer {{ request('bidang_ilmu') == '' ? 'border-[#187a41] bg-green-50' : 'border-gray-200' }}">
                                <input type="radio" name="bidang_ilmu" value="" class="hidden" {{ request('bidang_ilmu') == '' ? 'checked' : '' }}>
                                <span class="text-xs font-medium {{ request('bidang_ilmu') == '' ? 'text-[#187a41]' : 'text-gray-600' }}">Semua</span>
                            </label>
                            @foreach($bidangIlmuList as $k)
                            <label class="flex items-center gap-2 p-2 border rounded-lg cursor-pointer {{ request('bidang_ilmu') == $k ? 'border-[#187a41] bg-green-50' : 'border-gray-200' }}">
                                <input type="radio" name="bidang_ilmu" value="{{ $k }}" class="hidden" {{ request('bidang_ilmu') == $k ? 'checked' : '' }}>
                                <span class="text-xs font-medium {{ request('bidang_ilmu') == $k ? 'text-[#187a41]' : 'text-gray-600' }}">{{ $k }}</span>
                            </label>
                            @endforeach
                        </div>
                        <div class="flex gap-2">
                            <button type="button" @click="window.location.href='{{ route('admin.kitab.index') }}'" class="flex-1 py-2.5 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Reset Filter</button>
                            <button type="submit" class="flex-1 py-2.5 text-xs font-semibold text-white bg-[#187a41] hover:bg-green-700 rounded-xl transition-colors shadow-sm">Terapkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('form input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                const name = e.target.name;
                const form = e.target.closest('form');
                form.querySelectorAll('input[name="' + name + '"]').forEach(r => {
                    const label = r.closest('label');
                    const span = label.querySelector('span');
                    if (label && span) {
                        if (r.checked) {
                            label.classList.add('border-[#187a41]', 'bg-green-50');
                            label.classList.remove('border-gray-200');
                            span.classList.add('text-[#187a41]');
                            span.classList.remove('text-gray-600');
                        } else {
                            label.classList.remove('border-[#187a41]', 'bg-green-50');
                            label.classList.add('border-gray-200');
                            span.classList.remove('text-[#187a41]');
                            span.classList.add('text-gray-600');
                        }
                    }
                });
            });
        });
    });
</script>
</x-app-layout>



