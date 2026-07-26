<x-app-layout>
    <x-slot name="header">Pengaturan Sistem</x-slot>

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
                    <i class="fas fa-sliders-h text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white">Pengaturan Sistem</h1>
                    <p class="text-emerald-200 text-xs mt-0.5">Informasi konfigurasi sistem absensi</p>
                </div>
            </div>
        </div>

        {{-- Info Card --}}
        <div class="bg-white/70 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.05)] border border-white/60 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-hijau-tua/10 flex items-center justify-center text-hijau-tua">
                    <i class="fas fa-clock text-sm"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-700">Waktu Absen Mandiri</h2>
                    <p class="text-[10px] text-gray-400">Diatur langsung oleh Ustadz saat membuka sesi presensi</p>
                </div>
            </div>

            <div class="p-6 space-y-4">
                <div class="flex items-start gap-3 p-4 rounded-xl bg-emerald-50 border border-emerald-100">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-info-circle text-emerald-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-emerald-800">Cara Kerja Waktu Absen</p>
                        <p class="text-[11px] text-emerald-700 mt-1 leading-relaxed">
                            Waktu absen santri ditentukan langsung oleh <strong>Ustadz</strong> saat membuka sesi presensi pada tiap majelis.
                            Ustadz mengisi <strong>jam buka</strong> dan <strong>jam tutup</strong> absen secara manual sehingga lebih fleksibel dan akurat sesuai kondisi majelis.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex items-center gap-3 p-4 rounded-xl bg-amber-50 border border-amber-100">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-door-open text-amber-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-semibold text-amber-700 uppercase tracking-wide">Jam Buka</p>
                            <p class="text-xs text-amber-800 mt-0.5">Diisi ustadz saat buka presensi</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-4 rounded-xl bg-blue-50 border border-blue-100">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-door-closed text-blue-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-semibold text-blue-700 uppercase tracking-wide">Jam Tutup</p>
                            <p class="text-xs text-blue-800 mt-0.5">Diisi ustadz saat tutup presensi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
