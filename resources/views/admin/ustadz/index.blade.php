<x-app-layout>
    <x-slot name="header">Data Ustadz</x-slot>

    <div class="space-y-4 pb-10 md:pb-0" x-data="{ 
        showFilterModal: false, 
        filterStatus: '{{ request('status_aktif', '') }}',
        selectedIds: [],
        selectAllPages: false,
        isPageAllChecked: false,
        allIds: {{ json_encode($ustadzs->pluck('id')) }},
        pageIds: {{ json_encode($ustadzs->pluck('id')) }},
        
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

        {{-- ========================================== --}}
        {{--                DESKTOP VIEW                --}}
        {{-- ========================================== --}}
        {{-- Header Bar Desktop --}}
        <div class="hidden md:flex flex-col sm:flex-row items-start sm:items-end justify-between gap-3 mb-4">
            <form method="GET" class="flex items-end gap-2">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1 pl-1">Cari Ustadz</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Nama / NIP..."
                        class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent bg-white/80 w-48 h-[38px]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1 pl-1">Status Aktif</label>
                    <x-custom-select name="status_aktif" :options="['1'=>'Aktif', '0'=>'Tidak Aktif']" :selected="request('status_aktif')" placeholder="Semua Status" />
                </div>
                <button type="submit" class="bg-hijau-tua text-white px-4 py-2 rounded-lg text-sm hover:bg-hijau-muda transition-colors h-[38px] flex items-center gap-1.5">
                    <i class="fas fa-search"></i> Tampilkan
                </button>
            </form>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.ustadz.create') }}"
                   class="inline-flex items-center gap-2 bg-hijau-tua text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-hijau-muda transition-colors shadow-sm h-[38px]">
                    <i class="fas fa-plus"></i> Tambah Ustadz
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
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Foto</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Username</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">No. HP</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($ustadzs as $ustadz)
                    <tr class="group hover:bg-gray-100 transition-colors"
                        :class="selectedIds.includes({{ $ustadz->id }}) ? 'bg-red-50/70' : ''">
                        <td class="px-4 py-3 w-10 transition-opacity duration-200"
                            :class="selectedIds.includes({{ $ustadz->id }}) || selectedIds.length > 0 ? 'opacity-100' : 'opacity-0 group-hover:opacity-100 focus-within:opacity-100'">
                            <input type="checkbox"
                                   :checked="selectedIds.includes({{ $ustadz->id }})"
                                   @change="if($event.target.checked) { selectedIds.push({{ $ustadz->id }}) } else { selectedIds = selectedIds.filter(id => id !== {{ $ustadz->id }}); selectAllPages = false; isPageAllChecked = false; }"
                                   class="rounded border-gray-300 text-hijau-muda focus:ring-hijau-muda cursor-pointer">
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $loop->iteration + ($ustadzs->currentPage()-1)*$ustadzs->perPage() }}</td>
                        <td class="px-4 py-3">
                            <img src="{{ $ustadz->user->foto_profil ? asset('storage/'.$ustadz->user->foto_profil) : 'https://ui-avatars.com/api/?name='.urlencode($ustadz->user->name).'&color=14532D&background=F1F5F9' }}"
                                 class="w-9 h-9 rounded-full object-cover border-2 border-hijau-muda/30" alt="">
                        </td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $ustadz->user->name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $ustadz->user->username }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $ustadz->user->no_hp ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @if($ustadz->user->status_aktif)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-hijau-muda/20 text-green-800">Aktif</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">Non-Aktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1">
                                <a href="{{ route('admin.ustadz.show', $ustadz) }}" class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors" title="Detail"><i class="fas fa-eye text-sm"></i></a>
                                <a href="{{ route('admin.ustadz.edit', $ustadz) }}" class="p-1.5 rounded-lg text-biru hover:bg-biru/10 transition-colors" title="Edit"><i class="fas fa-edit text-sm"></i></a>
                                <form method="POST" action="{{ route('admin.ustadz.destroy', $ustadz) }}" class="inline"
                                      onsubmit="return confirm('Yakin hapus data ustadz ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-merah hover:bg-red-50 transition-colors" title="Hapus"><i class="fas fa-trash text-sm"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                            <i class="fas fa-chalkboard-teacher text-4xl mb-3 block text-gray-200"></i>
                            Tidak ada data ustadz.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @if($ustadzs->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-white/40">
                {{ $ustadzs->appends(request()->query())->links() }}
            </div>
            @endif
        </div>


        {{-- ========================================== --}}
        {{--                MOBILE VIEW                 --}}
        {{-- ========================================== --}}

        {{-- Top section: Search and Filter Mobile --}}
        <div class="md:hidden flex items-center gap-2 mb-4 bg-white p-3 rounded-xl shadow-sm border border-gray-100 sticky top-0 z-30">
            <form method="GET" class="flex-1 relative flex items-center">
                <i class="fas fa-search absolute left-3 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama ustadz..."
                       class="w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent bg-gray-50/50">
                <input type="hidden" name="status_aktif" value="{{ request('status_aktif') }}">
            </form>
            <button type="button" @click="showFilterModal = true" class="p-2 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 flex-shrink-0 flex items-center justify-center w-10 h-10 bg-white">
                <i class="fas fa-sliders-h"></i>
            </button>
        </div>

        {{-- Admin Actions Mobile --}}
        <div class="md:hidden flex items-center justify-end gap-2 mb-4">
            <a href="{{ route('admin.ustadz.create') }}"
               class="inline-flex items-center gap-2 bg-[#187a41] text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-green-700 transition-colors shadow-sm">
                <i class="fas fa-plus"></i> Tambah
            </a>
        </div>

        {{-- Title & Count Mobile --}}
        <div class="md:hidden flex items-center justify-between mb-3 px-1">
            <div>
                <h2 class="text-sm font-bold text-gray-800">Data Ustadz</h2>
                <p class="text-[10px] text-gray-500">Total {{ $ustadzs->total() }} Ustadz terdaftar</p>
            </div>
            @if(request()->filled('status_aktif'))
               <span class="px-3 py-1 text-[10px] font-semibold rounded-full bg-green-50 text-green-700 border border-green-200">
                   {{ request('status_aktif') == '1' ? 'Aktif' : 'Non-Aktif' }}
                   <a href="{{ route('admin.ustadz.index', ['search' => request('search')]) }}" class="ml-1 text-red-500 hover:text-red-700"><i class="fas fa-times"></i></a>
               </span>
            @endif
        </div>

        {{-- List of Ustadz Mobile --}}
        <div class="md:hidden space-y-0 relative bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 overflow-hidden">
            @forelse($ustadzs as $ustadz)
            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors relative"
                 x-data="{ pressTimer: null }"
                 @touchstart="pressTimer = setTimeout(() => { if(!selectedIds.includes({{ $ustadz->id }})) { selectedIds.push({{ $ustadz->id }}) } }, 500)"
                 @touchend="clearTimeout(pressTimer)"
                 @touchmove="clearTimeout(pressTimer)"
                 :class="selectedIds.includes({{ $ustadz->id }}) ? 'bg-red-50' : ''">
                
                {{-- Checkbox for Mobile --}}
                <div class="absolute left-2 top-1/2 -translate-y-1/2 z-10"
                     x-show="selectedIds.length > 0" style="display: none;"
                     x-transition.opacity>
                    <input type="checkbox"
                           :checked="selectedIds.includes({{ $ustadz->id }})"
                           @change="if($event.target.checked) { selectedIds.push({{ $ustadz->id }}) } else { selectedIds = selectedIds.filter(id => id !== {{ $ustadz->id }}); selectAllPages = false; isPageAllChecked = false; }"
                           class="rounded border-gray-300 text-[#187a41] focus:ring-[#187a41] shadow-sm w-5 h-5">
                </div>
                <div class="flex items-center gap-3 flex-1 pl-3 transition-transform duration-300"
                     :class="selectedIds.length > 0 ? 'translate-x-8' : ''">
                    <img src="{{ $ustadz->user->foto_profil ? asset('storage/'.$ustadz->user->foto_profil) : 'https://ui-avatars.com/api/?name='.urlencode($ustadz->user->name).'&color=14532D&background=F1F5F9' }}"
                         class="w-10 h-10 sm:w-12 sm:h-12 rounded-full object-cover bg-gray-100 flex-shrink-0" alt="">
                    <div class="flex-1 min-w-0 pr-2">
                        <h4 class="text-xs sm:text-sm font-bold text-gray-800 truncate" title="{{ $ustadz->user->name }}">
                            {{ $ustadz->user->name }}
                        </h4>
                        <div class="text-[10px] sm:text-[11px] text-gray-500 mt-0.5 flex items-center gap-1">
                            <span>Username: {{ $ustadz->user->username }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col items-end justify-between self-stretch py-1 pr-3">
                    @if($ustadz->user->status_aktif)
                        <span class="px-2 py-0.5 text-[9px] font-bold rounded-full bg-[#187a41] text-white tracking-wide">Aktif</span>
                    @else
                        <span class="px-2 py-0.5 text-[9px] font-bold rounded-full bg-gray-500 text-white tracking-wide">Non-Aktif</span>
                    @endif
                    
                    {{-- Action Menu Dropdown --}}
                    <div x-data="{ open: false }" class="relative mt-1">
                        <button @click="open = !open" @click.away="open = false" class="text-gray-400 hover:text-gray-700 p-1 rounded">
                            <i class="fas fa-ellipsis-v text-xs px-1"></i>
                        </button>
                        <div x-show="open" x-transition.opacity
                             class="absolute right-0 top-full mt-1 w-32 bg-white rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.1)] border border-gray-100 py-1.5 z-20" 
                             style="display: none;">
                            <a href="{{ route('admin.ustadz.show', $ustadz) }}" class="block px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 hover:text-[#187a41]">
                                <i class="fas fa-eye w-4"></i> Detail
                            </a>
                            <a href="{{ route('admin.ustadz.edit', $ustadz) }}" class="block px-4 py-2 text-xs font-medium text-blue-600 hover:bg-gray-50 hover:text-blue-700">
                                <i class="fas fa-edit w-4"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('admin.ustadz.destroy', $ustadz) }}" onsubmit="return confirm('Yakin hapus data ustadz ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="block w-full text-left px-4 py-2 text-xs font-medium text-red-600 hover:bg-gray-50 hover:text-red-700">
                                    <i class="fas fa-trash w-4"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-10 bg-white rounded-xl border border-gray-100">
                <i class="fas fa-chalkboard-teacher text-4xl text-gray-200 mb-2"></i>
                <p class="text-xs text-gray-500">Belum ada data ustadz</p>
            </div>
            @endforelse
        </div>

        {{-- Pagination Mobile --}}
        @if($ustadzs->hasPages())
        <div class="md:hidden mt-6 pb-8 px-1">
            <div class="flex items-center justify-between bg-white rounded-xl shadow-sm border border-gray-100 p-2">
                @if ($ustadzs->onFirstPage())
                    <span class="p-2 w-10 h-10 flex items-center justify-center rounded-lg text-gray-300">
                        <i class="fas fa-chevron-left"></i>
                    </span>
                @else
                    <a href="{{ $ustadzs->appends(request()->query())->previousPageUrl() }}" class="p-2 w-10 h-10 flex items-center justify-center rounded-lg text-[#187a41] hover:bg-green-50 transition-colors">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                @endif
                
                <span class="text-xs font-semibold text-gray-600">
                    Hal {{ $ustadzs->currentPage() }} dari {{ $ustadzs->lastPage() }}
                </span>
                
                @if ($ustadzs->hasMorePages())
                    <a href="{{ $ustadzs->appends(request()->query())->nextPageUrl() }}" class="p-2 w-10 h-10 flex items-center justify-center rounded-lg text-[#187a41] hover:bg-green-50 transition-colors">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                @else
                    <span class="p-2 w-10 h-10 flex items-center justify-center rounded-lg text-gray-300">
                        <i class="fas fa-chevron-right"></i>
                    </span>
                @endif
            </div>
        </div>
        @endif

        {{-- Filter Modal (Mobile Only) --}}
        <div x-show="showFilterModal" x-effect="document.body.style.overflow = showFilterModal ? 'hidden' : ''" class="md:hidden fixed inset-0 z-50 flex flex-col justify-end sm:items-center sm:justify-center bg-black/50" x-transition.opacity style="display: none;">
            <div @click.away="showFilterModal = false" 
                 class="bg-white w-full sm:w-auto sm:min-w-[400px] rounded-t-2xl sm:rounded-2xl shadow-xl overflow-hidden transform transition-transform duration-300" 
                 x-show="showFilterModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="translate-y-full sm:translate-y-0 sm:scale-95 sm:opacity-0"
                 x-transition:enter-end="translate-y-0 sm:scale-100 sm:opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="translate-y-0 sm:scale-100 sm:opacity-100"
                 x-transition:leave-end="translate-y-full sm:translate-y-0 sm:scale-95 sm:opacity-0">
                
                <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-gray-800">Filter Data</h3>
                    <button @click="showFilterModal = false" class="text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-full w-8 h-8 flex items-center justify-center transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-5">
                    <form method="GET" action="{{ route('admin.ustadz.index') }}">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <div class="mb-5">
                            <label class="block text-xs font-semibold text-gray-700 mb-2">Pilih Status</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label :class="filterStatus == '' ? 'border-[#187a41] bg-green-50' : 'border-gray-200 hover:bg-gray-50'" class="flex items-center gap-2 p-2 border rounded-lg cursor-pointer transition-colors">
                                    <input type="radio" name="status_aktif" value="" x-model="filterStatus" class="hidden">
                                    <span :class="filterStatus == '' ? 'text-[#187a41]' : 'text-gray-600'" class="text-xs font-medium">Semua</span>
                                </label>
                                <label :class="filterStatus == '1' ? 'border-[#187a41] bg-green-50' : 'border-gray-200 hover:bg-gray-50'" class="flex items-center gap-2 p-2 border rounded-lg cursor-pointer transition-colors">
                                    <input type="radio" name="status_aktif" value="1" x-model="filterStatus" class="hidden">
                                    <span :class="filterStatus == '1' ? 'text-[#187a41]' : 'text-gray-600'" class="text-xs font-medium">Aktif</span>
                                </label>
                                <label :class="filterStatus == '0' ? 'border-[#187a41] bg-green-50' : 'border-gray-200 hover:bg-gray-50'" class="flex items-center gap-2 p-2 border rounded-lg cursor-pointer transition-colors">
                                    <input type="radio" name="status_aktif" value="0" x-model="filterStatus" class="hidden">
                                    <span :class="filterStatus == '0' ? 'text-[#187a41]' : 'text-gray-600'" class="text-xs font-medium">Non-Aktif</span>
                                </label>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" @click="window.location.href='{{ route('admin.ustadz.index') }}'" class="flex-1 py-2.5 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Reset Filter</button>
                            <button type="submit" class="flex-1 py-2.5 text-xs font-semibold text-white bg-[#187a41] hover:bg-green-700 rounded-xl transition-colors shadow-sm">Terapkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


</x-app-layout>



