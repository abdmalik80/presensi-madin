<x-app-layout>
    <x-slot name="header">Detail Kitab</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        {{-- Tombol Kembali --}}
        <div>
            <a href="{{ route('santri.jadwal.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-hijau-tua transition-colors font-medium">
                <i class="fas fa-arrow-left"></i> Kembali ke Jadwal
            </a>
        </div>

        {{-- ===== MOBILE VIEW BANNER ===== --}}
        <div class="lg:hidden mb-6">
            <div class="relative overflow-hidden rounded-2xl shadow-lg" style="background: linear-gradient(135deg, #1a4731 0%, #166534 40%, #15803d 70%, #16a34a 100%);">
                <div class="absolute -top-12 -right-12 w-40 h-40 rounded-full opacity-10" style="background: radial-gradient(circle, #bbf7d0, transparent 70%)"></div>
                <div class="absolute bottom-0 left-1/4 w-24 h-24 rounded-full opacity-10" style="background: radial-gradient(circle, #6ee7b7, transparent 70%)"></div>
                <div class="relative p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl shrink-0" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.25);">
                            <i class="fas fa-book-open text-white"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[10px] text-emerald-100 font-semibold uppercase tracking-wider mb-0.5 truncate">{{ $jadwal->hari }} &bull; {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }} WIB</p>
                            <h2 class="text-base font-bold mb-0.5 leading-tight truncate text-white">{{ $jadwal->kitab->nama_kitab ?? 'Kitab' }}</h2>
                            <div class="flex items-center gap-1.5 text-[10px] text-emerald-100">
                                <i class="fas fa-chalkboard-teacher text-[9px]"></i>
                                <span class="truncate">Ustadz {{ $jadwal->ustadz->user->name ?? '-' }}</span>
                            </div>
                        </div>
                        @if($jadwal->keterangan)
                        <div class="shrink-0">
                            <div class="inline-flex items-center justify-center w-8 h-8 bg-white/20 rounded-full" title="{{ $jadwal->keterangan }}">
                                <i class="fas fa-info-circle text-white text-sm"></i>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== DESKTOP VIEW BANNER ===== --}}
        <div class="hidden lg:block relative overflow-hidden rounded-2xl mb-6 shadow-xl text-white p-6" style="background: linear-gradient(135deg, #1a4731 0%, #166534 40%, #15803d 70%, #16a34a 100%);">
            <div class="absolute -top-8 -right-8 w-48 h-48 rounded-full opacity-10" style="background: radial-gradient(circle, #bbf7d0, transparent 70%)"></div>
            <div class="absolute bottom-0 left-1/3 w-32 h-32 rounded-full opacity-10" style="background: radial-gradient(circle, #6ee7b7, transparent 70%)"></div>
            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-3xl shrink-0" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.25);"><i class="fas fa-book-open text-white"></i></div>
                <div class="flex-1 min-w-0">
                    <p class="text-emerald-200 text-xs font-medium tracking-wide uppercase mb-0.5">{{ $jadwal->hari }} &bull; {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }} WIB</p>
                    <h2 class="text-2xl font-extrabold text-white leading-tight truncate">{{ $jadwal->kitab->nama_kitab ?? 'Kitab' }}</h2>
                    <div class="flex flex-wrap items-center gap-2 mt-1.5 text-xs text-white/80">
                        <i class="fas fa-chalkboard-teacher text-[9px]"></i>
                        <span class="truncate">Ustadz {{ $jadwal->ustadz->user->name ?? '-' }}</span>
                    </div>
                </div>
                @if($jadwal->keterangan)
                <div class="flex flex-col items-end gap-1.5 flex-shrink-0">
                    <div class="inline-flex items-center gap-1 px-2 py-1 bg-white/20 rounded-md max-w-[120px]">
                        <i class="fas fa-info-circle text-[10px] shrink-0"></i>
                        <span class="text-[9px] font-semibold truncate">{{ $jadwal->keterangan }}</span>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Statistik Kehadiran --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white/60 backdrop-blur-xl rounded-2xl p-4 border border-white/50 shadow-sm flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check text-green-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Hadir</p>
                    <p class="text-xl font-bold text-gray-800">{{ $statistik['Hadir'] }}</p>
                </div>
            </div>
            
            <div class="bg-white/60 backdrop-blur-xl rounded-2xl p-4 border border-white/50 shadow-sm flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-info text-blue-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Izin</p>
                    <p class="text-xl font-bold text-gray-800">{{ $statistik['Izin'] }}</p>
                </div>
            </div>

            <div class="bg-white/60 backdrop-blur-xl rounded-2xl p-4 border border-white/50 shadow-sm flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-procedures text-amber-600 text-sm"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Sakit</p>
                    <p class="text-xl font-bold text-gray-800">{{ $statistik['Sakit'] }}</p>
                </div>
            </div>

            <div class="bg-white/60 backdrop-blur-xl rounded-2xl p-4 border border-white/50 shadow-sm flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-times text-red-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Alpa</p>
                    <p class="text-xl font-bold text-gray-800">{{ $statistik['Alpha'] }}</p>
                </div>
            </div>
        </div>

        {{-- Tab Nav --}}

        <div class="overflow-x-auto -mx-1 px-1" style="scrollbar-width: none;">
            <div class="flex gap-1 bg-gray-100/70 rounded-xl p-1 w-max min-w-full">
                <button onclick="switchTab('presensi')" id="tab-presensi"
                        class="tab-btn px-4 py-2 text-sm font-semibold rounded-lg transition-colors bg-white shadow text-hijau-tua whitespace-nowrap">
                    <i class="fas fa-clipboard-list mr-1"></i>Riwayat
                </button>
                @if($isHariIni)
                <button onclick="switchTab('absen')" id="tab-absen"
                        class="tab-btn px-4 py-2 text-sm font-semibold rounded-lg transition-colors text-gray-500 hover:text-gray-700 relative whitespace-nowrap">
                    <i class="fas fa-fingerprint mr-1"></i>Presensi Mandiri
                    @if(!$sudahAbsen && $presensiDibuka)
                    <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-green-500 rounded-full animate-ping"></span>
                    <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-green-500 rounded-full"></span>
                    @endif
                </button>
                @endif
                <button onclick="switchTab('berkas')" id="tab-berkas"
                        class="tab-btn px-4 py-2 text-sm font-semibold rounded-lg transition-colors text-gray-500 hover:text-gray-700 whitespace-nowrap">
                    <i class="fas fa-folder-open mr-1"></i>Berkas &amp; Materi
                    @if($newBerkasCount > 0)
                    <span id="badge-berkas" class="ml-1 bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full">{{ $newBerkasCount }} Baru</span>
                    @endif
                </button>
            </div>
        </div>

        {{-- Quick actions --}}
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('santri.izin.create', ['jadwal_id' => $jadwal->id]) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-blue-200 text-blue-700 bg-blue-50 text-sm font-semibold hover:bg-blue-100 transition-colors">
                <i class="fas fa-envelope-open-text"></i> Ajukan Izin Majelis Ini
            </a>
        </div>

        {{-- === TAB ABSEN MANDIRI === --}}
        @if($isHariIni)
        <div id="panel-absen" class="hidden">
            <div class="bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100/50 bg-white/40">
                    <h3 class="font-bold text-gray-800">Presensi Mandiri — Hari Ini</h3>
                    <p class="text-xs text-gray-500 mt-0.5">{{ now()->translatedFormat('l, d F Y') }}</p>
                </div>
                <div class="px-6 py-8 text-center">
                    @if($sudahAbsen)
                        {{-- Sudah absen --}}
                        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-check-circle text-4xl text-green-500"></i>
                        </div>
                        <p class="text-lg font-bold text-green-700">Sudah Hadir!</p>
                        <p class="text-sm text-gray-500 mt-1">Kehadiran Anda pada majelis ini hari ini sudah tercatat.</p>

                    @elseif($presensiDibuka)
                        {{-- Bisa absen --}}
                        <div class="w-20 h-20 bg-hijau-tua/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-fingerprint text-4xl text-hijau-tua"></i>
                        </div>
                        <p class="text-lg font-bold text-gray-800 mb-1">Presensi Dibuka!</p>
                        <p class="text-sm text-gray-500 mb-6">Ustadz telah membuka sesi presensi. Klik tombol di bawah untuk mencatat kehadiran Anda.</p>
                        <x-alert />
                        <form action="{{ route('santri.absen-mandiri.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
                            <button type="submit"
                                    class="inline-flex items-center gap-3 bg-hijau-tua hover:bg-hijau-muda text-white px-8 py-3.5 rounded-2xl font-bold text-base transition-all shadow-lg shadow-green-500/20 active:scale-95">
                                <i class="fas fa-fingerprint text-lg"></i>
                                Tandai Saya Hadir
                            </button>
                        </form>

                    @elseif($presensiHariIni && $presensiHariIni->status === 'ditutup')
                        {{-- Ditutup --}}
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-lock text-4xl text-gray-400"></i>
                        </div>
                        <p class="text-lg font-bold text-gray-600">Presensi Ditutup</p>
                        <p class="text-sm text-gray-400 mt-1">Ustadz telah menutup sesi presensi majelis ini.</p>

                    @else
                        {{-- Belum dibuka --}}
                        <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-clock text-4xl text-amber-400"></i>
                        </div>
                        <p class="text-lg font-bold text-gray-700">Menunggu Ustadz</p>
                        <p class="text-sm text-gray-500 mt-1">Presensi mandiri belum dibuka. Tunggu hingga Ustadz membuka sesi presensi.</p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- === TAB PRESENSI === --}}
        <div id="panel-presensi">
        {{-- Riwayat Presensi majelis ini --}}
        <div class="bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100/50 bg-white/40 flex justify-between items-center">
                <h3 class="font-bold text-gray-800">Riwayat Kehadiran Anda</h3>
            </div>
            
            @if($riwayatPresensi->isEmpty())
                <div class="px-6 py-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-history text-2xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-500 font-medium">Belum ada riwayat presensi</p>
                    <p class="text-xs text-gray-400 mt-1">Sistem belum mencatat riwayat kehadiran Anda di majelis ini.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/50 text-gray-500">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Pertemuan Ke-</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Dicatat Pada</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100/50 bg-white/20">
                            @foreach($riwayatPresensi as $riwayat)
                            <tr class="hover:bg-white/50 transition-colors">
                                <td class="px-6 py-3 text-sm font-bold text-hijau-tua">#{{ $loop->iteration }}</td>
                                <td class="px-6 py-3 text-sm font-medium text-gray-800">
                                    {{ \Carbon\Carbon::parse($riwayat->presensi->tanggal)->translatedFormat('l, d F Y') }}
                                </td>
                                <td class="px-6 py-3">
                                    @if($riwayat->status_kehadiran === 'Hadir')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 border border-green-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Hadir
                                        </span>
                                    @elseif($riwayat->status_kehadiran === 'Izin')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 border border-blue-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Izin
                                        </span>
                                    @elseif($riwayat->status_kehadiran === 'Sakit')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Sakit
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700 border border-red-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Alpa
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-xs text-gray-500">
                                    {{ $riwayat->created_at->format('H:i') }} WIB
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        </div>

        {{-- === TAB BERKAS === --}}
        <div id="panel-berkas" class="hidden space-y-3">
            <div class="bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100/50 bg-white/40">
                    <h3 class="font-bold text-gray-800">Berkas & Materi Majelis</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Berkas yang diupload ustadz untuk kitab ini</p>
                </div>

                @if($berkas->isEmpty())
                <div class="px-6 py-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-folder-open text-2xl text-gray-300"></i>
                    </div>
                    <p class="text-gray-500 font-medium">Belum ada berkas</p>
                    <p class="text-xs text-gray-400 mt-1">Ustadz belum mengupload materi untuk majelis ini.</p>
                </div>
                @else
                <div class="divide-y divide-gray-100/50">
                    @foreach($berkas as $b)
                    <div class="flex items-center gap-4 px-5 py-3 hover:bg-white/50 transition-colors group">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                            @if($b->tipe_file === 'image') bg-purple-100
                            @elseif($b->tipe_file === 'pdf') bg-red-100
                            @elseif($b->tipe_file === 'doc') bg-blue-100
                            @elseif($b->tipe_file === 'xls') bg-green-100
                            @else bg-gray-100 @endif">
                            <i class="fas {{ $b->file_icon }} text-lg"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800">{{ $b->judul }}</p>
                            <p class="text-xs text-gray-400">
                                {{ $b->tipe_label }} &bull; {{ \Carbon\Carbon::parse($b->tanggal)->translatedFormat('d M Y') }}
                                @if($b->nama_file) &bull; {{ $b->nama_file }} @endif
                            </p>
                        </div>
                        @if($b->upload_file)
                        <div class="flex gap-2 flex-shrink-0">
                            @if($b->tipe_file === 'image')
                            <a href="{{ asset('storage/'.$b->upload_file) }}" target="_blank"
                               class="text-xs bg-purple-50 text-purple-700 border border-purple-200 px-3 py-1.5 rounded-lg hover:bg-purple-100 transition-colors font-medium">
                                <i class="fas fa-eye mr-1"></i>Lihat
                            </a>
                            @else
                            <a href="{{ asset('storage/'.$b->upload_file) }}" target="_blank"
                               class="text-xs bg-gray-50 text-gray-700 border border-gray-200 px-3 py-1.5 rounded-lg hover:bg-gray-100 transition-colors font-medium">
                                <i class="fas fa-eye mr-1"></i>Lihat
                            </a>
                            @endif
                            <a href="{{ asset('storage/'.$b->upload_file) }}" download="{{ $b->nama_file ?? $b->judul }}"
                               class="text-xs bg-green-50 text-green-700 border border-green-200 px-3 py-1.5 rounded-lg hover:bg-green-100 transition-colors font-medium">
                                <i class="fas fa-download mr-1"></i>Unduh
                            </a>
                        </div>
                        @endif
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
            const tabs = ['presensi', 'absen', 'berkas'];
            tabs.forEach(t => {
                const panel = document.getElementById('panel-'+t);
                if (panel) panel.classList.add('hidden');
                const btn = document.getElementById('tab-'+t);
                if (btn) {
                    btn.classList.remove('bg-white','shadow','text-hijau-tua');
                    btn.classList.add('text-gray-500');
                }
            });
            const activePanel = document.getElementById('panel-'+tab);
            if (activePanel) activePanel.classList.remove('hidden');
            const activeBtn = document.getElementById('tab-'+tab);
            if (activeBtn) {
                activeBtn.classList.add('bg-white','shadow','text-hijau-tua');
                activeBtn.classList.remove('text-gray-500');
            }

            if (tab === 'berkas') {
                const badge = document.getElementById('badge-berkas');
                if (badge && !badge.classList.contains('hidden')) {
                    badge.classList.add('hidden');
                    fetch('{{ route("santri.jadwal.mark-berkas-read", $jadwal->id) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).catch(err => console.error(err));
                }
            }
        }

        // Jika ada absensi yang baru dibuka, otomatis arahkan ke tab absen
        @if($isHariIni && $presensiDibuka && !$sudahAbsen)
        document.addEventListener('DOMContentLoaded', () => switchTab('absen'));
        @endif
    </script>
    @endpush
</x-app-layout>

