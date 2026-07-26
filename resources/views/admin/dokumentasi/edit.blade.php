<x-app-layout>
    <x-slot name="header">Edit Berkas</x-slot>
    <div class="max-w-xl mx-auto">
        <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 p-8">

            <a href="{{ route('admin.dokumentasi.index') }}"
               class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-hijau-tua transition-colors mb-6 font-medium">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>

            <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                <i class="fas fa-edit text-amber-500"></i> Edit Berkas
            </h3>

            <form method="POST" action="{{ route('admin.dokumentasi.update', $dokumentasi) }}" enctype="multipart/form-data" class="space-y-5">
                @csrf @method('PUT')

                {{-- Judul --}}
<x-app-layout>
    <x-slot name="header">Edit Berkas</x-slot>
    <div class="max-w-xl mx-auto">
        <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 p-8">

            <a href="{{ route('admin.dokumentasi.index') }}"
               class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-hijau-tua transition-colors mb-6 font-medium">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>

            <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                <i class="fas fa-edit text-amber-500"></i> Edit Berkas
            </h3>

            <form method="POST" action="{{ route('admin.dokumentasi.update', $dokumentasi) }}" enctype="multipart/form-data" class="space-y-5">
                @csrf @method('PUT')

                {{-- Judul --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul <span class="text-red-500">*</span></label>
                    <input type="text" name="judul" value="{{ old('judul', $dokumentasi->judul) }}" required
                           class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-hijau-tua focus:outline-none">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Pilih Majelis --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Majelis</label>
                        @php
                            $majelisOptions = [];
                            foreach($jadwals->pluck('majelis')->filter()->unique('id') as $m) {
                                $majelisOptions[$m->id] = $m->nama_majelis;
                            }
                        @endphp
                        <x-custom-select name="majelis_id" :options="$majelisOptions" :selected="old('majelis_id', $dokumentasi->majelis_id)" placeholder="-- Semua --" width="w-full" onchange="filterChange('majelis', val)" />
                        @error('majelis_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Pilih Kitab --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kitab</label>
                        @php
                            $kitabOptions = [];
                            foreach($jadwals->pluck('kitab')->filter()->unique('id') as $k) {
                                $kitabOptions[$k->id] = $k->nama_kitab;
                            }
                        @endphp
                        <x-custom-select name="kitab_id" :options="$kitabOptions" :selected="old('kitab_id', $dokumentasi->kitab_id ?? $dokumentasi->jadwal?->kitab_id)" placeholder="-- Semua --" width="w-full" onchange="filterChange('kitab', val)" />
                        @error('kitab_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Pengampu --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pengampu</label>
                        @php
                            $pengampuOptions = [];
                            foreach($ustadzs as $u) {
                                $pengampuOptions[$u->id] = $u->nama;
                            }
                        @endphp
                        <x-custom-select name="pengampu_id" :options="$pengampuOptions" :selected="old('pengampu_id', $dokumentasi->pengampu_id)" placeholder="-- Semua --" width="w-full" onchange="filterChange('pengampu', val)" />
                        @error('pengampu_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Tanggal --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', $dokumentasi->tanggal) }}"
                           class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-hijau-tua focus:outline-none">
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" rows="3"
                              class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-hijau-tua focus:outline-none">{{ old('deskripsi', $dokumentasi->deskripsi) }}</textarea>
                </div>

                {{-- File saat ini --}}
                @if($dokumentasi->upload_file)
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                    <p class="text-xs text-gray-500 font-medium mb-2">Berkas Saat Ini:</p>
                    <div class="flex items-center gap-3">
                        @if($dokumentasi->tipe_file === 'image')
                            <img src="{{ asset('storage/'.$dokumentasi->upload_file) }}" class="w-16 h-16 object-cover rounded-lg border border-gray-200">
                        @else
                            <div class="w-12 h-12 bg-white rounded-lg border border-gray-200 flex items-center justify-center">
                                <i class="fas {{ $dokumentasi->file_icon }} text-2xl"></i>
                            </div>
                        @endif
                        <div>
                            <p class="text-sm font-medium text-gray-700">{{ $dokumentasi->nama_file ?? basename($dokumentasi->upload_file) }}</p>
                            <p class="text-xs text-gray-400">{{ $dokumentasi->tipe_label }}</p>
                        </div>
                        <a href="{{ asset('storage/'.$dokumentasi->upload_file) }}" target="_blank"
                           class="ml-auto text-xs text-hijau-tua hover:underline font-medium">Lihat</a>
                    </div>
                </div>
                @endif

                {{-- Upload File Baru --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ganti Berkas <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-5 text-center hover:border-hijau-tua transition-colors cursor-pointer" onclick="document.getElementById('file-edit-input').click()">
                        <i class="fas fa-cloud-upload-alt text-2xl text-gray-300 mb-1"></i>
                        <p class="text-sm text-gray-500">Klik untuk pilih berkas baru</p>
                        <p class="text-xs text-gray-400 mt-0.5">PDF, Word, Excel, PowerPoint, Gambar — Maks. 20MB</p>
                        <p id="file-edit-name" class="text-xs text-hijau-tua mt-2 font-semibold hidden"></p>
                    </div>
                    <input type="file" id="file-edit-input" name="upload_file"
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.webp"
                           class="hidden"
                           onchange="document.getElementById('file-edit-name').textContent = this.files[0]?.name; document.getElementById('file-edit-name').classList.remove('hidden')">
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                            class="bg-hijau-tua text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-hijau-muda transition-colors flex items-center gap-2">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <a href="{{ route('admin.dokumentasi.index') }}"
                       class="border border-gray-200 text-gray-600 px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
    </div>

    @push('scripts')
    <script>
        const jadwals = @json($jadwals->map(fn($j) => ['majelis_id' => $j->majelis_id, 'kitab_id' => $j->kitab_id, 'ustadz_id' => $j->ustadz_id]));

        window.filterChange = function(source, val) {
            setTimeout(() => {
                const majelisInput = document.querySelector('input[name="majelis_id"]');
                const kitabInput = document.querySelector('input[name="kitab_id"]');
                const ustadzInput = document.querySelector('input[name="pengampu_id"]');
                
                let mId = majelisInput?.value;
                let kId = kitabInput?.value;
                let uId = ustadzInput?.value;

                let validMajelis = new Set();
                let validKitab = new Set();
                let validUstadz = new Set();

                jadwals.forEach(j => {
                    // Validasi untuk opsi Majelis: harus cocok dengan Kitab dan Ustadz saat ini
                    if ((!kId || j.kitab_id == kId) && (!uId || j.ustadz_id == uId)) {
                        if (j.majelis_id) validMajelis.add(String(j.majelis_id));
                    }
                    
                    // Validasi untuk opsi Kitab: harus cocok dengan Majelis dan Ustadz saat ini
                    if ((!mId || j.majelis_id == mId) && (!uId || j.ustadz_id == uId)) {
                        if (j.kitab_id) validKitab.add(String(j.kitab_id));
                    }
                    
                    // Validasi untuk opsi Ustadz: harus cocok dengan Majelis dan Kitab saat ini
                    if ((!mId || j.majelis_id == mId) && (!kId || j.kitab_id == kId)) {
                        if (j.ustadz_id) validUstadz.add(String(j.ustadz_id));
                    }
                });

                const updateOptions = (input, validSet) => {
                    if (!input) return;
                    const wrapper = input.closest('[x-data]');
                    if (!wrapper) return;
                    const buttons = wrapper.querySelectorAll('button[data-val]');
                    let hasSelection = false;
                    
                    buttons.forEach(btn => {
                        const v = btn.getAttribute('data-val');
                        if (v === "") {
                            btn.style.display = 'block';
                            return;
                        }
                        if (validSet.has(String(v))) {
                            btn.style.display = 'block';
                            if (String(v) === String(input.value)) hasSelection = true;
                        } else {
                            btn.style.display = 'none';
                        }
                    });

                    if (input.value && !hasSelection) {
                        input.value = '';
                        input.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                };

                if (source !== 'majelis') updateOptions(majelisInput, validMajelis);
                if (source !== 'kitab') updateOptions(kitabInput, validKitab);
                if (source !== 'pengampu') updateOptions(ustadzInput, validUstadz);

            }, 50);
        };

        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.filterChange('init', null);
            }, 100);
        });
    </script>
    @endpush
</x-app-layout>
