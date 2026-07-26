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
    $bulanOptions = [];
    foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bln) {
        $bulanOptions[$i+1] = $bln;
    }
    $tahunOptions = [];
    foreach($tahunList as $th) {
        $tahunOptions[$th] = $th;
    }
    @endphp
    <x-slot name="header">Dokumentasi & Berkas</x-slot>

    <div class="space-y-5 pb-8 md:pb-0" x-data="{ showFilterModal: false }">
        <x-alert />

        {{-- Mobile Header & Filter Button --}}
        <div class="md:hidden flex items-center justify-between bg-white p-3 rounded-xl shadow-sm border border-gray-100 sticky top-0 z-30">
            <div>
                <h2 class="text-sm font-bold text-gray-800">Berkas Saya</h2>
                <p class="text-[10px] text-gray-500">
                    @if(request()->hasAny(['jadwal_id','tipe','bulan','tahun'])) Filter Aktif @else Pilih Filter @endif
                </p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" @click="showFilterModal = true" class="p-2 border border-[#187a41] bg-green-50 text-[#187a41] rounded-lg flex items-center justify-center w-10 h-10">
                    <i class="fas fa-filter"></i>
                </button>
            </div>
        </div>

        {{-- Header Bar --}}
        <div class="hidden md:flex bg-white/70 backdrop-blur-xl rounded-2xl border border-white/50 shadow-sm p-5 flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Semua Berkas Saya</h2>
                <p class="text-sm text-gray-500 mt-0.5">Kelola berkas & dokumentasi dari Semua Majelis yang Anda ampu</p>
            </div>
            <a href="{{ route('ustadz.dokumentasi.create') }}"
               class="inline-flex items-center gap-2 bg-hijau-tua text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-hijau-muda transition-all shadow hover:shadow-md">
                <i class="fas fa-upload"></i> Upload Berkas
            </a>
        </div>

                {{-- Desktop Filter Bar --}}
        <div class="hidden md:block bg-white/60 backdrop-blur-xl rounded-2xl border border-white/50 shadow-sm p-4 relative z-40">
            <form method="GET" action="{{ route('ustadz.dokumentasi.index') }}">
                <div class="grid grid-cols-[1.5fr_1.5fr_1fr_1fr_1fr_auto] gap-3 items-end">
                    <div class="min-w-0">
                        <label class="block text-xs font-semibold text-gray-600 mb-1 truncate">Filter Majelis</label>
                        <x-custom-select name="majelis_id" :options="$majelisOptions" :selected="request('majelis_id')" placeholder="Semua Majelis" width="w-full" onchange="filterKitab(val)" />
                    </div>
                    <div class="min-w-0">
                        <label class="block text-xs font-semibold text-gray-600 mb-1 truncate">Filter Kitab</label>
                        <x-custom-select name="kitab_id" :options="$kitabOptions" :selected="request('kitab_id')" placeholder="Semua Kitab" width="w-full" onchange="filterMajelis(val)" />
                    </div>
                    <div class="min-w-0">
                        <label class="block text-xs font-semibold text-gray-600 mb-1 truncate">Tipe Berkas</label>
                        <x-custom-select name="tipe" :options="$tipeOptions" :selected="request('tipe')" placeholder="Semua Tipe" width="w-full" />
                    </div>
                    <div class="min-w-0">
                        <label class="block text-xs font-semibold text-gray-600 mb-1 truncate">Bulan</label>
                        <x-custom-select name="bulan" :options="$bulanOptions" :selected="request('bulan')" placeholder="Semua Bulan" width="w-full" />
                    </div>
                    <div class="min-w-0">
                        <label class="block text-xs font-semibold text-gray-600 mb-1 truncate">Tahun</label>
                        <x-custom-select name="tahun" :options="$tahunOptions" :selected="request('tahun')" placeholder="Semua Tahun" width="w-full" />
                    </div>
                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        <button type="submit" class="bg-hijau-tua text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-hijau-muda transition-all shadow-sm flex items-center justify-center gap-1.5 h-[38px]">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        @if(request()->hasAny(['majelis_id','kitab_id','tipe','bulan','tahun']))
                        <a href="{{ route('ustadz.dokumentasi.index') }}" class="bg-white border border-gray-200 text-gray-600 px-3 py-2 rounded-lg text-sm font-semibold hover:bg-gray-50 transition-all shadow-sm flex items-center justify-center gap-1.5 h-[38px]" title="Reset">
                            <i class="fas fa-times"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        {{-- Stats --}}
        <div class="bg-white/60 backdrop-blur-xl rounded-2xl border border-white/50 shadow-sm px-5 py-3 flex items-center gap-2 text-sm text-gray-500">
            <i class="fas fa-folder-open text-green-500"></i>
            <span>Total: <strong class="text-gray-700">{{ $dokumentasis->total() }}</strong> berkas</span>
            @if(request()->hasAny(['majelis_id','kitab_id','tipe','bulan','tahun']))
                <span class="ml-2 px-2 py-0.5 bg-hijau-tua/10 text-hijau-tua text-xs rounded-full font-medium">Filter aktif</span>
            @endif
        </div>

        {{-- Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-5" style="position: relative; z-index: 10;">
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
                    <i class="fas {{ $dok->file_icon }} text-5xl"></i>
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ $dok->tipe_label }}</span>
                    @if($dok->nama_file)
                    <span class="text-[10px] text-gray-400 px-3 text-center truncate w-full text-center">{{ $dok->nama_file }}</span>
                    @endif
                </div>
                @endif

                {{-- Konten --}}
                <div class="p-4 space-y-2">
                    <div>
                        <h3 class="font-semibold text-gray-800 text-sm leading-snug">{{ $dok->judul }}</h3>
                        @if($dok->nama_file)
                        <p class="text-[10px] text-gray-500 truncate font-medium mt-0.5" title="{{ $dok->nama_file }}">
                            <i class="fas fa-file-alt mr-1 text-gray-400"></i>{{ $dok->nama_file }}
                        </p>
                        @endif
                    </div>

                    <div class="flex flex-wrap gap-x-3 gap-y-1 text-xs text-gray-500">
                        <span><i class="fas fa-calendar-alt mr-1 text-green-600"></i>
                            {{ \Carbon\Carbon::parse($dok->tanggal)->translatedFormat('d M Y') }}
                        </span>
                        @if($dok->jadwal)
                        <span><i class="fas fa-book mr-1 text-green-600"></i>
                            {{ $dok->jadwal->kitab->nama_kitab ?? '-' }}
                        </span>
                        @endif
                    </div>

                    @if($dok->deskripsi)
                    <p class="text-xs text-gray-600 leading-relaxed line-clamp-2 md:line-clamp-none">{{ Str::limit($dok->deskripsi, 80) }}</p>
                    @endif

                    {{-- Actions --}}
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
                            <a href="{{ route('ustadz.dokumentasi.edit', $dok) }}"
                               class="flex items-center gap-2 px-4 py-2 text-[10px] md:text-xs text-gray-700 hover:bg-amber-50 hover:text-amber-700 transition-colors">
                                <i class="fas fa-edit w-4 text-amber-500"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('ustadz.dokumentasi.destroy', $dok) }}" class="block w-full"
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
                @if(request()->hasAny(['majelis_id','kitab_id','tipe','bulan','tahun']))
                    <p class="text-gray-400 font-medium">Tidak ada berkas yang cocok dengan filter.</p>
                    <a href="{{ route('ustadz.dokumentasi.index') }}" class="inline-flex items-center gap-1.5 mt-4 text-sm text-hijau-tua hover:underline font-medium">
                        <i class="fas fa-times"></i> Reset Filter
                    </a>
                @else
                    <p class="text-gray-400 font-medium">Belum ada berkas.</p>
                    <p class="text-gray-300 text-sm mt-1">Upload berkas materi atau Dokumentasi Majelis Anda.</p>
                    <a href="{{ route('ustadz.dokumentasi.create') }}"
                       class="inline-flex items-center gap-2 mt-4 bg-hijau-tua text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-hijau-muda transition-all shadow">
                        <i class="fas fa-upload"></i> Upload Sekarang
                    </a>
                @endif
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($dokumentasis->hasPages())
        <div class="flex justify-center mt-5">
            {{ $dokumentasis->links() }}
        </div>
        @endif

        {{-- Mobile Filter Modal --}}
        <div x-show="showFilterModal" x-effect="document.body.style.overflow = showFilterModal ? 'hidden' : ''" class="md:hidden fixed inset-0 z-50 flex flex-col justify-end bg-black/50" x-transition.opacity style="display:none">
            <div @click.away="showFilterModal = false" class="bg-white w-full rounded-t-2xl shadow-xl overflow-hidden flex flex-col max-h-[85vh]"
                 x-show="showFilterModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full">
                <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center flex-shrink-0">
                    <h3 class="text-sm font-bold text-gray-800">Filter Dokumentasi</h3>
                    <button type="button" @click="showFilterModal = false" class="text-gray-400 bg-gray-50 rounded-full w-8 h-8 flex items-center justify-center hover:bg-gray-100 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-5 overflow-y-auto">
                    <form method="GET" action="{{ route('ustadz.dokumentasi.index') }}" class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-2">Majelis</label>
                            <x-custom-select name="majelis_id" :options="$majelisOptions" :selected="request('majelis_id')" placeholder="Semua Majelis" onchange="filterKitab(val)" />
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-2">Kitab</label>
                            <x-custom-select name="kitab_id" :options="$kitabOptions" :selected="request('kitab_id')" placeholder="Semua Kitab" onchange="filterMajelis(val)" />
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-2">Tipe Berkas</label>
                            <x-custom-select name="tipe" :options="$tipeOptions" :selected="request('tipe')" placeholder="Semua Tipe" />
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Bulan</label>
                                <x-custom-select name="bulan" :options="$bulanOptions" :selected="request('bulan')" placeholder="Semua Bulan" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Tahun</label>
                                <x-custom-select name="tahun" :options="$tahunOptions" :selected="request('tahun')" placeholder="Semua Tahun" />
                            </div>
                        </div>

                        <div class="flex gap-2 pt-2">
                            <button type="button" @click="window.location.href='{{ route('ustadz.dokumentasi.index') }}'" class="flex-1 py-2.5 text-xs font-semibold text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">Reset</button>
                            <button type="submit" @click="showFilterModal = false" class="flex-1 py-2.5 text-xs font-semibold text-white bg-[#187a41] hover:bg-hijau-muda rounded-xl transition-colors">Terapkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Floating Action Button for Upload (Mobile Only) --}}
        <a href="{{ route('ustadz.dokumentasi.create') }}" class="md:hidden fixed bottom-6 right-6 w-14 h-14 bg-hijau-tua text-white rounded-full flex items-center justify-center shadow-lg hover:bg-hijau-muda transition-transform active:scale-95 z-40">
            <i class="fas fa-plus text-xl"></i>
        </a>
    </div>

    <x-cascading-dropdown-script 
        primary-name="majelis_id" 
        secondary-name="kitab_id" 
        :primary-to-secondary-map="$majelisKitabMap ?? []" 
        :secondary-to-primary-map="$kitabMajelisMap ?? []"
        filter-secondary-fn="filterKitab"
        filter-primary-fn="filterMajelis"
    />
</x-app-layout>

