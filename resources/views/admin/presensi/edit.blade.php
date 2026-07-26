<x-app-layout>
    <x-slot name="header">Edit Presensi</x-slot>

    <div class="max-w-3xl mx-auto space-y-5">
        <x-alert />

        {{-- Info Sesi --}}
        <div class="bg-white/70 backdrop-blur-xl rounded-2xl border border-white/50 shadow-sm p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                        {{-- Panah kembali hanya di HP --}}
                        <a href="{{ route('admin.presensi.show', $presensi) }}"
                           class="md:hidden text-gray-400 hover:text-hijau-tua transition-colors">
                            <i class="fas fa-arrow-left text-sm"></i>
                        </a>
                        Edit Kehadiran Santri
                    </h2>
                    <p class="text-sm text-gray-500 mt-0.5">Status saat ini ditampilkan — ubah yang perlu saja, lalu simpan.</p>
                </div>
                {{-- Tombol Kembali hanya di Desktop --}}
                <a href="{{ route('admin.presensi.show', $presensi) }}"
                   class="hidden md:inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-hijau-tua transition-colors font-medium shrink-0">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-4 text-sm">
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
        </div>

        {{-- Form Edit Kehadiran --}}
        <form method="POST" action="{{ route('admin.presensi.update', $presensi) }}">
            @csrf @method('PUT')

            <div class="bg-white/60 backdrop-blur-xl rounded-2xl border border-white/50 shadow-sm overflow-hidden">

                {{-- Header --}}
                <div class="px-5 py-3 bg-gray-50/80 border-b border-gray-100 flex flex-wrap items-center justify-between gap-2">
                    <span class="text-sm font-semibold text-gray-700">
                        <i class="fas fa-users mr-1.5 text-green-600"></i>
                        Daftar Santri ({{ $santris->count() }} orang)
                    </span>
                    <div class="flex gap-2">
                        <button type="button" onclick="setAll('Hadir')"
                                class="w-28 text-xs px-3 py-1 bg-green-100 text-green-700 rounded-lg font-medium hover:bg-green-200 transition-colors text-center">
                            <i class="fas fa-check mr-1"></i>Semua Hadir
                        </button>
                        <button type="button" onclick="setAll('Alpha')"
                                class="w-28 text-xs px-3 py-1 bg-red-100 text-red-700 rounded-lg font-medium hover:bg-red-200 transition-colors text-center">
                            <i class="fas fa-times mr-1"></i>Semua Alpa
                        </button>
                    </div>
                </div>

                {{-- Daftar santri --}}
                <div class="sm:divide-y sm:divide-gray-100 p-3 sm:p-0 space-y-2.5 sm:space-y-0">
                    @forelse($santris as $i => $santri)
                    @php
                        $detail        = $presensi->details->firstWhere('santri_id', $santri->id);
                        $currentStatus = $detail?->status_kehadiran ?? null;
                        $currentKet    = $detail?->keterangan ?? '';
                    @endphp
                    <div class="bg-white sm:bg-transparent rounded-2xl sm:rounded-none border border-gray-100 sm:border-x-0 sm:border-t-0 sm:border-b shadow-sm sm:shadow-none px-4 sm:px-5 py-3.5 flex flex-col sm:flex-row sm:items-center gap-3 hover:bg-gray-50/40 transition-colors">

                        {{-- Nomor + Avatar + Nama --}}
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            {{-- Nomor di desktop: in-flow --}}
                            <span class="hidden sm:block text-xs text-gray-400 w-5 text-center shrink-0">{{ $i + 1 }}</span>
                            <img src="{{ $santri->user->foto_profil
                                ? asset('storage/'.$santri->user->foto_profil)
                                : 'https://ui-avatars.com/api/?name='.urlencode($santri->user->name).'&size=40&color=14532D&background=F1F5F9' }}"
                                 class="w-9 h-9 rounded-full object-cover shrink-0">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $santri->user->name }}</p>
                                <p class="text-xs text-gray-400">{{ $santri->nis ?? 'No NIS' }}</p>
                            </div>
                        </div>

                        {{-- Radio Status --}}
                        <input type="hidden" name="kehadiran[{{ $santri->id }}]" value="{{ $currentStatus ?? '' }}" id="hidden_{{ $santri->id }}">

                        <div class="grid grid-cols-4 sm:flex gap-1.5 shrink-0" role="group">
                            @foreach([
                                'Hadir' => ['#16a34a', 'bg-green-600 text-white border-green-600 shadow-sm shadow-green-200', 'bg-white text-green-700 border-green-300 hover:bg-green-50'],
                                'Izin'  => ['#2563eb', 'bg-blue-600 text-white border-blue-600 shadow-sm shadow-blue-200',  'bg-white text-blue-700 border-blue-300 hover:bg-blue-50'],
                                'Sakit' => ['#d97706', 'bg-amber-500 text-white border-amber-500 shadow-sm shadow-amber-200','bg-white text-amber-700 border-amber-300 hover:bg-amber-50'],
                                'Alpha' => ['#dc2626', 'bg-red-600 text-white border-red-600 shadow-sm shadow-red-200',   'bg-white text-red-700 border-red-300 hover:bg-red-50'],
                            ] as $status => [$color, $activeClass, $inactiveClass])
                            <button type="button"
                                    onclick="setStatus({{ $santri->id }}, '{{ $status }}')"
                                    id="btn_{{ $santri->id }}_{{ $status }}"
                                    class="status-btn text-xs font-semibold px-2 py-1.5 sm:px-3 rounded-lg border transition-all duration-150
                                           {{ $currentStatus === $status ? $activeClass : $inactiveClass }}">
                                {{ $status }}
                            </button>
                            @endforeach
                        </div>

                        {{-- Keterangan --}}
                        <div class="w-full sm:w-40 shrink-0">
                            <input type="text"
                                   name="keterangan[{{ $santri->id }}]"
                                   value="{{ old('keterangan.'.$santri->id, $currentKet) }}"
                                   placeholder="Keterangan..."
                                   class="w-full text-xs border border-gray-200 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-hijau-tua focus:border-transparent text-gray-600 bg-white">
                        </div>
                    </div>
                    @empty
                    <div class="px-5 py-12 text-center">
                        <i class="fas fa-users text-3xl text-gray-200 mb-2 block"></i>
                        <p class="text-sm text-gray-400">Tidak ada santri di majelis ini.</p>
                    </div>
                    @endforelse
                </div>

                {{-- Footer --}}
                <div class="px-5 py-4 bg-gray-50/80 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3">
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 bg-hijau-tua text-white px-6 py-2.5 rounded-xl text-sm font-semibold hover:bg-hijau-muda transition-colors shadow">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.presensi.show', $presensi) }}"
                       class="inline-flex items-center justify-center border border-gray-200 text-gray-600 px-6 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        // Definisi warna per status
        const statusStyles = {
            'Hadir': { active: 'bg-green-600 text-white border-green-600 shadow-sm shadow-green-200', inactive: 'bg-white text-green-700 border-green-300 hover:bg-green-50' },
            'Izin':  { active: 'bg-blue-600 text-white border-blue-600 shadow-sm shadow-blue-200',   inactive: 'bg-white text-blue-700 border-blue-300 hover:bg-blue-50' },
            'Sakit': { active: 'bg-amber-500 text-white border-amber-500 shadow-sm shadow-amber-200', inactive: 'bg-white text-amber-700 border-amber-300 hover:bg-amber-50' },
            'Alpha': { active: 'bg-red-600 text-white border-red-600 shadow-sm shadow-red-200',       inactive: 'bg-white text-red-700 border-red-300 hover:bg-red-50' },
        };
        const allStatuses = ['Hadir', 'Izin', 'Sakit', 'Alpha'];

        function setStatus(santriId, status) {
            // Update hidden input
            document.getElementById('hidden_' + santriId).value = status;

            // Update semua tombol status untuk santri ini
            allStatuses.forEach(s => {
                const btn = document.getElementById('btn_' + santriId + '_' + s);
                if (!btn) return;
                // Hapus semua class lama
                const styles = statusStyles[s];
                [...styles.active.split(' '), ...styles.inactive.split(' ')].forEach(c => btn.classList.remove(c));
                // Tambahkan class baru
                const newStyles = (s === status) ? styles.active : styles.inactive;
                newStyles.split(' ').forEach(c => btn.classList.add(c));
            });
        }

        function setAll(status) {
            // Ambil semua hidden input kehadiran
            document.querySelectorAll('input[name^="kehadiran["]').forEach(input => {
                const santriId = input.name.match(/\[(\d+)\]/)[1];
                setStatus(santriId, status);
            });
        }
    </script>
    @endpush
</x-app-layout>
