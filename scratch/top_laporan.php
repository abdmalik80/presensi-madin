<x-app-layout>
    <x-slot name="header">Laporan Presensi</x-slot>

    @php
    $majelisOptions = []; foreach($majelis as $m) $majelisOptions[$m->id] = $m->nama_majelis; $kitabOptions = []; foreach($kitabs as $k) $kitabOptions[$k->id] = $k->nama_kitab;
    @endphp


    <div class="space-y-4 pb-20 md:pb-0" x-data="{ showFilterModal: false }">

        {{-- Desktop Filter --}}
        <div class="hidden md:block bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 p-5">
            <form method="GET" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Cari Santri</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / NIS" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-hijau-tua bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Majelis</label>
                        <x-custom-select name="majelis_id" :options="$majelisOptions" :selected="request('majelis_id')" placeholder="Semua Majelis" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kitab</label>
                        <x-custom-select name="kitab_id" :options="$kitabOptions" :selected="request('kitab_id')" placeholder="Semua Kitab" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Dari Tanggal</label>
                        <input type="date" name="dari" value="{{ request('dari') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Sampai Tanggal</label>
                        <input type="date" name="sampai" value="{{ request('sampai') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent bg-white">
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit" class="bg-hijau-tua text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-hijau-muda transition-all shadow-sm flex items-center justify-center gap-2 w-full lg:w-auto h-[38px]">
                            <i class="fas fa-search"></i> Tampilkan
                        </button>
                        @if(request()->anyFilled(['majelis_id','kitab_id','dari','sampai','search']))
                        <a href="{{ route('admin.laporan.pdf', request()->query()) }}" target="_blank"
                           class="bg-rose-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-rose-700 transition-all shadow-sm flex items-center justify-center gap-2 w-full lg:w-auto h-[38px]" title="Export PDF">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        {{-- Mobile Header & Filter Button --}}
        <div class="md:hidden flex items-center justify-between bg-white p-3 rounded-xl shadow-sm border border-gray-100 sticky top-0 z-30">
            <div>
                <h2 class="text-sm font-bold text-gray-800">Laporan Presensi</h2>
                <p class="text-[10px] text-gray-500">
