<x-app-layout>
    <x-slot name="header">Ajukan Izin</x-slot>

    <div class="max-w-xl mx-auto space-y-5">

        {{-- Header Card --}}
        <div class="relative overflow-hidden bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50">
            <div class="h-1 bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-400"></div>
            <div class="p-5 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-500 flex-shrink-0">
                    <i class="fas fa-paper-plane text-lg"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-800">Formulir Pengajuan Izin</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Lengkapi data di bawah untuk mengajukan izin tidak hadir</p>
                </div>
            </div>
        </div>

        {{-- Form Card --}}
        <div class="bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 overflow-hidden">
            <form method="POST" action="{{ route('santri.izin.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="p-6 space-y-5">
                    {{-- Global Error (misal: duplicate izin) --}}
                    @if($errors->has('error'))
                        <div class="p-4 rounded-xl bg-red-50 border border-red-100 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0 text-red-500">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-red-800">Pengajuan Ditolak</h3>
                                <p class="text-xs text-red-600 mt-0.5">{{ $errors->first('error') }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Jadwal --}}
                    <div>
                        <label class="flex items-center gap-1.5 text-[10px] font-semibold text-gray-500 uppercase tracking-widest mb-2">
                            <i class="fas fa-calendar-check text-emerald-400 text-[9px]"></i> Jadwal <span class="text-red-400">*</span>
                        </label>
                        @php
                            $jadwalOptions = [];
                            foreach($jadwals as $jadwal) {
                                $jadwalOptions[$jadwal->id] = ($jadwal->kitab->nama_kitab ?? 'Tanpa Kitab') . ' / ' . $jadwal->ustadz->user->name;
                            }
                        @endphp
                        <x-custom-select name="jadwal_id" :options="$jadwalOptions" :selected="old('jadwal_id', request('jadwal_id'))" placeholder="-- Pilih Jadwal --" width="w-full" required="true" />
                        @error('jadwal_id')<p class="text-red-500 text-[11px] mt-1.5 flex items-center gap-1"><i class="fas fa-exclamation-circle text-[9px]"></i>{{ $message }}</p>@enderror
                    </div>

                    {{-- Tanggal --}}
                    <div>
                        <label class="flex items-center gap-1.5 text-[10px] font-semibold text-gray-500 uppercase tracking-widest mb-2">
                            <i class="fas fa-calendar-alt text-emerald-400 text-[9px]"></i> Tanggal Izin <span class="text-red-400">*</span>
                        </label>
                        <input type="date" name="tanggal_izin" value="{{ old('tanggal_izin', now()->format('Y-m-d')) }}" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:ring-2 focus:ring-emerald-400/50 focus:border-emerald-400 focus:outline-none transition">
                        @error('tanggal_izin')<p class="text-red-500 text-[11px] mt-1.5 flex items-center gap-1"><i class="fas fa-exclamation-circle text-[9px]"></i>{{ $message }}</p>@enderror
                    </div>

                    {{-- Alasan --}}
                    <div>
                        <label class="flex items-center gap-1.5 text-[10px] font-semibold text-gray-500 uppercase tracking-widest mb-2">
                            <i class="fas fa-pen-alt text-emerald-400 text-[9px]"></i> Alasan Izin <span class="text-red-400">*</span>
                        </label>
                        <textarea name="alasan" rows="4" required placeholder="Tuliskan alasan izin Anda secara jelas..."
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:ring-2 focus:ring-emerald-400/50 focus:border-emerald-400 focus:outline-none transition resize-none">{{ old('alasan') }}</textarea>
                        @error('alasan')<p class="text-red-500 text-[11px] mt-1.5 flex items-center gap-1"><i class="fas fa-exclamation-circle text-[9px]"></i>{{ $message }}</p>@enderror
                    </div>

                    {{-- Upload Bukti --}}
                    <div>
                        <label class="flex items-center gap-1.5 text-[10px] font-semibold text-gray-500 uppercase tracking-widest mb-2">
                            <i class="fas fa-cloud-upload-alt text-emerald-400 text-[9px]"></i> Upload Bukti
                            <span class="text-gray-300 font-normal normal-case tracking-normal">(opsional)</span>
                        </label>
                        <div class="relative">
                            <input type="file" name="upload_bukti" accept="image/*,application/pdf" id="file-upload"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-600 hover:file:bg-emerald-100 transition">
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1.5 flex items-center gap-1">
                            <i class="fas fa-info-circle text-[8px]"></i> Format: JPG, PNG, PDF. Maks 5MB.
                        </p>
                        @error('upload_bukti')<p class="text-red-500 text-[11px] mt-1.5 flex items-center gap-1"><i class="fas fa-exclamation-circle text-[9px]"></i>{{ $message }}</p>@enderror
                    </div>

                    {{-- Info Note --}}
                    <div class="flex items-start gap-3 p-3.5 rounded-xl bg-emerald-50/80 border border-emerald-100">
                        <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-info-circle text-emerald-500 text-[10px]"></i>
                        </div>
                        <p class="text-[11px] text-emerald-700 leading-relaxed">
                            Pengajuan izin akan divalidasi oleh <strong>ustadz/ustadzah</strong> pengampu Anda. Harap menunggu konfirmasi sebelum tanggal izin yang diminta.
                        </p>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="border-t border-gray-100 px-6 py-4 bg-gray-50/30 flex items-center justify-end gap-3">
                    <a href="{{ route('santri.izin.index') }}"
                       class="border border-gray-200 text-gray-500 px-5 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-100 transition-colors active:scale-95">
                        Batal
                    </a>
                    <button type="submit"
                            class="text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition hover:opacity-90 active:scale-95 flex items-center gap-2"
                            style="background: linear-gradient(135deg, #10b981, #14532D);">
                        <i class="fas fa-paper-plane text-xs"></i> Kirim Permohonan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
