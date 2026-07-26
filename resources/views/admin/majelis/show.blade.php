<x-app-layout>
    <x-slot name="header">Detail Majelis</x-slot>

    <div class="space-y-6">
        <x-alert />

        {{-- Back --}}
        <div>
            <a href="{{ route('admin.majelis.index') }}"
               class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-hijau-tua transition-colors font-medium">
                <i class="fas fa-arrow-left"></i> Kembali ke Data Majelis
            </a>
        </div>

        {{-- Header Card (TAMPILAN HP) --}}
        <div class="relative overflow-hidden rounded-2xl shadow-xl text-white p-6 mb-8 lg:hidden" style="background: linear-gradient(135deg, #1a4731 0%, #166534 40%, #15803d 70%, #16a34a 100%);">
            <div class="absolute -top-8 -right-8 w-48 h-48 rounded-full opacity-10" style="background: radial-gradient(circle, #bbf7d0, transparent 70%)"></div>
         <div class="absolute bottom-0 left-1/3 w-32 h-32 rounded-full opacity-10" style="background: radial-gradient(circle, #6ee7b7, transparent 70%)"></div>
         <div class="relative flex flex-col gap-4 z-10">
                <div class="flex items-start gap-4">
                    {{-- Icon --}}
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-3xl shrink-0" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.25);"><i class="fas fa-mosque text-white"></i></div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-emerald-200 text-xs font-medium tracking-wide uppercase mb-0.5">MAJELIS</p>
                        <h2 class="text-2xl font-extrabold text-white leading-tight mb-1.5">{{ $majelis->nama_majelis }}</h2>
                        
                        <div class="flex flex-wrap items-center gap-y-1.5 gap-x-3 mt-1.5 text-xs text-white/80">
                            @if($majelis->tempat)
                            <span class="flex items-center gap-1 shrink-0">
                                <i class="fas fa-map-marker-alt text-[10px]"></i>
                                {{ $majelis->tempat }}
                            </span>
                            @endif
                            @if($majelis->koordinator)
                            <span class="flex items-center gap-1 shrink-0">
                                <i class="fas fa-user-tie text-[10px]"></i>
                                {{ $majelis->koordinator }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Stats / Bottom --}}
                <div class="flex items-center justify-between border-t border-white/20 pt-3 mt-1">
                    <span class="text-[11px] font-medium text-emerald-100">Total Santri Terdaftar</span>
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/20 rounded-lg">
                        <i class="fas fa-users text-[10px]"></i>
                        <span class="text-sm font-bold leading-none">{{ $majelis->santris->count() }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Header Card (TAMPILAN DESKTOP) --}}
        <div class="relative overflow-hidden rounded-2xl shadow-xl text-white hidden lg:block mb-8"
             style="background: linear-gradient(135deg, #1a4731 0%, #166534 40%, #15803d 70%, #16a34a 100%);">
            <div class="absolute -top-8 -right-8 w-48 h-48 rounded-full opacity-10"
                 style="background: radial-gradient(circle, #bbf7d0, transparent 70%)"></div>
            <div class="absolute bottom-0 left-1/3 w-32 h-32 rounded-full opacity-10"
                 style="background: radial-gradient(circle, #6ee7b7, transparent 70%)"></div>
            <div class="absolute top-2 left-1/2 w-24 h-24 rounded-full opacity-5"
                 style="background: radial-gradient(circle, #ffffff, transparent 70%)"></div>
                 
            <div class="relative p-6">
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    {{-- Icon --}}
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center shrink-0"
                         style="background: rgba(255,255,255,0.15); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.25);">
                        <i class="fas fa-mosque text-3xl text-white"></i>
                    </div>

                    {{-- Info --}}
                    <div class="flex-1">
                        <p class="text-emerald-200 text-xs font-medium tracking-wide uppercase mb-0.5">MAJELIS</p>
                        <h2 class="text-2xl font-extrabold text-white leading-tight">{{ $majelis->nama_majelis }}</h2>
                        
                        <div class="flex flex-wrap items-center gap-2 mt-1.5">
                            @if($majelis->tempat)
                            <span class="inline-flex items-center gap-1 text-xs text-white/80">
                                <i class="fas fa-map-marker-alt text-emerald-300 text-[10px]"></i>
                                {{ $majelis->tempat }}
                            </span>
                            @endif
                            
                            @if($majelis->koordinator)
                            <span class="text-white/30 text-xs">·</span>
                            <span class="inline-flex items-center gap-1 text-xs text-white/80">
                                <i class="fas fa-user-tie text-emerald-300 text-[10px]"></i>
                                {{ $majelis->koordinator }}
                            </span>
                            @endif
                            
                            @if($majelis->keterangan)
                            <span class="text-white/30 text-xs">·</span>
                            <span class="inline-flex items-center gap-1 text-xs text-white/80">
                                <i class="fas fa-info-circle text-emerald-300 text-[10px]"></i>
                                {{ $majelis->keterangan }}
                            </span>
                            @endif
                        </div>
                    </div>

                    {{-- Stats --}}
                    <div class="flex sm:flex-col items-center sm:items-end gap-3 sm:gap-2 shrink-0">
                        <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full"
                             style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2);">
                            <i class="fas fa-users text-emerald-300 text-xs"></i>
                            <span class="text-white text-xs font-bold">{{ $majelis->santris->count() }} Santri</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action --}}
        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('admin.majelis.edit', $majelis) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50 transition-colors shadow-sm">
                <i class="fas fa-edit text-biru"></i> Edit Majelis
            </a>
        </div>

        {{-- Daftar Santri --}}
        <div class="bg-white/60 backdrop-blur-xl rounded-2xl border border-white/50 shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
            <div class="flex items-center justify-between p-5 border-b border-gray-100 rounded-t-2xl">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-hijau-tua/10 rounded-xl flex items-center justify-center text-hijau-tua">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-800">Daftar Santri</h3>
                        <p class="text-xs text-gray-500">Terdaftar di majelis {{ $majelis->nama_majelis }}</p>
                    </div>
                </div>
                <span class="text-xs font-semibold bg-hijau-tua/10 text-hijau-tua px-3 py-1.5 rounded-full">
                    {{ $majelis->santris->count() }} Santri
                </span>
            </div>

            @if($majelis->santris->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-gray-400">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                    <i class="fas fa-user-slash text-2xl text-gray-300"></i>
                </div>
                <p class="font-medium">Belum ada santri di majelis ini.</p>
                <p class="text-xs mt-1 text-gray-300">Tambah santri melalui menu Data Santri.</p>
                <a href="{{ route('admin.santri.create') }}"
                   class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-hijau-tua text-white text-sm rounded-xl hover:bg-hijau-muda transition-colors">
                    <i class="fas fa-plus"></i> Tambah Santri
                </a>
            </div>
            @else
            <div class="overflow-x-auto hidden md:block">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50/60">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Nama Santri</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">NIS</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Angkatan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Nama Wali</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">No. HP Wali</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($majelis->santris->sortBy(fn($s) => $s->user->name ?? '') as $santri)
                        <tr class="hover:bg-hijau-tua/5 transition-colors">
                            <td class="px-4 py-3 text-gray-400 text-xs">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-hijau-tua/10 flex items-center justify-center text-hijau-tua font-bold text-xs shrink-0">
                                        {{ strtoupper(substr($santri->user->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $santri->user->name ?? '-' }}</p>
                                        <p class="text-xs text-gray-400">{{ $santri->user->email ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-mono text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded">
                                    {{ $santri->nis ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $santri->angkatan ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $santri->nama_wali ?? '-' }}</td>
                            <td class="px-4 py-3">
                                @if($santri->no_hp_wali)
                                <span class="text-gray-600 text-xs">{{ $santri->no_hp_wali }}</span>
                                @else
                                <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.santri.show', $santri) }}"
                                   class="inline-flex items-center gap-1 text-xs text-hijau-tua hover:underline font-medium">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile List View --}}
            <div class="md:hidden space-y-2.5 relative p-3">
                @foreach($majelis->santris->sortBy(fn($s) => $s->user->name ?? '') as $santri)
                <div x-data="{ open: false }" 
                     class="bg-white/80 backdrop-blur-sm rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all relative"
                     :class="open ? 'z-50' : 'z-0'">

                    <div class="flex items-center gap-3 p-3">
                        {{-- Avatar --}}
                        <img src="{{ isset($santri->user) && $santri->user->foto_profil ? asset('storage/'.$santri->user->foto_profil) : 'https://ui-avatars.com/api/?name='.urlencode($santri->user->name ?? '?').'&color=14532D&background=F1F5F9' }}"
                             class="w-11 h-11 rounded-full object-cover border-2 border-white shadow-sm flex-shrink-0" alt="">

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-gray-800 truncate" title="{{ $santri->user->name ?? '-' }}">
                                {{ $santri->user->name ?? '-' }}
                            </h4>
                            <div class="text-[11px] text-gray-500 mt-0.5 flex items-center gap-1 flex-wrap">
                                <span class="font-mono">{{ $santri->nis ?? '-' }}</span>
                                <span class="text-gray-300">&bull;</span>
                                <span class="truncate">Angk: {{ $santri->angkatan ?? '-' }}</span>
                            </div>
                        </div>

                        {{-- Status + Action --}}
                        <div class="flex flex-col items-end gap-2 flex-shrink-0">
                            @if(isset($santri->user) && $santri->user->status_aktif)
                                <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-green-100 text-green-800 border border-green-200">Aktif</span>
                            @else
                                <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-gray-100 text-gray-600 border border-gray-200">Non-Aktif</span>
                            @endif

                            {{-- Dropdown Aksi --}}
                            <div class="relative">
                                <button @click="open = !open" @click.away="open = false"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg bg-gray-100 text-gray-500 hover:bg-gray-200 transition-colors">
                                    <i class="fas fa-ellipsis-v text-xs"></i>
                                </button>
                                <div x-show="open" x-transition.opacity
                                     class="absolute right-0 top-full mt-1 w-32 bg-white rounded-xl shadow-lg border border-gray-100 py-1.5"
                                     style="display:none; z-index: 100;">
                                    <a href="{{ route('admin.santri.show', $santri) }}"
                                       class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 hover:text-hijau-tua">
                                        <i class="fas fa-eye w-3"></i> Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

    </div>
</x-app-layout>
