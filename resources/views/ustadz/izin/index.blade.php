<x-app-layout>
    <x-slot name="header">Validasi Izin Santri</x-slot>

    <div class="space-y-4 pb-8 md:pb-0" x-data="{ showFilterModal: false, filterStatus: '{{ request('status', '') }}' }">
        <x-alert />

        {{-- Status Badge Summary --}}
        <div class="grid grid-cols-3 gap-2 md:gap-4">
            <a href="{{ route('ustadz.izin.index', array_merge(request()->query(), ['status' => 'Menunggu'])) }}"
               class="bg-white/60 backdrop-blur-xl rounded-2xl p-3 md:p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border {{ request('status') === 'Menunggu' ? 'border-yellow-400 ring-2 ring-yellow-200' : 'border-white/50' }} hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 flex flex-col items-center md:items-start text-center md:text-left">
                <div class="flex items-center justify-between mb-1 md:mb-2 w-full">
                    <div class="w-7 h-7 md:w-9 md:h-9 mx-auto md:mx-0 bg-yellow-100 rounded-full flex items-center justify-center text-yellow-600">
                        <i class="fas fa-hourglass-half text-[10px] md:text-sm"></i>
                    </div>
                </div>
                <h3 class="text-lg md:text-2xl font-bold text-yellow-600">{{ $countMenunggu }}</h3>
                <p class="text-[9px] md:text-xs font-medium text-gray-500 mt-0.5">Menunggu</p>
            </a>

            <a href="{{ route('ustadz.izin.index', array_merge(request()->query(), ['status' => 'Disetujui'])) }}"
               class="bg-white/60 backdrop-blur-xl rounded-2xl p-3 md:p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border {{ request('status') === 'Disetujui' ? 'border-green-400 ring-2 ring-green-200' : 'border-white/50' }} hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 flex flex-col items-center md:items-start text-center md:text-left">
                <div class="flex items-center justify-between mb-1 md:mb-2 w-full">
                    <div class="w-7 h-7 md:w-9 md:h-9 mx-auto md:mx-0 bg-green-100 rounded-full flex items-center justify-center text-green-600">
                        <i class="fas fa-check-circle text-[10px] md:text-sm"></i>
                    </div>
                </div>
                <h3 class="text-lg md:text-2xl font-bold text-green-600">{{ $countDisetujui }}</h3>
                <p class="text-[9px] md:text-xs font-medium text-gray-500 mt-0.5">Disetujui</p>
            </a>

            <a href="{{ route('ustadz.izin.index', array_merge(request()->query(), ['status' => 'Ditolak'])) }}"
               class="bg-white/60 backdrop-blur-xl rounded-2xl p-3 md:p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border {{ request('status') === 'Ditolak' ? 'border-red-400 ring-2 ring-red-200' : 'border-white/50' }} hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 flex flex-col items-center md:items-start text-center md:text-left">
                <div class="flex items-center justify-between mb-1 md:mb-2 w-full">
                    <div class="w-7 h-7 md:w-9 md:h-9 mx-auto md:mx-0 bg-red-100 rounded-full flex items-center justify-center text-red-500">
                        <i class="fas fa-times-circle text-[10px] md:text-sm"></i>
                    </div>
                </div>
                <h3 class="text-lg md:text-2xl font-bold text-red-500">{{ $countDitolak }}</h3>
                <p class="text-[9px] md:text-xs font-medium text-gray-500 mt-0.5">Ditolak</p>
            </a>
        </div>

        {{-- ========== DESKTOP VIEW ========== --}}
        {{-- Filter Bar Desktop --}}
        <div class="hidden md:block bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 p-4 mb-4 relative z-40">
            <form method="GET">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="flex flex-row items-end gap-3 w-full">
                    <div class="flex-1 min-w-0">
                        <label class="block text-xs font-semibold text-gray-600 mb-1 pl-1 truncate">Cari Santri</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Cari nama santri..."
                               class="w-full h-[38px] border border-gray-200 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent bg-white/80">
                    </div>

                    <div class="flex-1 min-w-0">
                        <label class="block text-xs font-semibold text-gray-600 mb-1 pl-1 truncate">Dari Tanggal</label>
                        <input type="date" name="dari" value="{{ request('dari') }}" class="w-full h-[38px] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent bg-white/80">
                    </div>

                    <div class="flex-1 min-w-0">
                        <label class="block text-xs font-semibold text-gray-600 mb-1 pl-1 truncate">Sampai Tanggal</label>
                        <input type="date" name="sampai" value="{{ request('sampai') }}" class="w-full h-[38px] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent bg-white/80">
                    </div>

                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        <button type="submit" class="bg-hijau-tua text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-hijau-muda transition-all shadow-sm flex items-center justify-center gap-1.5 h-[38px]">
                            <i class="fas fa-search"></i> Tampilkan
                        </button>

                        @if(request()->hasAny(['search','status','dari','sampai']))
                        <a href="{{ route('ustadz.izin.index') }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors h-[38px] flex items-center justify-center ml-auto shadow-sm">
                            Reset
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        {{-- Tabel Desktop --}}
        <div class="hidden md:block bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
                <span class="text-sm text-gray-500">
                    Total: <strong class="text-gray-800">{{ $izins->total() }}</strong> pengajuan
                    @if(request()->hasAny(['search','status','dari','sampai']))
                        <span class="ml-2 px-2 py-0.5 bg-hijau-tua/10 text-hijau-tua text-xs rounded-full font-medium">Filter aktif</span>
                    @endif
                </span>
            </div>
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-hijau-tua text-white">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Santri</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Tanggal Izin</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Kitab</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Alasan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Bukti</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($izins as $izin)
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-4 py-3 text-sm text-gray-400">{{ $loop->iteration + ($izins->currentPage()-1)*$izins->perPage() }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $izin->santri->user->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ \Carbon\Carbon::parse($izin->tanggal_izin)->translatedFormat('d M Y') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-800">
                            @if($izin->jadwal)
                                {{ $izin->jadwal->kitab->nama_kitab }}
                            @else
                                <span class="text-gray-400 text-xs">Semua Pelajaran</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 max-w-xs">{{ Str::limit($izin->alasan, 60) }}</td>
                        <td class="px-4 py-3 text-sm">
                            @if($izin->upload_bukti)
                                <a href="{{ asset('storage/'.$izin->upload_bukti) }}" target="_blank"
                                   class="text-hijau-tua hover:underline text-xs"><i class="fas fa-file-image mr-1"></i>Lihat</a>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($izin->status_validasi === 'Menunggu')
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 font-semibold">⏳ Menunggu</span>
                            @elseif($izin->status_validasi === 'Disetujui')
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 font-semibold">✅ Disetujui</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700 font-semibold">❌ Ditolak</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-1 flex-wrap">
                                <a href="{{ route('ustadz.izin.show', $izin) }}"
                                   class="px-2.5 py-1 text-xs bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                                    <i class="fas fa-eye mr-1"></i>Detail
                                </a>
                                @if($izin->status_validasi === 'Menunggu')
                                <form method="POST" action="{{ route('ustadz.izin.validasi', $izin) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status_validasi" value="Disetujui">
                                    <button type="submit" class="px-2.5 py-1 text-xs bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                        <i class="fas fa-check mr-1"></i>Setuju
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('ustadz.izin.validasi', $izin) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status_validasi" value="Ditolak">
                                    <button type="submit" class="px-2.5 py-1 text-xs bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                        <i class="fas fa-times mr-1"></i>Tolak
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-gray-400">
                            <i class="fas fa-envelope-open-text text-4xl mb-3 block text-gray-200"></i>
                            @if(request()->hasAny(['search','status','dari','sampai']))
                                Tidak ada izin yang cocok dengan filter.
                                <a href="{{ route('ustadz.izin.index') }}" class="block mt-2 text-sm text-hijau-tua hover:underline">Reset filter</a>
                            @else
                                Tidak ada pengajuan izin.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @if($izins->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-white/40">
                {{ $izins->appends(request()->query())->links() }}
            </div>
            @endif
        </div>

        {{-- ========== MOBILE VIEW ========== --}}
        {{-- Mobile Header/Filter Bar --}}
        <div class="md:hidden flex items-center gap-2 mb-3 bg-white p-3 rounded-xl shadow-sm border border-gray-100 sticky top-0 z-30">
            <form method="GET" class="flex-1 relative flex items-center">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <input type="hidden" name="dari" value="{{ request('dari') }}">
                <input type="hidden" name="sampai" value="{{ request('sampai') }}">

                <i class="fas fa-search absolute left-3 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari santri..."
                       class="w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent bg-gray-50/50">
            </form>
            <button type="button" @click="showFilterModal = true" class="p-2 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 flex items-center justify-center w-10 h-10 bg-white">
                <i class="fas fa-sliders-h"></i>
            </button>
        </div>

        <div class="md:hidden flex items-center justify-between mb-3 px-1">
            <div>
                <h2 class="text-sm font-bold text-gray-800">Daftar Pengajuan Izin</h2>
                <p class="text-[10px] text-gray-500">Total {{ $izins->total() }} Izin</p>
            </div>
            @if(request()->hasAny(['search','status','dari','sampai']))
            <a href="{{ route('ustadz.izin.index') }}" class="px-2 py-1 text-[10px] font-semibold text-red-600 bg-red-50 border border-red-200 rounded-full">
                <i class="fas fa-times mr-0.5"></i> Reset
            </a>
            @endif
        </div>

        {{-- Mobile List --}}
        <div class="md:hidden space-y-0 bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 overflow-hidden">
            @forelse($izins as $izin)
            <div class="p-3 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors relative">
                <div class="flex gap-3">
                    <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-user text-gray-400 text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0 pr-6">
                        <h4 class="text-xs font-bold text-gray-800 truncate">{{ $izin->santri->user->name ?? '-' }}</h4>
                        <div class="text-[10px] text-gray-500 mt-0.5 flex flex-col gap-0.5">
                            <span class="flex items-center gap-1"><i class="fas fa-calendar-day text-gray-400"></i> {{ \Carbon\Carbon::parse($izin->tanggal_izin)->translatedFormat('d M Y') }}</span>
                            @if($izin->jadwal)
                            <span class="truncate flex items-center gap-1"><i class="fas fa-book-open text-gray-400"></i> {{ $izin->jadwal->kitab->nama_kitab }}</span>
                            @endif
                        </div>
                        <div class="mt-2 text-[10px] text-gray-600 line-clamp-2 italic bg-gray-50 p-1.5 rounded-lg border border-gray-100">
                            "{{ $izin->alasan }}"
                        </div>
                    </div>
                </div>

                <div class="mt-3 flex items-center justify-between gap-2">
                    {{-- Status Badge --}}
                    @if($izin->status_validasi === 'Menunggu')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 font-semibold border border-yellow-200">
                            <i class="fas fa-hourglass-half text-[10px]"></i> Menunggu
                        </span>
                    @elseif($izin->status_validasi === 'Disetujui')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs rounded-full bg-green-100 text-green-800 font-semibold border border-green-200">
                            <i class="fas fa-check-circle text-[10px]"></i> Disetujui
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs rounded-full bg-red-100 text-red-700 font-semibold border border-red-200">
                            <i class="fas fa-times-circle text-[10px]"></i> Ditolak
                        </span>
                    @endif

                    {{-- Action Buttons --}}
                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('ustadz.izin.show', $izin) }}"
                           class="inline-flex items-center gap-1 px-2.5 py-1.5 text-[11px] font-medium bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors">
                            <i class="fas fa-eye text-[10px]"></i> Detail
                        </a>
                        @if($izin->status_validasi === 'Menunggu')
                        <form method="POST" action="{{ route('ustadz.izin.validasi', $izin) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status_validasi" value="Disetujui">
                            <button type="submit"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-[11px] font-semibold bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors shadow-sm">
                                <i class="fas fa-check text-[10px]"></i> Setuju
                            </button>
                        </form>
                        <form method="POST" action="{{ route('ustadz.izin.validasi', $izin) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status_validasi" value="Ditolak">
                            <button type="submit"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-[11px] font-semibold bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors shadow-sm">
                                <i class="fas fa-times text-[10px]"></i> Tolak
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-10">
                <i class="fas fa-envelope-open-text text-4xl text-gray-200 mb-2"></i>
                <p class="text-xs text-gray-500">Tidak ada pengajuan izin</p>
            </div>
            @endforelse
        </div>

        {{-- Pagination Mobile --}}
        @if($izins->hasPages())
        <div class="md:hidden mt-4 pb-8 px-1">
            <div class="flex items-center justify-between bg-white rounded-xl shadow-sm border border-gray-100 p-2">
                @if ($izins->onFirstPage())
                    <span class="p-2 w-10 h-10 flex items-center justify-center rounded-lg text-gray-300"><i class="fas fa-chevron-left"></i></span>
                @else
                    <a href="{{ $izins->appends(request()->query())->previousPageUrl() }}" class="p-2 w-10 h-10 flex items-center justify-center rounded-lg text-[#187a41] hover:bg-green-50 transition-colors"><i class="fas fa-chevron-left"></i></a>
                @endif
                <span class="text-xs font-semibold text-gray-600">Hal {{ $izins->currentPage() }} dari {{ $izins->lastPage() }}</span>
                @if ($izins->hasMorePages())
                    <a href="{{ $izins->appends(request()->query())->nextPageUrl() }}" class="p-2 w-10 h-10 flex items-center justify-center rounded-lg text-[#187a41] hover:bg-green-50 transition-colors"><i class="fas fa-chevron-right"></i></a>
                @else
                    <span class="p-2 w-10 h-10 flex items-center justify-center rounded-lg text-gray-300"><i class="fas fa-chevron-right"></i></span>
                @endif
            </div>
        </div>
        @endif

        {{-- Mobile Filter Modal --}}
        <div x-show="showFilterModal" x-effect="document.body.style.overflow = showFilterModal ? 'hidden' : ''" class="md:hidden fixed inset-0 z-50 flex flex-col justify-end bg-black/50" x-transition.opacity style="display:none">
            <div @click.away="showFilterModal = false" class="bg-white w-full rounded-t-2xl shadow-xl overflow-hidden flex flex-col max-h-[85vh]"
                 x-show="showFilterModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full">
                <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center flex-shrink-0">
                    <h3 class="text-sm font-bold text-gray-800">Filter Validasi Izin</h3>
                    <button @click="showFilterModal = false" class="text-gray-400 bg-gray-50 rounded-full w-8 h-8 flex items-center justify-center"><i class="fas fa-times"></i></button>
                </div>
                <div class="p-5 overflow-y-auto">
                    <form method="GET" action="{{ route('ustadz.izin.index') }}" class="space-y-4">
                        <input type="hidden" name="search" value="{{ request('search') }}">

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-2">Status Validasi</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label :class="filterStatus === '' ? 'border-[#187a41] bg-green-50' : 'border-gray-200 hover:bg-gray-50'" class="flex items-center gap-2 p-2 border rounded-lg cursor-pointer transition-colors">
                                    <input type="radio" name="status" value="" x-model="filterStatus" class="hidden">
                                    <span :class="filterStatus === '' ? 'text-[#187a41]' : 'text-gray-600'" class="text-[10px] font-medium">Semua Status</span>
                                </label>
                                <label :class="filterStatus === 'Menunggu' ? 'border-yellow-500 bg-yellow-50' : 'border-gray-200 hover:bg-gray-50'" class="flex items-center gap-2 p-2 border rounded-lg cursor-pointer transition-colors">
                                    <input type="radio" name="status" value="Menunggu" x-model="filterStatus" class="hidden">
                                    <i class="fas fa-hourglass-half text-[10px]" :class="filterStatus === 'Menunggu' ? 'text-yellow-600' : 'text-yellow-500'"></i>
                                    <span :class="filterStatus === 'Menunggu' ? 'text-yellow-600' : 'text-gray-600'" class="text-[10px] font-medium">Menunggu</span>
                                </label>
                                <label :class="filterStatus === 'Disetujui' ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:bg-gray-50'" class="flex items-center gap-2 p-2 border rounded-lg cursor-pointer transition-colors">
                                    <input type="radio" name="status" value="Disetujui" x-model="filterStatus" class="hidden">
                                    <i class="fas fa-check-circle text-[10px]" :class="filterStatus === 'Disetujui' ? 'text-green-600' : 'text-green-500'"></i>
                                    <span :class="filterStatus === 'Disetujui' ? 'text-green-600' : 'text-gray-600'" class="text-[10px] font-medium">Disetujui</span>
                                </label>
                                <label :class="filterStatus === 'Ditolak' ? 'border-red-500 bg-red-50' : 'border-gray-200 hover:bg-gray-50'" class="flex items-center gap-2 p-2 border rounded-lg cursor-pointer transition-colors">
                                    <input type="radio" name="status" value="Ditolak" x-model="filterStatus" class="hidden">
                                    <i class="fas fa-times-circle text-[10px]" :class="filterStatus === 'Ditolak' ? 'text-red-600' : 'text-red-500'"></i>
                                    <span :class="filterStatus === 'Ditolak' ? 'text-red-600' : 'text-gray-600'" class="text-[10px] font-medium">Ditolak</span>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Dari Tanggal</label>
                                <input type="date" name="dari" value="{{ request('dari') }}" class="w-full border border-gray-200 rounded-lg px-2 py-2 text-[10px] focus:ring-2 focus:ring-hijau-tua">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Sampai Tanggal</label>
                                <input type="date" name="sampai" value="{{ request('sampai') }}" class="w-full border border-gray-200 rounded-lg px-2 py-2 text-[10px] focus:ring-2 focus:ring-hijau-tua">
                            </div>
                        </div>

                        <div class="flex gap-2 pt-2">
                            <button type="button" @click="window.location.href='{{ route('ustadz.izin.index') }}'" class="flex-1 py-2.5 text-xs font-semibold text-gray-600 bg-gray-100 rounded-xl">Reset</button>
                            <button type="submit" @click="showFilterModal = false" class="flex-1 py-2.5 text-xs font-semibold text-white bg-[#187a41] rounded-xl">Terapkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

