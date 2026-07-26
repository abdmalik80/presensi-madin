<x-app-layout>
    @php
    $tipeOptions = [
        'image' => '🖼️ Gambar',
        'pdf'   => '📄 PDF',
        'doc'   => '📝 Word',
        'xls'   => '📊 Excel',
        'ppt'   => '📽️ PowerPoint',
        'other' => '📁 Lainnya',
    ];
    $bulanOptions = [];
    foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bln) {
        $bulanOptions[$i+1] = $bln;
    }
    $tahunOptions = [];
    foreach($tahunList as $th) {
        $tahunOptions[$th] = $th;
    }
    $jadwalOptions = [];
    foreach($jadwals as $j) {
        $jadwalOptions[$j->id] = ($j->kitab->nama_kitab ?? '-') . ' (' . $j->hari . ')';
    }
    @endphp
    <x-slot name="header">Berkas & Dokumentasi Majelis</x-slot>

    <div class="space-y-5" x-data="{ showFilterModal: false }">

        {{-- Header Info & Mobile Filter --}}
        <div class="bg-white/70 backdrop-blur-xl rounded-2xl border border-white/50 shadow-sm p-4 md:p-5 flex items-center justify-between gap-3 sticky top-0 z-30 md:static md:z-auto">
            <div class="min-w-0 flex-1">
                <h2 class="text-sm md:text-lg font-bold text-gray-800 truncate">Berkas & Dokumentasi</h2>
                <p class="text-[10px] md:text-sm text-gray-500 mt-0.5 truncate">Materi dan Dokumentasi Majelis</p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                {{-- Desktop Info --}}
                <div class="hidden md:flex items-center text-sm text-gray-400">
                    <i class="fas fa-folder-open mr-1 text-green-500"></i>
                    <strong class="text-gray-700 mr-1">{{ $dokumentasis->total() }}</strong> berkas
                    @if(request()->hasAny(['jadwal_id','tipe','bulan','tahun']))
                        <span class="ml-2 px-2 py-0.5 bg-hijau-tua/10 text-hijau-tua text-xs rounded-full font-medium">Filter aktif</span>
                    @endif
                </div>

                {{-- Mobile Info & Filter --}}
                <div class="md:hidden flex flex-col items-end">
                    <span class="text-[10px] text-gray-500"><strong class="text-gray-700">{{ $dokumentasis->total() }}</strong> berkas</span>
                    @if(request()->hasAny(['jadwal_id','tipe','bulan','tahun']))
                        <span class="text-[8px] text-hijau-tua font-bold mt-0.5 bg-hijau-tua/10 px-1.5 py-0.5 rounded">Filter Aktif</span>
                    @endif
                </div>

                <button type="button" @click="showFilterModal = true" class="md:hidden p-2 border border-hijau-tua bg-green-50 text-hijau-tua rounded-lg flex items-center justify-center w-8 h-8 transition-colors hover:bg-hijau-tua hover:text-white">
                    <i class="fas fa-filter text-xs"></i>
                </button>
            </div>
        </div>

        {{-- Filter Desktop --}}
        <div class="hidden md:block bg-white/60 backdrop-blur-xl rounded-2xl border border-white/50 shadow-sm p-4" style="position: relative; z-index: 40;">
            <form method="GET" action="{{ route('santri.dokumentasi.index') }}" class="flex flex-wrap gap-3 items-end">

                {{-- Filter Kitab/Jadwal --}}
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Kitab</label>
                    <x-custom-select name="jadwal_id" :options="$jadwalOptions" :selected="request('jadwal_id')" placeholder="Semua Kitab" />
                </div>

                {{-- Filter Tipe --}}
                <div class="flex-1 min-w-[130px]">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tipe Berkas</label>
                    <x-custom-select name="tipe" :options="$tipeOptions" :selected="request('tipe')" placeholder="Semua Tipe" />
                </div>
                {{-- Filter Bulan --}}
                <div class="flex-1 min-w-[120px]">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Bulan</label>
                    <x-custom-select name="bulan" :options="$bulanOptions" :selected="request('bulan')" placeholder="Semua Bulan" />
                </div>

                {{-- Filter Tahun --}}
                <div class="flex-1 min-w-[130px]">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tahun</label>
                    <x-custom-select name="tahun" :options="$tahunOptions" :selected="request('tahun')" placeholder="Semua Tahun" />
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-hijau-tua text-white text-sm rounded-lg hover:bg-hijau-muda transition-colors font-medium">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    @if(request()->hasAny(['jadwal_id','tipe','bulan','tahun']))
                    <a href="{{ route('santri.dokumentasi.index') }}" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition-colors font-medium">
                        <i class="fas fa-times mr-1"></i> Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- ===== UNIFIED GRID ===== --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5" style="position: relative; z-index: 10;">
            @forelse($dokumentasis as $dok)
            <div class="group bg-white/60 backdrop-blur-xl rounded-2xl border border-white/50 shadow-sm overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-200">
                
                {{-- Preview Area --}}
                @if($dok->tipe_file === 'image' && $dok->upload_file)
                <div class="relative overflow-hidden rounded-t-2xl cursor-pointer"
                     onclick="openLightbox('{{ asset('storage/'.$dok->upload_file) }}', '{{ addslashes($dok->judul) }}')">
                    <img src="{{ asset('storage/'.$dok->upload_file) }}"
                         class="w-full h-40 md:h-44 object-cover group-hover:scale-105 transition-transform duration-300"
                         alt="{{ $dok->judul }}">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent pointer-events-none"></div>
                    <span class="absolute top-2 right-2 bg-white/90 text-purple-600 text-[10px] font-bold px-2 py-0.5 rounded-full shadow pointer-events-none">
                        <i class="fas fa-image mr-1"></i>Gambar
                    </span>
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/20 pointer-events-none">
                        <span class="bg-white/90 text-gray-800 text-xs font-semibold px-3 py-1.5 rounded-full shadow-md flex items-center gap-1.5">
                            <i class="fas fa-expand-alt text-green-600"></i> Lihat Penuh
                        </span>
                    </div>
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
                    </div>

                    @if($dok->deskripsi)
                    <p class="text-[10px] md:text-xs text-gray-600 leading-relaxed line-clamp-2">{{ Str::limit($dok->deskripsi, 80) }}</p>
                    @endif

                    @if($dok->upload_file)
                    <div class="pt-2 border-t border-gray-100 flex gap-2">
                        @if($dok->tipe_file === 'image')
                        <button onclick="openLightbox('{{ asset('storage/'.$dok->upload_file) }}', '{{ addslashes($dok->judul) }}')"
                                class="flex-1 inline-flex items-center justify-center gap-1.5 text-xs bg-purple-50 text-purple-700 border border-purple-200 font-semibold px-3 py-1.5 rounded-lg hover:bg-purple-100 transition-colors">
                            <i class="fas fa-expand-alt"></i> Lihat
                        </button>
                        @else
                        <a href="{{ asset('storage/'.$dok->upload_file) }}" target="_blank"
                           class="flex-1 inline-flex items-center justify-center gap-1.5 text-xs bg-gray-50 text-gray-700 border border-gray-200 font-semibold px-3 py-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fas fa-eye"></i> Lihat
                        </a>
                        @endif
                        <a href="{{ asset('storage/'.$dok->upload_file) }}" download="{{ $dok->nama_file ?? $dok->judul }}"
                           class="flex-1 inline-flex items-center justify-center gap-1.5 text-xs bg-green-50 text-green-700 border border-green-200 font-semibold px-3 py-1.5 rounded-lg hover:bg-green-100 transition-colors">
                            <i class="fas fa-download"></i> Unduh
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="col-span-full py-20 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 md:w-20 md:h-20 rounded-full bg-gray-100 mb-3 md:mb-4">
                    <i class="fas fa-folder-open text-3xl md:text-4xl text-gray-300"></i>
                </div>
                @if(request()->hasAny(['jadwal_id','tipe','bulan','tahun']))
                    <p class="text-gray-400 font-medium text-sm md:text-base">Tidak ada berkas yang cocok dengan filter.</p>
                    <a href="{{ route('santri.dokumentasi.index') }}" class="inline-flex items-center gap-1.5 mt-4 text-xs md:text-sm text-hijau-tua hover:underline font-medium">
                        <i class="fas fa-times"></i> Reset Filter
                    </a>
                @else
                    <p class="text-sm text-gray-400 font-medium">Belum ada berkas dari ustadz.</p>
                @endif
            </div>
            @endforelse



        {{-- Pagination --}}
        @if($dokumentasis->hasPages())
        <div class="flex justify-center">
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
                    <form method="GET" action="{{ route('santri.dokumentasi.index') }}" class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-2">Pilih Kitab / Pelajaran</label>
                            <x-custom-select name="jadwal_id" :options="$jadwalOptions" :selected="request('jadwal_id')" placeholder="Semua Kitab" />
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
                            <button type="button" @click="window.location.href='{{ route('santri.dokumentasi.index') }}'" class="flex-1 py-2.5 text-xs font-semibold text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">Reset</button>
                            <button type="submit" @click="showFilterModal = false" class="flex-1 py-2.5 text-xs font-semibold text-white bg-[#187a41] hover:bg-hijau-muda rounded-xl transition-colors">Tampilkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Lightbox --}}
    <div id="lightbox"
         class="fixed inset-0 z-50 hidden flex items-center justify-center p-4"
         style="background: rgba(0,0,0,0.85); backdrop-filter: blur(8px);"
         onclick="closeLightbox(event)">
        <div class="relative max-w-4xl w-full mx-auto" onclick="event.stopPropagation()">
            <button onclick="closeLightbox()"
                    class="absolute -top-10 right-0 text-white/80 hover:text-white text-2xl transition-colors z-10">
                <i class="fas fa-times"></i>
            </button>
            <img id="lightbox-img" src="" alt=""
                 class="w-full max-h-[75vh] object-contain rounded-2xl shadow-2xl">
            <div class="bg-white/10 backdrop-blur-sm rounded-b-2xl px-5 py-3 flex items-center justify-between mt-0.5">
                <p id="lightbox-title" class="text-white font-semibold text-sm truncate"></p>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openLightbox(src, title) {
            const lb = document.getElementById('lightbox');
            document.getElementById('lightbox-img').src = src;
            document.getElementById('lightbox-title').textContent = title;
            lb.classList.remove('hidden');
            lb.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
        function closeLightbox(event) {
            if (!event || event.target === document.getElementById('lightbox')) {
                const lb = document.getElementById('lightbox');
                lb.classList.add('hidden');
                lb.classList.remove('flex');
                document.body.style.overflow = '';
            }
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeLightbox();
        });
    </script>
    @endpush
</x-app-layout>
