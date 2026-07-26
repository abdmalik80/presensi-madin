<x-app-layout>
    @php
    $jadwalOptions = [];
    foreach($jadwals as $j) $jadwalOptions[$j->id] = ($j->majelis->nama_majelis ?? '') . ' - ' . $j->hari;
    @endphp
    <x-slot name="header">Presensi</x-slot>

    <div class="space-y-4 pb-10 md:pb-0" x-data="{ showFilterModal: false }">
        <x-alert />
        

        {{-- Jadwal Hari Ini Section --}}
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Semua Jadwal Hari Ini</h2>
                    <p class="text-xs text-gray-500">{{ $hariIniStr }}, {{ now()->translatedFormat('d M Y') }}</p>
                </div>
            </div>

            @if($jadwalHariIni->isEmpty())
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 text-center">
                    <div class="w-12 h-12 bg-amber-100 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-calendar-times text-xl"></i>
                    </div>
                    <p class="text-sm font-semibold text-amber-700">Tidak ada jadwal pengajian hari ini.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($jadwalHariIni as $j)
                        @php
                            $presensiHariIni = $j->presensis->first();
                            $isOpen = $presensiHariIni && $presensiHariIni->status === 'dibuka';
                        @endphp
                        <div class="bg-white/80 backdrop-blur-md rounded-2xl border border-green-100 p-5 shadow-sm hover:shadow-md transition-all group relative overflow-hidden flex flex-col h-full">
                            <div class="absolute -right-6 -top-6 w-24 h-24 bg-green-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
                            <div class="relative z-10 flex flex-col h-full">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="w-10 h-10 bg-green-100 text-hijau-tua rounded-xl flex items-center justify-center font-bold">
                                        <i class="fas fa-mosque"></i>
                                    </div>
                                    <span class="px-2.5 py-1 bg-green-50 border border-green-100 text-green-700 text-[10px] font-bold uppercase tracking-wider rounded-lg">
                                        {{ $j->jam_mulai ? substr($j->jam_mulai, 0, 5) : 'Waktu TBA' }}
                                    </span>
                                </div>
                                <div class="flex-grow">
                                    <h3 class="text-base font-bold text-gray-800 mb-1 group-hover:text-hijau-tua transition-colors">
                                        {{ $j->majelis->nama_majelis ?? 'Majelis Tidak Diketahui' }}
                                    </h3>
                                    <p class="text-xs text-gray-500 flex items-center gap-1.5 mb-1">
                                        <i class="fas fa-book-open text-gray-400"></i>
                                        Kitab {{ $j->kitab->nama_kitab ?? '-' }}
                                    </p>
                                    <p class="text-xs text-gray-500 flex items-center gap-1.5 mb-3">
                                        <i class="fas fa-chalkboard-teacher text-gray-400"></i>
                                        {{ $j->ustadz->user->name ?? 'Belum ada ustadz' }}
                                    </p>

                                    {{-- Status absen santri --}}
                                    <div class="mb-4">
                                        @if($isOpen)
                                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold bg-green-100 text-green-700 px-3 py-1.5 rounded-full border border-green-200">
                                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span> Presensi Santri: Dibuka
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold bg-gray-100 text-gray-500 px-3 py-1.5 rounded-full border border-gray-200">
                                                <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span> Presensi Santri: Ditutup
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex flex-col gap-2 mt-auto">
                                    <div class="grid grid-cols-2 gap-2">
                                        <a href="{{ route('admin.jadwal.show', $j->id) }}" 
                                           class="flex items-center justify-center gap-1.5 w-full py-2.5 bg-white border border-gray-200 text-gray-600 text-center text-xs font-semibold rounded-xl hover:bg-gray-50 hover:text-hijau-tua hover:border-hijau-tua/30 transition-all">
                                            <i class="fas fa-folder-open"></i> Detail
                                        </a>
                                        <a href="{{ route('admin.presensi.create', ['jadwal_id' => $j->id]) }}" 
                                           class="flex items-center justify-center gap-1.5 w-full py-2.5 bg-hijau-tua hover:bg-hijau-muda text-white text-center text-xs font-semibold rounded-xl transition-all shadow-md shadow-hijau-tua/20 hover:-translate-y-0.5">
                                            <i class="fas fa-edit"></i> Isi Manual
                                        </a>
                                    </div>
                                    @if($isOpen)
                                        <form method="POST" action="{{ route('admin.presensi.toggle') }}">
                                            @csrf
                                            <input type="hidden" name="jadwal_id" value="{{ $j->id }}">
                                            <button type="submit" onclick="return confirm('Tutup presensi santri sekarang?')"
                                                    class="w-full py-2.5 text-xs font-semibold rounded-xl transition-all border flex items-center justify-center gap-1.5 bg-red-50 border-red-200 text-red-600 hover:bg-red-100">
                                                <i class="fas fa-lock"></i> Tutup Absen Santri
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" onclick="openModalBukaAbsen('{{ $j->id }}', '{{ $j->jam_mulai ? \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') : '' }}', '{{ $j->jam_selesai ? \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') : '' }}')"
                                                class="w-full py-2.5 text-xs font-semibold rounded-xl transition-all border flex items-center justify-center gap-1.5 bg-blue-50 border-blue-200 text-blue-600 hover:bg-blue-100">
                                            <i class="fas fa-lock-open"></i> Buka Absen Santri
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ========== DESKTOP: Riwayat Presensi ========== --}}
        <div class="hidden md:block bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-hijau-tua/10 flex items-center justify-center text-hijau-tua">
                        <i class="fas fa-clipboard-list text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-700">Riwayat Presensi</h3>
                        <p class="text-[10px] text-gray-400">{{ $presensis->total() }} total catatan</p>
                    </div>
                </div>
                {{-- Desktop Filter Form --}}
                <div class="hidden sm:flex flex-wrap items-center gap-2">
                    <form method="GET" action="{{ route('admin.presensi.index') }}" class="flex flex-wrap gap-2 items-center" 
                          x-data="{ mode: '{{ request('jadwal_id') ? 'jadwal' : 'tanggal' }}' }">
                        
                        <!-- Toggle Mode Pencarian -->
                        <div class="flex bg-gray-100/80 p-1 rounded-lg border border-gray-200 items-center h-[36px]">
                            <button type="button" @click="mode = 'tanggal'" 
                                    :class="mode === 'tanggal' ? 'bg-white shadow-sm text-[#187a41] font-bold border border-gray-100' : 'text-gray-500 hover:text-gray-700'"
                                    class="px-3 py-1 text-xs rounded-md transition-all h-full flex items-center">
                                <i class="fas fa-calendar-day mr-1.5"></i> Tanggal
                            </button>
                            <button type="button" @click="mode = 'jadwal'" 
                                    :class="mode === 'jadwal' ? 'bg-white shadow-sm text-[#187a41] font-bold border border-gray-100' : 'text-gray-500 hover:text-gray-700'"
                                    class="px-3 py-1 text-xs rounded-md transition-all h-full flex items-center">
                                <i class="fas fa-mosque mr-1.5"></i> Majelis
                            </button>
                        </div>

                        <!-- Input Tanggal (Hanya aktif jika mode = tanggal) -->
                        <div x-show="mode === 'tanggal'" style="display: {{ request('jadwal_id') ? 'none' : 'flex' }}; gap: 0.5rem;" class="items-center">
                            <input type="date" name="dari" value="{{ request('dari') }}" :disabled="mode !== 'tanggal'"
                                class="border border-gray-200 rounded-lg px-2 py-1.5 text-xs focus:ring-2 focus:ring-hijau-tua bg-white/80 h-[36px]">
                            <span class="text-xs text-gray-500">-</span>
                            <input type="date" name="sampai" value="{{ request('sampai') }}" :disabled="mode !== 'tanggal'"
                                class="border border-gray-200 rounded-lg px-2 py-1.5 text-xs focus:ring-2 focus:ring-hijau-tua bg-white/80 h-[36px]">
                        </div>

                        <!-- Input Jadwal (Hanya aktif jika mode = jadwal) -->
                        <div x-show="mode === 'jadwal'" style="display: {{ request('jadwal_id') ? 'block' : 'none' }};">
                            <x-custom-select name="jadwal_id" :options="$jadwalOptions" :selected="request('jadwal_id')" placeholder="Semua Jadwal" />
                        </div>

                        <button type="submit" class="bg-hijau-tua text-white px-4 py-1.5 rounded-lg text-sm hover:bg-hijau-muda transition-colors flex items-center gap-1 h-[36px]">
                            <i class="fas fa-search text-xs"></i> Cari
                        </button>
                        
                        @if(request()->anyFilled(['dari','sampai','jadwal_id']))
                        <a href="{{ route('admin.presensi.index') }}" class="px-3 py-1.5 rounded-lg text-sm border border-gray-200 text-gray-500 hover:bg-gray-50 transition-colors h-[36px] flex items-center">
                            <i class="fas fa-times text-xs mr-1"></i> Reset
                        </a>
                        @endif
                    </form>
                    <div class="h-6 w-px bg-gray-200 mx-1"></div>
                    <a href="{{ route('admin.presensi.create') }}"
                       class="inline-flex items-center gap-2 bg-hijau-tua/10 text-hijau-tua border border-hijau-tua/20 px-4 py-1.5 h-[36px] rounded-lg text-sm font-semibold hover:bg-hijau-tua hover:text-white transition-all shadow-sm">
                        <i class="fas fa-calendar-plus text-xs"></i> Rekam Manual
                    </a>
                </div>
            </div>
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-hijau-tua text-white">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Majelis</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Ustadz</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Jumlah Hadir</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($presensis as $p)
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $loop->iteration + ($presensis->currentPage()-1)*$presensis->perPage() }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d M Y') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $p->jadwal->majelis->nama_majelis ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $p->jadwal->ustadz->user->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 font-semibold">
                                {{ $p->details->where('status_kehadiran','Hadir')->count() }} hadir
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1">
                                <a href="{{ route('admin.presensi.show', $p) }}" class="p-1.5 rounded-lg text-hijau-tua hover:bg-hijau-muda/20 transition-colors" title="Detail"><i class="fas fa-eye text-sm"></i></a>
                                <a href="{{ route('admin.presensi.edit', $p) }}" class="p-1.5 rounded-lg text-amber-500 hover:bg-amber-50 transition-colors" title="Edit"><i class="fas fa-edit text-sm"></i></a>
                                <form method="POST" action="{{ route('admin.presensi.destroy', $p) }}" class="inline" onsubmit="return confirm('Yakin hapus data presensi ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-merah hover:bg-red-50 transition-colors"><i class="fas fa-trash text-sm"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-gray-400">
                            <i class="fas fa-clipboard-check text-4xl mb-3 block text-gray-200"></i>
                            Tidak ada data presensi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @if($presensis->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-white/40">
                {{ $presensis->appends(request()->query())->links() }}
            </div>
            @endif
        </div>

        {{-- ========== MOBILE: Riwayat Presensi ========== --}}
        {{-- Filter bar --}}
        <div class="md:hidden flex items-center gap-2 mb-3 bg-white p-3 rounded-xl shadow-sm border border-gray-100 sticky top-0 z-30">
            <div class="flex-1">
                <p class="text-xs font-bold text-gray-700">Riwayat Presensi</p>
                <p class="text-[10px] text-gray-400">{{ $presensis->total() }} catatan</p>
            </div>
            <button type="button" @click="showFilterModal = true" class="p-2 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 flex items-center justify-center w-10 h-10 bg-white">
                <i class="fas fa-sliders-h"></i>
            </button>
            <a href="{{ route('admin.presensi.create') }}" class="inline-flex items-center gap-1 bg-[#187a41] text-white px-3 py-2 rounded-lg text-xs font-medium">
                <i class="fas fa-calendar-plus"></i> Manual
            </a>
        </div>

        <div class="md:hidden space-y-0 bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50">
            @forelse($presensis as $p)
            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-3 flex-1 pl-3">
                    <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-clipboard-check text-[#187a41] text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0 pr-2">
                        <h4 class="text-xs font-bold text-gray-800 truncate">{{ $p->jadwal->majelis->nama_majelis ?? '-' }}</h4>
                        <div class="text-[10px] text-gray-500 mt-0.5 flex items-center gap-1">
                            <span>{{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d M Y') }}</span>
                            <span class="text-gray-300">&bull;</span>
                            <span class="font-semibold text-green-700">{{ $p->details->where('status_kehadiran','Hadir')->count() }} hadir</span>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col items-end justify-center self-stretch py-1 pr-3">
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @click.away="open = false" class="text-gray-400 hover:text-gray-700 p-1 rounded">
                            <i class="fas fa-ellipsis-v text-xs px-1"></i>
                        </button>
                        <div x-show="open" x-transition.opacity class="absolute right-0 top-full mt-1 w-32 bg-white rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.15)] border border-gray-100 py-1.5 z-50" style="display:none">
                            <a href="{{ route('admin.presensi.show', $p) }}" class="block px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 hover:text-[#187a41]"><i class="fas fa-eye w-4"></i> Detail</a>
                            <a href="{{ route('admin.presensi.edit', $p) }}" class="block px-4 py-2 text-xs font-medium text-amber-600 hover:bg-gray-50"><i class="fas fa-edit w-4"></i> Edit</a>
                            <form method="POST" action="{{ route('admin.presensi.destroy', $p) }}" onsubmit="return confirm('Yakin hapus?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="block w-full text-left px-4 py-2 text-xs font-medium text-red-600 hover:bg-gray-50"><i class="fas fa-trash w-4"></i> Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-10">
                <i class="fas fa-clipboard-check text-4xl text-gray-200 mb-2"></i>
                <p class="text-xs text-gray-500">Tidak ada data presensi</p>
            </div>
            @endforelse
        </div>

        {{-- Pagination Mobile --}}
        @if($presensis->hasPages())
        <div class="md:hidden mt-4 pb-8 px-1">
            <div class="flex items-center justify-between bg-white rounded-xl shadow-sm border border-gray-100 p-2">
                @if ($presensis->onFirstPage())
                    <span class="p-2 w-10 h-10 flex items-center justify-center rounded-lg text-gray-300"><i class="fas fa-chevron-left"></i></span>
                @else
                    <a href="{{ $presensis->appends(request()->query())->previousPageUrl() }}" class="p-2 w-10 h-10 flex items-center justify-center rounded-lg text-[#187a41] hover:bg-green-50 transition-colors"><i class="fas fa-chevron-left"></i></a>
                @endif
                <span class="text-xs font-semibold text-gray-600">Hal {{ $presensis->currentPage() }} dari {{ $presensis->lastPage() }}</span>
                @if ($presensis->hasMorePages())
                    <a href="{{ $presensis->appends(request()->query())->nextPageUrl() }}" class="p-2 w-10 h-10 flex items-center justify-center rounded-lg text-[#187a41] hover:bg-green-50 transition-colors"><i class="fas fa-chevron-right"></i></a>
                @else
                    <span class="p-2 w-10 h-10 flex items-center justify-center rounded-lg text-gray-300"><i class="fas fa-chevron-right"></i></span>
                @endif
            </div>
        </div>
        @endif

        {{-- Filter Modal Mobile --}}
        <div x-show="showFilterModal" x-effect="document.body.style.overflow = showFilterModal ? 'hidden' : ''" class="md:hidden fixed inset-0 z-50 flex flex-col justify-end bg-black/50" x-transition.opacity style="display:none">
            <div @click.away="showFilterModal = false" class="bg-white w-full rounded-t-2xl shadow-xl"
                 x-show="showFilterModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full">
                <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-gray-800">Filter Presensi</h3>
                    <button @click="showFilterModal = false" class="text-gray-400 bg-gray-50 rounded-full w-8 h-8 flex items-center justify-center"><i class="fas fa-times"></i></button>
                </div>
                <form method="GET" action="{{ route('admin.presensi.index') }}" class="p-5 space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Dari</label>
                            <input type="date" name="dari" value="{{ request('dari') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-hijau-tua">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Sampai</label>
                            <input type="date" name="sampai" value="{{ request('sampai') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-hijau-tua">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Jadwal / Majelis</label>
                        <x-custom-select name="jadwal_id" :options="$jadwalOptions" :selected="request('jadwal_id')" placeholder="Semua Jadwal" width="w-full" />
                    </div>
                    <div class="flex gap-2 pt-2">
                        <button type="button" @click="window.location.href='{{ route('admin.presensi.index') }}'" class="flex-1 py-2.5 text-xs font-semibold text-gray-600 bg-gray-100 rounded-xl">Reset</button>
                        <button type="submit" @click="showFilterModal = false" class="flex-1 py-2.5 text-xs font-semibold text-white bg-[#187a41] rounded-xl">Terapkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        function openModalBukaAbsen(jadwalId, jamMulai, jamSelesai) {
            document.getElementById('buka_jadwal_id').value = jadwalId;
            document.getElementById('input_jam_buka').value = jamMulai;
            document.getElementById('input_jam_tutup').value = jamSelesai;
            
            const modal = document.getElementById('modalBukaAbsen');
            const content = document.getElementById('modalBukaAbsenContent');
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }
        
        function closeModalBukaAbsen() {
            const modal = document.getElementById('modalBukaAbsen');
            const content = document.getElementById('modalBukaAbsenContent');
            
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 300);
        }
    </script>
    @endpush

    @push('modals')
    <div id="modalBukaAbsen" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4 transition-opacity">
        <div id="modalBukaAbsenContent" class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden transform scale-95 opacity-0 transition-all duration-300">
            <div class="px-6 py-4 bg-gradient-to-r from-emerald-600 to-teal-500 flex justify-between items-center">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i class="fas fa-unlock-alt"></i> Buka Presensi Santri
                </h3>
                <button type="button" onclick="closeModalBukaAbsen()" class="text-emerald-100 hover:text-white transition-colors bg-white/10 hover:bg-white/20 w-8 h-8 rounded-full flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form method="POST" action="{{ route('admin.presensi.toggle') }}">
                @csrf
                <input type="hidden" name="jadwal_id" id="buka_jadwal_id">
                
                <div class="p-6 space-y-5">
                    
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex items-start gap-3">
                        <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                        <p class="text-[11px] text-blue-700 leading-relaxed">
                            Secara default, jam di bawah disi sesuai dengan jam majelis. Anda bebas merubahnya untuk memberi kelonggaran waktu bagi santri.
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="flex items-center gap-1.5 text-xs font-semibold text-gray-700 mb-2">
                                <i class="fas fa-hourglass-start text-emerald-500"></i> Jam Buka
                            </label>
                            <input type="time" name="jam_buka" id="input_jam_buka" required 
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all font-semibold text-gray-700">
                        </div>
                        <div>
                            <label class="flex items-center gap-1.5 text-xs font-semibold text-gray-700 mb-2">
                                <i class="fas fa-hourglass-end text-rose-500"></i> Jam Tutup
                            </label>
                            <input type="time" name="jam_tutup" id="input_jam_tutup" required 
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all font-semibold text-gray-700">
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3 border-t border-gray-100">
                    <button type="button" onclick="closeModalBukaAbsen()" 
                            class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 rounded-xl transition-all shadow-sm">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-6 py-2.5 text-sm font-bold text-white rounded-xl transition-all shadow-lg shadow-emerald-600/20 hover:shadow-emerald-600/40 hover:-translate-y-0.5 flex items-center gap-2"
                            style="background: linear-gradient(135deg, #10b981 0%, #047857 100%);">
                        <i class="fas fa-check-circle"></i> Buka Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endpush
</x-app-layout>

