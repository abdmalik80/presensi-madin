<x-app-layout>
    <x-slot name="header">Pengaturan Presensi Mandiri</x-slot>

    <div class="max-w-2xl mx-auto space-y-6">
        <x-alert />

        {{-- Page Header --}}
        <div class="relative overflow-hidden rounded-2xl shadow-lg"
             style="background: linear-gradient(135deg, #1a4731 0%, #166534 40%, #15803d 70%, #16a34a 100%);">
            <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full opacity-10"
                 style="background: radial-gradient(circle, #bbf7d0, transparent 70%)"></div>
            <div class="relative p-6 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center"
                     style="background: rgba(255,255,255,0.15); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.2);">
                    <i class="fas fa-clock text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white">Pengaturan Presensi Mandiri</h1>
                    <p class="text-emerald-200 text-xs mt-0.5">Kelola mode presensi untuk majelis Anda</p>
                </div>
            </div>
        </div>

        {{-- Info Waktu --}}
        <div class="flex items-start gap-3 p-4 rounded-xl bg-emerald-50/80 border border-emerald-100">
            <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-info-circle text-emerald-600 text-sm"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-emerald-800">Waktu Presensi Mandiri</p>
                <p class="text-[11px] text-emerald-700 mt-0.5 leading-relaxed">
                    Waktu buka dan tutup presensi ditentukan langsung saat Anda <strong>membuka sesi presensi</strong> pada tiap majelis.
                    Tidak perlu mengatur toleransi — cukup isi jam buka dan jam tutup sesuai kebutuhan majelis Anda.
                </p>
            </div>
        </div>

        {{-- Mode Presensi --}}
        @php $ustadz = auth()->user()->ustadz; @endphp
        <div class="bg-white/70 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.05)] border border-white/60 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-hijau-tua/10 flex items-center justify-center text-hijau-tua">
                    <i class="fas fa-toggle-on text-sm"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-700">Mode Presensi</h2>
                    <p class="text-[10px] text-gray-400">Pilih cara pencatatan kehadiran untuk majelis Anda</p>
                </div>
            </div>

            <form method="POST" action="{{ route('ustadz.setting.mode') }}" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- By Ustadz --}}
                    <label class="relative cursor-pointer group">
                        <input type="radio" name="mode_presensi" value="by_ustadz"
                               class="sr-only peer"
                               {{ ($ustadz && $ustadz->mode_presensi === 'by_ustadz') || !$ustadz ? 'checked' : '' }}>
                        <div class="flex flex-col items-center gap-3 p-5 rounded-xl border-2 border-gray-200 bg-white
                                    peer-checked:border-hijau-tua peer-checked:bg-hijau-tua/5 transition-all group-hover:border-gray-300">
                            <div class="w-10 h-10 rounded-xl bg-hijau-tua/10 flex items-center justify-center text-hijau-tua">
                                <i class="fas fa-chalkboard-teacher text-lg"></i>
                            </div>
                            <div class="text-center">
                                <p class="text-sm font-bold text-gray-700">Input oleh Ustadz</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">Ustadz mencatat kehadiran santri satu per satu</p>
                            </div>
                            <div class="w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:border-hijau-tua flex items-center justify-center">
                                <div class="w-2 h-2 rounded-full bg-hijau-tua opacity-0 peer-checked:opacity-100 transition"></div>
                            </div>
                        </div>
                    </label>

                    {{-- By Santri --}}
                    <label class="relative cursor-pointer group">
                        <input type="radio" name="mode_presensi" value="by_santri"
                               class="sr-only peer"
                               {{ ($ustadz && $ustadz->mode_presensi === 'by_santri') ? 'checked' : '' }}>
                        <div class="flex flex-col items-center gap-3 p-5 rounded-xl border-2 border-gray-200 bg-white
                                    peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all group-hover:border-gray-300">
                            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600">
                                <i class="fas fa-fingerprint text-lg"></i>
                            </div>
                            <div class="text-center">
                                <p class="text-sm font-bold text-gray-700">Presensi Mandiri Santri</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">Santri presensi sendiri dalam waktu yang ditentukan</p>
                            </div>
                            <div class="w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:border-blue-500 flex items-center justify-center">
                                <div class="w-2 h-2 rounded-full bg-blue-500 opacity-0 peer-checked:opacity-100 transition"></div>
                            </div>
                        </div>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                    <button type="submit"
                            class="px-6 py-2.5 rounded-xl text-white text-sm font-semibold transition hover:opacity-90 active:scale-95 flex items-center gap-2 shadow-lg"
                            style="background: linear-gradient(135deg, #22C55E, #14532D);">
                        <i class="fas fa-save"></i> Simpan Mode
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>
