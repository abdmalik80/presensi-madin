<x-app-layout>
    <x-slot name="header">Data Santri</x-slot>

    @php
    $majelisOptions = []; foreach($majelisList as $m) $majelisOptions[$m->id] = $m->nama_majelis;
    @endphp


    @php $allIdsJson = json_encode($allIds); @endphp

    <div class="space-y-4"
         x-data="{
             showImportModal: false,
             showFilterModal: false,
             filterMajelis: '{{ request('majelis_id', '') }}',
             selectedIds: [],
             pageIds: {{ json_encode($santris->pluck('id')->toArray()) }},
             allIds: {{ $allIdsJson }},
             selectAllPages: false,
             get isPageAllChecked() {
                 return this.pageIds.length > 0 && this.pageIds.every(id => this.selectedIds.includes(id));
             },
             togglePageAll(val) {
                 if (val) {
                     this.pageIds.forEach(id => { if (!this.selectedIds.includes(id)) this.selectedIds.push(id); });
                 } else {
                     this.selectedIds = this.selectedIds.filter(id => !this.pageIds.includes(id));
                     this.selectAllPages = false;
                 }
             },
             selectEveryPage() {
                 this.selectedIds = [...this.allIds];
                 this.selectAllPages = true;
             },
             clearAll() {
                 this.selectedIds = [];
                 this.selectAllPages = false;
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
                    <label class="block text-xs font-semibold text-gray-600 mb-1 pl-1">Cari Santri</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Nama / NIS..."
                        class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent bg-white/80 w-48 h-[38px]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1 pl-1">Majelis</label>
                    <x-custom-select name="majelis_id" :options="$majelisOptions" :selected="request('majelis_id')" placeholder="Semua Majelis" />
                </div>
                <button type="submit" class="bg-hijau-tua text-white px-4 py-2 rounded-lg text-sm hover:bg-hijau-muda transition-colors h-[38px] flex items-center gap-1.5">
                    <i class="fas fa-search"></i> Tampilkan
                </button>
            </form>
            <div class="flex items-center gap-2">
                <button @click="showImportModal = true" type="button"
                   class="inline-flex items-center gap-2 bg-blue-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-blue-500 transition-colors shadow-sm h-[38px]">
                    <i class="fas fa-file-import"></i> Import Santri
                </button>
                <a href="{{ route('admin.santri.create') }}"
                   class="inline-flex items-center gap-2 bg-hijau-tua text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-hijau-muda transition-colors shadow-sm h-[38px]">
                    <i class="fas fa-plus"></i> Tambah Santri
                </a>
            </div>
        </div>

        {{-- Table Desktop --}}
        {{-- Bulk Action Toolbar (Shared) --}}
        <div x-show="selectedIds.length > 0" style="display: none;" x-transition
             class="mb-4 bg-red-50 border border-red-100 rounded-xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4"
             >
            <div>
                <p class="text-sm font-semibold text-red-700">
                    <i class="fas fa-check-square mr-1"></i>
                    <span x-text="selectedIds.length"></span>
                    <span x-text="selectAllPages ? ' santri (semua data) dipilih' : ' santri dipilih'"></span>
                </p>
                {{-- Tawaran pilih semua halaman --}}
                <p x-show="isPageAllChecked && !selectAllPages && allIds.length > pageIds.length"
                   class="text-xs text-red-600 mt-0.5">
                    Semua {{ $santris->count() }} santri di halaman ini dipilih.
                    <button type="button" @click="selectEveryPage()"
                            class="font-bold underline hover:text-red-800 ml-1">
                        Pilih semua {{ count($allIds) }} santri?
                    </button>
                </p>
                <p x-show="selectAllPages" class="text-xs text-red-600 mt-0.5">
                    Semua {{ count($allIds) }} santri dipilih.
                    <button type="button" @click="clearAll()" class="font-bold underline hover:text-red-800 ml-1">Batalkan pilihan</button>
                </p>
            </div>
                        <form method="POST" action="{{ route('admin.santri.bulk-action') }}" id="bulkActionForm">
                @csrf
                <input type="hidden" name="action" id="bulkActionType" value="">
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>

                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" @click="if(confirm('Yakin set aktif ' + selectedIds.length + ' santri?')) { document.getElementById('bulkActionType').value='active'; document.getElementById('bulkActionForm').submit(); }"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-600 text-white text-xs font-semibold hover:bg-green-700 transition-colors shadow-sm">
                        <i class="fas fa-check-circle"></i> Set Aktif
                    </button>
                    <button type="button" @click="if(confirm('Yakin set non-aktif ' + selectedIds.length + ' santri?')) { document.getElementById('bulkActionType').value='inactive'; document.getElementById('bulkActionForm').submit(); }"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-orange-500 text-white text-xs font-semibold hover:bg-orange-600 transition-colors shadow-sm">
                        <i class="fas fa-times-circle"></i> Set Non-Aktif
                    </button>
                    <div class="hidden sm:block w-px h-5 bg-red-200 mx-1"></div>
                    <button type="button" @click="if(confirm('Yakin hapus ' + selectedIds.length + ' santri? Tindakan ini tidak dapat dibatalkan!')) { document.getElementById('bulkActionType').value='delete'; document.getElementById('bulkActionForm').submit(); }"
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
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Foto</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">NIS</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Majelis</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">No HP</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($santris as $santri)
                                                            <tr class="group hover:bg-gray-100 transition-colors" 
                        :class="selectedIds.includes({{ $santri->id }}) ? 'bg-red-50/70' : ''">
                        <td class="px-4 py-3 w-10 transition-opacity duration-200"
                            :class="selectedIds.includes({{ $santri->id }}) || selectedIds.length > 0 ? 'opacity-100' : 'opacity-0 group-hover:opacity-100 focus-within:opacity-100'">
                            <input type="checkbox"
                                   :checked="selectedIds.includes({{ $santri->id }})"
                                   @change="
                                       if ($event.target.checked) {
                                           if (!selectedIds.includes({{ $santri->id }})) selectedIds.push({{ $santri->id }});
                                       } else {
                                           selectedIds = selectedIds.filter(id => id !== {{ $santri->id }});
                                           selectAllPages = false;
                                       }
                                   "
                                   class="cb-santri rounded border-gray-300 text-red-600 focus:ring-red-400 cursor-pointer w-4 h-4">
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $loop->iteration + ($santris->currentPage()-1)*$santris->perPage() }}</td>
                        <td class="px-4 py-3">
                            <img src="{{ $santri->user->foto_profil ? asset('storage/'.$santri->user->foto_profil) : 'https://ui-avatars.com/api/?name='.urlencode($santri->user->name).'&color=14532D&background=F1F5F9' }}"
                                 class="w-9 h-9 rounded-full object-cover border-2 border-hijau-muda/30" alt="">
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            @if($santri->nis && in_array($santri->nis, $duplicateNis))
                                <span class="text-red-600 font-bold bg-red-50 px-2 py-1 rounded border border-red-200" title="Peringatan: NIS Ganda / Duplikat">
                                    {{ $santri->nis }}
                                    <i class="fas fa-exclamation-triangle text-xs ml-1"></i>
                                </span>
                            @else
                                {{ $santri->nis ?? '-' }}
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $santri->user->name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $santri->majelis->nama_majelis ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $santri->user->no_hp ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @if($santri->user->status_aktif)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-hijau-muda/20 text-green-800">Aktif</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">Non-Aktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1">
                                <a href="{{ route('admin.santri.show', $santri) }}" class="p-1.5 rounded-lg text-hijau-tua hover:bg-hijau-muda/20 transition-colors" title="Detail"><i class="fas fa-eye text-sm"></i></a>
                                <a href="{{ route('admin.santri.edit', $santri) }}" class="p-1.5 rounded-lg text-biru hover:bg-biru/10 transition-colors" title="Edit"><i class="fas fa-edit text-sm"></i></a>
                                <form method="POST" action="{{ route('admin.santri.destroy', $santri) }}" class="inline"
                                      onsubmit="return confirm('Yakin hapus data santri ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-merah hover:bg-red-50 transition-colors" title="Hapus"><i class="fas fa-trash text-sm"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-12 text-center text-gray-400">
                            <i class="fas fa-users text-4xl mb-3 block text-gray-200"></i>
                            Tidak ada data santri.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>


        {{-- ========================================== --}}
        {{--                MOBILE VIEW                 --}}
        {{-- ========================================== --}}

        {{-- Top section: Search and Filter Mobile --}}
        <div class="md:hidden flex items-center gap-2 mb-4 bg-white p-3 rounded-xl shadow-sm border border-gray-100 sticky top-0 z-30">
            <form method="GET" class="flex-1 relative flex items-center">
                <i class="fas fa-search absolute left-3 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama santri..."
                       class="w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent bg-gray-50/50">
                <input type="hidden" name="majelis_id" value="{{ request('majelis_id') }}">
            </form>
            <button type="button" @click="showFilterModal = true" class="p-2 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 flex-shrink-0 flex items-center justify-center w-10 h-10 bg-white">
                <i class="fas fa-sliders-h"></i>
            </button>
        </div>

        {{-- Admin Actions Mobile --}}
        <div class="md:hidden flex items-center justify-end gap-2 mb-4">
            <button @click="showImportModal = true" type="button"
               class="inline-flex items-center gap-2 bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-blue-500 transition-colors shadow-sm">
                <i class="fas fa-file-import"></i> Import
            </button>
            <a href="{{ route('admin.santri.create') }}"
               class="inline-flex items-center gap-2 bg-[#187a41] text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-green-700 transition-colors shadow-sm">
                <i class="fas fa-plus"></i> Tambah
            </a>
        </div>

        {{-- Title & Count Mobile --}}
        <div class="md:hidden flex items-center justify-between mb-3 px-1">
            <div>
                <h2 class="text-sm font-bold text-gray-800">Data Santri</h2>
                <p class="text-[10px] text-gray-500">Total {{ $santris->total() }} Santri terdaftar</p>
            </div>
            @if(request('majelis_id'))
               @php $selectedMajelis = $majelisList->firstWhere('id', request('majelis_id')); @endphp
               @if($selectedMajelis)
               <span class="px-3 py-1 text-[10px] font-semibold rounded-full bg-green-50 text-green-700 border border-green-200">
                   {{ $selectedMajelis->nama_majelis }}
                   <a href="{{ route('admin.santri.index', ['search' => request('search')]) }}" class="ml-1 text-red-500 hover:text-red-700"><i class="fas fa-times"></i></a>
               </span>
               @endif
            @endif
        </div>

        {{-- List of Santri Mobile --}}
        <div class="md:hidden space-y-2.5 relative">
            @forelse($santris as $santri)
            <div x-data="{ open: false, timer: null }"
                   @touchstart="timer = setTimeout(() => { if (!selectedIds.includes({{ $santri->id }})) { selectedIds.push({{ $santri->id }}); if (navigator.vibrate) navigator.vibrate(50); } }, 500)"
                   @touchend="clearTimeout(timer)"
                   @touchmove="clearTimeout(timer)"
                   class="bg-white/80 backdrop-blur-sm rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all relative"
                 :class="[selectedIds.includes({{ $santri->id }}) ? 'ring-2 ring-red-400 bg-red-50/40' : '', open ? 'z-50' : 'z-0']">

                {{-- Bulk Select Checkbox --}}
                <div class="absolute top-3 left-3 z-10"
                     x-show="selectedIds.length > 0 || isPageAllChecked">
                    <input type="checkbox"
                           :checked="selectedIds.includes({{ $santri->id }})"
                           @change="
                               if ($event.target.checked) {
                                   if (!selectedIds.includes({{ $santri->id }})) selectedIds.push({{ $santri->id }});
                               } else {
                                   selectedIds = selectedIds.filter(id => id !== {{ $santri->id }});
                                   selectAllPages = false;
                               }
                           "
                           class="rounded border-gray-300 text-red-600 focus:ring-red-400 cursor-pointer w-4 h-4">
                </div>

                <div class="flex items-center gap-3 p-3">
                    {{-- Avatar --}}
                    <img src="{{ $santri->user->foto_profil ? asset('storage/'.$santri->user->foto_profil) : 'https://ui-avatars.com/api/?name='.urlencode($santri->user->name).'&color=14532D&background=F1F5F9' }}"
                         class="w-11 h-11 rounded-full object-cover border-2 border-white shadow-sm flex-shrink-0" alt="">

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-bold text-gray-800 truncate" title="{{ $santri->user->name }}">
                            {{ $santri->user->name }}
                        </h4>
                        <div class="text-[11px] text-gray-500 mt-0.5 flex items-center gap-1 flex-wrap">
                            @if($santri->nis && in_array($santri->nis, $duplicateNis))
                                <span class="text-red-500 font-medium">NIS: {{ $santri->nis }} <i class="fas fa-exclamation-triangle text-[9px]"></i></span>
                            @else
                                <span class="font-mono">{{ $santri->nis ?? '-' }}</span>
                            @endif
                            <span class="text-gray-300">&bull;</span>
                            <span class="truncate">{{ $santri->majelis->nama_majelis ?? '-' }}</span>
                        </div>
                    </div>

                    {{-- Status + Action --}}
                    <div class="flex flex-col items-end gap-2 flex-shrink-0">
                        @if($santri->user->status_aktif)
                            <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-green-100 text-green-800 border border-green-200">Aktif</span>
                        @else
                            <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-gray-100 text-gray-600 border border-gray-200">Non-Aktif</span>
                        @endif

                        {{-- Dropdown Aksi --}}
                        <div class="relative">
                            <button @click="open = !open" @click.away="open = false"
                                    class="w-7 h-7 flex items-center justify-center rounded-lg bg-gray-100 text-gray-500 hover:bg-gray-200 transition-colors">
                                <i class="fas fa-ellipsis-v text-xs"></i>
                            </button>
                            <div x-show="open" x-transition.opacity
                                 class="absolute right-0 top-full mt-1 w-32 bg-white rounded-xl shadow-lg border border-gray-100 py-1.5"
                                 style="display:none; z-index: 100;">
                                <a href="{{ route('admin.santri.show', $santri) }}"
                                   class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 hover:text-hijau-tua">
                                    <i class="fas fa-eye w-3"></i> Detail
                                </a>
                                <a href="{{ route('admin.santri.edit', $santri) }}"
                                   class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-blue-600 hover:bg-blue-50">
                                    <i class="fas fa-edit w-3"></i> Edit
                                </a>
                                <form method="POST" action="{{ route('admin.santri.destroy', $santri) }}"
                                      onsubmit="return confirm('Yakin hapus data santri ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="w-full flex items-center gap-2 px-4 py-2 text-xs font-medium text-red-600 hover:bg-red-50">
                                        <i class="fas fa-trash w-3"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-10 bg-white rounded-xl border border-gray-100">
                <i class="fas fa-users text-4xl text-gray-200 mb-2"></i>
                <p class="text-xs text-gray-500">Belum ada data santri</p>
            </div>
            @endforelse
        </div>


        {{-- ========================================== --}}
        {{--                SHARED COMPONENTS           --}}
        {{-- ========================================== --}}

        

        {{-- Pagination Desktop --}}
        <div class="hidden md:block mt-6 pb-4">
            <div class="bg-white/60 backdrop-blur-xl border border-white/50 rounded-2xl px-6 py-4">
                <div class="w-full overflow-x-auto no-scrollbar">
                    {{ $santris->appends(request()->query())->links() }}
                </div>
            </div>
        </div>

        {{-- Pagination Mobile --}}
        @if($santris->hasPages())
        <div class="md:hidden mt-6 pb-8 px-1">
            <div class="flex items-center justify-between bg-white rounded-xl shadow-sm border border-gray-100 p-2">
                @if ($santris->onFirstPage())
                    <span class="p-2 w-10 h-10 flex items-center justify-center rounded-lg text-gray-300">
                        <i class="fas fa-chevron-left"></i>
                    </span>
                @else
                    <a href="{{ $santris->appends(request()->query())->previousPageUrl() }}" class="p-2 w-10 h-10 flex items-center justify-center rounded-lg text-[#187a41] hover:bg-green-50 transition-colors">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                @endif
                
                <span class="text-xs font-semibold text-gray-600">
                    Hal {{ $santris->currentPage() }} dari {{ $santris->lastPage() }}
                </span>
                
                @if ($santris->hasMorePages())
                    <a href="{{ $santris->appends(request()->query())->nextPageUrl() }}" class="p-2 w-10 h-10 flex items-center justify-center rounded-lg text-[#187a41] hover:bg-green-50 transition-colors">
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
                    <form method="GET" action="{{ route('admin.santri.index') }}">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <div class="mb-5">
                            <label class="block text-xs font-semibold text-gray-700 mb-2">Pilih Majelis</label>
                            <div class="grid grid-cols-2 gap-2 max-h-48 overflow-y-auto pr-1 custom-scrollbar">
                                <label :class="filterMajelis == '' ? 'border-[#187a41] bg-green-50' : 'border-gray-200 hover:bg-gray-50'" class="flex items-center gap-2 p-2 border rounded-lg cursor-pointer transition-colors">
                                    <input type="radio" name="majelis_id" value="" x-model="filterMajelis" class="hidden">
                                    <span :class="filterMajelis == '' ? 'text-[#187a41]' : 'text-gray-600'" class="text-xs font-medium">Semua Majelis</span>
                                </label>
                                @foreach($majelisList as $m)
                                <label :class="filterMajelis == '{{ $m->id }}' ? 'border-[#187a41] bg-green-50' : 'border-gray-200 hover:bg-gray-50'" class="flex items-center gap-2 p-2 border rounded-lg cursor-pointer transition-colors">
                                    <input type="radio" name="majelis_id" value="{{ $m->id }}" x-model="filterMajelis" class="hidden">
                                    <span :class="filterMajelis == '{{ $m->id }}' ? 'text-[#187a41]' : 'text-gray-600'" class="text-xs font-medium">{{ $m->nama_majelis }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" @click="window.location.href='{{ route('admin.santri.index') }}'" class="flex-1 py-2.5 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Reset Filter</button>
                            <button type="submit" class="flex-1 py-2.5 text-xs font-semibold text-white bg-[#187a41] hover:bg-green-700 rounded-xl transition-colors shadow-sm">Terapkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Import Modal (Shared) --}}
        <div x-show="showImportModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" x-transition.opacity style="display: none;">
            <div @click.away="showImportModal = false" class="bg-white rounded-2xl md:rounded-xl shadow-xl w-full max-w-sm md:max-w-md mx-4 overflow-hidden transform transition-all" x-show="showImportModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                <div class="px-5 md:px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 md:bg-gray-50">
                    <h3 class="text-sm md:text-lg font-bold text-gray-800">Import Santri</h3>
                    <button @click="showImportModal = false" class="text-gray-400 hover:text-gray-600 bg-white md:bg-transparent hover:bg-gray-100 rounded-full w-8 h-8 flex items-center justify-center transition-colors shadow-sm md:shadow-none">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-5 md:p-6">
                    <div class="mb-4 bg-blue-50 text-blue-700 p-3 md:p-0 md:bg-transparent md:text-gray-600 rounded-xl text-[11px] md:text-sm leading-relaxed">
                        Unduh template CSV/Excel, isi data dengan benar, lalu upload kembali ke sini.
                        <a href="{{ route('admin.santri.template') }}" class="mt-2 flex items-center justify-center md:justify-start gap-2 font-semibold bg-white md:bg-blue-50 text-blue-600 hover:text-blue-800 md:border-0 border border-blue-200 px-3 py-2 md:py-1.5 md:inline-flex rounded-lg shadow-sm md:shadow-none transition-colors">
                            <i class="fas fa-download"></i> Download Template
                        </a>
                    </div>
                    <form action="{{ route('admin.santri.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-5 md:mb-4">
                            <label class="block text-xs md:text-sm font-semibold md:font-medium text-gray-700 mb-2 md:mb-1">Upload File (CSV/Excel)</label>
                            <input type="file" name="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required 
                                   class="block w-full text-xs md:text-sm text-gray-500 file:mr-3 md:file:mr-4 file:py-2 file:px-3 md:file:px-4 file:rounded-lg md:file:rounded-full file:border-0 file:text-xs md:file:text-sm file:font-semibold file:bg-green-50 md:file:bg-hijau-muda/20 file:text-[#187a41] md:file:text-hijau-tua hover:file:bg-green-100 md:hover:file:bg-hijau-muda/30 cursor-pointer border border-gray-200 md:border-0 rounded-lg md:rounded-none">
                        </div>
                        <div class="flex md:justify-end gap-2 md:mt-6">
                            <button type="button" @click="showImportModal = false" class="flex-1 md:flex-none px-4 py-2 text-xs md:text-sm font-semibold md:font-normal text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl md:rounded-lg transition-colors">Batal</button>
                            <button type="submit" class="flex-1 md:flex-none px-4 py-2 text-xs md:text-sm font-semibold md:font-normal bg-[#187a41] md:bg-hijau-tua text-white rounded-xl md:rounded-lg hover:bg-green-700 md:hover:bg-hijau-muda transition-colors shadow-sm">Upload & Import</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1; 
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1; 
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8; 
        }
        /* Hide default pagination info to make it cleaner on mobile */
        @media (max-width: 767px) {
            .hidden.sm\:flex-1.sm\:flex.sm\:items-center.sm\:justify-between {
                display: none !important;
            }
        }
    </style>


</x-app-layout>



