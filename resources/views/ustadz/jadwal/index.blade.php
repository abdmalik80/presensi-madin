<x-app-layout>
    <x-slot name="header">Jadwal Mengajar Saya</x-slot>

    <div class="space-y-4">
        <x-alert />

        {{-- DESKTOP VIEW --}}
        <div class="hidden md:grid grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($jadwals as $jadwal)
                @php
                    $hariMap = ['Sunday'=>'Ahad','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
                    $hariIni = $hariMap[now()->format('l')] ?? '';
                    $isToday = $jadwal->hari === $hariIni;
                    $presensiToday = $jadwal->presensis->first();
                    $isOpen = $presensiToday && $presensiToday->status === 'dibuka';
                @endphp
                <div class="bg-white/80 backdrop-blur-md rounded-2xl border {{ $isToday ? 'border-hijau-tua ring-2 ring-hijau-tua/20 shadow-lg' : 'border-green-100 shadow-sm hover:shadow-md' }} p-4 sm:p-5 transition-all group relative overflow-hidden flex flex-col h-full">
                    <!-- Background decoration -->
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-green-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
                    
                    <div class="relative z-10 flex flex-col h-full">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 text-hijau-tua rounded-xl flex items-center justify-center font-bold shadow-sm">
                                <i class="fas fa-mosque text-base sm:text-lg"></i>
                            </div>
                            <div class="flex flex-col items-end gap-1.5">
                                <span class="px-3 py-1 flex items-center gap-1.5 {{ $isToday ? 'bg-orange-500 animate-pulse' : 'bg-hijau-tua' }} text-white text-[10px] font-bold uppercase tracking-wider rounded-lg shadow-sm">
                                    @if($isToday) <span class="w-1.5 h-1.5 bg-white rounded-full"></span> @endif
                                    {{ $jadwal->hari }} {{ $isToday ? '• Hari Ini' : '' }}
                                </span>
                                <span class="px-2.5 py-1 bg-white border border-gray-100 text-gray-600 text-[10px] font-bold uppercase tracking-wider rounded-lg">
                                    <i class="fas fa-clock mr-1 text-gray-400"></i>{{ $jadwal->jam_mulai ? substr($jadwal->jam_mulai, 0, 5) : 'TBA' }} - {{ $jadwal->jam_selesai ? substr($jadwal->jam_selesai, 0, 5) : 'TBA' }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="flex-grow">
                            <h3 class="text-base sm:text-lg font-bold text-gray-800 mb-1 group-hover:text-hijau-tua transition-colors">
                                {{ $jadwal->majelis->nama_majelis ?? 'Majelis Tidak Diketahui' }}
                            </h3>
                            <p class="text-xs sm:text-sm text-gray-500 flex items-center gap-1.5 mb-3 sm:mb-4">
                                <i class="fas fa-book-open text-gray-400"></i>
                                Kitab {{ $jadwal->kitab->nama_kitab ?? '-' }}
                            </p>

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
                                <a href="{{ route('ustadz.jadwal.show', $jadwal->id) }}" 
                                   class="flex items-center justify-center gap-1.5 w-full py-2.5 bg-white border border-gray-200 text-gray-600 text-center text-xs font-semibold rounded-xl hover:bg-gray-50 hover:text-hijau-tua hover:border-hijau-tua/30 transition-all">
                                    <i class="fas fa-folder-open"></i> Detail
                                </a>
                                <a href="{{ route('ustadz.presensi.create', ['jadwal_id' => $jadwal->id]) }}" 
                                   class="flex items-center justify-center gap-1.5 w-full py-2.5 bg-hijau-tua hover:bg-hijau-muda text-white text-center text-xs font-semibold rounded-xl transition-all shadow-md shadow-hijau-tua/20 hover:-translate-y-0.5">
                                    <i class="fas fa-edit"></i> Presensi
                                </a>
                            </div>
                            
                            @if($isOpen)
                                <form method="POST" action="{{ route('ustadz.presensi.toggle') }}">
                                    @csrf
                                    <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
                                    <button type="submit" onclick="return confirm('Tutup presensi santri sekarang?')"
                                            class="w-full py-2.5 text-xs font-semibold rounded-xl transition-all border flex items-center justify-center gap-1.5 bg-red-50 border-red-200 text-red-600 hover:bg-red-100">
                                        <i class="fas fa-lock"></i> Tutup Presensi Santri
                                    </button>
                                </form>
                            @else
                                <button type="button" onclick="openModalBukaAbsen('{{ $jadwal->id }}', '{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}', '{{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}')"
                                        class="w-full py-2.5 text-xs font-semibold rounded-xl transition-all border flex items-center justify-center gap-1.5 bg-blue-50 border-blue-200 text-blue-600 hover:bg-blue-100">
                                    <i class="fas fa-lock-open"></i> Buka Presensi Santri
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 p-12 text-center text-gray-400">
                        <i class="fas fa-calendar-alt text-5xl mb-4 block text-gray-300"></i>
                        <p class="text-base font-semibold text-gray-500">Belum ada jadwal mengajar.</p>
                        <p class="text-sm mt-1">Jadwal yang ditugaskan kepada Anda akan muncul di sini.</p>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- MOBILE VIEW --}}
        <div class="md:hidden space-y-2.5 relative">
            @forelse($jadwals as $jadwal)
                @php
                    $hariMap = ['Sunday'=>'Ahad','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
                    $hariIni = $hariMap[now()->format('l')] ?? '';
                    $isToday = $jadwal->hari === $hariIni;
                    $presensiToday = $jadwal->presensis->first();
                    $isOpen = $presensiToday && $presensiToday->status === 'dibuka';
                @endphp
                <div x-data="{ open: false }" class="bg-white/80 backdrop-blur-sm rounded-2xl border {{ $isToday ? 'border-hijau-tua ring-2 ring-hijau-tua/20 shadow-md' : 'border-gray-100 shadow-sm hover:shadow-md' }} transition-all relative p-3" :class="open ? 'z-50' : 'z-0'">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                             <div class="w-11 h-11 rounded-full bg-green-50 flex items-center justify-center flex-shrink-0 text-hijau-tua border border-green-100">
                                 <i class="fas fa-mosque"></i>
                             </div>
                             <div>
                                 <h4 class="text-sm font-bold text-gray-800">{{ $jadwal->majelis->nama_majelis ?? 'Majelis Tidak Diketahui' }}</h4>
                                 <p class="text-[11px] text-gray-500 mt-0.5"><i class="fas fa-book-open mr-1 text-gray-400"></i> {{ $jadwal->kitab->nama_kitab ?? '-' }}</p>
                                 <p class="text-[11px] text-gray-500 mt-0.5"><i class="fas fa-clock mr-1 text-gray-400"></i> {{ substr($jadwal->jam_mulai,0,5) }} - {{ substr($jadwal->jam_selesai,0,5) }}</p>
                             </div>
                        </div>
                        
                        <div class="flex flex-col items-end gap-2 flex-shrink-0">
                             <span class="px-2.5 py-0.5 flex items-center gap-1 {{ $isToday ? 'bg-orange-500 animate-pulse' : 'bg-hijau-tua' }} text-white text-[10px] font-bold rounded-full uppercase tracking-wider shadow-sm">
                                @if($isToday) <span class="w-1.5 h-1.5 bg-white rounded-full"></span> @endif
                                {{ $jadwal->hari }}{{ $isToday ? ' (Hari Ini)' : '' }}
                             </span>
                             
                             <div class="relative">
                                 <button @click="open = !open" @click.away="open = false" class="w-7 h-7 flex items-center justify-center rounded-lg bg-gray-100 text-gray-500 hover:bg-gray-200 transition-colors">
                                     <i class="fas fa-ellipsis-v text-xs"></i>
                                 </button>
                                 <div x-show="open" x-transition.opacity class="absolute right-0 top-full mt-1 w-36 bg-white rounded-xl shadow-lg border border-gray-100 py-1.5" style="display:none; z-index: 100;">
                                     <a href="{{ route('ustadz.jadwal.show', $jadwal->id) }}" class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 hover:text-hijau-tua">
                                         <i class="fas fa-folder-open w-3 text-gray-400"></i> Detail
                                     </a>
                                     <a href="{{ route('ustadz.presensi.create', ['jadwal_id' => $jadwal->id]) }}" class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 hover:text-hijau-tua">
                                         <i class="fas fa-edit w-3 text-hijau-tua"></i> Isi Presensi
                                     </a>
                                 </div>
                             </div>
                        </div>
                    </div>
                    
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        @if($isOpen)
                            <div class="flex items-center justify-between">
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-bold bg-green-100 text-green-700 px-2.5 py-1 rounded-full border border-green-200">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span> Absen Dibuka
                                </span>
                                <form method="POST" action="{{ route('ustadz.presensi.toggle') }}" class="m-0">
                                    @csrf
                                    <input type="hidden" name="jadwal_id" value="{{ $jadwal->id }}">
                                    <button type="submit" onclick="return confirm('Tutup presensi santri sekarang?')" class="text-[10px] font-semibold text-red-600 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg border border-red-100 transition-colors flex items-center gap-1">
                                        <i class="fas fa-lock"></i> Tutup
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="flex items-center justify-between">
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-bold bg-gray-100 text-gray-500 px-2.5 py-1 rounded-full border border-gray-200">
                                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span> Absen Ditutup
                                </span>
                                <button type="button" onclick="openModalBukaAbsen('{{ $jadwal->id }}', '{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}', '{{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}')" class="text-[10px] font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg border border-blue-100 transition-colors flex items-center gap-1">
                                    <i class="fas fa-lock-open"></i> Buka Santri
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-10 bg-white rounded-xl border border-gray-100">
                    <i class="fas fa-calendar-alt text-4xl text-gray-200 mb-2 block"></i>
                    <p class="text-xs text-gray-500">Belum ada jadwal mengajar.</p>
                </div>
            @endforelse
        </div>
    </div>

    @push('modals')
    <!-- Modal Buka Presensi -->
    <div id="modalBukaAbsen" class="fixed inset-0 hidden bg-gray-900/60 backdrop-blur-sm items-center justify-center p-4 transition-all" style="z-index: 9999;">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="modalBukaAbsenContent">
            
            {{-- Modal Header --}}
            <div class="relative bg-gradient-to-br from-hijau-tua to-emerald-700 p-6 text-white overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                <div class="relative z-10 flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner">
                            <i class="fas fa-fingerprint text-2xl text-emerald-100"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold leading-tight">Buka Presensi Santri</h3>
                            <p class="text-emerald-100 text-xs mt-1">Tentukan batas waktu presensi mandiri.</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeModalBukaAbsen()" class="w-8 h-8 flex items-center justify-center rounded-full bg-black/10 hover:bg-black/20 text-white transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <form action="{{ route('ustadz.presensi.toggle') }}" method="POST">
                @csrf
                <input type="hidden" name="jadwal_id" id="buka_jadwal_id">
                
                <div class="p-6 space-y-5">
                    
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex items-start gap-3">
                        <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                        <p class="text-[11px] text-blue-700 leading-relaxed">
                            Secara default, jam di bawah disi sesuai dengan jam majelis Anda. Anda bebas merubahnya untuk memberi kelonggaran waktu bagi santri.
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
</x-app-layout>
