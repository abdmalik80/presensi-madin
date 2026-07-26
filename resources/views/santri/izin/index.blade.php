<x-app-layout>
    <x-slot name="header">Riwayat Izin Saya</x-slot>

    <div class="space-y-4 pb-20 md:pb-0">
        <x-alert />

        <div class="flex justify-between items-center">
            <p class="text-sm text-gray-500 hidden md:block">Daftar pengajuan izin yang telah Anda buat</p>
            <a href="{{ route('santri.izin.create') }}"
               class="inline-flex items-center gap-2 bg-hijau-tua text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-hijau-muda transition-colors ml-auto">
                <i class="fas fa-plus"></i> Ajukan Izin
            </a>
        </div>

        {{-- ===== DESKTOP TABLE ===== --}}
        <div class="hidden md:block bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-hijau-tua text-white">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Tanggal Izin</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Kitab / Ustadz</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Alasan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Bukti</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Catatan Pengampu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($izins as $izin)
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ \Carbon\Carbon::parse($izin->tanggal_izin)->translatedFormat('d M Y') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-800">
                            @if($izin->jadwal)
                                {{ $izin->jadwal->kitab->nama_kitab }}<br>
                                <span class="text-xs text-gray-500">{{ $izin->jadwal->ustadz->user->name }}</span>
                            @else
                                <span class="text-gray-400 text-xs">Semua Pelajaran</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ Str::limit($izin->alasan, 60) }}</td>
                        <td class="px-4 py-3 text-sm">
                            @if($izin->upload_bukti)
                                <a href="{{ asset('storage/'.$izin->upload_bukti) }}" target="_blank" class="text-hijau-tua hover:underline text-xs">
                                    <i class="fas fa-file-image mr-1"></i>Lihat
                                </a>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($izin->status_validasi === 'Menunggu')
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 font-semibold">⏳ Menunggu</span>
                            @elseif($izin->status_validasi === 'Disetujui')
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 font-semibold">✅ Disetujui</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700 font-semibold">❌ Ditolak</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $izin->catatan_pengampu ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                            <i class="fas fa-envelope-open-text text-4xl mb-3 block text-gray-200"></i>
                            Belum ada pengajuan izin.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ===== MOBILE CARD LIST ===== --}}
        <div class="md:hidden bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 overflow-hidden">
            @forelse($izins as $izin)
            @php
                $statusColor = match($izin->status_validasi) {
                    'Disetujui' => ['left' => 'bg-green-500',  'bg' => 'bg-green-100',  'text' => 'text-green-800',  'ring' => 'ring-green-200',  'label' => '✅ Disetujui'],
                    'Ditolak'   => ['left' => 'bg-red-500',    'bg' => 'bg-red-100',    'text' => 'text-red-700',    'ring' => 'ring-red-200',    'label' => '❌ Ditolak'],
                    default     => ['left' => 'bg-yellow-400', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'ring' => 'ring-yellow-200', 'label' => '⏳ Menunggu'],
                };
            @endphp
            <div class="flex gap-3 px-4 py-3.5 border-b border-gray-100 last:border-0 hover:bg-gray-50/60 transition-colors">
                {{-- Garis accent kiri berwarna sesuai status --}}
                <div class="w-1 self-stretch rounded-full {{ $statusColor['left'] }} flex-shrink-0"></div>

                {{-- Konten utama --}}
                <div class="flex-1 min-w-0 space-y-2">

                    {{-- Baris atas: tanggal + badge status --}}
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-xs font-bold text-gray-800">
                            <i class="fas fa-calendar-day text-gray-400 mr-1 text-[10px]"></i>
                            {{ \Carbon\Carbon::parse($izin->tanggal_izin)->translatedFormat('d M Y') }}
                        </p>
                        <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-semibold rounded-full flex-shrink-0 {{ $statusColor['bg'] }} {{ $statusColor['text'] }} ring-1 {{ $statusColor['ring'] }}">
                            {{ $statusColor['label'] }}
                        </span>
                    </div>

                    {{-- Kitab & Ustadz --}}
                    <div class="flex items-center gap-1 text-[10px] text-gray-600">
                        <i class="fas fa-book-open text-gray-300 text-[8px]"></i>
                        @if($izin->jadwal)
                            <span class="font-semibold text-gray-700">{{ $izin->jadwal->kitab->nama_kitab }}</span>
                            <span class="text-gray-300">·</span>
                            <span class="text-gray-500 truncate">{{ $izin->jadwal->ustadz->user->name }}</span>
                        @else
                            <span class="text-gray-400">Semua Pelajaran</span>
                        @endif
                    </div>

                    {{-- Alasan --}}
                    <p class="text-[10px] text-gray-500 italic bg-gray-50 px-2 py-1.5 rounded-lg border border-gray-100 line-clamp-2">
                        "{{ $izin->alasan }}"
                    </p>

                    {{-- Catatan pengampu (jika ada) --}}
                    @if($izin->catatan_pengampu)
                    <div class="flex items-start gap-1.5 text-[10px] px-2 py-1.5 rounded-lg
                        {{ $izin->status_validasi === 'Disetujui' ? 'text-green-700 bg-green-50 border border-green-100' : 'text-red-700 bg-red-50 border border-red-100' }}">
                        <i class="fas fa-comment-dots mt-0.5 text-[8px] flex-shrink-0"></i>
                        <span>{{ $izin->catatan_pengampu }}</span>
                    </div>
                    @endif

                    {{-- Baris bawah: bukti + nomor urut --}}
                    <div class="flex items-center justify-between pt-1">
                        @if($izin->upload_bukti)
                            <a href="{{ asset('storage/'.$izin->upload_bukti) }}" target="_blank"
                               class="inline-flex items-center gap-1 text-[10px] text-hijau-tua font-semibold bg-green-50 border border-green-200 px-2 py-1 rounded-lg">
                                <i class="fas fa-file-image text-[8px]"></i> Lihat Bukti
                            </a>
                        @else
                            <span class="text-[10px] text-gray-400 italic">Tanpa bukti</span>
                        @endif
                        <span class="text-[10px] text-gray-300 font-bold">#{{ $loop->iteration }}</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="py-14 text-center">
                <i class="fas fa-envelope-open-text text-4xl text-gray-200 mb-3 block"></i>
                <p class="text-sm font-semibold text-gray-500">Belum ada pengajuan izin</p>
                <p class="text-[10px] text-gray-400 mt-1">Tap tombol di bawah untuk membuat pengajuan baru</p>
                <a href="{{ route('santri.izin.create') }}"
                   class="inline-flex items-center gap-2 mt-4 bg-hijau-tua text-white px-5 py-2 rounded-lg text-xs font-medium">
                    <i class="fas fa-plus"></i> Ajukan Izin Sekarang
                </a>
            </div>
            @endforelse
        </div>


    </div>
</x-app-layout>
