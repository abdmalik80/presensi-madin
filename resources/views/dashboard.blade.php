<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-hijau-tua leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        {{-- Semua data sudah disiapkan oleh DashboardController --}}

        @if($role === 'admin')
            <!-- Admin Dashboard -->

            {{-- ===== TAMPILAN MOBILE (disembunyikan di desktop) ===== --}}
            <div class="lg:hidden">

            {{-- Admin Greeting Card (Like mobile design) --}}
            <div class="relative overflow-hidden rounded-2xl mb-6 shadow-xl text-white p-5" style="background: linear-gradient(135deg, #1a4731 0%, #166534 40%, #15803d 70%, #16a34a 100%);">
                    <div class="absolute -top-8 -right-8 w-48 h-48 rounded-full opacity-10" style="background: radial-gradient(circle, #bbf7d0, transparent 70%)"></div>
                    <div class="absolute bottom-0 left-1/3 w-32 h-32 rounded-full opacity-10" style="background: radial-gradient(circle, #6ee7b7, transparent 70%)"></div>
                    <div class="relative z-10 flex items-center gap-3">
                    {{-- Avatar --}}
                    <div class="w-12 h-12 rounded-full border-[3px] border-emerald-300/30 shadow-md overflow-hidden flex-shrink-0 bg-emerald-900 flex items-center justify-center">
                        @if(auth()->user()->foto_profil)
                            <img src="{{ asset('storage/'.auth()->user()->foto_profil) }}" class="w-full h-full object-cover">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&color=14532D&background=F1F5F9" class="w-full h-full object-cover">
                        @endif
                    </div>
                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] text-emerald-100 font-semibold uppercase tracking-wider mb-0.5">{{ mb_strtoupper($salamAdmin) }}</p>
                        <h2 class="text-base font-bold mb-0.5 leading-tight truncate text-white">{{ auth()->user()->name }}</h2>
                        <div class="flex items-center gap-1.5 text-[10px] text-emerald-100">
                            <i class="fas fa-sitemap text-[9px]"></i>
                            <span>Admin Sistem</span>
                        </div>
                    </div>
                    {{-- Right Badge --}}
                    <div class="flex flex-col items-end gap-1.5 flex-shrink-0">
                        <p class="text-[9px] font-bold uppercase tracking-wider text-emerald-100 text-right leading-tight">
                            {{ \Carbon\Carbon::now()->translatedFormat('d M Y') }}
                        </p>
                        <div class="inline-flex items-center gap-1 px-2 py-1 bg-white/20 rounded-md">
                            <i class="far fa-calendar-alt text-[10px]"></i>
                            <span class="text-[10px] font-semibold">{{ $totalMajelis }} Majelis</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ringkasan Data --}}
            <h3 class="text-sm font-bold text-gray-800 mb-3 px-1">Ringkasan Data</h3>
            <div class="grid grid-cols-2 gap-3 mb-6">
                <!-- Card Santri -->
                <div class="bg-white rounded-xl p-3 shadow-[0_2px_10px_rgb(0,0,0,0.04)] border border-gray-50 flex flex-col items-center justify-center text-center">
                    <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-400 mb-2">
                        <i class="fas fa-user-friends text-xs"></i>
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 mb-0.5">{{ number_format($totalSantri) }}</h4>
                    <p class="text-[9px] text-gray-500 font-medium leading-tight">Total Santri</p>
                </div>

                <!-- Card Ustadz -->
                <div class="bg-white rounded-xl p-3 shadow-[0_2px_10px_rgb(0,0,0,0.04)] border border-gray-50 flex flex-col items-center justify-center text-center">
                    <div class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-400 mb-2">
                        <i class="fas fa-id-badge text-xs"></i>
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 mb-0.5">{{ number_format($totalUstadz) }}</h4>
                    <p class="text-[9px] text-gray-500 font-medium leading-tight">Total Ustadz</p>
                </div>

                <!-- Card Majelis -->
                <div class="bg-white rounded-xl p-3 shadow-[0_2px_10px_rgb(0,0,0,0.04)] border border-gray-50 flex flex-col items-center justify-center text-center">
                    <div class="w-8 h-8 rounded-full bg-yellow-50 flex items-center justify-center text-yellow-500 mb-2">
                        <i class="fas fa-book text-xs"></i>
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 mb-0.5">{{ number_format($totalMajelis) }}</h4>
                    <p class="text-[9px] text-gray-500 font-medium leading-tight">Total Majelis</p>
                </div>

                <!-- Card Jadwal -->
                <div class="bg-white rounded-xl p-3 shadow-[0_2px_10px_rgb(0,0,0,0.04)] border border-gray-50 flex flex-col items-center justify-center text-center">
                    <div class="w-8 h-8 rounded-full bg-fuchsia-50 flex items-center justify-center text-fuchsia-400 mb-2">
                        <i class="far fa-calendar-check text-xs"></i>
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 mb-0.5">{{ number_format($totalJadwal) }}</h4>
                    <p class="text-[9px] text-gray-500 font-medium leading-tight">Total Jadwal</p>
                </div>

                <!-- Card Presensi -->
                <div class="bg-white rounded-xl p-3 shadow-[0_2px_10px_rgb(0,0,0,0.04)] border border-gray-50 flex flex-col items-center justify-center text-center">
                    <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-400 mb-2">
                        <i class="fas fa-clipboard-list text-xs"></i>
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 mb-0.5">{{ number_format($presensiBuilanIni) }}</h4>
                    <p class="text-[9px] text-gray-500 font-medium leading-tight">Presensi Bulan Ini</p>
                </div>

                <!-- Card Izin -->
                <div class="bg-white rounded-xl p-3 shadow-[0_2px_10px_rgb(0,0,0,0.04)] border border-gray-50 flex flex-col items-center justify-center text-center">
                    <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 mb-2">
                        <i class="far fa-check-circle text-xs"></i>
                    </div>
                    <h4 class="text-xl font-bold text-gray-900 mb-0.5">{{ number_format($izinPendingAdmin) }}</h4>
                    <p class="text-[9px] text-gray-500 font-medium leading-tight">Izin Pending</p>
                </div>
            </div>

            {{-- Jadwal Hari Ini --}}
            <div class="bg-white rounded-xl shadow-[0_2px_10px_rgb(0,0,0,0.04)] border border-gray-50 mb-6 overflow-hidden">
                <!-- Header -->
                <div class="p-4 border-b border-gray-50 flex items-center justify-between bg-emerald-50/30">
                    <div class="flex items-center gap-2.5">
                        <i class="far fa-calendar-alt text-[#2A864D] text-base"></i>
                        <div>
                            <h3 class="text-[13px] font-bold text-[#2A864D] leading-tight">Jadwal Hari Ini</h3>
                            <p class="text-[10px] text-gray-400">{{ \Carbon\Carbon::now()->translatedFormat('l, d M Y') }}</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-0.5 bg-[#2A864D]/10 text-[#2A864D] text-[10px] font-bold rounded-full">{{ $jadwalHariIni->count() }} Sesi</span>
                </div>
                <!-- List -->
                <div class="divide-y divide-gray-50">
                    @forelse($jadwalHariIni as $jadwal)
                        <div class="p-4 flex gap-4">
                            <!-- Time -->
                            <div class="w-10 flex-shrink-0 text-center flex flex-col justify-start pt-0.5">
                                <div class="text-[11px] font-bold text-gray-800 flex flex-col gap-0.5 items-center">
                                    <span class="flex items-center justify-center gap-1"><i class="far fa-clock text-[9px] text-[#2A864D]"></i> {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}</span>
                                    <span class="text-gray-400 font-normal">{{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</span>
                                </div>
                            </div>
                            <!-- Details -->
                            <div class="flex-1 min-w-0 flex flex-col justify-center">
                                <div class="flex items-start justify-between gap-2 mb-1">
                                    <h4 class="text-[13px] font-bold text-gray-800 truncate">{{ $jadwal->majelis->nama_majelis ?? '-' }}</h4>
                                    <span class="px-2 py-0.5 bg-gray-50 text-gray-500 text-[9px] font-medium rounded-full shrink-0 flex items-center gap-1 border border-gray-200">
                                        <i class="far fa-clock text-[8px]"></i> Menunggu
                                    </span>
                                </div>
                                <p class="text-[10px] text-gray-400 mb-1.5">{{ $jadwal->majelis->tempat ?? 'Lokasi tidak diketahui' }}</p>
                                <div class="flex flex-col gap-1.5 text-[10px] text-gray-500 font-medium">
                                    <span class="flex items-center gap-1.5"><i class="fas fa-book w-3 text-center text-gray-400"></i> {{ $jadwal->kitab->nama_kitab ?? '-' }}</span>
                                    <span class="flex items-center gap-1.5"><i class="fas fa-graduation-cap w-3 text-center text-gray-400"></i> {{ $jadwal->ustadz->user->name ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-400 text-xs">
                            Tidak ada jadwal hari ini.
                        </div>
                    @endforelse
                </div>
                <!-- Button -->
                <div class="bg-white text-center border-t border-gray-50">
                    <a href="{{ route('admin.jadwal.index') }}" class="block p-3 text-[11px] font-bold text-[#2A864D] hover:bg-gray-50 transition-colors">Lihat Semua Jadwal</a>
                </div>
            </div>

            {{-- Statistik Kehadiran 7 Hari --}}
            <div class="bg-white rounded-xl shadow-[0_2px_10px_rgb(0,0,0,0.04)] border border-gray-50 mb-6 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-[13px] font-bold text-gray-800">Statistik Kehadiran 7 Hari</h3>
                    <div class="flex items-center gap-2.5 text-[9px] text-gray-500">
                        <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-[#2A864D]"></span> Hadir</span>
                        <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-gray-800"></span> Izin</span>
                    </div>
                </div>
                <div class="relative h-48 w-full">
                    <canvas class="weeklyAttendanceChart"></canvas>
                </div>
            </div>

            {{-- Aktivitas Presensi Terbaru --}}
            <div class="bg-white rounded-xl shadow-[0_2px_10px_rgb(0,0,0,0.04)] border border-gray-50 p-5 mb-6">
                <h3 class="text-[13px] font-bold text-gray-800 mb-4">Aktivitas Presensi Terbaru</h3>
                <div class="space-y-4">
                    @forelse($recentActivities as $act)
                    <div class="flex items-start gap-3 border-b border-gray-50 pb-4 last:border-0 last:pb-0">
                        <div class="w-7 h-7 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center shrink-0 mt-0.5">
                            <i class="far fa-check-circle text-[13px]"></i>
                        </div>
                        <div>
                            <p class="text-[11px] text-gray-500 leading-relaxed">
                                <strong class="text-gray-800 font-bold">{{ $act->pencatat->user->name ?? 'Admin' }}</strong> mencatat presensi 
                                <span class="font-bold text-[#2A864D]">{{ $act->jadwal->majelis->nama_majelis ?? '-' }}</span>
                            </p>
                            <p class="text-[9px] text-gray-400 mt-1">{{ $act->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-[11px] text-gray-400">Belum ada aktivitas presensi.</p>
                    @endforelse
                </div>
            </div>

            </div>{{-- END lg:hidden (mobile) --}}

            {{-- ===== TAMPILAN DESKTOP (disembunyikan di mobile) ===== --}}
            <div class="hidden lg:block">

            {{-- Admin Greeting Card --}}
            <div class="relative overflow-hidden rounded-2xl mb-8 shadow-xl"
                 style="background: linear-gradient(135deg, #1a4731 0%, #166534 40%, #15803d 70%, #16a34a 100%);">
                <div class="absolute -top-8 -right-8 w-48 h-48 rounded-full opacity-10"
                     style="background: radial-gradient(circle, #bbf7d0, transparent 70%)"></div>
                <div class="absolute bottom-0 left-1/3 w-32 h-32 rounded-full opacity-10"
                     style="background: radial-gradient(circle, #6ee7b7, transparent 70%)"></div>
                <div class="absolute top-2 left-1/2 w-24 h-24 rounded-full opacity-5"
                     style="background: radial-gradient(circle, #ffffff, transparent 70%)"></div>
                <div class="relative p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center text-3xl shrink-0 overflow-hidden shadow-md"
                             style="background: rgba(255,255,255,0.15); backdrop-filter: blur(8px); border: 3px solid rgba(110,231,183,0.4);">
                            @if(auth()->user()->foto_profil)
                                <img src="{{ asset('storage/'.auth()->user()->foto_profil) }}" class="w-full h-full object-cover" alt="">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&color=14532D&background=F1F5F9" class="w-full h-full object-cover" alt="">
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="text-emerald-200 text-xs font-medium tracking-wide uppercase mb-0.5">{{ $salamAdmin }}</p>
                            <h2 class="text-2xl font-extrabold text-white leading-tight">{{ auth()->user()->name }}</h2>
                            <div class="flex flex-wrap items-center gap-2 mt-1.5">
                                <span class="inline-flex items-center gap-1 text-xs text-white/80">
                                    <i class="fas fa-user-shield text-emerald-300 text-[10px]"></i>
                                    Administrator Sistem
                                </span>
                                <span class="text-white/30 text-xs">·</span>
                                <span class="inline-flex items-center gap-1 text-xs text-white/80">
                                    <i class="fas fa-users text-emerald-300 text-[10px]"></i>
                                    {{ $totalSantri }} santri terdaftar
                                </span>
                            </div>
                        </div>
                        <div class="flex sm:flex-col items-center sm:items-end gap-3 sm:gap-2 shrink-0">
                            <div class="text-right">
                                <p class="text-emerald-200 text-xs">{{ \Carbon\Carbon::now()->translatedFormat('l') }}</p>
                                <p class="text-white font-bold text-sm">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                            </div>
                            @if($izinPendingAdmin > 0)
                            <a href="{{ route('admin.izin.index') }}"
                               class="flex items-center gap-1.5 px-3 py-1.5 rounded-full transition hover:opacity-80"
                               style="background: rgba(239,68,68,0.7); border: 1px solid rgba(239,68,68,0.4);">
                                <i class="fas fa-envelope-open-text text-white text-xs"></i>
                                <span class="text-white text-xs font-bold">{{ $izinPendingAdmin }} izin pending</span>
                            </a>
                            @else
                            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full"
                                 style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2);">
                                <i class="fas fa-calendar-day text-emerald-300 text-xs"></i>
                                <span class="text-white text-xs font-bold">{{ $jadwalHariIni->count() }} Majelis Hari Ini</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stat Cards Desktop --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
                <a href="{{ route('admin.santri.index') }}" class="bg-white/60 backdrop-blur-xl rounded-2xl p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-9 h-9 bg-hijau-muda/20 rounded-full flex items-center justify-center text-hijau-muda">
                            <i class="fas fa-users text-sm"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-hijau-tua">{{ number_format($totalSantri) }}</h3>
                    <p class="text-xs font-medium text-gray-500 mt-0.5">Total Santri</p>
                </a>
                <a href="{{ route('admin.ustadz.index') }}" class="bg-white/60 backdrop-blur-xl rounded-2xl p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-9 h-9 bg-biru/20 rounded-full flex items-center justify-center text-biru">
                            <i class="fas fa-chalkboard-teacher text-sm"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-hijau-tua">{{ number_format($totalUstadz) }}</h3>
                    <p class="text-xs font-medium text-gray-500 mt-0.5">Total Ustadz</p>
                </a>
                <a href="{{ route('admin.majelis.index') }}" class="bg-white/60 backdrop-blur-xl rounded-2xl p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-9 h-9 bg-kuning/20 rounded-full flex items-center justify-center text-yellow-600">
                            <i class="fas fa-mosque text-sm"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-hijau-tua">{{ number_format($totalMajelis) }}</h3>
                    <p class="text-xs font-medium text-gray-500 mt-0.5">Majelis</p>
                </a>
                <a href="{{ route('admin.jadwal.index') }}" class="bg-white/60 backdrop-blur-xl rounded-2xl p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-9 h-9 bg-purple-100 rounded-full flex items-center justify-center text-purple-500">
                            <i class="fas fa-calendar-alt text-sm"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-hijau-tua">{{ number_format($totalJadwal) }}</h3>
                    <p class="text-xs font-medium text-gray-500 mt-0.5">Total Jadwal</p>
                </a>
                <a href="{{ route('admin.presensi.index') }}" class="bg-white/60 backdrop-blur-xl rounded-2xl p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-9 h-9 bg-hijau-tua/20 rounded-full flex items-center justify-center text-hijau-tua">
                            <i class="fas fa-clipboard-check text-sm"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-hijau-tua">{{ $presensiBuilanIni }}</h3>
                    <p class="text-xs font-medium text-gray-500 mt-0.5">Presensi Bulan Ini</p>
                </a>
                <a href="{{ route('admin.izin.index', ['status' => 'Menunggu']) }}"
                   class="bg-white/60 backdrop-blur-xl rounded-2xl p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border {{ $izinPendingAdmin > 0 ? 'border-orange-200 ring-1 ring-orange-100' : 'border-white/50' }} hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between mb-2">
                        <div class="w-9 h-9 {{ $izinPendingAdmin > 0 ? 'bg-orange-100' : 'bg-gray-100' }} rounded-full flex items-center justify-center {{ $izinPendingAdmin > 0 ? 'text-orange-500' : 'text-gray-400' }}">
                            <i class="fas fa-envelope-open-text text-sm"></i>
                        </div>
                        @if($izinPendingAdmin > 0)
                        <span class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></span>
                        @endif
                    </div>
                    <h3 class="text-2xl font-bold {{ $izinPendingAdmin > 0 ? 'text-orange-500' : 'text-hijau-tua' }}">{{ $izinPendingAdmin }}</h3>
                    <p class="text-xs font-medium text-gray-500 mt-0.5">Izin Pending</p>
                </a>
            </div>

            {{-- Jadwal Hari Ini (Desktop) --}}
            <div class="bg-white/60 backdrop-blur-xl rounded-2xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 mb-8">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-hijau-tua/10 rounded-xl flex items-center justify-center text-hijau-tua">
                            <i class="fas fa-calendar-day text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Jadwal Hari Ini</h3>
                            <p class="text-xs text-gray-500">{{ $hariIni }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                        </div>
                    </div>
                    <span class="text-xs font-medium bg-hijau-tua/10 text-hijau-tua px-3 py-1 rounded-full">
                        {{ $jadwalHariIni->count() }} Sesi
                    </span>
                </div>
                @if($jadwalHariIni->isEmpty())
                    <div class="flex flex-col items-center justify-center py-10 text-gray-400">
                        <i class="fas fa-calendar-times text-4xl mb-3 opacity-40"></i>
                        <p class="text-sm">Tidak ada jadwal hari ini.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide border-b border-gray-100">
                                    <th class="pb-3 pr-4">Waktu</th>
                                    <th class="pb-3 pr-4">Majelis</th>
                                    <th class="pb-3 pr-4">Kitab</th>
                                    <th class="pb-3 pr-4">Ustadz</th>
                                    <th class="pb-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($jadwalHariIni as $jadwal)
                                @php
                                    $jamMulai = \Carbon\Carbon::parse($jadwal->jam_mulai);
                                    $jamSelesai = \Carbon\Carbon::parse($jadwal->jam_selesai);
                                    $now = \Carbon\Carbon::now();
                                    $sudahSelesai = $now->gt($jamSelesai);
                                    $sedangBerlangsung = $now->between($jamMulai, $jamSelesai);
                                    $sudahDipresensi = $jadwal->sudah_dipresensi ?? false;
                                @endphp
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="py-3 pr-4">
                                        <span class="font-mono text-xs font-semibold text-gray-700">{{ $jamMulai->format('H:i') }}</span>
                                        <span class="text-gray-400 text-xs mx-1">–</span>
                                        <span class="font-mono text-xs text-gray-500">{{ $jamSelesai->format('H:i') }}</span>
                                    </td>
                                    <td class="py-3 pr-4">
                                        <div class="font-medium text-gray-800">{{ $jadwal->majelis->nama_majelis ?? '-' }}</div>
                                        <div class="text-xs text-gray-400">{{ $jadwal->majelis->tempat ?? '' }}</div>
                                    </td>
                                    <td class="py-3 pr-4 text-gray-600">{{ $jadwal->kitab->nama_kitab ?? '-' }}</td>
                                    <td class="py-3 pr-4 text-gray-600">{{ $jadwal->ustadz->user->name ?? '-' }}</td>
                                    <td class="py-3">
                                        @if($sudahDipresensi)
                                            <span class="inline-flex items-center gap-1 text-xs font-medium bg-green-100 text-green-700 px-2.5 py-1 rounded-full">
                                                <i class="fas fa-check text-[10px]"></i> Tercatat
                                            </span>
                                        @elseif($sedangBerlangsung)
                                            <span class="inline-flex items-center gap-1 text-xs font-medium bg-blue-100 text-blue-700 px-2.5 py-1 rounded-full animate-pulse">
                                                <i class="fas fa-circle text-[8px]"></i> Berlangsung
                                            </span>
                                        @elseif($sudahSelesai)
                                            <span class="inline-flex items-center gap-1 text-xs font-medium bg-red-100 text-red-600 px-2.5 py-1 rounded-full">
                                                <i class="fas fa-times text-[10px]"></i> Belum Dipresensi
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-xs font-medium bg-gray-100 text-gray-500 px-2.5 py-1 rounded-full">
                                                <i class="fas fa-clock text-[10px]"></i> Menunggu
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Chart + Aktivitas (Desktop) --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="col-span-2 bg-white/60 backdrop-blur-xl rounded-2xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Statistik Kehadiran 7 Hari Terakhir</h3>
                        <div class="flex items-center gap-3 text-xs text-gray-500">
                            <span class="flex items-center gap-1.5"><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:rgba(34,197,94,0.85);flex-shrink:0;"></span> Hadir</span>
                            <span class="flex items-center gap-1.5"><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:rgba(251,191,36,0.85);flex-shrink:0;"></span> Izin/Sakit</span>
                            <span class="flex items-center gap-1.5"><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:rgba(248,113,113,0.85);flex-shrink:0;"></span> Alpha</span>
                        </div>
                    </div>
                    <div class="relative h-56">
                        <canvas class="weeklyAttendanceChart"></canvas>
                    </div>
                </div>
                <div class="bg-white/60 backdrop-blur-xl rounded-2xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Aktivitas Presensi Terbaru</h3>
                    <div class="space-y-4">
                        @forelse($recentActivities as $act)
                        <div class="flex items-start">
                            <div class="w-8 h-8 rounded-full bg-biru/20 flex items-center justify-center text-biru mt-1">
                                <i class="fas fa-check-circle text-sm"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-800">{{ $act->pencatat->user->name ?? 'Admin' }} mencatat presensi</p>
                                <p class="text-xs text-gray-500">{{ $act->jadwal->majelis->nama_majelis ?? '-' }} • {{ $act->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @empty
                        <p class="text-sm text-gray-500">Belum ada aktivitas presensi.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            </div>{{-- END hidden lg:block (desktop) --}}

        @elseif($role === 'ustadz')
            {{-- Data ustadz sudah disiapkan DashboardController --}}
            {{-- ===== TAMPILAN MOBILE (disembunyikan di desktop) ===== --}}
            <div class="lg:hidden">
                {{-- Ustadz Greeting Card (Mobile) --}}
                <div class="relative overflow-hidden rounded-2xl mb-6 shadow-xl text-white p-5" style="background: linear-gradient(135deg, #1a4731 0%, #166534 40%, #15803d 70%, #16a34a 100%);">
                    <div class="absolute -top-8 -right-8 w-48 h-48 rounded-full opacity-10" style="background: radial-gradient(circle, #bbf7d0, transparent 70%)"></div>
                    <div class="absolute bottom-0 left-1/3 w-32 h-32 rounded-full opacity-10" style="background: radial-gradient(circle, #6ee7b7, transparent 70%)"></div>
                    <div class="relative z-10 flex items-center gap-3">
                        {{-- Avatar --}}
                        <div class="w-12 h-12 rounded-full border-[3px] border-emerald-300/30 shadow-md overflow-hidden flex-shrink-0 bg-emerald-900 flex items-center justify-center">
                            @if(auth()->user()->foto_profil)
                                <img src="{{ asset('storage/'.auth()->user()->foto_profil) }}" class="w-full h-full object-cover">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&color=14532D&background=F1F5F9" class="w-full h-full object-cover" alt="">
                            @endif
                        </div>
                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-[10px] text-emerald-100 font-semibold uppercase tracking-wider mb-0.5">{{ mb_strtoupper($salamU) }}</p>
                            <h2 class="text-base font-bold mb-0.5 leading-tight truncate text-white">{{ auth()->user()->name }}</h2>
                            <div class="flex items-center gap-1.5 text-[10px] text-emerald-100">
                                <i class="fas fa-book-open text-[9px]"></i>
                                <span>{{ $totalJadwalU }} Majelis</span>
                            </div>
                        </div>
                        {{-- Right Badge --}}
                        <div class="flex flex-col items-end gap-1.5 flex-shrink-0">
                            <p class="text-[9px] font-bold uppercase tracking-wider text-emerald-100 text-right leading-tight">
                                {{ \Carbon\Carbon::now()->translatedFormat('d M Y') }}
                            </p>
                            @if($izinPendingU > 0)
                            <a href="{{ route('ustadz.izin.index') }}" class="inline-flex items-center gap-1 px-2 py-1 bg-red-500/80 rounded-md border border-red-400/50">
                                <i class="fas fa-exclamation-circle text-[10px]"></i>
                                <span class="text-[10px] font-semibold text-white">{{ $izinPendingU }} Izin</span>
                            </a>
                            @else
                            <div class="inline-flex items-center gap-1 px-2 py-1 bg-white/20 rounded-md">
                                <i class="far fa-calendar-alt text-[10px]"></i>
                                <span class="text-[10px] font-semibold">{{ $jadwalHariIniU->count() }} Jadwal</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Ustadz Stat Cards (Mobile) --}}
                <h3 class="text-sm font-bold text-gray-800 mb-3 px-1">Ringkasan Data</h3>
                <div class="grid grid-cols-2 gap-3 mb-6">
                    <div class="bg-white rounded-xl p-3 shadow-[0_2px_10px_rgb(0,0,0,0.04)] border border-gray-50 flex flex-col items-center justify-center text-center">
                        <div class="w-8 h-8 rounded-full bg-hijau-tua/10 flex items-center justify-center text-hijau-tua mb-2">
                            <i class="fas fa-calendar-day text-xs"></i>
                        </div>
                        <h4 class="text-xl font-bold text-gray-900 mb-0.5">{{ $jadwalHariIniU->count() }}</h4>
                        <p class="text-[9px] text-gray-500 font-medium leading-tight">Jadwal Hari Ini</p>
                    </div>
                    <div class="bg-white rounded-xl p-3 shadow-[0_2px_10px_rgb(0,0,0,0.04)] border border-gray-50 flex flex-col items-center justify-center text-center">
                        <div class="w-8 h-8 rounded-full bg-biru/10 flex items-center justify-center text-biru mb-2">
                            <i class="fas fa-chalkboard-teacher text-xs"></i>
                        </div>
                        <h4 class="text-xl font-bold text-gray-900 mb-0.5">{{ $totalJadwalU }}</h4>
                        <p class="text-[9px] text-gray-500 font-medium leading-tight">Total Majelis</p>
                    </div>
                    <div class="bg-white rounded-xl p-3 shadow-[0_2px_10px_rgb(0,0,0,0.04)] border border-gray-50 flex flex-col items-center justify-center text-center">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600 mb-2">
                            <i class="fas fa-clipboard-check text-xs"></i>
                        </div>
                        <h4 class="text-xl font-bold text-gray-900 mb-0.5">{{ $presensiBuilanU }}</h4>
                        <p class="text-[9px] text-gray-500 font-medium leading-tight">Presensi Bulan Ini</p>
                    </div>
                    <div class="bg-white rounded-xl p-3 shadow-[0_2px_10px_rgb(0,0,0,0.04)] border border-gray-50 flex flex-col items-center justify-center text-center">
                        <div class="w-8 h-8 rounded-full {{ $izinPendingU > 0 ? 'bg-orange-50 text-orange-400' : 'bg-gray-100 text-gray-400' }} flex items-center justify-center mb-2">
                            <i class="fas fa-file-alt text-xs"></i>
                        </div>
                        <h4 class="text-xl font-bold {{ $izinPendingU > 0 ? 'text-orange-500' : 'text-gray-900' }} mb-0.5">{{ $izinPendingU }}</h4>
                        <p class="text-[9px] text-gray-500 font-medium leading-tight">Izin Pending</p>
                    </div>
                </div>
            </div>

            {{-- ===== TAMPILAN DESKTOP ===== --}}
            <div class="hidden lg:block">
                <div class="relative overflow-hidden rounded-2xl mb-8 shadow-xl"
                     style="background: linear-gradient(135deg, #1a4731 0%, #166534 40%, #15803d 70%, #16a34a 100%);">
                    {{-- Blob dekorasi --}}
                    <div class="absolute -top-8 -right-8 w-48 h-48 rounded-full opacity-10"
                         style="background: radial-gradient(circle, #bbf7d0, transparent 70%)"></div>
                    <div class="absolute bottom-0 left-1/3 w-32 h-32 rounded-full opacity-10"
                         style="background: radial-gradient(circle, #6ee7b7, transparent 70%)"></div>
                    <div class="absolute top-2 left-1/2 w-24 h-24 rounded-full opacity-5"
                         style="background: radial-gradient(circle, #ffffff, transparent 70%)"></div>

                    <div class="relative p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                            {{-- Avatar --}}
                            <div class="w-16 h-16 rounded-full flex items-center justify-center text-3xl shrink-0 overflow-hidden shadow-md"
                                 style="background: rgba(255,255,255,0.15); backdrop-filter: blur(8px); border: 3px solid rgba(110,231,183,0.4);">
                                @if(auth()->user()->foto_profil)
                                    <img src="{{ asset('storage/'.auth()->user()->foto_profil) }}" class="w-full h-full object-cover" alt="">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&color=14532D&background=F1F5F9" class="w-full h-full object-cover" alt="">
                                @endif
                            </div>

                            {{-- Info --}}
                            <div class="flex-1">
                                <p class="text-emerald-200 text-xs font-medium tracking-wide uppercase mb-0.5">{{ $salamU }}, {{ $sapaanU }}</p>
                                <h2 class="text-2xl font-extrabold text-white leading-tight">{{ auth()->user()->name }}</h2>
                                <div class="flex flex-wrap items-center gap-2 mt-1.5">
                                    <span class="inline-flex items-center gap-1 text-xs text-white/80">
                                        <i class="fas fa-book-open text-emerald-300 text-[10px]"></i>
                                        {{ $totalJadwalU }} total jadwal mengajar
                                    </span>
                                    <span class="text-white/30 text-xs">·</span>
                                    <span class="inline-flex items-center gap-1 text-xs text-white/80">
                                        <i class="fas fa-clipboard-check text-emerald-300 text-[10px]"></i>
                                        {{ $presensiBuilanU }}x presensi bulan ini
                                    </span>
                                </div>
                            </div>

                            {{-- Date + badge --}}
                            <div class="flex sm:flex-col items-center sm:items-end gap-3 sm:gap-2 shrink-0">
                                <div class="text-right">
                                    <p class="text-emerald-200 text-xs">{{ $hariIniU }}</p>
                                    <p class="text-white font-bold text-sm">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                                </div>
                                @if($izinPendingU > 0)
                                <a href="{{ route('ustadz.izin.index') }}"
                                   class="flex items-center gap-1.5 px-3 py-1.5 rounded-full transition hover:opacity-80"
                                   style="background: rgba(239,68,68,0.7); border: 1px solid rgba(239,68,68,0.4);">
                                    <i class="fas fa-exclamation-circle text-white text-xs"></i>
                                    <span class="text-white text-xs font-bold">{{ $izinPendingU }} izin pending</span>
                                </a>
                                @else
                                <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full"
                                     style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2);">
                                    <i class="fas fa-calendar-day text-emerald-300 text-xs"></i>
                                    <span class="text-white text-xs font-bold">{{ $jadwalHariIniU->count() }} Majelis Hari Ini</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- USTADZ: Stat Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white/60 backdrop-blur-xl rounded-2xl p-6 border border-white/50 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 mb-1">Jadwal Hari Ini</p>
                                <h3 class="text-3xl font-bold text-hijau-tua">{{ $jadwalHariIniU->count() }}</h3>
                                <p class="text-xs text-gray-400 mt-1">{{ $hariIniU }}</p>
                            </div>
                            <div class="w-12 h-12 bg-hijau-tua/10 rounded-full flex items-center justify-center text-hijau-tua">
                                <i class="fas fa-calendar-day text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white/60 backdrop-blur-xl rounded-2xl p-6 border border-white/50 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 mb-1">Total Majelis</p>
                                <h3 class="text-3xl font-bold text-hijau-tua">{{ $totalJadwalU }}</h3>
                                <p class="text-xs text-gray-400 mt-1">Semua hari</p>
                            </div>
                            <div class="w-12 h-12 bg-biru/10 rounded-full flex items-center justify-center text-biru">
                                <i class="fas fa-chalkboard-teacher text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white/60 backdrop-blur-xl rounded-2xl p-6 border border-white/50 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 mb-1">Presensi Bulan Ini</p>
                                <h3 class="text-3xl font-bold text-hijau-tua">{{ $presensiBuilanU }}</h3>
                                <p class="text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-green-600">
                                <i class="fas fa-clipboard-check text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white/60 backdrop-blur-xl rounded-2xl p-6 border border-white/50 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 mb-1">Izin Perlu Validasi</p>
                                <h3 class="text-3xl font-bold {{ $izinPendingU > 0 ? 'text-orange-500' : 'text-hijau-tua' }}">{{ $izinPendingU }}</h3>
                                <p class="text-xs text-gray-400 mt-1">Menunggu persetujuan</p>
                            </div>
                            <div class="w-12 h-12 bg-orange-50 rounded-full flex items-center justify-center text-orange-400">
                                <i class="fas fa-file-alt text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- USTADZ: Jadwal Hari Ini + Aksi --}}
            <div class="bg-white/60 backdrop-blur-xl rounded-2xl p-6 border border-white/50 shadow-[0_8px_30px_rgb(0,0,0,0.04)] mb-8">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-hijau-tua/10 rounded-xl flex items-center justify-center text-hijau-tua">
                            <i class="fas fa-calendar-day text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 leading-tight">Jadwal Mengajar</h3>
                            <p class="text-xs text-gray-500">{{ $hariIniU }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('ustadz.jadwal.index') }}"
                       class="text-xs font-medium text-hijau-tua hover:underline flex items-center gap-1 bg-transparent py-0 rounded-none">
                        Semua <i class="fas fa-arrow-right text-[10px]"></i>
                    </a>
                </div>

                @if($jadwalHariIniU->isEmpty())
                    <div class="flex flex-col items-center justify-center py-10 text-gray-400">
                        <i class="fas fa-calendar-times text-4xl mb-3 opacity-40"></i>
                        <p class="text-sm">Tidak ada jadwal mengajar hari ini.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        {{-- DESKTOP TABLE --}}
                        <table class="w-full text-sm hidden md:table">
                            <thead>
                                <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide border-b border-gray-100">
                                    <th class="pb-3 pr-4">Waktu</th>
                                    <th class="pb-3 pr-4">Majelis</th>
                                    <th class="pb-3 pr-4">Kitab</th>
                                    <th class="pb-3 pr-4">Status Waktu</th>
                                    <th class="pb-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($jadwalHariIniU as $jdwl)
                                @php
                                    $jMulai      = \Carbon\Carbon::parse($jdwl->jam_mulai);
                                    $jSelesai    = \Carbon\Carbon::parse($jdwl->jam_selesai);
                                    $now         = \Carbon\Carbon::now();
                                    $berlangsung = $now->between($jMulai, $jSelesai);
                                    $selesai     = $now->gt($jSelesai);
                                    $presensiHariIni = $jdwl->presensis->first(); // eager loaded
                                    $sudahDipresensi = !is_null($presensiHariIni);
                                @endphp
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="py-3 pr-4">
                                        <span class="font-mono text-xs font-semibold text-gray-700">{{ $jMulai->format('H:i') }}</span>
                                        <span class="text-gray-400 text-xs mx-1">–</span>
                                        <span class="font-mono text-xs text-gray-500">{{ $jSelesai->format('H:i') }}</span>
                                    </td>
                                    <td class="py-3 pr-4">
                                        <div class="font-medium text-gray-800">{{ $jdwl->majelis->nama_majelis ?? '-' }}</div>
                                        <div class="text-xs text-gray-400">{{ $jdwl->majelis->tempat ?? '' }}</div>
                                    </td>
                                    <td class="py-3 pr-4 text-gray-600">{{ $jdwl->kitab->nama_kitab ?? '-' }}</td>
                                    <td class="py-3 pr-4">
                                        @if($berlangsung)
                                            <span class="inline-flex items-center gap-1 text-xs font-medium bg-blue-100 text-blue-700 px-2.5 py-1 rounded-full animate-pulse">
                                                <i class="fas fa-circle text-[8px]"></i> Berlangsung
                                            </span>
                                        @elseif($selesai)
                                            <span class="inline-flex items-center gap-1 text-xs font-medium bg-gray-100 text-gray-500 px-2.5 py-1 rounded-full">
                                                <i class="fas fa-check text-[10px]"></i> Selesai
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-xs font-medium bg-gray-100 text-gray-500 px-2.5 py-1 rounded-full">
                                                <i class="fas fa-clock text-[10px]"></i> Menunggu
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-right">
                                        @if($sudahDipresensi)
                                            <div class="flex items-center justify-end gap-2">
                                                <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700 bg-green-100 px-2.5 py-1 rounded-full">
                                                    <i class="fas fa-check"></i> Tercatat
                                                </span>
                                                <a href="{{ route('ustadz.presensi.show', $presensiHariIni->id) }}"
                                                   class="text-[11px] text-hijau-tua border border-hijau-tua/30 px-3 py-1.5 rounded-full hover:bg-hijau-tua/5 transition-colors">
                                                    <i class="fas fa-eye mr-1"></i>Lihat
                                                </a>
                                            </div>
                                        @else
                                            <a href="{{ route('ustadz.presensi.create', ['jadwal_id' => $jdwl->id]) }}"
                                               class="inline-flex items-center gap-1.5 text-xs font-medium bg-hijau-tua text-white px-3 py-1.5 rounded-full hover:bg-hijau-muda transition-colors">
                                                <i class="fas fa-pen text-[10px]"></i> Catat Presensi
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{-- MOBILE CARDS (Tampilan seperti di Admin) --}}
                        <div class="md:hidden mt-2 bg-white rounded-xl shadow-[0_2px_10px_rgb(0,0,0,0.04)] border border-gray-50 overflow-hidden">
                            <div class="divide-y divide-gray-50">
                                @foreach($jadwalHariIniU as $jdwl)
                                    @php
                                        $jMulai      = \Carbon\Carbon::parse($jdwl->jam_mulai);
                                        $jSelesai    = \Carbon\Carbon::parse($jdwl->jam_selesai);
                                        $now         = \Carbon\Carbon::now();
                                        $berlangsung = $now->between($jMulai, $jSelesai);
                                        $selesai     = $now->gt($jSelesai);
                                        $presensiHariIni = $jdwl->presensis->first(); // eager loaded
                                        $sudahDipresensi = !is_null($presensiHariIni);
                                    @endphp
                                    <div class="p-4 flex gap-4 relative">
                                        <!-- Time -->
                                        <div class="w-10 flex-shrink-0 text-center flex flex-col justify-start pt-0.5">
                                            <div class="text-[11px] font-bold text-gray-800 flex flex-col gap-0.5 items-center">
                                                <span class="flex items-center justify-center gap-1"><i class="far fa-clock text-[9px] text-[#2A864D]"></i> {{ $jMulai->format('H:i') }}</span>
                                                <span class="text-gray-400 font-normal">{{ $jSelesai->format('H:i') }}</span>
                                            </div>
                                        </div>
                                        <!-- Details -->
                                        <div class="flex-1 min-w-0 flex flex-col justify-center">
                                            <div class="flex items-start justify-between gap-2 mb-1">
                                                <h4 class="text-[13px] font-bold text-gray-800 truncate">{{ $jdwl->majelis->nama_majelis ?? '-' }}</h4>
                                                @if($berlangsung)
                                                    <span class="px-2 py-0.5 bg-blue-50 text-blue-600 text-[9px] font-bold rounded-full shrink-0 flex items-center gap-1 animate-pulse border border-blue-200">
                                                        <i class="fas fa-circle text-[6px]"></i> Berlangsung
                                                    </span>
                                                @elseif($selesai)
                                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-500 text-[9px] font-bold rounded-full shrink-0 flex items-center gap-1 border border-gray-200">
                                                        <i class="fas fa-check text-[8px]"></i> Selesai
                                                    </span>
                                                @else
                                                    <span class="px-2 py-0.5 bg-yellow-50 text-yellow-600 text-[9px] font-bold rounded-full shrink-0 flex items-center gap-1 border border-yellow-200">
                                                        <i class="far fa-clock text-[8px]"></i> Menunggu
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-[10px] text-gray-400 mb-1.5">{{ $jdwl->majelis->tempat ?? 'Lokasi tidak diketahui' }}</p>
                                            <div class="flex flex-col gap-1.5 text-[10px] text-gray-500 font-medium mb-3">
                                                <span class="flex items-center gap-1.5"><i class="fas fa-book w-3 text-center text-gray-400"></i> {{ $jdwl->kitab->nama_kitab ?? '-' }}</span>
                                            </div>
                                            
                                            <!-- Aksi Catat Presensi -->
                                            <div class="pt-2 border-t border-gray-100">
                                                @if($sudahDipresensi)
                                                    <div class="flex items-center justify-between">
                                                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#2A864D]">
                                                            <i class="fas fa-check-circle"></i> Selesai Dicatat
                                                        </span>
                                                        <a href="{{ route('ustadz.presensi.show', $presensiHariIni->id) }}"
                                                           class="text-[11px] font-bold text-[#2A864D] hover:underline">
                                                            Lihat
                                                        </a>
                                                    </div>
                                                @else
                                                    <a href="{{ route('ustadz.presensi.create', ['jadwal_id' => $jdwl->id]) }}"
                                                       class="w-full inline-flex items-center justify-center gap-1.5 text-[11px] font-bold bg-[#2A864D] text-white px-3 py-1.5 rounded-lg shadow-sm hover:opacity-90 transition-opacity">
                                                        <i class="fas fa-pen text-[9px]"></i> Catat Presensi
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- USTADZ: Presensi Terakhir --}}
            <div class="bg-white/60 backdrop-blur-xl rounded-2xl p-4 md:p-6 border border-white/50 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
                <div class="flex items-center justify-between mb-4 md:mb-5 pb-3 md:pb-0 border-b border-gray-100 md:border-0">
                    <div class="flex items-center gap-2.5 md:gap-3">
                        <div class="w-8 h-8 md:w-10 md:h-10 bg-biru/10 rounded-lg md:rounded-xl flex items-center justify-center text-biru shrink-0">
                            <i class="fas fa-history text-sm md:text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-[13px] md:text-lg font-bold text-gray-800 leading-tight">Presensi Terakhir</h3>
                            <p class="md:hidden text-[10px] text-gray-500">Baru dicatat</p>
                        </div>
                    </div>
                    <a href="{{ route('ustadz.presensi.index') }}"
                       class="text-[11px] md:text-xs font-bold md:font-medium text-hijau-tua hover:underline flex items-center gap-1 shrink-0 bg-green-50 md:bg-transparent px-2 md:px-0 py-1 md:py-0 rounded-md md:rounded-none">
                        Semua <i class="fas fa-arrow-right text-[9px] md:text-[10px]"></i>
                    </a>
                </div>
                @php
                    $recentU = \App\Models\Presensi::with(['jadwal.majelis','jadwal.kitab','details'])
                        ->where('dicatat_oleh', $ustadz->id)
                        ->latest('tanggal')->take(5)->get();
                @endphp
                @forelse($recentU as $rec)
                @php
                    $totalSiswa = $rec->details->count();
                    $hadirSiswa = $rec->details->where('status_kehadiran','Hadir')->count();
                    $pct = $totalSiswa > 0 ? round(($hadirSiswa/$totalSiswa)*100) : 0;
                @endphp
                <div class="flex items-center gap-4 py-3 border-b border-gray-50 last:border-0">
                    <div class="w-10 h-10 rounded-xl bg-hijau-tua/10 flex items-center justify-center text-hijau-tua shrink-0">
                        <i class="fas fa-clipboard-list text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $rec->jadwal->majelis->nama_majelis ?? '-' }}</p>
                        <p class="text-xs text-gray-500">
                            {{ $rec->jadwal->kitab->nama_kitab ?? '-' }} &nbsp;·&nbsp;
                            {{ \Carbon\Carbon::parse($rec->tanggal)->translatedFormat('d M Y') }}
                        </p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-sm font-bold text-hijau-tua">{{ $pct }}%</p>
                        <p class="text-xs text-gray-400">{{ $hadirSiswa }}/{{ $totalSiswa }} hadir</p>
                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-10 text-gray-400">
                    <i class="fas fa-inbox text-3xl mb-2 opacity-40"></i>
                    <p class="text-sm">Belum ada presensi dicatat.</p>
                </div>
                @endforelse
            </div>

        @elseif($role === 'santri')
            {{-- ===== TAMPILAN MOBILE ===== --}}
            <div class="lg:hidden">
                {{-- Santri Greeting Card (Mobile) --}}
                <div class="relative overflow-hidden rounded-2xl mb-6 shadow-xl text-white p-5" style="background: linear-gradient(135deg, #1a4731 0%, #166534 40%, #15803d 70%, #16a34a 100%);">
                    <div class="absolute -top-8 -right-8 w-48 h-48 rounded-full opacity-10" style="background: radial-gradient(circle, #bbf7d0, transparent 70%)"></div>
                    <div class="absolute bottom-0 left-1/3 w-32 h-32 rounded-full opacity-10" style="background: radial-gradient(circle, #6ee7b7, transparent 70%)"></div>
                    <div class="relative z-10 flex items-center gap-3">
                        <div class="w-14 h-14 rounded-full border-[3px] border-emerald-300/40 shadow-md overflow-hidden flex-shrink-0 bg-emerald-900 flex items-center justify-center">
                            @if(auth()->user()->foto_profil)
                                <img src="{{ asset('storage/'.auth()->user()->foto_profil) }}" class="w-full h-full object-cover">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&color=14532D&background=F1F5F9" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[10px] text-emerald-100 font-semibold uppercase tracking-wider mb-0.5">Selamat Datang Kembali</p>
                            <h2 class="text-base font-bold mb-0.5 leading-tight truncate text-white">{{ auth()->user()->name }}</h2>
                            <div class="flex items-center gap-1.5 text-[10px] text-emerald-100">
                                <i class="fas fa-mosque text-[9px]"></i>
                                <span class="truncate">{{ $santri?->majelis?->nama_majelis ?? 'Belum terdaftar' }}</span>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-1.5 flex-shrink-0">
                            <p class="text-[9px] font-bold uppercase tracking-wider text-emerald-100 text-right leading-tight">
                                {{ \Carbon\Carbon::now()->translatedFormat('d M Y') }}
                            </p>
                            <div class="inline-flex items-center gap-1 px-2 py-1 bg-white/20 rounded-md">
                                <i class="fas fa-chart-pie text-[10px]"></i>
                                <span class="text-[10px] font-semibold">{{ $pctHadirS }}% Hadir</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Santri Stat Cards (Mobile) --}}
                <h3 class="text-sm font-bold text-gray-800 mb-3 px-1">Ringkasan Kehadiran</h3>
                <div class="grid grid-cols-2 gap-3 mb-6">
                    <div class="bg-white rounded-xl p-3 shadow-[0_2px_10px_rgb(0,0,0,0.04)] border border-gray-50 flex flex-col items-center justify-center text-center">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600 mb-2">
                            <i class="fas fa-user-check text-xs"></i>
                        </div>
                        <h4 class="text-xl font-bold text-gray-900 mb-0.5">{{ $pctHadirS }}%</h4>
                        <p class="text-[9px] text-gray-500 font-medium leading-tight">Kehadiran Bulan Ini</p>
                        <div class="mt-2 w-full h-1 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-hijau-tua rounded-full" style="width: {{ $pctHadirS }}%"></div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl p-3 shadow-[0_2px_10px_rgb(0,0,0,0.04)] border border-gray-50 flex flex-col items-center justify-center text-center">
                        <div class="w-8 h-8 rounded-full bg-biru/10 flex items-center justify-center text-biru mb-2">
                            <i class="fas fa-clipboard-list text-xs"></i>
                        </div>
                        <h4 class="text-xl font-bold text-gray-900 mb-0.5">{{ $hadirS }}</h4>
                        <p class="text-[9px] text-gray-500 font-medium leading-tight">Hadir dari {{ $totalPertemuanS }} Sesi</p>
                    </div>
                    <div class="bg-white rounded-xl p-3 shadow-[0_2px_10px_rgb(0,0,0,0.04)] border border-gray-50 flex flex-col items-center justify-center text-center">
                        <div class="w-8 h-8 rounded-full bg-yellow-50 flex items-center justify-center text-yellow-500 mb-2">
                            <i class="fas fa-file-medical text-xs"></i>
                        </div>
                        <h4 class="text-xl font-bold text-gray-900 mb-0.5">{{ $izinS }}</h4>
                        <p class="text-[9px] text-gray-500 font-medium leading-tight">Izin / Sakit</p>
                    </div>
                    <div class="bg-white rounded-xl p-3 shadow-[0_2px_10px_rgb(0,0,0,0.04)] border border-gray-50 flex flex-col items-center justify-center text-center">
                        <div class="w-8 h-8 rounded-full {{ $izinPendingS > 0 ? 'bg-orange-50 text-orange-400' : 'bg-gray-100 text-gray-400' }} flex items-center justify-center mb-2">
                            <i class="fas fa-hourglass-half text-xs"></i>
                        </div>
                        <h4 class="text-xl font-bold {{ $izinPendingS > 0 ? 'text-orange-500' : 'text-gray-900' }} mb-0.5">{{ $izinPendingS }}</h4>
                        <p class="text-[9px] text-gray-500 font-medium leading-tight">Izin Pending</p>
                    </div>
                </div>

                {{-- Jadwal Hari Ini (Mobile) --}}
                <div class="bg-white rounded-xl shadow-[0_2px_10px_rgb(0,0,0,0.04)] border border-gray-50 mb-4 overflow-hidden">
                    <div class="p-4 border-b border-gray-50 flex items-center justify-between bg-emerald-50/30">
                        <div class="flex items-center gap-2">
                            <i class="far fa-calendar-alt text-[#2A864D] text-base"></i>
                            <div>
                                <h3 class="text-[13px] font-bold text-[#2A864D] leading-tight">Jadwal Hari Ini</h3>
                                <p class="text-[10px] text-gray-500">{{ $hariIniS }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                            </div>
                        </div>
                        <a href="{{ route('santri.jadwal.index') }}" class="text-[11px] font-bold text-[#2A864D]">Semua <i class="fas fa-arrow-right text-[10px]"></i></a>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @forelse($jadwalHariIniS as $jdwlS)
                        @php
                            $jmS = \Carbon\Carbon::parse($jdwlS->jam_mulai);
                            $jsS = \Carbon\Carbon::parse($jdwlS->jam_selesai);
                            $berlangsungS = \Carbon\Carbon::now()->between($jmS, $jsS);
                            $statusSantriHariIni = $jdwlS->status_santri_hari_ini ?? null;
                            $badgeColorM = match($statusSantriHariIni) {
                                'Hadir' => 'bg-green-100 text-green-700',
                                'Izin'  => 'bg-yellow-100 text-yellow-700',
                                'Sakit' => 'bg-blue-100 text-blue-600',
                                'Alpha' => 'bg-red-100 text-red-600',
                                default => null,
                            };
                        @endphp
                        <div class="p-3 flex items-center gap-3">
                            <div class="text-center min-w-[44px]">
                                <p class="text-xs font-mono font-bold text-gray-700">{{ $jmS->format('H:i') }}</p>
                                <p class="text-[10px] text-gray-400">{{ $jsS->format('H:i') }}</p>
                            </div>
                            <div class="w-px h-8 bg-gray-200 shrink-0"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ $jdwlS->kitab->nama_kitab ?? '-' }}</p>
                                <p class="text-[10px] text-gray-500">{{ $jdwlS->ustadz->user->name ?? '-' }}</p>
                            </div>
                            <div class="shrink-0">
                                @if($badgeColorM)
                                    <span class="text-[10px] font-semibold px-2 py-1 rounded-full {{ $badgeColorM }}">{{ $statusSantriHariIni }}</span>
                                @elseif($berlangsungS)
                                    <span class="text-[10px] font-semibold bg-blue-100 text-blue-700 px-2 py-1 rounded-full animate-pulse">● Live</span>
                                @else
                                    <span class="text-[10px] text-gray-400 bg-gray-100 px-2 py-1 rounded-full">Menunggu</span>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="p-6 text-center text-gray-400 text-xs">Tidak ada jadwal hari ini.</div>
                        @endforelse
                    </div>
                    <div class="border-t border-gray-50 text-center">
                        <a href="{{ route('santri.izin.create') }}" class="block p-3 text-[11px] font-bold text-[#2A864D] hover:bg-gray-50 transition-colors">
                            <i class="fas fa-plus-circle mr-1"></i> Ajukan Izin
                        </a>
                    </div>
                </div>
            </div>

            {{-- ===== TAMPILAN DESKTOP ===== --}}
            <div class="hidden lg:block">
                {{-- SANTRI: Greeting --}}
                <div class="relative overflow-hidden rounded-2xl mb-8 shadow-xl"
                     style="background: linear-gradient(135deg, #1a4731 0%, #166534 40%, #15803d 70%, #16a34a 100%);">
                    <div class="absolute -top-8 -right-8 w-48 h-48 rounded-full opacity-10" style="background: radial-gradient(circle, #bbf7d0, transparent 70%)"></div>
                    <div class="absolute bottom-0 left-1/3 w-32 h-32 rounded-full opacity-10" style="background: radial-gradient(circle, #6ee7b7, transparent 70%)"></div>
                    <div class="relative p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center text-3xl shrink-0 overflow-hidden shadow-md"
                                 style="background: rgba(255,255,255,0.15); backdrop-filter: blur(8px); border: 3px solid rgba(110,231,183,0.4);">
                                @if(auth()->user()->foto_profil)
                                    <img src="{{ asset('storage/'.auth()->user()->foto_profil) }}" class="w-full h-full object-cover" alt="">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&color=14532D&background=F1F5F9" class="w-full h-full object-cover" alt="">
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="text-emerald-200 text-xs font-medium tracking-wide uppercase mb-0.5">Selamat datang kembali</p>
                                <h2 class="text-2xl font-extrabold text-white leading-tight">{{ auth()->user()->name }}</h2>
                                <div class="flex flex-wrap items-center gap-2 mt-1.5">
                                    <span class="inline-flex items-center gap-1 text-xs text-white/80"><i class="fas fa-mosque text-emerald-300 text-[10px]"></i>{{ $santri?->majelis?->nama_majelis ?? 'Belum terdaftar di majelis' }}</span>
                                    @if($santri?->nis)
                                    <span class="text-white/30 text-xs">·</span>
                                    <span class="inline-flex items-center gap-1 text-xs text-white/80"><i class="fas fa-id-card text-emerald-300 text-[10px]"></i>NIS: {{ $santri->nis }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex sm:flex-col items-center sm:items-end gap-3 sm:gap-2 shrink-0">
                                <div class="text-right">
                                    <p class="text-emerald-200 text-xs">{{ $hariIniS }}</p>
                                    <p class="text-white font-bold text-sm">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                                </div>
                                <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2);">
                                    <i class="fas fa-chart-pie text-emerald-300 text-xs"></i>
                                    <span class="text-white text-xs font-bold">{{ $pctHadirS }}% Hadir</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SANTRI: Stat Cards --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div class="bg-white/60 backdrop-blur-xl rounded-2xl p-5 border border-white/50 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
                        <div class="flex items-center gap-3 mb-3"><div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center text-green-600"><i class="fas fa-user-check text-sm"></i></div><p class="text-xs font-medium text-gray-500">Kehadiran</p></div>
                        <p class="text-3xl font-bold text-hijau-tua">{{ $pctHadirS }}%</p>
                        <p class="text-xs text-gray-400 mt-1">Bulan ini</p>
                        <div class="mt-3 h-1.5 bg-gray-100 rounded-full overflow-hidden"><div class="h-full bg-hijau-tua rounded-full transition-all" style="width: {{ $pctHadirS }}%"></div></div>
                    </div>
                    <div class="bg-white/60 backdrop-blur-xl rounded-2xl p-5 border border-white/50 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
                        <div class="flex items-center gap-3 mb-3"><div class="w-9 h-9 bg-biru/10 rounded-lg flex items-center justify-center text-biru"><i class="fas fa-clipboard-list text-sm"></i></div><p class="text-xs font-medium text-gray-500">Total Hadir</p></div>
                        <p class="text-3xl font-bold text-hijau-tua">{{ $hadirS }}</p>
                        <p class="text-xs text-gray-400 mt-1">dari {{ $totalPertemuanS }} pertemuan</p>
                    </div>
                    <div class="bg-white/60 backdrop-blur-xl rounded-2xl p-5 border border-white/50 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
                        <div class="flex items-center gap-3 mb-3"><div class="w-9 h-9 bg-yellow-50 rounded-lg flex items-center justify-center text-yellow-500"><i class="fas fa-file-medical text-sm"></i></div><p class="text-xs font-medium text-gray-500">Izin / Sakit</p></div>
                        <p class="text-3xl font-bold text-hijau-tua">{{ $izinS }}</p>
                        <p class="text-xs text-gray-400 mt-1">Bulan ini</p>
                    </div>
                    <div class="bg-white/60 backdrop-blur-xl rounded-2xl p-5 border border-white/50 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
                        <div class="flex items-center gap-3 mb-3"><div class="w-9 h-9 {{ $izinPendingS > 0 ? 'bg-orange-50 text-orange-400' : 'bg-gray-100 text-gray-400' }} rounded-lg flex items-center justify-center"><i class="fas fa-hourglass-half text-sm"></i></div><p class="text-xs font-medium text-gray-500">Izin Pending</p></div>
                        <p class="text-3xl font-bold {{ $izinPendingS > 0 ? 'text-orange-500' : 'text-hijau-tua' }}">{{ $izinPendingS }}</p>
                        <p class="text-xs text-gray-400 mt-1">Belum divalidasi</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                    {{-- SANTRI: Jadwal Hari Ini --}}
                    <div class="lg:col-span-3 bg-white/60 backdrop-blur-xl rounded-2xl p-6 border border-white/50 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
                        <div class="flex items-center justify-between mb-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-hijau-tua/10 rounded-xl flex items-center justify-center text-hijau-tua"><i class="fas fa-calendar-day"></i></div>
                                <div><h3 class="text-base font-semibold text-gray-800">Jadwal Hari Ini</h3><p class="text-xs text-gray-500">{{ $hariIniS }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p></div>
                            </div>
                            <a href="{{ route('santri.jadwal.index') }}" class="text-xs text-hijau-tua hover:underline flex items-center gap-1">Semua <i class="fas fa-arrow-right text-[10px]"></i></a>
                        </div>
                        @if($jadwalHariIniS->isEmpty())
                            <div class="flex flex-col items-center justify-center py-10 text-gray-400"><i class="fas fa-calendar-times text-3xl mb-2 opacity-40"></i><p class="text-sm">Tidak ada jadwal hari ini.</p></div>
                        @else
                            <div class="space-y-3">
                                @foreach($jadwalHariIniS as $jdwlS)
                                @php
                                    $jmS = \Carbon\Carbon::parse($jdwlS->jam_mulai); $jsS = \Carbon\Carbon::parse($jdwlS->jam_selesai); $nowS = \Carbon\Carbon::now();
                                    $berlangsungS = $nowS->between($jmS, $jsS); $selesaiS = $nowS->gt($jsS);
                                    $statusSantriHariIni = $jdwlS->status_santri_hari_ini ?? null;
                                @endphp
                                <div class="flex items-center gap-3 p-3 rounded-xl {{ $berlangsungS ? 'bg-blue-50 border border-blue-200' : 'bg-gray-50/60 border border-gray-100' }}">
                                    <div class="text-center min-w-[52px]"><p class="text-xs font-mono font-bold text-gray-700">{{ $jmS->format('H:i') }}</p><p class="text-[10px] text-gray-400">{{ $jsS->format('H:i') }}</p></div>
                                    <div class="w-px h-8 bg-gray-200 shrink-0"></div>
                                    <div class="flex-1 min-w-0"><p class="text-sm font-semibold text-gray-800 truncate">{{ $jdwlS->kitab->nama_kitab ?? '-' }}</p><p class="text-xs text-gray-500">{{ $jdwlS->ustadz->user->name ?? '-' }}</p></div>
                                    <div class="shrink-0">
                                        @if($statusSantriHariIni)
                                            @php $bc = match($statusSantriHariIni){'Hadir'=>'bg-green-100 text-green-700','Izin'=>'bg-yellow-100 text-yellow-700','Sakit'=>'bg-blue-100 text-blue-600',default=>'bg-red-100 text-red-600'}; @endphp
                                            <span class="text-[10px] font-semibold px-2 py-1 rounded-full {{ $bc }}">{{ $statusSantriHariIni }}</span>
                                        @elseif($berlangsungS)
                                            <span class="text-[10px] font-semibold bg-blue-100 text-blue-700 px-2 py-1 rounded-full animate-pulse">● Berlangsung</span>
                                        @elseif($selesaiS)
                                            <span class="text-[10px] text-gray-400 bg-gray-100 px-2 py-1 rounded-full">Selesai</span>
                                        @else
                                            <span class="text-[10px] text-gray-400 bg-gray-100 px-2 py-1 rounded-full">Menunggu</span>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- SANTRI: Riwayat Presensi Terakhir --}}
                    <div class="lg:col-span-2 bg-white/60 backdrop-blur-xl rounded-2xl p-6 border border-white/50 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
                        <div class="flex items-center justify-between mb-5">
                            <div class="flex items-center gap-3"><div class="w-10 h-10 bg-biru/10 rounded-xl flex items-center justify-center text-biru"><i class="fas fa-history"></i></div><h3 class="text-base font-semibold text-gray-800">Riwayat Terakhir</h3></div>
                            <a href="{{ route('santri.presensi.index') }}" class="text-xs text-hijau-tua hover:underline flex items-center gap-1">Semua <i class="fas fa-arrow-right text-[10px]"></i></a>
                        </div>
                        @forelse($riwayatS as $rwyt)
                        @php
                            $stKehadiran = $rwyt->status_kehadiran;
                            $iconMap = ['Hadir'=>['icon'=>'fa-check','bg'=>'bg-green-100','color'=>'text-green-600','badge'=>'bg-green-100 text-green-700'],'Izin'=>['icon'=>'fa-file-alt','bg'=>'bg-yellow-50','color'=>'text-yellow-500','badge'=>'bg-yellow-100 text-yellow-700'],'Sakit'=>['icon'=>'fa-heartbeat','bg'=>'bg-blue-50','color'=>'text-blue-400','badge'=>'bg-blue-100 text-blue-600'],'Alpha'=>['icon'=>'fa-times','bg'=>'bg-red-50','color'=>'text-red-400','badge'=>'bg-red-100 text-red-600']];
                            $ic = $iconMap[$stKehadiran] ?? $iconMap['Alpha'];
                        @endphp
                        <div class="flex items-center gap-3 py-2.5 border-b border-gray-50 last:border-0">
                            <div class="w-8 h-8 rounded-lg {{ $ic['bg'] }} {{ $ic['color'] }} flex items-center justify-center shrink-0"><i class="fas {{ $ic['icon'] }} text-xs"></i></div>
                            <div class="flex-1 min-w-0"><p class="text-xs font-semibold text-gray-800 truncate">{{ $rwyt->presensi?->jadwal?->kitab?->nama_kitab ?? '-' }}</p><p class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($rwyt->presensi?->tanggal)->translatedFormat('d M Y') }}</p></div>
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $ic['badge'] }} shrink-0">{{ $stKehadiran }}</span>
                        </div>
                        @empty
                        <div class="flex flex-col items-center justify-center py-10 text-gray-400"><i class="fas fa-inbox text-3xl mb-2 opacity-40"></i><p class="text-sm">Belum ada riwayat presensi.</p></div>
                        @endforelse
                        <div class="mt-5 pt-4 border-t border-gray-100">
                            <a href="{{ route('santri.izin.create') }}" class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl bg-hijau-tua/5 hover:bg-hijau-tua/10 text-hijau-tua text-xs font-medium transition-colors border border-hijau-tua/20"><i class="fas fa-plus-circle"></i> Ajukan Izin</a>
                        </div>
                    </div>
                </div>
            </div>

        @else
            {{-- Role tidak dikenal --}}
            <div class="bg-white/60 backdrop-blur-xl rounded-2xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50">
                <div class="text-gray-900 text-lg">
                    Selamat datang, <span class="font-bold">{{ auth()->user()->name }}</span>! Anda login sebagai {{ ucfirst($role) }}.
                </div>
            </div>
        @endif
    </div>

    @if(auth()->user()->role === 'admin')
    <script>
        // Grafik langsung dirender — Chart.js sudah ada di <head> layout
        (function() {
            const canvases = document.querySelectorAll('.weeklyAttendanceChart');
            canvases.forEach(function(ctx) {
                const existing = Chart.getChart(ctx);
                if (existing) existing.destroy();

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($chartLabels) !!},
                        datasets: [
                            {
                                label: 'Hadir',
                                data: {!! json_encode($chartHadir) !!},
                                backgroundColor: 'rgba(34,197,94,0.75)',
                                borderRadius: 6,
                                borderSkipped: false,
                            },
                            {
                                label: 'Izin / Sakit',
                                data: {!! json_encode($chartIzin) !!},
                                backgroundColor: 'rgba(251,191,36,0.75)',
                                borderRadius: 6,
                                borderSkipped: false,
                            },
                            {
                                label: 'Alpha',
                                data: {!! json_encode($chartAlpha) !!},
                                backgroundColor: 'rgba(248,113,113,0.75)',
                                borderRadius: 6,
                                borderSkipped: false,
                            },
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(17,24,39,0.85)',
                                padding: 10,
                                cornerRadius: 10,
                                titleFont: { size: 12, weight: 'bold' },
                                bodyFont: { size: 11 },
                                callbacks: {
                                    label: function(ctx) { return ' ' + ctx.dataset.label + ': ' + ctx.parsed.y + ' santri'; }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { font: { size: 11 }, color: '#6B7280' }
                            },
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(0,0,0,0.04)' },
                                ticks: { font: { size: 11 }, color: '#6B7280', stepSize: 1 }
                            }
                        }
                    }
                });
            });
        })();
    </script>
    @endif

</x-app-layout>
