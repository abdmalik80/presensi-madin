<x-app-layout>
    <x-slot name="header">Detail Kitab</x-slot>
<div class="max-w-2xl mx-auto space-y-5">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.kitab.index') }}" class="text-gray-400 hover:text-gray-600 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-800">Detail Kitab</h1>
            <p class="text-sm text-gray-500 mt-0.5">Informasi lengkap data kitab</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-start gap-4 mb-6">
            <div class="w-12 h-12 rounded-xl bg-hijau-tua/10 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-book-open text-hijau-tua text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $kitab->nama_kitab }}</h2>
                @if($kitab->bidang_ilmu)
                <span class="mt-1 inline-block px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">{{ $kitab->bidang_ilmu }}</span>
                @endif
            </div>
        </div>

        <div class="space-y-3 text-sm">
            <div class="bg-gray-50 rounded-xl p-4">
                <span class="text-gray-400 text-xs uppercase font-semibold tracking-wide block mb-1">Pengarang / Muallif</span>
                <span class="text-gray-800">{{ $kitab->pengarang ?: '-' }}</span>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <span class="text-gray-400 text-xs uppercase font-semibold tracking-wide block mb-1">Ditambahkan</span>
                <span class="font-semibold text-gray-800">{{ $kitab->created_at->translatedFormat('d M Y') }}</span>
            </div>
        </div>
    </div>

    {{-- Jadwal yang pakai kitab ini --}}
    @php $jadwals = $kitab->jadwals ?? collect(); @endphp
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800 text-sm">Digunakan di Jadwal</h3>
            <span class="text-xs bg-hijau-muda/20 text-hijau-tua font-semibold px-2 py-1 rounded-full">{{ $jadwals->count() }} jadwal</span>
        </div>
        @if($jadwals->count())

        {{-- Desktop Table (hidden on mobile) --}}
        <table class="hidden md:table min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Hari</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Jam</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Majelis</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Ustadz</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($jadwals as $jadwal)
                <tr class="hover:bg-gray-50/60">
                    <td class="px-4 py-2.5 text-sm font-medium text-gray-800">{{ $jadwal->hari }}</td>
                    <td class="px-4 py-2.5 text-sm text-gray-600">{{ $jadwal->jam_mulai }} – {{ $jadwal->jam_selesai }}</td>
                    <td class="px-4 py-2.5 text-sm text-gray-600">{{ $jadwal->majelis->nama_majelis ?? '-' }}</td>
                    <td class="px-4 py-2.5 text-sm text-gray-600">{{ $jadwal->ustadz->user->name ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Mobile Cards (hidden on desktop) --}}
        <div class="md:hidden divide-y divide-gray-100">
            @foreach($jadwals as $jadwal)
            <div class="px-4 py-3 flex items-start gap-3">
                {{-- Day badge --}}
                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-hijau-tua/10 flex flex-col items-center justify-center">
                    <span class="text-[9px] font-bold text-hijau-tua uppercase leading-none">
                        {{ substr($jadwal->hari, 0, 3) }}
                    </span>
                </div>
                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2 mb-0.5">
                        <p class="text-xs font-bold text-gray-800 truncate">
                            {{ $jadwal->majelis->nama_majelis ?? '-' }}
                        </p>
                        <span class="text-[10px] font-semibold text-hijau-tua bg-hijau-tua/10 px-2 py-0.5 rounded-full whitespace-nowrap flex-shrink-0">
                            {{ $jadwal->jam_mulai ? substr($jadwal->jam_mulai, 0, 5) : '-' }}
                            @if($jadwal->jam_selesai) – {{ substr($jadwal->jam_selesai, 0, 5) }} @endif
                        </span>
                    </div>
                    <p class="text-[11px] text-gray-500 flex items-center gap-1">
                        <i class="fas fa-chalkboard-teacher text-gray-400 text-[10px]"></i>
                        {{ $jadwal->ustadz->user->name ?? '-' }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>

        @else
        <div class="px-6 py-8 text-center text-gray-400 text-sm">
            <i class="fas fa-calendar-times text-3xl mb-2 block text-gray-200"></i>
            Kitab ini belum digunakan di jadwal manapun.
        </div>
        @endif
    </div>

    <div class="flex gap-3">
        <a href="{{ route('admin.kitab.edit', $kitab) }}"
           class="inline-flex items-center gap-2 bg-hijau-tua text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-hijau-muda transition-colors">
            <i class="fas fa-edit"></i> Edit
        </a>
        <a href="{{ route('admin.kitab.index') }}"
           class="border border-gray-200 text-gray-600 px-5 py-2 rounded-lg text-sm hover:bg-gray-50 transition-colors">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

</div>
</x-app-layout>
