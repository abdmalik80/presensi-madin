<x-app-layout>
            @php
    $tipeOptions = [
        'image' => '<i class="far fa-image text-blue-500 w-4 text-center mr-1"></i> Gambar',
        'pdf'   => '<i class="far fa-file-pdf text-red-500 w-4 text-center mr-1"></i> PDF',
        'doc'   => '<i class="far fa-file-word text-blue-600 w-4 text-center mr-1"></i> Word',
        'xls'   => '<i class="far fa-file-excel text-green-500 w-4 text-center mr-1"></i> Excel',
        'ppt'   => '<i class="far fa-file-powerpoint text-orange-500 w-4 text-center mr-1"></i> PowerPoint',
        'other' => '<i class="far fa-file text-gray-500 w-4 text-center mr-1"></i> Lainnya',
    ];
    $majelisOptions = [];
    foreach($majeliss as $m) $majelisOptions[$m->id] = $m->nama_majelis;
    $kitabOptions = [];
    foreach($kitabs as $k) $kitabOptions[$k->id] = $k->nama_kitab;
    $ustadzOptions = [];
    foreach($ustadzs as $u) $ustadzOptions[$u->id] = $u->nama;
    $bulanOptions = [];
    foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bln) {
        $bulanOptions[$i+1] = $bln;
    }
    $tahunOptions = [];
    foreach($tahunList ?? [] as $th) {
        $tahunOptions[$th] = $th;
    }
    @endphp
    <x-slot name="header">Dokumentasi Pembelajaran</x-slot>

    <div class="space-y-5 pb-8 md:pb-0" x-data="{ showFilterModal: false }">
        <x-alert />

        {{-- Header Bar --}}
        <div class="bg-white/70 backdrop-blur-xl rounded-2xl border border-white/50 shadow-sm p-4 md:p-5 flex flex-row items-center justify-between gap-3">
            <div class="flex-1">
                <h2 class="text-sm md:text-lg font-semibold text-gray-800">Semua Dokumentasi</h2>
                <p class="hidden md:block text-sm text-gray-500 mt-0.5">Kelola seluruh dokumentasi pembelajaran dari semua ustadz</p>
                <p class="md:hidden text-[10px] text-gray-500 mt-0.5">{{ $dokumentasis->total() }} berkas</p>
            </div>
            <a href="{{ route('admin.dokumentasi.create') }}"
               class="inline-flex items-center gap-1.5 md:gap-2 bg-hijau-tua text-white px-3 md:px-5 py-2 md:py-2.5 rounded-lg md:rounded-xl text-xs md:text-sm font-semibold hover:bg-hijau-muda transition-all shadow hover:shadow-md">
                <i class="fas fa-upload"></i> <span class="hidden sm:inline">Upload Berkas</span><span class="sm:hidden">Upload</span>
            </a>
            <button @click="showFilterModal = true" class="md:hidden p-2 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 flex items-center justify-center bg-white shadow-sm h-full aspect-square">
                <i class="fas fa-filter text-xs"></i>
            </button>
        </div>

                                {{-- Filter Bar Desktop --}}
        <div class="hidden md:block bg-white/60 backdrop-blur-xl rounded-2xl border border-white/50 shadow-sm p-4 relative z-40">
            <form method="GET" action="{{ route('admin.dokumentasi.index') }}">
                <div class="flex flex-col md:flex-row gap-2 items-end w-full">
                    <div class="flex-1 min-w-0 w-full">
                        <label class="block text-xs font-semibold text-gray-600 mb-1 truncate">Majelis</label>
                        <x-custom-select name="majelis_id" :options="$majelisOptions" :selected="request('majelis_id')" placeholder="Semua" width="w-full" onchange="filterChange('majelis', val)" />
                    </div>
                    <div class="flex-1 min-w-0 w-full">
                        <label class="block text-xs font-semibold text-gray-600 mb-1 truncate">Kitab</label>
                        <x-custom-select name="kitab_id" :options="$kitabOptions" :selected="request('kitab_id')" placeholder="Semua" width="w-full" onchange="filterChange('kitab', val)" />
                    </div>
                    <div class="flex-1 min-w-0 w-full">
                        <label class="block text-xs font-semibold text-gray-600 mb-1 truncate">Ustadz</label>
                        <x-custom-select name="pengampu_id" :options="$ustadzOptions" :selected="request('pengampu_id')" placeholder="Semua" width="w-full" onchange="filterChange('pengampu', val)" />
                    </div>
                    <div class="flex-1 min-w-0 w-full">
                        <label class="block text-xs font-semibold text-gray-600 mb-1 truncate">Tipe Berkas</label>
                        <x-custom-select name="tipe" :options="$tipeOptions" :selected="request('tipe')" placeholder="Semua" width="w-full" />
                    </div>
                    <div class="flex-1 min-w-0 w-full">
                        <label class="block text-xs font-semibold text-gray-600 mb-1 truncate">Bulan</label>
                        <x-custom-select name="bulan" :options="$bulanOptions" :selected="request('bulan')" placeholder="Semua" width="w-full" />
                    </div>
                    <div class="flex-1 min-w-0 w-full">
                        <label class="block text-xs font-semibold text-gray-600 mb-1 truncate">Tahun</label>
                        <x-custom-select name="tahun" :options="$tahunOptions" :selected="request('tahun')" placeholder="Semua" width="w-full" />
                    </div>
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <button type="submit" class="bg-hijau-tua text-white px-3 py-2 rounded-lg text-sm font-semibold hover:bg-hijau-muda transition-all shadow-sm flex items-center justify-center gap-1.5 h-[38px]" title="Filter">
                            <i class="fas fa-filter"></i>
                        </button>
                        @if(request()->anyFilled(['majelis_id','kitab_id','pengampu_id','tipe','bulan','tahun']))
                        <a href="{{ route('admin.dokumentasi.index') }}" class="bg-white border border-gray-200 text-gray-600 px-3 py-2 rounded-lg text-sm font-semibold hover:bg-gray-50 transition-all shadow-sm flex items-center justify-center gap-1.5 h-[38px]" title="Reset">
                            <i class="fas fa-times"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        {{-- Stats --}}
        <div class="hidden md:flex bg-white/60 backdrop-blur-xl rounded-2xl border border-white/50 shadow-sm px-5 py-3 items-center gap-2 text-sm text-gray-500">
            <i class="fas fa-folder-open text-green-500"></i>
            <span>Total: <strong class="text-gray-700">{{ $dokumentasis->total() }}</strong> berkas</span>
            @if(request()->hasAny(['majelis_id','pengampu_id','tipe','bulan','tahun']))
                <span class="ml-2 px-2 py-0.5 bg-hijau-tua/10 text-hijau-tua text-xs rounded-full font-medium">Filter aktif</span>
            @endif
        </div>

        {{-- Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5">
            @forelse($dokumentasis as $dok)
            <div class="group bg-white/60 backdrop-blur-xl rounded-2xl border border-white/50 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-200 relative">

                {{-- Preview Area --}}
                @if($dok->tipe_file === 'image' && $dok->upload_file)
                <div class="relative overflow-hidden rounded-t-2xl">
                    <img src="{{ asset('storage/'.$dok->upload_file) }}"
                         class="w-full h-40 md:h-44 object-cover group-hover:scale-105 transition-transform duration-300"
                         alt="{{ $dok->judul }}">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                    <span class="absolute top-2 right-2 bg-white/90 text-purple-600 text-[10px] font-bold px-2 py-0.5 rounded-full shadow">
                        <i class="fas fa-image mr-1"></i>Gambar
                    </span>
                </div>
                @else
                <div class="w-full h-40 md:h-44 bg-gradient-to-br from-gray-50 to-gray-100 flex flex-col items-center justify-center gap-2 relative rounded-t-2xl">
                    <i class="fas {{ $dok->file_icon }} text-4xl md:text-5xl"></i>
                    <span class="text-[10px] md:text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ $dok->tipe_label }}</span>
                    @if($dok->nama_file)
                    <span class="text-[9px] md:text-[10px] text-gray-400 px-3 text-center truncate w-full text-center">{{ $dok->nama_file }}</span>
                    @endif
                </div>
                @endif

                {{-- Konten --}}
                <div class="p-3 md:p-4 space-y-2">
                    <h3 class="font-semibold text-gray-800 text-xs md:text-sm leading-snug truncate">{{ $dok->judul }}</h3>

                    <div class="flex flex-wrap gap-x-2 md:gap-x-3 gap-y-1 text-[10px] md:text-xs text-gray-500">
                        <span><i class="fas fa-calendar-alt mr-1 text-green-600"></i>
                            {{ \Carbon\Carbon::parse($dok->tanggal)->translatedFormat('d M Y') }}
                        </span>
                        <span class="truncate max-w-[120px]" title="{{ $dok->pengampu->user->name ?? 'Semua Ustadz' }}"><i class="fas fa-chalkboard-teacher mr-1 text-green-600"></i>
                            {{ $dok->pengampu->user->name ?? 'Semua Ustadz' }}
                        </span>
                        <span class="truncate max-w-[120px]" title="{{ $dok->kitab->nama_kitab ?? $dok->jadwal->kitab->nama_kitab ?? 'Semua Kitab' }}"><i class="fas fa-book mr-1 text-green-600"></i>
                            {{ $dok->kitab->nama_kitab ?? $dok->jadwal->kitab->nama_kitab ?? 'Semua Kitab' }}
                        </span>
                        <span class="truncate max-w-[120px]" title="{{ $dok->majelis->nama_majelis ?? 'Semua Majelis' }}"><i class="fas fa-users mr-1 text-green-600"></i>
                            {{ $dok->majelis->nama_majelis ?? 'Semua Majelis' }}
                        </span>
                    </div>

                    @if($dok->deskripsi)
                    <p class="text-[10px] md:text-xs text-gray-600 leading-relaxed line-clamp-2 md:line-clamp-none">{{ Str::limit($dok->deskripsi, 80) }}</p>
                    @endif

                    {{-- Actions Dropdown --}}
                    <div class="flex items-center justify-end pt-2 border-t border-gray-100 relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open" @click.away="open = false" 
                                class="p-1.5 md:p-2 text-gray-500 hover:text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors focus:outline-none">
                            <i class="fas fa-ellipsis-h w-4 md:w-5"></i>
                        </button>
                        
                        <div x-show="open" x-transition.opacity.duration.200ms
                             class="absolute right-0 bottom-full mb-2 w-32 md:w-36 bg-white rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-gray-100 py-1.5 z-20"
                             style="display: none;">
                            @if($dok->upload_file)
                            <a href="{{ asset('storage/'.$dok->upload_file) }}" target="_blank"
                               class="flex items-center gap-2 px-4 py-2 text-[10px] md:text-xs text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors">
                                <i class="fas fa-eye w-4 text-green-500"></i> Lihat
                            </a>
                            <a href="{{ asset('storage/'.$dok->upload_file) }}" download="{{ $dok->nama_file ?? $dok->judul }}"
                               class="flex items-center gap-2 px-4 py-2 text-[10px] md:text-xs text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                <i class="fas fa-download w-4 text-blue-500"></i> Unduh
                            </a>
                            @endif
                            <a href="{{ route('admin.dokumentasi.edit', $dok) }}"
                               class="flex items-center gap-2 px-4 py-2 text-[10px] md:text-xs text-gray-700 hover:bg-amber-50 hover:text-amber-700 transition-colors">
                                <i class="fas fa-edit w-4 text-amber-500"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('admin.dokumentasi.destroy', $dok) }}" class="block w-full"
                                  onsubmit="return confirm('Yakin hapus berkas ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-[10px] md:text-xs text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors text-left">
                                    <i class="fas fa-trash w-4 text-red-500"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full py-20 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 md:w-20 md:h-20 rounded-full bg-green-50 mb-3 md:mb-4">
                    <i class="fas fa-folder-open text-3xl md:text-4xl text-green-200"></i>
                </div>
                <p class="text-gray-400 font-medium text-sm md:text-base">Belum ada berkas dokumentasi.</p>
                <p class="text-gray-300 text-xs md:text-sm mt-1">Upload berkas materi atau Dokumentasi Majelis.</p>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($dokumentasis->hasPages())
        <div class="hidden md:flex justify-center mt-6">
            {{ $dokumentasis->links() }}
        </div>
        <div class="md:hidden mt-4 pb-8 px-1">
            <div class="flex items-center justify-between bg-white rounded-xl shadow-sm border border-gray-100 p-2">
                @if ($dokumentasis->onFirstPage())
                    <span class="p-2 w-10 h-10 flex items-center justify-center rounded-lg text-gray-300"><i class="fas fa-chevron-left"></i></span>
                @else
                    <a href="{{ $dokumentasis->appends(request()->query())->previousPageUrl() }}" class="p-2 w-10 h-10 flex items-center justify-center rounded-lg text-[#187a41] hover:bg-green-50 transition-colors"><i class="fas fa-chevron-left"></i></a>
                @endif
                <span class="text-xs font-semibold text-gray-600">Hal {{ $dokumentasis->currentPage() }} dari {{ $dokumentasis->lastPage() }}</span>
                @if ($dokumentasis->hasMorePages())
                    <a href="{{ $dokumentasis->appends(request()->query())->nextPageUrl() }}" class="p-2 w-10 h-10 flex items-center justify-center rounded-lg text-[#187a41] hover:bg-green-50 transition-colors"><i class="fas fa-chevron-right"></i></a>
                @else
                    <span class="p-2 w-10 h-10 flex items-center justify-center rounded-lg text-gray-300"><i class="fas fa-chevron-right"></i></span>
                @endif
            </div>
        </div>
        @endif

        {{-- Filter Modal Mobile --}}
        <div x-show="showFilterModal" x-effect="document.body.style.overflow = showFilterModal ? 'hidden' : ''" class="md:hidden fixed inset-0 z-50 flex flex-col justify-end bg-black/50" x-transition.opacity style="display:none">
            <div @click.away="showFilterModal = false" class="bg-white w-full rounded-t-2xl shadow-xl overflow-hidden flex flex-col max-h-[85vh]"
                 x-show="showFilterModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full">
                <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center flex-shrink-0">
                    <h3 class="text-sm font-bold text-gray-800">Filter Dokumentasi</h3>
                    <button @click="showFilterModal = false" class="text-gray-400 bg-gray-50 rounded-full w-8 h-8 flex items-center justify-center"><i class="fas fa-times"></i></button>
                </div>
                <div class="p-5 overflow-y-auto">
                                          <form method="GET" action="{{ route('admin.dokumentasi.index') }}" class="space-y-4">
                          <div>
                              <label class="block text-xs font-semibold text-gray-700 mb-2">Majelis</label>
                              <x-custom-select name="majelis_id" :options="$majelisOptions" :selected="request('majelis_id')" placeholder="Semua Majelis" width="w-full" onchange="filterChange('majelis', val)" />
                          </div>
                          <div>
                              <label class="block text-xs font-semibold text-gray-700 mb-2">Kitab</label>
                              <x-custom-select name="kitab_id" :options="$kitabOptions" :selected="request('kitab_id')" placeholder="Semua Kitab" width="w-full" onchange="filterChange('kitab', val)" />
                          </div>
                          <div>
                              <label class="block text-xs font-semibold text-gray-700 mb-2">Ustadz</label>
                              <x-custom-select name="pengampu_id" :options="$ustadzOptions" :selected="request('pengampu_id')" placeholder="Semua Ustadz" width="w-full" onchange="filterChange('pengampu', val)" />
                          </div>
                          <div>
                              <label class="block text-xs font-semibold text-gray-700 mb-2">Tipe Berkas</label>
                              <x-custom-select name="tipe" :options="$tipeOptions" :selected="request('tipe')" placeholder="Semua Tipe" width="w-full" />
                          </div>
                          <div class="flex gap-3">
                              <div class="flex-1">
                                  <label class="block text-xs font-semibold text-gray-700 mb-2">Bulan</label>
                                  <x-custom-select name="bulan" :options="$bulanOptions" :selected="request('bulan')" placeholder="Semua Bulan" width="w-full" />
                              </div>
                              <div class="flex-1">
                                  <label class="block text-xs font-semibold text-gray-700 mb-2">Tahun</label>
                                  <x-custom-select name="tahun" :options="$tahunOptions" :selected="request('tahun')" placeholder="Semua Tahun" width="w-full" />
                              </div>
                          </div>
                          <div class="flex gap-2 pt-2">
                              <button type="button" @click="window.location.href='{{ route('admin.dokumentasi.index') }}'" class="flex-1 py-2.5 text-xs font-semibold text-gray-600 bg-gray-100 rounded-xl">Reset</button>
                              <button type="submit" @click="showFilterModal = false" class="flex-1 py-2.5 text-xs font-semibold text-white bg-[#187a41] rounded-xl">Terapkan</button>
                          </div>
                      </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const jadwals = @json($jadwals->map(fn($j) => ['majelis_id' => $j->majelis_id, 'kitab_id' => $j->kitab_id, 'ustadz_id' => $j->ustadz_id]));

        window.filterChange = function(source, val) {
            setTimeout(() => {
                // Determine which form triggered the change (desktop vs mobile)
                // We'll update both desktop and mobile forms simultaneously to keep them in sync
                const majelisInputs = document.querySelectorAll('input[name="majelis_id"]');
                const kitabInputs = document.querySelectorAll('input[name="kitab_id"]');
                const ustadzInputs = document.querySelectorAll('input[name="pengampu_id"]');
                
                // Use the value from the triggered input for filtering
                // We'll just grab the values from the first set (Desktop form usually) 
                // but if we want to be exact, we should take the one that triggered it. 
                // But since they are bound to Alpine or we just dispatch events, they should sync up if we update all of them.
                
                // For simplicity, let's take the first non-empty values or the one from the first form
                let mId = majelisInputs[0]?.value || majelisInputs[1]?.value;
                let kId = kitabInputs[0]?.value || kitabInputs[1]?.value;
                let uId = ustadzInputs[0]?.value || ustadzInputs[1]?.value;

                let validMajelis = new Set();
                let validKitab = new Set();
                let validUstadz = new Set();

                jadwals.forEach(j => {
                    // Validasi untuk opsi Majelis: harus cocok dengan Kitab dan Ustadz saat ini
                    if ((!kId || j.kitab_id == kId) && (!uId || j.ustadz_id == uId)) {
                        if (j.majelis_id) validMajelis.add(String(j.majelis_id));
                    }
                    
                    // Validasi untuk opsi Kitab: harus cocok dengan Majelis dan Ustadz saat ini
                    if ((!mId || j.majelis_id == mId) && (!uId || j.ustadz_id == uId)) {
                        if (j.kitab_id) validKitab.add(String(j.kitab_id));
                    }
                    
                    // Validasi untuk opsi Ustadz: harus cocok dengan Majelis dan Kitab saat ini
                    if ((!mId || j.majelis_id == mId) && (!kId || j.kitab_id == kId)) {
                        if (j.ustadz_id) validUstadz.add(String(j.ustadz_id));
                    }
                });

                const updateOptions = (inputs, validSet) => {
                    inputs.forEach(input => {
                        if (!input) return;
                        const wrapper = input.closest('[x-data]');
                        if (!wrapper) return;
                        const buttons = wrapper.querySelectorAll('button[data-val]');
                        let hasSelection = false;
                        
                        buttons.forEach(btn => {
                            const v = btn.getAttribute('data-val');
                            if (v === "") {
                                btn.style.display = 'block';
                                return;
                            }
                            if (validSet.has(String(v))) {
                                btn.style.display = 'block';
                                if (String(v) === String(input.value)) hasSelection = true;
                            } else {
                                btn.style.display = 'none';
                            }
                        });

                        if (input.value && !hasSelection) {
                            input.value = '';
                            input.dispatchEvent(new Event('input', { bubbles: true }));
                        }
                    });
                };

                if (source !== 'majelis') updateOptions(majelisInputs, validMajelis);
                if (source !== 'kitab') updateOptions(kitabInputs, validKitab);
                if (source !== 'pengampu') updateOptions(ustadzInputs, validUstadz);

            }, 50);
        };

        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.filterChange('init', null);
            }, 100);
        });
    </script>
    @endpush
</x-app-layout>



