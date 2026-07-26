<x-app-layout>
    <x-slot name="header">Detail Majelis</x-slot>

    <div class="space-y-6">
        <x-alert />

        {{-- Back --}}
        <div>
            <a href="{{ route('admin.jadwal.index') }}"
               class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-hijau-tua transition-colors font-medium">
                <i class="fas fa-arrow-left"></i> Kembali ke Jadwal
            </a>
        </div>

        {{-- Header Card (TAMPILAN HP) --}}
        <div class="relative overflow-hidden rounded-xl shadow-sm text-white p-5 mb-2 md:hidden"
             style="background: #2A864D;">
            <div class="absolute -top-8 -right-8 w-40 h-40 rounded-full opacity-10"
                 style="background: radial-gradient(circle, #bbf7d0, transparent 70%)"></div>
                 
            <div class="relative flex flex-col gap-3">
                <div class="flex items-start gap-4">
                    {{-- Icon --}}
                    <div class="w-12 h-12 rounded-full border-2 border-white/20 flex-shrink-0 bg-[#1e6138] flex items-center justify-center">
                        <i class="fas fa-book-open text-lg text-white"></i>
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] text-emerald-100 font-semibold uppercase tracking-wider mb-0.5">
                            <i class="fas fa-clock mr-1"></i>{{ $jadwal->hari }}, {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} WIB
                        </p>
                        <h2 class="text-lg font-bold mb-1 leading-tight">{{ $jadwal->kitab->nama_kitab ?? 'Kitab' }}</h2>
                        
                        <div class="flex flex-wrap items-center gap-1.5 text-[11px] text-emerald-100">
                            <span class="flex items-center gap-1 shrink-0">
                                <i class="fas fa-mosque text-[10px]"></i>
                                {{ $jadwal->majelis->nama_majelis ?? '-' }}
                            </span>
                        </div>
                    </div>
                </div>
                
                @if($jadwal->keterangan)
                <div class="border-t border-white/20 pt-2 mt-1">
                    <p class="text-[11px] text-emerald-100 leading-tight">
                        <i class="fas fa-info-circle mr-1 text-[10px]"></i> {{ $jadwal->keterangan }}
                    </p>
                </div>
                @endif
            </div>
        </div>

        {{-- Header Info Majelis (TAMPILAN DESKTOP) --}}
        <div class="relative overflow-hidden rounded-2xl shadow-xl text-white hidden md:block mb-8" style="background: linear-gradient(135deg, #1a4731 0%, #166534 40%, #15803d 70%, #16a34a 100%);">
            <div class="absolute -top-8 -right-8 w-48 h-48 rounded-full opacity-10" style="background: radial-gradient(circle, #bbf7d0, transparent 70%)"></div>
            <div class="absolute bottom-0 left-1/3 w-32 h-32 rounded-full opacity-10" style="background: radial-gradient(circle, #6ee7b7, transparent 70%)"></div>
            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center gap-4 p-6">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-3xl shrink-0" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.25);">
                    <i class="fas fa-book-open text-white"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-emerald-200 text-xs font-medium tracking-wide uppercase mb-0.5">
                        <i class="fas fa-clock mr-1"></i>{{ $jadwal->hari }}, {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }} WIB
                    </p>
                    <h2 class="text-2xl font-extrabold text-white leading-tight truncate">{{ $jadwal->kitab->nama_kitab ?? 'Kitab' }}</h2>
                    <div class="flex flex-wrap items-center gap-2 mt-1.5 text-xs text-white/80">
                        <span class="truncate"><i class="fas fa-mosque text-emerald-300 mr-1 text-[10px]"></i>{{ $jadwal->majelis->nama_majelis ?? '-' }}</span>
                    </div>
                </div>
            </div>
            @if($jadwal->keterangan)
            <div class="relative z-10 px-6 pb-5 border-t border-white/10 pt-4 mt-2 mx-2">
                <p class="text-xs text-emerald-100 leading-tight">
                    <i class="fas fa-info-circle mr-1 opacity-70 text-[10px]"></i> {{ $jadwal->keterangan }}
                </p>
            </div>            @endif
        </div>

        {{-- Tab Nav --}}
        <div class="bg-white/60 backdrop-blur-xl rounded-2xl border border-white/50 shadow-sm">
            <div class="flex border-b border-gray-100 overflow-x-auto hide-scrollbar custom-scroll whitespace-nowrap rounded-t-2xl">
                <button onclick="switchTab('berkas')" id="tab-berkas"
                        class="tab-btn flex-1 min-w-max px-4 py-3.5 text-xs sm:text-sm font-semibold transition-colors border-b-2 border-hijau-tua text-hijau-tua">
                    <i class="fas fa-folder-open mr-1 sm:mr-2"></i>Berkas & Materi
                    <span class="ml-1 bg-hijau-tua text-white text-[10px] sm:text-xs px-2 py-0.5 rounded-full">{{ $statBerkas['total'] ?? 0 }}</span>
                </button>
                <button onclick="switchTab('presensi')" id="tab-presensi"
                        class="tab-btn flex-1 min-w-max px-4 py-3.5 text-xs sm:text-sm font-semibold transition-colors border-b-2 border-transparent text-gray-500 hover:text-gray-700">
                    <i class="fas fa-clipboard-list mr-1 sm:mr-2"></i>Riwayat Presensi
                </button>
                <button onclick="switchTab('santri')" id="tab-santri"
                        class="tab-btn flex-1 min-w-max px-4 py-3.5 text-xs sm:text-sm font-semibold transition-colors border-b-2 border-transparent text-gray-500 hover:text-gray-700">
                    <i class="fas fa-users mr-1 sm:mr-2"></i>Daftar Santri
                    <span class="ml-1 bg-gray-200 text-gray-600 text-[10px] sm:text-xs px-2 py-0.5 rounded-full">{{ isset($santriList) ? $santriList->count() : 0 }}</span>
                </button>
            </div>

            {{-- === TAB BERKAS === --}}
            <div id="panel-berkas" class="p-5 space-y-4">
                {{-- Stat berkas --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="bg-gray-50 rounded-xl p-3 text-center">
                        <p class="text-2xl font-bold text-gray-800">{{ $statBerkas['total'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">Total Berkas</p>
                    </div>
                    <div class="bg-purple-50 rounded-xl p-3 text-center">
                        <p class="text-2xl font-bold text-purple-700">{{ $statBerkas['image'] ?? 0 }}</p>
                        <p class="text-xs text-purple-500 mt-0.5">Gambar</p>
                    </div>
                    <div class="bg-red-50 rounded-xl p-3 text-center">
                        <p class="text-2xl font-bold text-red-600">{{ $statBerkas['pdf'] ?? 0 }}</p>
                        <p class="text-xs text-red-500 mt-0.5">PDF</p>
                    </div>
                    <div class="bg-blue-50 rounded-xl p-3 text-center">
                        <p class="text-2xl font-bold text-blue-600">{{ $statBerkas['doc'] ?? 0 }}</p>
                        <p class="text-xs text-blue-500 mt-0.5">Word</p>
                    </div>
                </div>

                {{-- Header Berkas --}}
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="font-bold text-gray-800 text-lg">Daftar Berkas</h3>
                    <a href="{{ route('admin.dokumentasi.create', ['jadwal_id' => $jadwal->id]) }}"
                       class="inline-flex items-center gap-2 bg-hijau-tua text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-hijau-muda transition-all shadow">
                        <i class="fas fa-upload"></i> Upload
                    </a>
                </div>

                {{-- Daftar Berkas --}}
                @if(empty($berkas) || $berkas->isEmpty())
                <div class="py-16 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-folder-open text-2xl text-gray-300"></i>
                    </div>
                    <p class="text-gray-400 font-medium">Belum ada berkas untuk majelis ini.</p>
                </div>
                @else
                <div class="space-y-2">
                    @foreach($berkas as $b)
                        <div x-data="{ open: false }" class="flex items-center gap-3 sm:gap-4 p-3 bg-gray-50/80 rounded-xl hover:bg-white border border-gray-100 transition-colors group relative" :class="open ? 'z-50' : 'z-0'">
                            {{-- File icon / preview --}}
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                                @if($b->tipe_file === 'image') bg-purple-100
                                @elseif($b->tipe_file === 'pdf') bg-red-100
                                @elseif($b->tipe_file === 'doc') bg-blue-100
                                @elseif($b->tipe_file === 'xls') bg-green-100
                                @elseif($b->tipe_file === 'ppt') bg-orange-100
                                @else bg-gray-100 @endif">
                                <i class="fas {{ $b->file_icon ?? 'fa-file' }} text-lg"></i>
                            </div>

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-bold text-gray-800 truncate mb-0.5">
                                    {{ $b->judul ?? $b->nama_file ?? 'Berkas' }}
                                </h4>
                                <div class="flex items-center gap-2 text-[10px] sm:text-xs text-gray-500">
                                    @if($b->ukuran_file)
                                    <span class="flex items-center gap-1"><i class="fas fa-hdd opacity-70"></i> {{ $b->ukuran_file }}</span>
                                    @endif
                                    <span class="text-gray-300">|</span>
                                    <span class="flex items-center gap-1"><i class="far fa-clock opacity-70"></i> {{ $b->created_at->diffForHumans() }}</span>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center gap-1 sm:gap-2">
                                @if($b->upload_file)
                                <a href="{{ asset('storage/'.$b->upload_file) }}" target="_blank"
                                   class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-lg text-blue-600 hover:bg-blue-50 transition-colors tooltip"
                                   data-tooltip="Lihat">
                                    <i class="fas fa-eye text-xs sm:text-sm"></i>
                                </a>
                                <a href="{{ asset('storage/'.$b->upload_file) }}" download="{{ $b->nama_file ?? $b->judul ?? 'download' }}"
                                   class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-lg text-emerald-600 hover:bg-emerald-50 transition-colors tooltip"
                                   data-tooltip="Download">
                                    <i class="fas fa-download text-xs sm:text-sm"></i>
                                </a>
                                @endif
                                
                                <form method="POST" action="{{ route('admin.dokumentasi.destroy', $b->id ?? 0) }}" class="inline-block"
                                      onsubmit="return confirm('Hapus berkas ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-lg text-red-500 hover:bg-red-50 hover:text-red-600 transition-colors tooltip" data-tooltip="Hapus">
                                        <i class="fas fa-trash-alt text-xs sm:text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- === TAB PRESENSI === --}}
            <div id="panel-presensi" class="hidden p-5">
                @if($presensis->isEmpty())
                <div class="py-16 text-center">
                    <i class="fas fa-clipboard-list text-4xl text-gray-200 mb-3 block"></i>
                    <p class="text-gray-400">Belum ada catatan presensi.</p>
                </div>
                @else
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/60">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Dicatat Oleh</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($presensis as $p)
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <td class="px-4 py-3 text-sm font-medium text-gray-800">
                                    {{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('l, d F Y') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $p->pencatat->user->name ?? 'Sistem' }}
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.presensi.show', $p) }}"
                                       class="text-xs text-hijau-tua hover:underline font-medium">
                                        <i class="fas fa-eye mr-1"></i>Detail
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile List View --}}
                <div class="md:hidden space-y-2 mt-2">
                    @foreach($presensis as $p)
                    <div class="flex items-center justify-between p-3 bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-calendar-check text-sm"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-gray-800">{{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('l, d F Y') }}</h4>
                                <p class="text-[10px] text-gray-500 mt-0.5">Dicatat: {{ $p->pencatat->user->name ?? 'Sistem' }}</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.presensi.show', $p) }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-hijau-tua/10 text-hijau-tua hover:bg-hijau-tua/20 transition-colors">
                            <i class="fas fa-eye text-sm"></i>
                        </a>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- === TAB DAFTAR SANTRI === --}}
            <div id="panel-santri" class="hidden p-5">

                {{-- Info bulan --}}
                <div class="flex items-center justify-between mb-4 gap-2">
                    <div class="flex items-center gap-1.5 sm:gap-2 text-[11px] sm:text-sm text-gray-600 leading-tight">
                        <i class="fas fa-calendar-alt text-hijau-tua"></i>
                        <span>Statistik kehadiran: <strong>{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</strong></span>
                    </div>
                    <span class="text-[9px] sm:text-xs bg-hijau-tua/10 text-hijau-tua px-2.5 sm:px-3 py-1 rounded-full font-semibold whitespace-nowrap text-center">
                        {{ $santriList->count() }} Santri<span class="hidden sm:inline"> Terdaftar</span>
                    </span>
                </div>

                @if($santriList->isEmpty())
                <div class="py-16 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-users text-2xl text-gray-300"></i>
                    </div>
                    <p class="text-gray-400 font-medium">Belum ada santri di majelis ini.</p>
                    <p class="text-xs text-gray-300 mt-1">Santri perlu didaftarkan ke majelis <strong>{{ $jadwal->majelis->nama_majelis ?? '-' }}</strong> terlebih dahulu.</p>
                </div>
                @else
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-hijau-tua/5">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">No</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Nama Santri</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">NIS</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-green-600 uppercase tracking-wide">Hadir</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-yellow-600 uppercase tracking-wide">Izin/Sakit</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-red-500 uppercase tracking-wide">Alpha</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">% Hadir</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($santriList->values() as $item)
                            @php
                                $pct = $item['pct'];
                                $pctColor = is_null($pct) ? 'text-gray-400'
                                    : ($pct >= 75 ? 'text-green-600' : ($pct >= 50 ? 'text-yellow-600' : 'text-red-500'));
                                $barColor = is_null($pct) ? 'bg-gray-200'
                                    : ($pct >= 75 ? 'bg-green-500' : ($pct >= 50 ? 'bg-yellow-400' : 'bg-red-400'));
                            @endphp
                            <tr class="hover:bg-hijau-tua/5 transition-colors">
                                <td class="px-4 py-3 text-gray-400 text-xs">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-hijau-tua/10 flex items-center justify-center text-hijau-tua font-bold text-xs shrink-0">
                                            {{ strtoupper(substr($item['santri']->user->name ?? '?', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-800">{{ $item['santri']->user->name ?? '-' }}</p>
                                            @if($item['santri']->nama_wali)
                                            <p class="text-xs text-gray-400">Wali: {{ $item['santri']->nama_wali }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-mono text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded">
                                        {{ $item['santri']->nis ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-700 font-bold text-sm">
                                        {{ $item['hadir'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-yellow-50 text-yellow-600 font-bold text-sm">
                                        {{ $item['izin'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-50 text-red-500 font-bold text-sm">
                                        {{ $item['alpha'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2 justify-center">
                                        <div class="w-16 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full {{ $barColor }} rounded-full transition-all"
                                                 style="width: {{ $pct ?? 0 }}%"></div>
                                        </div>
                                        <span class="text-xs font-bold {{ $pctColor }} min-w-[36px] text-right">
                                            {{ is_null($pct) ? '-' : $pct.'%' }}
                                        </span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile List View --}}
                <div class="md:hidden space-y-2.5 mt-2">
                    @foreach($santriList->values() as $item)
                    @php
                        $pct = $item['pct'];
                        $pctColor = is_null($pct) ? 'text-gray-400'
                            : ($pct >= 75 ? 'text-green-600' : ($pct >= 50 ? 'text-yellow-600' : 'text-red-500'));
                        $bgColor = is_null($pct) ? 'bg-gray-100'
                            : ($pct >= 75 ? 'bg-green-50' : ($pct >= 50 ? 'bg-yellow-50' : 'bg-red-50'));
                        $borderColor = is_null($pct) ? 'border-gray-200'
                            : ($pct >= 75 ? 'border-green-200' : ($pct >= 50 ? 'border-yellow-200' : 'border-red-200'));
                    @endphp
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-3 hover:shadow-md transition-all">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-hijau-tua/10 flex items-center justify-center text-hijau-tua font-bold text-sm shrink-0">
                                    {{ strtoupper(substr($item['santri']->user->name ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-800">{{ $item['santri']->user->name ?? '-' }}</h4>
                                    <p class="text-[11px] text-gray-500 font-mono">{{ $item['santri']->nis ?? '-' }}</p>
                                </div>
                            </div>
                            <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full {{ $bgColor }} {{ $pctColor }} border {{ $borderColor }}">
                                {{ is_null($pct) ? '-' : $pct.'%' }}
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-2">
                            <div class="bg-green-50 border border-green-100 rounded-lg p-1.5 text-center">
                                <p class="text-[9px] text-green-600 font-semibold uppercase tracking-wider mb-0.5">Hadir</p>
                                <p class="text-xs font-bold text-green-700">{{ $item['hadir'] }}</p>
                            </div>
                            <div class="bg-yellow-50 border border-yellow-100 rounded-lg p-1.5 text-center">
                                <p class="text-[9px] text-yellow-600 font-semibold uppercase tracking-wider mb-0.5">Izin/Sakit</p>
                                <p class="text-xs font-bold text-yellow-700">{{ $item['izin'] }}</p>
                            </div>
                            <div class="bg-red-50 border border-red-100 rounded-lg p-1.5 text-center">
                                <p class="text-[9px] text-red-500 font-semibold uppercase tracking-wider mb-0.5">Alpha</p>
                                <p class="text-xs font-bold text-red-600">{{ $item['alpha'] }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function switchTab(tab) {
            // Sembunyikan semua panel
            ['berkas','presensi','santri'].forEach(t => {
                document.getElementById('panel-'+t).classList.add('hidden');
                const btn = document.getElementById('tab-'+t);
                btn.classList.remove('border-hijau-tua','text-hijau-tua');
                btn.classList.add('border-transparent','text-gray-500');
                // Reset badge warna santri
                const badge = btn.querySelector('span');
                if (badge && t === 'santri') {
                    badge.classList.remove('bg-hijau-tua','text-white');
                    badge.classList.add('bg-gray-200','text-gray-600');
                }
            });
            // Tampilkan panel aktif
            document.getElementById('panel-'+tab).classList.remove('hidden');
            const activeBtn = document.getElementById('tab-'+tab);
            activeBtn.classList.add('border-hijau-tua','text-hijau-tua');
            activeBtn.classList.remove('border-transparent','text-gray-500');
            // Aktifkan badge santri jika tab santri
            if (tab === 'santri') {
                const badge = activeBtn.querySelector('span');
                if (badge) {
                    badge.classList.remove('bg-gray-200','text-gray-600');
                    badge.classList.add('bg-hijau-tua','text-white');
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
