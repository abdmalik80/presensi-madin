<x-app-layout>
    <x-slot name="header">Detail Ustadz</x-slot>

    <div class="max-w-3xl mx-auto space-y-5">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.ustadz.index') }}" class="text-gray-400 hover:text-gray-600 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-800">Detail Ustadz</h1>
            <p class="text-sm text-gray-500 mt-0.5">Informasi lengkap data ustadz</p>
        </div>
    </div>

    {{-- Card Profil --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-5 mb-6">
            <img src="{{ $ustadz->user->foto_profil ? asset('storage/'.$ustadz->user->foto_profil) : 'https://ui-avatars.com/api/?name='.urlencode($ustadz->user->name).'&size=80&color=14532D&background=F1F5F9' }}"
                 class="w-20 h-20 rounded-full object-cover border-4 border-hijau-muda/20" alt="">
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $ustadz->user->name }}</h2>
                <p class="text-sm text-gray-500">{{ '@' . $ustadz->user->username }}</p>
                @if($ustadz->user->status_aktif)
                    <span class="mt-1 inline-block px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                @else
                    <span class="mt-1 inline-block px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-700">Non-Aktif</span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div class="bg-gray-50 rounded-xl p-4">
                <span class="text-gray-400 text-xs uppercase font-semibold tracking-wide block mb-1">Jenis Kelamin</span>
                <span class="font-semibold text-gray-800">
                    {{ $ustadz->user->jenis_kelamin === 'L' ? 'Laki-laki' : ($ustadz->user->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}
                </span>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <span class="text-gray-400 text-xs uppercase font-semibold tracking-wide block mb-1">No. HP</span>
                <span class="font-semibold text-gray-800">{{ $ustadz->user->no_hp ?? '-' }}</span>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 sm:col-span-2">
                <span class="text-gray-400 text-xs uppercase font-semibold tracking-wide block mb-1">Alamat</span>
                <span class="font-semibold text-gray-800">{{ $ustadz->user->alamat ?? '-' }}</span>
            </div>
            <div class="bg-gray-50 rounded-xl p-4">
                <span class="text-gray-400 text-xs uppercase font-semibold tracking-wide block mb-1">Bergabung</span>
                <span class="font-semibold text-gray-800">{{ $ustadz->created_at->translatedFormat('d M Y') }}</span>
            </div>
        </div>
    </div>

    {{-- Jadwal Mengajar --}}
    @php $jadwals = $ustadz->jadwals ?? collect(); @endphp
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800 text-sm">Jadwal Mengajar</h3>
        </div>
        @if($jadwals->count())
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Hari</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Jam</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Majelis</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Kitab</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($jadwals as $jadwal)
                <tr class="hover:bg-gray-50/60">
                    <td class="px-4 py-2.5 text-sm font-medium text-gray-800">{{ $jadwal->hari }}</td>
                    <td class="px-4 py-2.5 text-sm text-gray-600">{{ $jadwal->jam_mulai }} – {{ $jadwal->jam_selesai }}</td>
                    <td class="px-4 py-2.5 text-sm text-gray-600">{{ $jadwal->majelis->nama_majelis ?? '-' }}</td>
                    <td class="px-4 py-2.5 text-sm text-gray-600">{{ $jadwal->kitab->nama_kitab ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="px-6 py-8 text-center text-gray-400 text-sm">
            <i class="fas fa-calendar-times text-3xl mb-2 block text-gray-200"></i>
            Belum ada jadwal mengajar.
        </div>
        @endif
    </div>

    <div class="flex gap-3">
        <a href="{{ route('admin.ustadz.edit', $ustadz) }}"
           class="inline-flex items-center gap-2 bg-hijau-tua text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-hijau-muda transition-colors">
            <i class="fas fa-edit"></i> Edit
        </a>
        <a href="{{ route('admin.ustadz.index') }}"
           class="border border-gray-200 text-gray-600 px-5 py-2 rounded-lg text-sm hover:bg-gray-50 transition-colors">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

</div>
</x-app-layout>
