<x-app-layout>
    @php
    $jadwalOptions = [];
    foreach($jadwals as $j) {
        $jadwalOptions[$j->id] = ($j->majelis->nama_majelis ?? '') . ' | ' . $j->hari . ' ' . $j->jam_mulai;
    }
    @endphp
    <x-slot name="header">Rekam Presensi</x-slot>

<style>
    /* Custom button styles handled via JS */
    /* Santri row animation */
    .santri-row {
        animation: fadeSlideIn 0.3s ease forwards;
        opacity: 0;
    }
    @keyframes fadeSlideIn {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* Loading skeleton */
    .skeleton-pulse {
        background: linear-gradient(90deg, #f3f4f6 25%, #e5e7eb 50%, #f3f4f6 75%);
        background-size: 200% 100%;
        animation: skeletonShimmer 1.5s infinite;
    }
    @keyframes skeletonShimmer {
        0%   { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    /* Smooth scrollbar */
    .custom-scroll::-webkit-scrollbar { width: 5px; }
    .custom-scroll::-webkit-scrollbar-track { background: transparent; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 9999px; }
    .custom-scroll::-webkit-scrollbar-thumb:hover { background: #9ca3af; }

    /* Counter badge animation */
    .counter-badge {
        transition: all 0.3s ease;
    }
    .counter-badge.active {
        transform: scale(1.05);
    }
</style>

<div class="max-w-3xl mx-auto space-y-5">

    {{-- Tombol Kembali --}}
    <div class="mb-4">
        <a href="{{ route('ustadz.presensi.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-gray-800 transition-colors">
            <i class="fas fa-arrow-left"></i> Kembali ke Riwayat
        </a>
    </div>

    {{-- Page Header (Matched with Dashboard) --}}
    <div class="lg:hidden">
        <div class="relative overflow-hidden rounded-2xl mb-8 shadow-xl text-white p-5" style="background: linear-gradient(135deg, #1a4731 0%, #166534 40%, #15803d 70%, #16a34a 100%);">
            <div class="absolute -top-8 -right-8 w-48 h-48 rounded-full opacity-10" style="background: radial-gradient(circle, #bbf7d0, transparent 70%)"></div>
            <div class="absolute bottom-0 left-1/3 w-32 h-32 rounded-full opacity-10" style="background: radial-gradient(circle, #6ee7b7, transparent 70%)"></div>
            <div class="relative z-10 flex items-center gap-3">
                <div class="w-12 h-12 rounded-full border-2 border-white/20 overflow-hidden flex-shrink-0 bg-[#1e6138] flex items-center justify-center">
                    <i class="fas fa-clipboard-check text-white"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[10px] text-emerald-100 font-semibold uppercase tracking-wider mb-0.5 truncate">Pilih jadwal untuk presensi</p>
                    <h2 class="text-base font-bold mb-0.5 leading-tight truncate text-white">Rekam Presensi</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="hidden lg:block">
        <div class="relative overflow-hidden rounded-2xl mb-8 shadow-xl" style="background: linear-gradient(135deg, #1a4731 0%, #166534 40%, #15803d 70%, #16a34a 100%);">
            <div class="absolute -top-8 -right-8 w-48 h-48 rounded-full opacity-10" style="background: radial-gradient(circle, #bbf7d0, transparent 70%)"></div>
            <div class="absolute bottom-0 left-1/3 w-32 h-32 rounded-full opacity-10" style="background: radial-gradient(circle, #6ee7b7, transparent 70%)"></div>
            <div class="relative p-6">
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-3xl shrink-0" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.25);">
                        <i class="fas fa-clipboard-check text-white"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-emerald-200 text-xs font-medium tracking-wide uppercase mb-0.5 truncate">Pilih jadwal untuk menampilkan daftar santri</p>
                        <h2 class="text-2xl font-extrabold text-white leading-tight truncate">Rekam Presensi</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-sm text-green-700 animate-pulse-once">
            <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-check-circle text-green-600"></i>
            </div>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-700">
            <div class="flex items-center gap-2 mb-1 font-semibold">
                <i class="fas fa-exclamation-triangle text-red-500"></i> Terjadi kesalahan
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-xs">
                @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- Main Card --}}
    <div class="bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 overflow-hidden">
        <form method="POST" action="{{ route('ustadz.presensi.store') }}" id="form-presensi">
            @csrf

            {{-- Filter: Jadwal & Tanggal --}}
            <div class="p-6 pb-0">
                @if(isset($selectedJadwal))
                    {{-- Clean Read-Only View --}}
                    <input type="hidden" id="jadwal_id" name="jadwal_id" value="{{ $selectedJadwal->id }}">
                    <input type="hidden" name="tanggal" value="{{ now()->format('Y-m-d') }}">
                    
                    <div class="bg-hijau-tua/5 border border-hijau-tua/10 rounded-xl p-5 mb-2">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800 mb-1">{{ $selectedJadwal->majelis->nama_majelis ?? '-' }}</h3>
                                <p class="text-sm text-gray-600 flex items-center gap-2">
                                    <i class="fas fa-book-open text-hijau-tua"></i> Kitab {{ $selectedJadwal->kitab->nama_kitab ?? '-' }}
                                </p>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="inline-block px-2 py-0.5 sm:px-3 sm:py-1 bg-white border border-gray-200 rounded-lg text-[10px] sm:text-xs font-bold text-gray-600 mb-1 sm:mb-2 shadow-sm">
                                    <i class="fas fa-calendar-day mr-1"></i> {{ now()->translatedFormat('d M Y') }}
                                </span>
                                <p class="text-[10px] sm:text-xs text-gray-500 font-medium">{{ $selectedJadwal->hari }}, {{ substr($selectedJadwal->jam_mulai, 0, 5) }}</p>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Full Select View (For "Presensi Hari Lain") --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="flex items-center gap-1.5 text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                                <i class="fas fa-book-open text-hijau-tua text-[10px]"></i> Jadwal <span class="text-red-400">*</span>
                            </label>
                            <x-custom-select name="jadwal_id" :options="$jadwalOptions" :selected="request('jadwal_id', old('jadwal_id'))" placeholder="-- Pilih Jadwal --" onchange="loadSantri(this.value)" />
                        </div>
                        <div>
                            <label class="flex items-center gap-1.5 text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                                <i class="fas fa-calendar-alt text-hijau-tua text-[10px]"></i> Tanggal <span class="text-red-400">*</span>
                            </label>
                            <input type="date" name="tanggal"
                                   value="{{ old('tanggal', now()->format('Y-m-d')) }}" required
                                   onclick="this.showPicker()"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-hijau-muda/50 focus:border-hijau-muda bg-white transition cursor-pointer">
                        </div>
                    </div>
                @endif
            </div>

            {{-- Santri Area (populated via AJAX) --}}
            <div class="p-6" id="santri-area">
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-amber-50 flex items-center justify-center mb-4">
                        <i class="fas fa-hand-pointer text-amber-400 text-2xl"></i>
                    </div>
                    <p class="text-sm font-semibold text-gray-600">Pilih jadwal terlebih dahulu</p>
                    <p class="text-xs text-gray-400 mt-1">Daftar santri akan muncul secara otomatis setelah jadwal dipilih</p>
                </div>
            </div>

            {{-- Submit Buttons (shown after santri loaded) --}}
            <div id="btn-submit" class="hidden border-t border-gray-100 px-4 sm:px-6 py-4 bg-gray-50/50 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-xs text-gray-400 hidden sm:block">
                    <i class="fas fa-info-circle mr-1"></i> Pastikan semua santri sudah diisi statusnya
                </p>
                <div class="flex w-full sm:w-auto gap-3">
                    <a href="{{ route('ustadz.presensi.index') }}"
                       class="flex-1 sm:flex-none flex items-center justify-center px-4 py-2.5 rounded-xl border border-gray-200 text-gray-500 text-sm font-medium hover:bg-gray-100 transition active:scale-95">
                        Batal
                    </a>
                    <button type="submit"
                            class="flex-[2] sm:flex-none flex items-center justify-center px-4 py-2.5 rounded-xl text-white text-sm font-semibold transition hover:opacity-90 active:scale-95 gap-2"
                            style="background: linear-gradient(135deg, #22C55E, #14532D);">
                        <i class="fas fa-save"></i> Simpan Presensi
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function getTanggal() {
        return document.querySelector('input[name="tanggal"]').value;
    }

    function loadSantri(jadwalId) {
        const area      = document.getElementById('santri-area');
        const btnSubmit = document.getElementById('btn-submit');

        if (!jadwalId) {
            area.innerHTML = `
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-amber-50 flex items-center justify-center mb-4">
                        <i class="fas fa-hand-pointer text-amber-400 text-2xl"></i>
                    </div>
                    <p class="text-sm font-semibold text-gray-600">Pilih jadwal terlebih dahulu</p>
                    <p class="text-xs text-gray-400 mt-1">Daftar santri akan muncul secara otomatis setelah jadwal dipilih</p>
                </div>`;
            btnSubmit.classList.add('hidden');
            return;
        }

        // Loading skeleton
        area.innerHTML = `
            <div class="space-y-3">
                ${[1,2,3,4,5].map(() => `
                    <div class="flex items-center gap-4 p-3">
                        <div class="w-8 h-8 rounded-full skeleton-pulse"></div>
                        <div class="flex-1 space-y-2">
                            <div class="h-3 w-32 rounded skeleton-pulse"></div>
                            <div class="h-2 w-20 rounded skeleton-pulse"></div>
                        </div>
                        <div class="flex gap-2">
                            <div class="h-7 w-14 rounded-full skeleton-pulse"></div>
                            <div class="h-7 w-14 rounded-full skeleton-pulse"></div>
                            <div class="h-7 w-14 rounded-full skeleton-pulse"></div>
                            <div class="h-7 w-14 rounded-full skeleton-pulse"></div>
                        </div>
                    </div>
                `).join('')}
            </div>`;

        const tanggal = getTanggal();
        fetch(`{{ route('ustadz.presensi.santris') }}?jadwal_id=${jadwalId}&tanggal=${tanggal}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.santris.length === 0) {
                area.innerHTML = `
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mb-4">
                            <i class="fas fa-users-slash text-gray-300 text-2xl"></i>
                        </div>
                        <p class="text-sm font-semibold text-gray-500">Tidak ada santri terdaftar</p>
                        <p class="text-xs text-gray-400 mt-1">Tidak ditemukan santri di jadwal ini</p>
                    </div>`;
                btnSubmit.classList.add('hidden');
                return;
            }

            const denied   = data.denied   || [];
            const approved = data.approved || [];
            const existing = data.existing || {};

            let rows = data.santris.map((s, i) => {
                let autoStatus = null;
                let autoBadge  = '';
                if (denied.includes(s.id)) {
                    autoStatus = 'Alpha';
                    autoBadge  = `<span class="ml-2 text-[9px] font-bold bg-red-100 text-red-600 px-2 py-0.5 rounded-full uppercase tracking-wide">Izin Ditolak</span>`;
                } else if (approved.includes(s.id)) {
                    autoStatus = 'Izin';
                    autoBadge  = `<span class="ml-2 text-[9px] font-bold bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full uppercase tracking-wide">Izin Disetujui</span>`;
                }

                const statuses = [
                    { val: 'Hadir', activeClass: 'bg-green-600 text-white border-green-600 shadow-sm shadow-green-200', inactiveClass: 'bg-white text-green-700 border-green-300 hover:bg-green-50' },
                    { val: 'Izin',  activeClass: 'bg-blue-600 text-white border-blue-600 shadow-sm shadow-blue-200', inactiveClass: 'bg-white text-blue-700 border-blue-300 hover:bg-blue-50' },
                    { val: 'Sakit', activeClass: 'bg-amber-500 text-white border-amber-500 shadow-sm shadow-amber-200', inactiveClass: 'bg-white text-amber-700 border-amber-300 hover:bg-amber-50' },
                    { val: 'Alpha', activeClass: 'bg-red-600 text-white border-red-600 shadow-sm shadow-red-200', inactiveClass: 'bg-white text-red-700 border-red-300 hover:bg-red-50' }
                ];

                const buttons = statuses.map(st => {
                    const isActive = (autoStatus === st.val);
                    const classes = isActive ? st.activeClass : st.inactiveClass;
                    return `
                        <button type="button"
                                onclick="setStatus(${s.id}, '${st.val}')"
                                id="btn_${s.id}_${st.val}"
                                class="status-btn text-xs font-semibold px-2 py-1.5 sm:px-3 rounded-lg border transition-all duration-150 ${classes}">
                            ${st.val}
                        </button>`;
                }).join('');

                return `
                    <div class="santri-row bg-white sm:bg-transparent rounded-2xl sm:rounded-none border border-gray-100 sm:border-x-0 sm:border-t-0 sm:border-b shadow-sm sm:shadow-none px-4 sm:px-5 py-3.5 flex flex-col sm:flex-row sm:items-center gap-3 hover:bg-gray-50/40 transition-colors group"
                         style="animation-delay: ${i * 0.04}s">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <span class="hidden sm:block text-xs text-gray-400 w-5 text-center shrink-0">${i+1}</span>
                            <img src="${s.avatar}" class="w-9 h-9 rounded-full object-cover shrink-0" onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(s.name)}&background=16a34a&color=fff&size=36'">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-800 truncate">${s.name} ${autoBadge}</p>
                                <p class="text-xs text-gray-400 font-mono">${s.nis || '-'}</p>
                            </div>
                        </div>
                        <input type="hidden" name="kehadiran[${s.id}]" value="${autoStatus || ''}" id="hidden_${s.id}">
                        <div class="grid grid-cols-4 sm:flex gap-1.5 shrink-0" role="group">${buttons}</div>
                        <div class="w-full sm:w-40 shrink-0">
                            <input type="text"
                                   name="keterangan[${s.id}]"
                                   placeholder="Keterangan..."
                                   class="w-full text-xs border border-gray-200 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-hijau-tua focus:border-transparent text-gray-600 bg-white">
                        </div>
                    </div>`;
            }).join('');

            area.innerHTML = `
                <div>
                    {{-- Header --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-hijau-tua/10 flex items-center justify-center text-hijau-tua">
                                <i class="fas fa-users text-sm"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-gray-700">Daftar Kehadiran</h3>
                                <p class="text-[10px] text-gray-400">${data.santris.length} santri terdaftar</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" onclick="setAllStatus('Hadir')"
                                    class="text-[11px] font-semibold text-green-700 bg-green-50 hover:bg-green-100 px-3 py-1.5 rounded-lg transition flex items-center gap-1">
                                <i class="fas fa-check-double text-[9px]"></i> Semua Hadir
                            </button>
                            <button type="button" onclick="clearAll()"
                                    class="text-[11px] font-semibold text-gray-500 bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-lg transition flex items-center gap-1">
                                <i class="fas fa-eraser text-[9px]"></i> Reset
                            </button>
                        </div>
                    </div>

                    {{-- Counter badges --}}
                    <div class="grid grid-cols-4 gap-2 mb-4" id="status-counters">
                        <div class="counter-badge text-center py-2 rounded-xl bg-green-50 border border-green-100">
                            <p class="text-lg font-bold text-green-700" id="cnt-hadir">0</p>
                            <p class="text-[10px] text-green-600">Hadir</p>
                        </div>
                        <div class="counter-badge text-center py-2 rounded-xl bg-blue-50 border border-blue-100">
                            <p class="text-lg font-bold text-blue-700" id="cnt-izin">0</p>
                            <p class="text-[10px] text-blue-600">Izin</p>
                        </div>
                        <div class="counter-badge text-center py-2 rounded-xl bg-yellow-50 border border-yellow-100">
                            <p class="text-lg font-bold text-yellow-700" id="cnt-sakit">0</p>
                            <p class="text-[10px] text-yellow-600">Sakit</p>
                        </div>
                        <div class="counter-badge text-center py-2 rounded-xl bg-red-50 border border-red-100">
                            <p class="text-lg font-bold text-red-700" id="cnt-alpa">0</p>
                            <p class="text-[10px] text-red-600">Alpha</p>
                        </div>
                    </div>

                    {{-- Santri list --}}
                    <div class="border border-gray-100 rounded-xl overflow-hidden">
                        <div class="max-h-[460px] overflow-y-auto custom-scroll divide-y divide-gray-50">
                            ${rows}
                        </div>
                    </div>

                    {{-- Note --}}
                    <div class="mt-3 flex items-center gap-2 px-3 py-2 rounded-lg bg-amber-50/80 border border-amber-100">
                        <i class="fas fa-info-circle text-amber-400 text-xs shrink-0"></i>
                        <span class="text-[11px] text-amber-700">Santri yang <strong>tidak dipilih statusnya</strong> tidak akan tercatat. Pastikan semua santri sudah diisi.</span>
                    </div>
                </div>`;

            btnSubmit.classList.remove('hidden');
            updateCounters();
        })
        .catch(() => {
            area.innerHTML = `
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-red-50 flex items-center justify-center mb-4">
                        <i class="fas fa-exclamation-triangle text-red-400 text-2xl"></i>
                    </div>
                    <p class="text-sm font-semibold text-red-600">Gagal memuat data</p>
                    <p class="text-xs text-gray-400 mt-1">Periksa koneksi internet dan coba lagi</p>
                </div>`;
        });
    }

    const statusStyles = {
        'Hadir': { active: 'bg-green-600 text-white border-green-600 shadow-sm shadow-green-200', inactive: 'bg-white text-green-700 border-green-300 hover:bg-green-50' },
        'Izin':  { active: 'bg-blue-600 text-white border-blue-600 shadow-sm shadow-blue-200',   inactive: 'bg-white text-blue-700 border-blue-300 hover:bg-blue-50' },
        'Sakit': { active: 'bg-amber-500 text-white border-amber-500 shadow-sm shadow-amber-200', inactive: 'bg-white text-amber-700 border-amber-300 hover:bg-amber-50' },
        'Alpha': { active: 'bg-red-600 text-white border-red-600 shadow-sm shadow-red-200',       inactive: 'bg-white text-red-700 border-red-300 hover:bg-red-50' },
    };
    const allStatuses = ['Hadir', 'Izin', 'Sakit', 'Alpha'];

    function setStatus(santriId, status) {
        document.getElementById('hidden_' + santriId).value = status;
        allStatuses.forEach(s => {
            const btn = document.getElementById('btn_' + santriId + '_' + s);
            if (!btn) return;
            const styles = statusStyles[s];
            [...styles.active.split(' '), ...styles.inactive.split(' ')].forEach(c => btn.classList.remove(c));
            const newStyles = (s === status) ? styles.active : styles.inactive;
            newStyles.split(' ').forEach(c => btn.classList.add(c));
        });
        updateCounters();
    }

    function setAllStatus(status) {
        document.querySelectorAll('input[name^="kehadiran["]').forEach(input => {
            const match = input.name.match(/\[(\d+)\]/);
            if (match) setStatus(match[1], status);
        });
    }

    function clearAll() {
        document.querySelectorAll('input[name^="kehadiran["]').forEach(input => {
            const match = input.name.match(/\[(\d+)\]/);
            if (match) {
                const santriId = match[1];
                input.value = '';
                allStatuses.forEach(s => {
                    const btn = document.getElementById('btn_' + santriId + '_' + s);
                    if (btn) {
                        const styles = statusStyles[s];
                        styles.active.split(' ').forEach(c => btn.classList.remove(c));
                        styles.inactive.split(' ').forEach(c => btn.classList.add(c));
                    }
                });
            }
        });
        updateCounters();
    }

    function updateCounters() {
        const counts = { 'Hadir': 0, 'Izin': 0, 'Sakit': 0, 'Alpha': 0 };
        document.querySelectorAll('input[name^="kehadiran["]').forEach(input => {
            const val = input.value;
            if (counts.hasOwnProperty(val)) counts[val]++;
        });
        
        const mapIds = { 'Hadir': 'hadir', 'Izin': 'izin', 'Sakit': 'sakit', 'Alpha': 'alpa' };
        for (const [status, count] of Object.entries(counts)) {
            const el = document.getElementById(`cnt-${mapIds[status]}`);
            if (el) {
                el.textContent = count;
                el.closest('.counter-badge').classList.toggle('active', count > 0);
            }
        }
    }

    // Auto load - berjalan langsung (untuk navigasi AJAX) DAN saat DOMContentLoaded (full reload)
    function initPage() {
        const sel = document.getElementById('jadwal_id');
        if (!sel) return;
        if (sel.value) loadSantri(sel.value);

        // Reload santri when date changes (to update izin data)
        const tanggalInput = document.querySelector('input[name="tanggal"]');
        if (tanggalInput) {
            tanggalInput.addEventListener('change', () => {
                const s = document.getElementById('jadwal_id');
                if (s && s.value) loadSantri(s.value);
            });
        }
    }

    // Jika DOM sudah ready (navigasi AJAX), langsung jalankan
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPage);
    } else {
        initPage();
    }
</script>

</x-app-layout>
