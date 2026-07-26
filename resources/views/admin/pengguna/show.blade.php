<x-app-layout>
    <x-slot name="header">Detail Pengguna</x-slot>

    <div class="max-w-2xl mx-auto space-y-4">
        <x-alert />

        <div class="bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 overflow-hidden">
            {{-- Header Profil --}}
            <div class="relative h-28 bg-gradient-to-r from-hijau-tua to-hijau-muda">
                <div class="absolute -bottom-12 left-6">
                    <img src="{{ $pengguna->foto_profil ? asset('storage/'.$pengguna->foto_profil) : 'https://ui-avatars.com/api/?name='.urlencode($pengguna->name).'&color=14532D&background=F1F5F9&size=128' }}"
                         class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md" alt="">
                </div>
            </div>
            <div class="pt-16 px-6 pb-6">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">{{ $pengguna->name }}</h2>
                        <p class="text-sm text-gray-500 font-mono">{{ '@' . $pengguna->username }}</p>
                    </div>
                    <div class="flex gap-2">
                        @php
                            $roleColor = match($pengguna->role) {
                                'admin'  => 'bg-purple-100 text-purple-700',
                                'ustadz' => 'bg-blue-100 text-blue-700',
                                'santri' => 'bg-green-100 text-green-700',
                                default  => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <span class="px-3 py-1 text-xs font-bold rounded-full {{ $roleColor }}">{{ ucfirst($pengguna->role) }}</span>
                        @if($pengguna->status_aktif)
                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-700">Aktif</span>
                        @else
                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-red-100 text-red-700">Non-Aktif</span>
                        @endif
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-gray-50/60 rounded-xl p-4">
                        <p class="text-xs text-gray-400 mb-1"><i class="fas fa-envelope mr-1"></i> Email</p>
                        <p class="text-sm font-medium text-gray-700">{{ $pengguna->email ?? '-' }}</p>
                    </div>
                    <div class="bg-gray-50/60 rounded-xl p-4">
                        <p class="text-xs text-gray-400 mb-1"><i class="fas fa-phone mr-1"></i> No. HP</p>
                        <p class="text-sm font-medium text-gray-700">{{ $pengguna->no_hp ?? '-' }}</p>
                    </div>
                    <div class="bg-gray-50/60 rounded-xl p-4">
                        <p class="text-xs text-gray-400 mb-1"><i class="fas fa-venus-mars mr-1"></i> Jenis Kelamin</p>
                        <p class="text-sm font-medium text-gray-700">{{ $pengguna->jenis_kelamin === 'L' ? 'Laki-laki' : ($pengguna->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}</p>
                    </div>
                    <div class="bg-gray-50/60 rounded-xl p-4">
                        <p class="text-xs text-gray-400 mb-1"><i class="fas fa-calendar mr-1"></i> Bergabung</p>
                        <p class="text-sm font-medium text-gray-700">{{ $pengguna->created_at->translatedFormat('d F Y') }}</p>
                    </div>
                    <div class="bg-gray-50/60 rounded-xl p-4 sm:col-span-2">
                        <p class="text-xs text-gray-400 mb-1"><i class="fas fa-map-marker-alt mr-1"></i> Alamat</p>
                        <p class="text-sm font-medium text-gray-700">{{ $pengguna->alamat ?? '-' }}</p>
                    </div>
                </div>

                <div class="flex justify-between items-center mt-6 pt-4 border-t border-gray-100">
                    <a href="{{ route('admin.pengguna.index') }}"
                       class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.pengguna.edit', $pengguna) }}"
                           class="inline-flex items-center gap-2 bg-biru text-white px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition-colors">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        @if($pengguna->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.pengguna.destroy', $pengguna) }}"
                              onsubmit="return confirm('Yakin hapus pengguna {{ $pengguna->name }}?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center gap-2 bg-merah text-white px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition-colors">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
