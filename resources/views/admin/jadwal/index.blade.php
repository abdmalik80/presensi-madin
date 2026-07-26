<x-app-layout>
    @php
    $hariOptions = [];
    foreach($hariList as $h) $hariOptions[$h] = $h;
    $majelisOptions = [];
    foreach($majelis as $m) $majelisOptions[$m->id] = $m->nama_majelis;
    @endphp
    <x-slot name="header">Data Jadwal</x-slot>

    <div class="space-y-4" x-data="{ 
        showFilterModal: false,
        selectedIds: [],
        selectAllPages: false,
        isPageAllChecked: false,
        allIds: {{ json_encode($jadwals->pluck('id')) }},
        pageIds: {{ json_encode($jadwals->pluck('id')) }},
        
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
                    <label class="block text-xs font-semibold text-gray-600 mb-1 pl-1">Hari</label>
                    <x-custom-select name="hari" :options="$hariOptions" :selected="request('hari')" placeholder="Semua Hari" />
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
                <a href="{{ route('admin.jadwal.create') }}"
                   class="inline-flex items-center gap-2 bg-hijau-tua text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-hijau-muda transition-colors shadow-sm h-[38px]">
                    <i class="fas fa-plus"></i> Tambah Jadwal
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
                    <span x-text="selectAllPages ? ' jadwal (semua data) dipilih' : ' jadwal dipilih'"></span>
                </p>
                <p x-show="isPageAllChecked && !selectAllPages && allIds.length > pageIds.length"
                   class="text-xs text-red-600 mt-0.5">
                    Semua {{ $jadwals->count() }} jadwal di halaman ini dipilih.
                    <button type="button" @click="selectEveryPage()"
                            class="font-bold underline hover:text-red-800 ml-1">
                        Pilih semua <span x-text="allIds.length"></span> jadwal?
                    </button>
                </p>
                <p x-show="selectAllPages" class="text-xs text-red-600 mt-0.5">
                    Semua <span x-text="allIds.length"></span> jadwal dipilih.
                    <button type="button" @click="clearAll()" class="font-bold underline hover:text-red-800 ml-1">Batalkan pilihan</button>
                </p>
            </div>
            
            <form method="POST" action="{{ route('admin.jadwal.bulk-action') }}" id="bulkActionFormJadwal">
                @csrf
                <input type="hidden" name="action" id="bulkActionTypeJadwal" value="">
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>

                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" @click="if(confirm('Yakin hapus ' + selectedIds.length + ' jadwal? Tindakan ini tidak dapat dibatalkan!')) { document.getElementById('bulkActionTypeJadwal').value='delete'; document.getElementById('bulkActionFormJadwal').submit(); }"
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
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Majelis</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Kitab</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Ustadz</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Hari</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Jam</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($jadwals as $jadwal)
                                        <tr class="group hover:bg-gray-100 transition-colors"
                        :class="selectedIds.includes({{ $jadwal->id }}) ? 'bg-red-50/70' : ''">
                        <td class="px-4 py-3 w-10 transition-opacity duration-200"
                            :class="selectedIds.includes({{ $jadwal->id }}) || selectedIds.length > 0 ? 'opacity-100' : 'opacity-0 group-hover:opacity-100 focus-within:opacity-100'">
                            <input type="checkbox"
                                   :checked="selectedIds.includes({{ $jadwal->id }})"
                                   @change="
                                       if ($event.target.checked) {
                                           if (!selectedIds.includes({{ $jadwal->id }})) selectedIds.push({{ $jadwal->id }});
                                       } else {
                                           selectedIds = selectedIds.filter(id => id !== {{ $jadwal->id }});
                                           this.selectAllPages = false;
                                       }
                                   "
                                   class="rounded border-gray-300 text-red-600 focus:ring-red-400 cursor-pointer w-4 h-4">
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $loop->iteration + ($jadwals->currentPage()-1)*$jadwals->perPage() }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $jadwal->majelis->nama_majelis ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $jadwal->kitab->nama_kitab ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $jadwal->ustadz->user->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $jadwal->hari }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1">
                                <a href="{{ route('admin.jadwal.show', $jadwal) }}" class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors" title="Detail"><i class="fas fa-eye text-sm"></i></a>
                                <a href="{{ route('admin.jadwal.edit', $jadwal) }}" class="p-1.5 rounded-lg text-biru hover:bg-biru/10 transition-colors"><i class="fas fa-edit text-sm"></i></a>
                                <form method="POST" action="{{ route('admin.jadwal.destroy', $jadwal) }}" class="inline"
                                      onsubmit="return confirm('Yakin hapus jadwal ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-merah hover:bg-red-50 transition-colors"><i class="fas fa-trash text-sm"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                            <i class="fas fa-calendar-alt text-4xl mb-3 block text-gray-200"></i>
                            Tidak ada data jadwal.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @if($jadwals->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-white/40">
                {{ $jadwals->appends(request()->query())->links() }}
            </div>
            @endif
        </div>

        {{-- MOBILE VIEW --}}
        <div class="md:hidden flex items-center gap-2 mb-4 bg-white p-3 rounded-xl shadow-sm border border-gray-100 sticky top-0 z-30">
            <div class="flex-1 flex items-center gap-2">
                <span class="text-xs font-semibold text-gray-600 whitespace-nowrap">
                    @if(request('hari')) <span class="text-[#187a41]">{{ ucfirst(request('hari')) }}</span> @else Semua Hari @endif
                </span>
            </div>
            <button type="button" @click="showFilterModal = true" class="p-2 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 flex-shrink-0 flex items-center justify-center w-10 h-10 bg-white">
                <i class="fas fa-sliders-h"></i>
            </button>
        </div>

        <div class="md:hidden flex items-center justify-between mb-3 px-1">
            <div>
                <h2 class="text-sm font-bold text-gray-800">Jadwal Majelis</h2>
                <p class="text-[10px] text-gray-500">Total {{ $jadwals->total() }} Jadwal</p>
            </div>
            <a href="{{ route('admin.jadwal.create') }}"
               class="inline-flex items-center gap-1.5 bg-[#187a41] text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-green-700 transition-colors shadow-sm">
                <i class="fas fa-plus"></i> Tambah
            </a>
        </div>

        <div class="md:hidden space-y-0 bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50">
            @forelse($jadwals as $jadwal)
            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-3 flex-1 pl-3">
                    <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-calendar-alt text-blue-600 text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0 pr-2">
                        <h4 class="text-xs font-bold text-gray-800 truncate">{{ $jadwal->majelis->nama_majelis ?? '-' }}</h4>
                        <div class="text-[10px] text-gray-500 mt-0.5 flex items-center gap-1 flex-wrap">
                            <span class="font-medium text-gray-700">{{ $jadwal->kitab->nama_kitab ?? '-' }}</span>
                            <span class="text-gray-300">&bull;</span>
                            <span>{{ $jadwal->ustadz->user->name ?? '-' }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col items-end justify-between self-stretch py-1 pr-3">
                    <span class="px-2 py-0.5 text-[9px] font-bold rounded-full bg-blue-50 text-blue-700 border border-blue-200 tracking-wide whitespace-nowrap">
                        {{ $jadwal->hari }}
                    </span>
                    <div x-data="{ open: false }" class="relative mt-1">
                        <button @click="open = !open" @click.away="open = false" class="text-gray-400 hover:text-gray-700 p-1 rounded">
                            <i class="fas fa-ellipsis-v text-xs px-1"></i>
                        </button>
                        <div x-show="open" x-transition.opacity class="absolute right-0 top-full mt-1 w-32 bg-white rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.15)] border border-gray-100 py-1.5 z-50" style="display:none">
                            <a href="{{ route('admin.jadwal.show', $jadwal) }}" class="block px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 hover:text-[#187a41]"><i class="fas fa-eye w-4"></i> Detail</a>
                            <a href="{{ route('admin.jadwal.edit', $jadwal) }}" class="block px-4 py-2 text-xs font-medium text-blue-600 hover:bg-gray-50"><i class="fas fa-edit w-4"></i> Edit</a>
                            <form method="POST" action="{{ route('admin.jadwal.destroy', $jadwal) }}" onsubmit="return confirm('Yakin hapus jadwal ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="block w-full text-left px-4 py-2 text-xs font-medium text-red-600 hover:bg-gray-50"><i class="fas fa-trash w-4"></i> Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-10">
                <i class="fas fa-calendar-alt text-4xl text-gray-200 mb-2"></i>
                <p class="text-xs text-gray-500">Belum ada data jadwal</p>
            </div>
            @endforelse
        </div>

        {{-- Pagination Mobile --}}
        @if($jadwals->hasPages())
        <div class="md:hidden mt-4 pb-8 px-1">
            <div class="flex items-center justify-between bg-white rounded-xl shadow-sm border border-gray-100 p-2">
                @if ($jadwals->onFirstPage())
                    <span class="p-2 w-10 h-10 flex items-center justify-center rounded-lg text-gray-300"><i class="fas fa-chevron-left"></i></span>
                @else
                    <a href="{{ $jadwals->appends(request()->query())->previousPageUrl() }}" class="p-2 w-10 h-10 flex items-center justify-center rounded-lg text-[#187a41] hover:bg-green-50 transition-colors"><i class="fas fa-chevron-left"></i></a>
                @endif
                <span class="text-xs font-semibold text-gray-600">Hal {{ $jadwals->currentPage() }} dari {{ $jadwals->lastPage() }}</span>
                @if ($jadwals->hasMorePages())
                    <a href="{{ $jadwals->appends(request()->query())->nextPageUrl() }}" class="p-2 w-10 h-10 flex items-center justify-center rounded-lg text-[#187a41] hover:bg-green-50 transition-colors"><i class="fas fa-chevron-right"></i></a>
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
                    <h3 class="text-sm font-bold text-gray-800">Filter Jadwal</h3>
                    <button @click="showFilterModal = false" class="text-gray-400 bg-gray-50 rounded-full w-8 h-8 flex items-center justify-center"><i class="fas fa-times"></i></button>
                </div>
                <div class="p-5">
                    <form method="GET" action="{{ route('admin.jadwal.index') }}" x-data="{ hari: '{{ request('hari') }}', majelis_id: '{{ request('majelis_id') }}' }">
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-gray-700 mb-2">Hari</label>
                            <div class="grid grid-cols-3 gap-2">
                                <label class="flex items-center justify-center p-2 border rounded-lg cursor-pointer" :class="hari === '' ? 'border-[#187a41] bg-green-50' : 'border-gray-200'">
                                    <input type="radio" name="hari" value="" class="hidden" x-model="hari">
                                    <span class="text-xs font-medium" :class="hari === '' ? 'text-[#187a41]' : 'text-gray-600'">Semua</span>
                                </label>
                                @foreach($hariList as $h)
                                <label class="flex items-center justify-center p-2 border rounded-lg cursor-pointer" :class="hari === '{{ strtolower($h) }}' ? 'border-[#187a41] bg-green-50' : 'border-gray-200'">
                                    <input type="radio" name="hari" value="{{ strtolower($h) }}" class="hidden" x-model="hari">
                                    <span class="text-xs font-medium" :class="hari === '{{ strtolower($h) }}' ? 'text-[#187a41]' : 'text-gray-600'">{{ $h }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-gray-700 mb-2">Majelis</label>
                            <div class="grid grid-cols-2 gap-2 max-h-36 overflow-y-auto">
                                <label class="flex items-center gap-2 p-2 border rounded-lg cursor-pointer" :class="majelis_id === '' ? 'border-[#187a41] bg-green-50' : 'border-gray-200'">
                                    <input type="radio" name="majelis_id" value="" class="hidden" x-model="majelis_id">
                                    <span class="text-xs font-medium" :class="majelis_id === '' ? 'text-[#187a41]' : 'text-gray-600'">Semua</span>
                                </label>
                                @foreach($majelis as $m)
                                <label class="flex items-center gap-2 p-2 border rounded-lg cursor-pointer" :class="majelis_id === '{{ $m->id }}' ? 'border-[#187a41] bg-green-50' : 'border-gray-200'">
                                    <input type="radio" name="majelis_id" value="{{ $m->id }}" class="hidden" x-model="majelis_id">
                                    <span class="text-xs font-medium" :class="majelis_id === '{{ $m->id }}' ? 'text-[#187a41]' : 'text-gray-600'">{{ $m->nama_majelis }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" @click="window.location.href='{{ route('admin.jadwal.index') }}'" class="flex-1 py-2.5 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Reset Filter</button>
                            <button type="submit" class="flex-1 py-2.5 text-xs font-semibold text-white bg-[#187a41] hover:bg-green-700 rounded-xl transition-colors shadow-sm">Terapkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

