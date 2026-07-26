import re

path = 'resources/views/santri/jadwal/show.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# The file currently has a mangled desktop banner and missing Hadir stat.
# Let's cleanly replace everything from <x-app-layout> down to the start of "Tab Nav"
# We'll reconstruct it perfectly.

replacement = r'''<x-app-layout>
    <x-slot name="header">Detail Kitab</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        {{-- Tombol Kembali --}}
        <div>
            <a href="{{ route('santri.jadwal.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-hijau-tua transition-colors font-medium">
                <i class="fas fa-arrow-left"></i> Kembali ke Jadwal
            </a>
        </div>

        {{-- ===== MOBILE VIEW BANNER ===== --}}
        <div class="lg:hidden mb-6">
            <div class="relative overflow-hidden rounded-2xl shadow-lg" style="background: linear-gradient(135deg, #1a4731 0%, #166534 40%, #15803d 70%, #16a34a 100%);">
                <div class="absolute -top-12 -right-12 w-40 h-40 rounded-full opacity-10" style="background: radial-gradient(circle, #bbf7d0, transparent 70%)"></div>
                <div class="absolute bottom-0 left-1/4 w-24 h-24 rounded-full opacity-10" style="background: radial-gradient(circle, #6ee7b7, transparent 70%)"></div>
                <div class="relative p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl shrink-0" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.25);">
                            <i class="fas fa-book-open text-white"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[10px] text-emerald-100 font-semibold uppercase tracking-wider mb-0.5 truncate">{{ ->hari }} &bull; {{ \Carbon\Carbon::parse(->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse(->jam_selesai)->format('H:i') }} WIB</p>
                            <h2 class="text-base font-bold mb-0.5 leading-tight truncate text-white">{{ ->kitab->nama_kitab ?? 'Kitab' }}</h2>
                            <div class="flex items-center gap-1.5 text-[10px] text-emerald-100">
                                <i class="fas fa-chalkboard-teacher text-[9px]"></i>
                                <span class="truncate">Ustadz {{ ->ustadz->user->name ?? '-' }}</span>
                            </div>
                        </div>
                        @if(->keterangan)
                        <div class="shrink-0">
                            <div class="inline-flex items-center justify-center w-8 h-8 bg-white/20 rounded-full" title="{{ ->keterangan }}">
                                <i class="fas fa-info-circle text-white text-sm"></i>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== DESKTOP VIEW BANNER ===== --}}
        <div class="hidden lg:block relative overflow-hidden rounded-2xl mb-6 shadow-xl text-white p-6" style="background: linear-gradient(135deg, #1a4731 0%, #166534 40%, #15803d 70%, #16a34a 100%);">
            <div class="absolute -top-8 -right-8 w-48 h-48 rounded-full opacity-10" style="background: radial-gradient(circle, #bbf7d0, transparent 70%)"></div>
            <div class="absolute bottom-0 left-1/3 w-32 h-32 rounded-full opacity-10" style="background: radial-gradient(circle, #6ee7b7, transparent 70%)"></div>
            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-3xl shrink-0" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.25);"><i class="fas fa-book-open text-white"></i></div>
                <div class="flex-1 min-w-0">
                    <p class="text-emerald-200 text-xs font-medium tracking-wide uppercase mb-0.5">{{ ->hari }} &bull; {{ \Carbon\Carbon::parse(->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse(->jam_selesai)->format('H:i') }} WIB</p>
                    <h2 class="text-2xl font-extrabold text-white leading-tight truncate">{{ ->kitab->nama_kitab ?? 'Kitab' }}</h2>
                    <div class="flex flex-wrap items-center gap-2 mt-1.5 text-xs text-white/80">
                        <i class="fas fa-chalkboard-teacher text-[9px]"></i>
                        <span class="truncate">Ustadz {{ ->ustadz->user->name ?? '-' }}</span>
                    </div>
                </div>
                @if(->keterangan)
                <div class="flex flex-col items-end gap-1.5 flex-shrink-0">
                    <div class="inline-flex items-center gap-1 px-2 py-1 bg-white/20 rounded-md max-w-[120px]">
                        <i class="fas fa-info-circle text-[10px] shrink-0"></i>
                        <span class="text-[9px] font-semibold truncate">{{ ->keterangan }}</span>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Statistik Kehadiran --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white/60 backdrop-blur-xl rounded-2xl p-4 border border-white/50 shadow-sm flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check text-green-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Hadir</p>
                    <p class="text-xl font-bold text-gray-800">{{ ['Hadir'] }}</p>
                </div>
            </div>
            
            <div class="bg-white/60 backdrop-blur-xl rounded-2xl p-4 border border-white/50 shadow-sm flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-info text-blue-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Izin</p>
                    <p class="text-xl font-bold text-gray-800">{{ ['Izin'] }}</p>
                </div>
            </div>

            <div class="bg-white/60 backdrop-blur-xl rounded-2xl p-4 border border-white/50 shadow-sm flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-procedures text-amber-600 text-sm"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Sakit</p>
                    <p class="text-xl font-bold text-gray-800">{{ ['Sakit'] }}</p>
                </div>
            </div>

            <div class="bg-white/60 backdrop-blur-xl rounded-2xl p-4 border border-white/50 shadow-sm flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-times text-red-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Alpa</p>
                    <p class="text-xl font-bold text-gray-800">{{ ['Alpha'] }}</p>
                </div>
            </div>
        </div>

        {{-- Tab Nav --}}'''

# Find everything before {{-- Tab Nav --}} and replace it.
match = re.search(r'\{\{-- Tab Nav --\}\}', content)
if match:
    new_content = replacement + '\n' + content[match.end():]
    with open(path, 'w', encoding='utf-8') as f:
        f.write(new_content)
    print("Fixed successfully.")
else:
    print("Could not find Tab Nav marker.")
