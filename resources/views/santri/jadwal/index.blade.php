<x-app-layout>
    <x-slot name="header">Jadwal Majelis Saya</x-slot>

    <div class="space-y-6">
        @php
            $santri    = auth()->user()->santri;
            $hariOrder = ['Ahad','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
            $hariIni   = str_replace('Minggu', 'Ahad', now()->translatedFormat('l'));
            $grouped   = $jadwals->groupBy('hari');
            $jadwalHariIni = $grouped[$hariIni] ?? collect();

            // Warna latar per hari (Gradient Mewah)
            
            
            $hariIcon = [
                'Ahad'   => 'fa-sun',
                'Senin'  => 'fa-star',
                'Selasa' => 'fa-moon',
                'Rabu'   => 'fa-cloud',
                'Kamis'  => 'fa-bolt',
                'Jumat'  => 'fa-mosque',
                'Sabtu'  => 'fa-leaf',
            ];
        @endphp

        {{-- ===== TAMPILAN MOBILE ===== --}}
        <div class="lg:hidden">
            <div class="relative overflow-hidden rounded-2xl mb-6 shadow-xl text-white p-5" style="background: linear-gradient(135deg, #1a4731 0%, #166534 40%, #15803d 70%, #16a34a 100%);">
                <div class="absolute -top-8 -right-8 w-48 h-48 rounded-full opacity-10" style="background: radial-gradient(circle, #bbf7d0, transparent 70%)"></div>
                <div class="absolute bottom-0 left-1/3 w-32 h-32 rounded-full opacity-10" style="background: radial-gradient(circle, #6ee7b7, transparent 70%)"></div>
                <div class="relative z-10 flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full border-2 border-white/20 overflow-hidden flex-shrink-0 bg-[#1e6138] flex items-center justify-center">
                        <i class="fas fa-book-open text-white"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] text-emerald-100 font-semibold uppercase tracking-wider mb-0.5 truncate">{{ str_replace('Minggu', 'Ahad', now()->translatedFormat('l, d F Y')) }}</p>
                        <h2 class="text-base font-bold mb-0.5 leading-tight truncate text-white">Jadwal Majelis Saya</h2>
                        <div class="flex items-center gap-1.5 text-[10px] text-emerald-100">
                            <i class="fas fa-user-graduate text-[9px]"></i>
                            <span class="truncate">{{ $santri->majelis->nama_majelis ?? 'Santri' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== TAMPILAN DESKTOP ===== --}}
        <div class="hidden lg:block">
            <div class="relative overflow-hidden rounded-2xl mb-8 shadow-xl" style="background: linear-gradient(135deg, #1a4731 0%, #166534 40%, #15803d 70%, #16a34a 100%);">
                <div class="absolute -top-8 -right-8 w-48 h-48 rounded-full opacity-10" style="background: radial-gradient(circle, #bbf7d0, transparent 70%)"></div>
                <div class="absolute bottom-0 left-1/3 w-32 h-32 rounded-full opacity-10" style="background: radial-gradient(circle, #6ee7b7, transparent 70%)"></div>
                <div class="relative p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                        <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-3xl shrink-0" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.25);">
                            <i class="fas fa-book-open text-white"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-emerald-200 text-xs font-medium tracking-wide uppercase mb-0.5 truncate">{{ str_replace('Minggu', 'Ahad', now()->translatedFormat('l, d F Y')) }}</p>
                            <h2 class="text-2xl font-extrabold text-white leading-tight truncate">Jadwal Majelis Saya</h2>
                            <div class="flex flex-wrap items-center gap-2 mt-1.5">
                                <span class="inline-flex items-center gap-1 text-xs text-white/80"><i class="fas fa-user-graduate text-emerald-300 text-[10px]"></i> {{ $santri->majelis->nama_majelis ?? 'Santri' }}</span>
                            </div>
                        </div>
                        <div class="flex sm:flex-col items-center sm:items-end gap-3 sm:gap-2 shrink-0">
                            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2);">
                                <span class="text-[11px] font-bold text-white">{{ $jadwals->count() }}</span>
                                <span class="text-[9px] font-semibold uppercase tracking-wider text-emerald-100">Jadwal</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== JADWAL HARI INI (highlight) ===== --}}
        @if($jadwalHariIni->isNotEmpty())
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-4 px-1">
                <div class="w-2.5 h-2.5 bg-green-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(34,197,94,0.6)]"></div>
                <h2 class="text-lg font-extrabold text-gray-800 tracking-tight">Majelis Hari Ini</h2>
                <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-md">{{ $hariIni }}</span>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
                
                @foreach($jadwalHariIni as $jadwal)
                @php
                    $jm = \Carbon\Carbon::parse($jadwal->jam_mulai);
                    $js = \Carbon\Carbon::parse($jadwal->jam_selesai);
                    $now = \Carbon\Carbon::now();
                    $berlangsung = $now->between($jm, $js);
                    $selesai = $now->gt($js);
                    
                    $st = $jadwalStats[$jadwal->id] ?? [];
                    $statusSantriHariIni = $st['status_santri_hari_ini'] ?? null;
                @endphp
                <a href="{{ route('santri.jadwal.show', $jadwal->id) }}" class="block hover:-translate-y-0.5 transition-transform mb-3">
                    <div class="flex items-center gap-4 p-4 min-h-[88px] rounded-xl bg-white shadow-[0_2px_10px_rgb(0,0,0,0.04)] border {{ $berlangsung ? 'border-blue-300 shadow-blue-500/10' : 'border-gray-100 hover:shadow-md' }} transition-shadow">
                        <div class="text-center min-w-[52px]">
                            <p class="text-xs font-mono font-bold text-gray-700">{{ $jm->format('H:i') }}</p>
                            <p class="text-[10px] text-gray-400">{{ $js->format('H:i') }}</p>
                        </div>
                        <div class="w-px h-10 bg-gray-200 shrink-0"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ $jadwal->kitab->nama_kitab ?? '-' }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $jadwal->ustadz->user->name ?? '-' }}</p>
                        </div>
                        <div class="shrink-0">
                            @if($statusSantriHariIni)
                                @php $bc = match($statusSantriHariIni){'Hadir'=>'bg-green-100 text-green-700','Izin'=>'bg-yellow-100 text-yellow-700','Sakit'=>'bg-blue-100 text-blue-600',default=>'bg-red-100 text-red-600'}; @endphp
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $bc }}">{{ $statusSantriHariIni }}</span>
                            @elseif($berlangsung)
                                <span class="text-[10px] font-semibold bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full animate-pulse">● Berlangsung</span>
                            @elseif($selesai)
                                <span class="text-[10px] text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">Selesai</span>
                            @else
                                <span class="text-[10px] text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">Menunggu</span>
                            @endif
                        </div>
                    </div>
                </a>
                @endforeach

            </div>
        </div>
        @endif

        {{-- ===== JADWAL LENGKAP PER HARI ===== --}}
        @if($jadwals->isEmpty())
            <div class="bg-white/60 backdrop-blur-xl rounded-2xl border border-white/50 py-20 text-center shadow-sm">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                    <i class="fas fa-calendar-times text-4xl text-gray-300"></i>
                </div>
                <p class="text-gray-600 font-bold text-lg">Belum ada Jadwal Majelis</p>
                <p class="text-gray-400 text-sm mt-1">
                    @if(!$santri || !$santri->majelis_id)
                        Anda belum terdaftar di majelis apapun.
                    @else
                        Silakan tunggu admin mengunggah jadwal kelas Anda.
                    @endif
                </p>
            </div>
        @else
            <div>
                <h2 class="text-lg font-extrabold text-gray-800 tracking-tight mb-4 px-1 flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-[#187a41]"></i> Semua Jadwal Mingguan
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($hariOrder as $hari)
                        @if(isset($grouped[$hari]))
                            @foreach($grouped[$hari] as $jadwal)
                                @php
                                    $st = $jadwalStats[$jadwal->id] ?? [];
                                    $presensiHariIni = $st['presensiHariIni'] ?? null;
                                    $sudahAbsen = $st['sudahAbsen'] ?? false;
                                    $dibuka = $presensiHariIni && $presensiHariIni->status === 'dibuka';
                                    $isToday = ($hari === $hariIni);
                                @endphp

                                
                                
                                <a href="{{ route('santri.jadwal.show', $jadwal->id) }}" class="block hover:-translate-y-0.5 transition-transform mb-3">
                                    <div class="flex items-center gap-4 p-4 min-h-[88px] rounded-xl bg-white shadow-[0_2px_10px_rgb(0,0,0,0.04)] border {{ $isToday ? 'border-[#187a41]/40 shadow-[#187a41]/5' : 'border-gray-100 hover:shadow-md' }} transition-shadow">
                                        <div class="text-center min-w-[52px]">
                                            <p class="text-xs font-mono font-bold text-gray-700">{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}</p>
                                            <p class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</p>
                                        </div>
                                        <div class="w-px h-10 bg-gray-200 shrink-0"></div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-2">
                                                <p class="text-sm font-semibold text-gray-800 truncate">{{ $jadwal->kitab->nama_kitab ?? '-' }}</p>
                                                <span class="text-[9px] px-2 py-0.5 rounded-full {{ $isToday ? 'bg-[#187a41] text-white' : 'bg-gray-200 text-gray-600' }}">{{ $hari }}</span>
                                            </div>
                                            <p class="text-xs text-gray-500 truncate">{{ $jadwal->ustadz->user->name ?? '-' }}</p>
                                        </div>
                                        <div class="shrink-0 text-gray-300">
                                            <i class="fas fa-chevron-right text-[10px]"></i>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    @push('scripts')
    <script>
        // Auto-scroll ke jadwal hari ini saat halaman dimuat
        document.addEventListener('DOMContentLoaded', () => {
            const todaySection = document.querySelector('[data-today="1"]');
            if (todaySection) {
                todaySection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    </script>
    @endpush
</x-app-layout>