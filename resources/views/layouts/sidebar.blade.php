<style>
    /* Custom Scrollbar for Sidebar Menu */
    #sidebar-menu::-webkit-scrollbar {
        width: 6px;
    }
    
    /* Track: transparent so it blends perfectly with the green background */
    #sidebar-menu::-webkit-scrollbar-track {
        background: transparent; 
    }
    
    /* Thumb: transparent by default (hidden) */
    #sidebar-menu::-webkit-scrollbar-thumb {
        background-color: transparent;
        border-radius: 10px;
    }

    /* Thumb: visible when hovering over the menu area */
    #sidebar-menu:hover::-webkit-scrollbar-thumb {
        background-color: rgba(255, 255, 255, 0.3);
    }
    
    /* For Firefox */
    #sidebar-menu {
        scrollbar-width: thin;
        scrollbar-color: transparent transparent;
        transition: scrollbar-color 0.3s ease;
    }
    
    #sidebar-menu:hover {
        scrollbar-color: rgba(255, 255, 255, 0.3) transparent;
    }
</style>
<aside id="sidebar" class="flex flex-col w-64 border-r border-green-900" style="background: linear-gradient(160deg, #22C55E 0%, #14532D 100%); height: 100dvh; height: 100vh;">
    <div class="px-4 pb-6 shrink-0 border-b border-white/10" style="padding-top: 1.25rem;">
        <a href="#" class="flex flex-col items-center justify-center gap-0">
            <img src="{{ asset('images/logo-nq.png') }}" alt="Logo NQ" class="w-16 h-16 object-contain drop-shadow-lg mb-3">
            <span class="text-[10px] uppercase font-semibold text-white/80 tracking-wider text-center">Sistem Informasi Presensi</span>
            <span class="text-lg font-bold text-white tracking-wide leading-tight text-center mt-[-2px]">Nawwir Quluubana</span>
        </a>
    </div>

    <div id="sidebar-menu" class="flex flex-col justify-between flex-1 overflow-y-auto px-4 pt-6" style="padding-bottom: max(5rem, env(safe-area-inset-bottom, 2rem));">
        <nav>
            @php
                $role    = auth()->user()->role;
                $current = request()->route()->getName();
                $activeClass = 'flex items-center px-4 py-2 mt-2 text-white rounded-lg font-semibold border-l-4 border-white bg-white/20';
                $normalClass = 'flex items-center px-4 py-2 mt-2 text-white/80 transition-colors rounded-lg hover:bg-white/20 hover:text-white';
            @endphp
            
            <a class="{{ $current === 'dashboard' ? $activeClass : $normalClass }}" href="{{ route('dashboard') }}">
                <i class="fas fa-home w-5 h-5"></i>
                <span class="ml-3 text-sm font-medium whitespace-nowrap">Dashboard</span>
            </a>



            @if($role === 'admin')
                {{-- KELOLA DATA --}}
                <div class="text-xs uppercase text-white/50 font-semibold mb-2 mt-4 px-4">Kelola Data</div>
                <a class="{{ str_starts_with($current, 'admin.santri') ? $activeClass : $normalClass }}" href="{{ route('admin.santri.index') }}">
                    <i class="fas fa-users w-5 h-5"></i>
                    <span class="ml-3 text-sm font-medium whitespace-nowrap">Data Santri</span>
                </a>
                <a class="{{ str_starts_with($current, 'admin.ustadz') ? $activeClass : $normalClass }}" href="{{ route('admin.ustadz.index') }}">
                    <i class="fas fa-chalkboard-teacher w-5 h-5"></i>
                    <span class="ml-3 text-sm font-medium whitespace-nowrap">Data Ustadz</span>
                </a>
                <a class="{{ str_starts_with($current, 'admin.majelis') ? $activeClass : $normalClass }}" href="{{ route('admin.majelis.index') }}">
                    <i class="fas fa-mosque w-5 h-5"></i>
                    <span class="ml-3 text-sm font-medium whitespace-nowrap">Data Majelis</span>
                </a>
                <a class="{{ str_starts_with($current, 'admin.kitab') ? $activeClass : $normalClass }}" href="{{ route('admin.kitab.index') }}">
                    <i class="fas fa-book w-5 h-5"></i>
                    <span class="ml-3 text-sm font-medium whitespace-nowrap">Data Kitab</span>
                </a>
                <a class="{{ str_starts_with($current, 'admin.jadwal') ? $activeClass : $normalClass }}" href="{{ route('admin.jadwal.index') }}">
                    <i class="fas fa-calendar-alt w-5 h-5"></i>
                    <span class="ml-3 text-sm font-medium whitespace-nowrap">Jadwal Majelis</span>
                </a>
                <a class="{{ str_starts_with($current, 'admin.pengguna') ? $activeClass : $normalClass }}" href="{{ route('admin.pengguna.index') }}">
                    <i class="fas fa-user-cog w-5 h-5"></i>
                    <span class="ml-3 text-sm font-medium whitespace-nowrap">Pengguna</span>
                </a>


                {{-- PRESENSI & TRANSAKSI --}}
                <div class="text-xs uppercase text-white/50 font-semibold mb-2 mt-4 px-4">Presensi</div>
                <a class="{{ str_starts_with($current, 'admin.presensi') ? $activeClass : $normalClass }}" href="{{ route('admin.presensi.index') }}">
                    <i class="fas fa-clipboard-check w-5 h-5"></i>
                    <span class="ml-3 text-sm font-medium whitespace-nowrap">Input Presensi</span>
                </a>
                <a class="{{ str_starts_with($current, 'admin.izin') ? $activeClass : $normalClass }}" href="{{ route('admin.izin.index') }}">
                    <i class="fas fa-envelope-open-text w-5 h-5"></i>
                    <span class="ml-3 text-sm font-medium whitespace-nowrap">Validasi Izin</span>
                </a>

                {{-- LAPORAN --}}
                <div class="text-xs uppercase text-white/50 font-semibold mb-2 mt-4 px-4">Laporan</div>
                <a class="{{ $current === 'admin.laporan.index' ? $activeClass : $normalClass }}" href="{{ route('admin.laporan.index') }}">
                    <i class="fas fa-file-alt w-5 h-5"></i>
                    <span class="ml-3 text-sm font-medium whitespace-nowrap">Rekap Presensi</span>
                </a>

                {{-- PEMBELAJARAN --}}
                <div class="text-xs uppercase text-white/50 font-semibold mb-2 mt-4 px-4">Pembelajaran</div>
                <a class="{{ str_starts_with($current, 'admin.dokumentasi') ? $activeClass : $normalClass }}" href="{{ route('admin.dokumentasi.index') }}">
                    <i class="fas fa-camera w-5 h-5"></i>
                    <span class="ml-3 text-sm font-medium whitespace-nowrap">Dokumentasi Majelis</span>
                </a>


            @elseif($role === 'ustadz')
                {{-- Majelis & Presensi --}}
                <div class="text-xs uppercase text-white/50 font-semibold mb-2 mt-4 px-4">Madrasah</div>
                <a class="{{ str_starts_with($current, 'ustadz.presensi') || $current === 'ustadz.jadwal.show' ? $activeClass : $normalClass }}" href="{{ route('ustadz.presensi.index') }}">
                    <i class="fas fa-chalkboard-teacher w-5 h-5"></i>
                    <span class="ml-3 text-sm font-medium whitespace-nowrap">Majelis & Presensi</span>
                </a>
                <a class="{{ str_starts_with($current, 'ustadz.izin') ? $activeClass : $normalClass }}" href="{{ route('ustadz.izin.index') }}">
                    <i class="fas fa-envelope-open-text w-5 h-5"></i>
                    <span class="ml-3 text-sm font-medium whitespace-nowrap">Validasi Izin</span>
                </a>

                {{-- PEMBELAJARAN --}}
                <div class="text-xs uppercase text-white/50 font-semibold mb-2 mt-4 px-4">Pembelajaran</div>
                <a class="{{ str_starts_with($current, 'ustadz.dokumentasi') ? $activeClass : $normalClass }}" href="{{ route('ustadz.dokumentasi.index') }}">
                    <i class="fas fa-camera w-5 h-5"></i>
                    <span class="ml-3 text-sm font-medium whitespace-nowrap">Dokumentasi Majelis</span>
                </a>

                {{-- LAPORAN --}}
                <div class="text-xs uppercase text-white/50 font-semibold mb-2 mt-4 px-4">Laporan</div>
                <a class="{{ str_starts_with($current, 'ustadz.laporan') ? $activeClass : $normalClass }}" href="{{ route('ustadz.laporan.index') }}">
                    <i class="fas fa-chart-bar w-5 h-5"></i>
                    <span class="ml-3 text-sm font-medium whitespace-nowrap">Rekap Presensi</span>
                </a>



            @elseif($role === 'santri')

                {{-- PRESENSI MANDIRI --}}
                <div class="text-xs uppercase text-white/50 font-semibold mb-2 mt-4 px-4">Presensi</div>
                <a class="{{ str_starts_with($current, 'santri.absen-mandiri') ? $activeClass : $normalClass }}" href="{{ route('santri.absen-mandiri.index') }}">
                    <i class="fas fa-fingerprint w-5 h-5"></i>
                    <span class="ml-3 text-sm font-medium whitespace-nowrap">Presensi Mandiri</span>
                </a>
                <a class="{{ str_starts_with($current, 'santri.presensi') ? $activeClass : $normalClass }}" href="{{ route('santri.presensi.index') }}">
                    <i class="fas fa-clipboard-list w-5 h-5"></i>
                    <span class="ml-3 text-sm font-medium whitespace-nowrap">Riwayat Presensi</span>
                </a>

                {{-- MAJELIS --}}
                <div class="text-xs uppercase text-white/50 font-semibold mb-2 mt-4 px-4">Majelis</div>
                <a class="{{ str_starts_with($current, 'santri.jadwal') ? $activeClass : $normalClass }}" href="{{ route('santri.jadwal.index') }}">
                    <i class="fas fa-calendar-alt w-5 h-5"></i>
                    <span class="ml-3 text-sm font-medium whitespace-nowrap">Jadwal Majelis</span>
                </a>

                {{-- IZIN --}}
                <div class="text-xs uppercase text-white/50 font-semibold mb-2 mt-4 px-4">Perizinan</div>
                <a class="{{ str_starts_with($current, 'santri.izin') ? $activeClass : $normalClass }}" href="{{ route('santri.izin.index') }}">
                    <i class="fas fa-envelope-open-text w-5 h-5"></i>
                    <span class="ml-3 text-sm font-medium whitespace-nowrap">Ajukan Izin</span>
                </a>

                {{-- DOKUMENTASI --}}
                <div class="text-xs uppercase text-white/50 font-semibold mb-2 mt-4 px-4">Pembelajaran</div>
                <a class="{{ str_starts_with($current, 'santri.dokumentasi') ? $activeClass : $normalClass }}" href="{{ route('santri.dokumentasi.index') }}">
                    <i class="fas fa-camera w-5 h-5"></i>
                    <span class="ml-3 text-sm font-medium whitespace-nowrap">Dokumentasi Majelis</span>
                </a>
            @endif

            <div class="text-xs uppercase text-white/50 font-semibold mb-2 mt-4 px-4">Akun</div>
            <a class="{{ str_starts_with($current, 'profile') ? $activeClass : $normalClass }}" href="{{ route('profile.edit') }}">
                <i class="fas fa-user-circle w-5 h-5"></i>
                <span class="ml-3 text-sm font-medium whitespace-nowrap">Profil</span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center w-full px-4 py-2 mt-2 text-gray-300 transition-colors rounded-lg hover:bg-merah hover:text-white">
                    <i class="fas fa-sign-out-alt w-5 h-5"></i>
                    <span class="ml-3 text-sm font-medium whitespace-nowrap">Logout</span>
                </button>
            </form>
        </nav>
        
        <div class="mt-4 flex flex-col items-center px-2">
            <div class="flex items-center w-full bg-white bg-opacity-10 rounded-lg p-2 gap-2">
                <img class="object-cover w-8 h-8 rounded-full flex-shrink-0" src="{{ auth()->user()->foto_profil ? asset('storage/'.auth()->user()->foto_profil) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&color=14532D&background=F1F5F9' }}" alt="avatar">
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[10px] text-gray-300 capitalize truncate">{{ auth()->user()->role }}</p>
                </div>
            </div>
            <div class="mt-3 text-center opacity-40">
                <p class="text-[9px] text-white">Presensi Madin v1.0.0</p>
            </div>
        </div>
    </div>
</aside>


<script>
    // Bulletproof event delegation
    document.addEventListener('click', function(e) {
        // If clicking inside the sidebar nav area (either an A tag or a BUTTON for logout)
        const link = e.target.closest('#sidebar a, #sidebar button');
        
        if (link) {
            // Ignore logo clicks
            if (link.getAttribute('href') === '#') return;
            
            // Only on mobile
            if (window.innerWidth < 1024) {
                const sidebar = document.getElementById('sidebar-container');
                if (sidebar && !sidebar.classList.contains('-translate-x-full')) {
                    if (typeof toggleSidebar === 'function') {
                        toggleSidebar();
                    }
                }
            }
        }
    });
</script>