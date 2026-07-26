<x-app-layout>
    <x-slot name="header">Detail Santri</x-slot>

    <div class="max-w-2xl mx-auto space-y-6">
        <div class="bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 p-8">
            <div class="flex items-center gap-6 mb-6">
                <img src="{{ $santri->user->foto_profil ? asset('storage/'.$santri->user->foto_profil) : 'https://ui-avatars.com/api/?name='.urlencode($santri->user->name).'&color=14532D&background=F1F5F9&size=100' }}"
                     class="w-20 h-20 rounded-full object-cover border-4 border-hijau-muda/30" alt="">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">{{ $santri->user->name }}</h2>
                    <span class="text-sm text-gray-500">NIS: {{ $santri->nis ?? '-' }}</span><br>
                    @if($santri->user->status_aktif)
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                    @else
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-700">Non-Aktif</span>
                    @endif
                </div>
            </div>

            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div><dt class="text-gray-500">Username</dt><dd class="font-medium text-gray-800">{{ $santri->user->username }}</dd></div>
                <div><dt class="text-gray-500">No. HP</dt><dd class="font-medium text-gray-800">{{ $santri->user->no_hp ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Jenis Kelamin</dt><dd class="font-medium text-gray-800">{{ $santri->user->jenis_kelamin == 'L' ? 'Laki-laki' : ($santri->user->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</dd></div>
                <div><dt class="text-gray-500">Majelis</dt><dd class="font-medium text-gray-800">{{ $santri->majelis->nama_majelis ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Tempat Lahir</dt><dd class="font-medium text-gray-800">{{ $santri->tempat_lahir ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Tanggal Lahir</dt><dd class="font-medium text-gray-800">{{ $santri->tanggal_lahir ? \Carbon\Carbon::parse($santri->tanggal_lahir)->translatedFormat('d M Y') : '-' }}</dd></div>
                <div><dt class="text-gray-500">Nama Wali</dt><dd class="font-medium text-gray-800">{{ $santri->nama_wali ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">No. HP Wali</dt><dd class="font-medium text-gray-800">{{ $santri->no_hp_wali ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Angkatan</dt><dd class="font-medium text-gray-800">{{ $santri->angkatan ?? '-' }}</dd></div>
                <div class="col-span-2"><dt class="text-gray-500">Alamat</dt><dd class="font-medium text-gray-800">{{ $santri->user->alamat ?? '-' }}</dd></div>
            </dl>

            <div class="flex gap-3 mt-6 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.santri.edit', $santri) }}" class="bg-hijau-tua text-white px-5 py-2 rounded-lg text-sm hover:bg-hijau-muda transition-colors">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
                <a href="{{ route('admin.santri.index') }}" class="border border-gray-200 text-gray-600 px-5 py-2 rounded-lg text-sm hover:bg-gray-50 transition-colors">
                    Kembali
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
