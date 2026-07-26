<x-app-layout>
    <x-slot name="header">Detail Presensi</x-slot>

    <div class="max-w-3xl mx-auto space-y-5">
        <x-alert />

        <div class="bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 p-6">

            {{-- Header --}}
            <div class="flex items-start justify-between gap-4 mb-5">
                <div>
                    <h2 class="text-base font-semibold text-gray-800">Detail Sesi Presensi</h2>
                    <p class="text-xs text-gray-400 mt-0.5">
                        Dicatat: {{ $presensi->created_at?->translatedFormat('d M Y, H:i') ?? '-' }}
                    </p>
                </div>
            </div>

            {{-- Info Sesi --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm mb-5">
                <div class="bg-gray-50 rounded-xl p-3">
                    <span class="text-gray-500 text-xs block mb-0.5">Tanggal</span>
                    <span class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($presensi->tanggal)->translatedFormat('d M Y') }}</span>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <span class="text-gray-500 text-xs block mb-0.5">Majelis</span>
                    <span class="font-semibold text-gray-800">{{ $presensi->jadwal->majelis->nama_majelis ?? '-' }}</span>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <span class="text-gray-500 text-xs block mb-0.5">Kitab</span>
                    <span class="font-semibold text-gray-800">{{ $presensi->jadwal->kitab->nama_kitab ?? '-' }}</span>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <span class="text-gray-500 text-xs block mb-0.5">Ustadz</span>
                    <span class="font-semibold text-gray-800">{{ $presensi->jadwal->ustadz->user->name ?? '-' }}</span>
                </div>
            </div>

            {{-- Summary badges --}}
            <div class="flex flex-wrap gap-2 mb-5">
                @php
                    $hadir = $presensi->details->where('status_kehadiran','Hadir')->count();
                    $izin  = $presensi->details->where('status_kehadiran','Izin')->count();
                    $sakit = $presensi->details->where('status_kehadiran','Sakit')->count();
                    $alpa  = $presensi->details->where('status_kehadiran','Alpha')->count();
                    $total = $presensi->details->count();
                @endphp
                <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-800 font-semibold">
                    <i class="fas fa-check mr-1"></i>Hadir: {{ $hadir }}
                </span>
                <span class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-800 font-semibold">
                    <i class="fas fa-file-alt mr-1"></i>Izin: {{ $izin }}
                </span>
                <span class="px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 font-semibold">
                    <i class="fas fa-medkit mr-1"></i>Sakit: {{ $sakit }}
                </span>
                <span class="px-3 py-1 text-xs rounded-full bg-red-100 text-red-800 font-semibold">
                    <i class="fas fa-times mr-1"></i>Alpa: {{ $alpa }}
                </span>
                @if($total > 0)
                <span class="px-3 py-1 text-xs rounded-full bg-green-600 text-white font-semibold ml-auto">
                    {{ $total > 0 ? round($hadir / $total * 100) : 0 }}% Kehadiran
                </span>
                @endif
            </div>

            {{-- Desktop Table --}}
            <div class="hidden md:block rounded-xl overflow-hidden border border-gray-100">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">No</th>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Santri</th>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white/40">
                        @forelse($presensi->details as $i => $detail)
                        @php
                            $badgeMap = [
                                'Hadir' => 'bg-green-100 text-green-800',
                                'Izin'  => 'bg-blue-100 text-blue-800',
                                'Sakit' => 'bg-yellow-100 text-yellow-800',
                                'Alpha' => 'bg-red-100 text-red-800',
                            ];
                            $cls = $badgeMap[$detail->status_kehadiran] ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <tr class="hover:bg-gray-50/60">
                            <td class="px-4 py-3 text-sm text-gray-400">{{ $i + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $detail->santri->user->foto_profil
                                        ? asset('storage/'.$detail->santri->user->foto_profil)
                                        : 'https://ui-avatars.com/api/?name='.urlencode($detail->santri->user->name ?? 'S').'&size=32&color=14532D&background=F1F5F9' }}"
                                         class="w-7 h-7 rounded-full object-cover">
                                    <span class="text-sm font-medium text-gray-800">{{ $detail->santri->user->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-1 text-xs rounded-full font-semibold {{ $cls }}">
                                    {{ $detail->status_kehadiran }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $detail->keterangan ?: '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-gray-400">
                                <i class="fas fa-clipboard text-2xl mb-2 block text-gray-200"></i>
                                Belum ada detail presensi.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden rounded-xl border border-gray-100 overflow-hidden">
                @forelse($presensi->details as $i => $detail)
                @php
                    $badgeMap2 = [
                        'Hadir' => ['bg-green-100 text-green-800',  'fas fa-check-circle text-green-500'],
                        'Izin'  => ['bg-blue-100 text-blue-800',    'fas fa-file-alt text-blue-500'],
                        'Sakit' => ['bg-yellow-100 text-yellow-800','fas fa-heartbeat text-yellow-500'],
                        'Alpha' => ['bg-red-100 text-red-800',      'fas fa-times-circle text-red-500'],
                    ];
                    [$cls2, $icon2] = $badgeMap2[$detail->status_kehadiran] ?? ['bg-gray-100 text-gray-600', 'fas fa-question text-gray-400'];
                @endphp
                <div class="flex items-center gap-3 px-3 py-2.5 border-b border-gray-100 last:border-0 bg-white/60 hover:bg-gray-50/60 transition-colors">
                    {{-- Nomor --}}
                    <span class="text-[10px] text-gray-400 w-5 text-center flex-shrink-0">{{ $i + 1 }}</span>
                    {{-- Avatar --}}
                    <img src="{{ $detail->santri->user->foto_profil
                        ? asset('storage/'.$detail->santri->user->foto_profil)
                        : 'https://ui-avatars.com/api/?name='.urlencode($detail->santri->user->name ?? 'S').'&size=40&color=14532D&background=F1F5F9' }}"
                         class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                    {{-- Nama --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-800 truncate">{{ $detail->santri->user->name ?? '-' }}</p>
                        @if($detail->keterangan)
                        <p class="text-[10px] text-gray-400 truncate">{{ $detail->keterangan }}</p>
                        @endif
                    </div>
                    {{-- Status badge --}}
                    <span class="px-2 py-0.5 text-[10px] rounded-full font-bold {{ $cls2 }} flex-shrink-0 flex items-center gap-1">
                        <i class="{{ $icon2 }} text-[9px]"></i>
                        {{ $detail->status_kehadiran }}
                    </span>
                </div>
                @empty
                <div class="px-4 py-10 text-center text-gray-400">
                    <i class="fas fa-clipboard text-2xl mb-2 block text-gray-200"></i>
                    <p class="text-xs">Belum ada detail presensi.</p>
                </div>
                @endforelse
            </div>

            {{-- Footer actions Desktop --}}
            <div class="mt-5 hidden md:flex items-center gap-3">
                <a href="{{ route('admin.presensi.index') }}"
                   class="border border-gray-200 text-gray-600 px-5 py-2 rounded-lg text-sm hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
                <a href="{{ route('admin.presensi.edit', $presensi) }}"
                   class="inline-flex items-center gap-1.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold px-5 py-2 rounded-lg transition-colors">
                    <i class="fas fa-edit"></i> Edit Kehadiran
                </a>
                <form method="POST" action="{{ route('admin.presensi.destroy', $presensi) }}"
                      onsubmit="return confirm('Yakin hapus sesi presensi ini? Semua data kehadiran akan ikut terhapus.')"
                      class="ml-auto">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 text-red-500 hover:text-red-700 text-sm font-medium transition-colors border border-red-200 hover:border-red-300 px-4 py-2 rounded-lg hover:bg-red-50">
                        <i class="fas fa-trash"></i> Hapus Sesi
                    </button>
                </form>
            </div>

            {{-- Footer actions Mobile --}}
            <div class="mt-4 md:hidden grid grid-cols-2 gap-2">
                <a href="{{ route('admin.presensi.index') }}"
                   class="flex items-center justify-center gap-1.5 border border-gray-200 text-gray-600 py-2.5 rounded-xl text-sm hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left text-xs"></i> Kembali
                </a>
                <a href="{{ route('admin.presensi.edit', $presensi) }}"
                   class="flex items-center justify-center gap-1.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold py-2.5 rounded-xl transition-colors">
                    <i class="fas fa-edit text-xs"></i> Edit
                </a>
                <form method="POST" action="{{ route('admin.presensi.destroy', $presensi) }}"
                      onsubmit="return confirm('Yakin hapus sesi presensi ini?')"
                      class="col-span-2">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="w-full flex items-center justify-center gap-1.5 text-red-500 text-sm font-medium border border-red-200 py-2.5 rounded-xl hover:bg-red-50 transition-colors">
                        <i class="fas fa-trash text-xs"></i> Hapus Sesi Presensi
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
