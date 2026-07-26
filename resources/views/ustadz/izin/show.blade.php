<x-app-layout>
    <x-slot name="header">Detail Izin</x-slot>
<div class="max-w-2xl mx-auto space-y-5">

    <div class="flex items-center gap-3">
        <a href="{{ route('ustadz.izin.index') }}" class="text-gray-400 hover:text-gray-600 transition">
            <i class="fas fa-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-800">Detail Pengajuan Izin</h1>
            <p class="text-sm text-gray-500">Periksa dan beri keputusan izin santri</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 text-sm flex items-center gap-2">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Info Izin --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
            <i class="fas fa-info-circle text-green-600"></i> Informasi Izin
        </h3>
        <div class="grid grid-cols-2 gap-x-6 gap-y-4">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Nama Santri</p>
                <p class="text-sm font-semibold text-gray-800">{{ $izin->santri->user->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Tanggal Izin</p>
                <p class="text-sm text-gray-700">{{ \Carbon\Carbon::parse($izin->tanggal_izin)->translatedFormat('d F Y') }}</p>
            </div>
            <div class="col-span-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Alasan</p>
                <p class="text-sm text-gray-700 bg-gray-50 rounded-xl p-3">{{ $izin->alasan }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Bukti Pendukung</p>
                @if($izin->upload_bukti)
                    <a href="{{ asset('storage/'.$izin->upload_bukti) }}" target="_blank"
                       class="inline-flex items-center gap-1 text-green-600 hover:underline text-sm">
                        <i class="fas fa-file-image"></i> Lihat File
                    </a>
                @else
                    <p class="text-sm text-gray-400 italic">Tidak ada bukti</p>
                @endif
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Status Saat Ini</p>
                @if($izin->status_validasi === 'Menunggu')
                    <span class="px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 font-semibold">⏳ Menunggu</span>
                @elseif($izin->status_validasi === 'Disetujui')
                    <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-800 font-semibold">✅ Disetujui</span>
                @else
                    <span class="px-3 py-1 text-xs rounded-full bg-red-100 text-red-700 font-semibold">❌ Ditolak</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Catatan Pengampu yang sudah ada --}}
    @if($izin->catatan_pengampu)
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4">
        <p class="text-xs font-semibold text-blue-500 uppercase mb-1">Catatan Pengampu</p>
        <p class="text-sm text-blue-800">{{ $izin->catatan_pengampu }}</p>
    </div>
    @endif

    {{-- Form Validasi --}}
    @if($izin->status_validasi === 'Menunggu')
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
            <i class="fas fa-stamp text-green-600"></i> Berikan Keputusan
        </h3>

        <form method="POST" action="{{ route('ustadz.izin.validasi', $izin) }}">
            @csrf @method('PATCH')

            {{-- Pilihan Keputusan --}}
            <div class="flex gap-4 mb-5">
                <label class="flex-1 flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition has-[:checked]:border-green-500 has-[:checked]:bg-green-50 border-gray-200">
                    <input type="radio" name="status_validasi" value="Disetujui" required class="accent-green-600">
                    <div>
                        <p class="text-sm font-semibold text-gray-700">✅ Setujui</p>
                        <p class="text-xs text-gray-400">Izin disetujui</p>
                    </div>
                </label>
                <label class="flex-1 flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition has-[:checked]:border-red-400 has-[:checked]:bg-red-50 border-gray-200">
                    <input type="radio" name="status_validasi" value="Ditolak" class="accent-red-500">
                    <div>
                        <p class="text-sm font-semibold text-gray-700">❌ Tolak</p>
                        <p class="text-xs text-gray-400">Izin ditolak</p>
                    </div>
                </label>
            </div>

            {{-- Catatan Pengampu --}}
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Catatan Pengampu
                    <span class="text-gray-400 font-normal">(sangat disarankan saat menolak)</span>
                </label>
                <textarea name="catatan_pengampu" rows="3"
                          class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent bg-gray-50 transition"
                          placeholder="Tulis catatan atau alasan keputusan untuk santri...">{{ old('catatan_pengampu') }}</textarea>
                <p class="text-xs text-gray-400 mt-1">Catatan ini akan ditampilkan di riwayat izin santri.</p>
            </div>

            <button type="submit"
                    class="px-6 py-2.5 rounded-xl text-white text-sm font-semibold transition hover:opacity-90 active:scale-95"
                    style="background: linear-gradient(135deg, #22C55E, #14532D);">
                <i class="fas fa-check-double mr-1"></i> Simpan Keputusan
            </button>
        </form>
    </div>
    @else
    <div class="bg-gray-50 rounded-2xl border border-gray-200 p-4 text-center text-sm text-gray-500">
        <i class="fas fa-lock mr-2 text-gray-300"></i>
        Izin ini sudah divalidasi dan tidak dapat diubah.
    </div>
    @endif

</div>
</x-app-layout>
