<x-app-layout>
    <x-slot name="header">Upload Berkas</x-slot>
    <div class="max-w-xl mx-auto">
        <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 p-8">

            {{-- Back link --}}
            <a href="{{ route('ustadz.dokumentasi.index') }}"
               class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-hijau-tua transition-colors mb-6 font-medium">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>

            <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                <i class="fas fa-upload text-hijau-tua"></i> Upload Berkas Baru
            </h3>

            <form method="POST" action="{{ route('ustadz.dokumentasi.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                {{-- Judul --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul <span class="text-red-500">*</span></label>
                    <input type="text" name="judul" value="{{ old('judul') }}" required
                           class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-hijau-tua focus:outline-none">
                    @error('judul')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Pilih Majelis & Kitab --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div x-data>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Majelis <span class="text-red-500">*</span></label>
                        <x-custom-select name="majelis_id" :options="$majelisOptions" :selected="old('majelis_id', $jadwals->firstWhere('id', $selectedJadwal)?->majelis_id)" placeholder="-- Pilih Majelis --" width="w-full" onchange="filterKitab(val)" />
                        @error('majelis_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div x-data>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kitab <span class="text-red-500">*</span></label>
                        <x-custom-select name="kitab_id" :options="$kitabOptions" :selected="old('kitab_id', $jadwals->firstWhere('id', $selectedJadwal)?->kitab_id)" placeholder="-- Pilih Kitab --" width="w-full" onchange="filterMajelis(val)" />
                        @error('kitab_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Tanggal --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', now()->format('Y-m-d')) }}" required
                           class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-hijau-tua focus:outline-none">
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" rows="3"
                              class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-hijau-tua focus:outline-none">{{ old('deskripsi') }}</textarea>
                </div>

                {{-- Upload File --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload Berkas <span class="text-red-500">*</span></label>
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-hijau-tua transition-colors cursor-pointer" onclick="document.getElementById('file-input').click()">
                        <i class="fas fa-cloud-upload-alt text-3xl text-gray-300 mb-2"></i>
                        <p class="text-sm text-gray-500 font-medium">Klik untuk pilih berkas</p>
                        <p class="text-xs text-gray-400 mt-1">PDF, Word, Excel, PowerPoint, Gambar — Maks. 20MB</p>
                        <p id="file-name" class="text-xs text-hijau-tua mt-2 font-semibold hidden"></p>
                    </div>
                    <input type="file" id="file-input" name="upload_file" required
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.webp"
                           class="hidden"
                           onchange="document.getElementById('file-name').textContent = this.files[0]?.name; document.getElementById('file-name').classList.remove('hidden')">
                    @error('upload_file')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                            class="bg-hijau-tua text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-hijau-muda transition-colors flex items-center gap-2">
                        <i class="fas fa-upload"></i> Upload
                    </button>
                    <a href="{{ route('ustadz.dokumentasi.index') }}"
                       class="border border-gray-200 text-gray-600 px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <x-cascading-dropdown-script 
        primary-name="majelis_id" 
        secondary-name="kitab_id" 
        :primary-to-secondary-map="$majelisKitabMap ?? []" 
        :secondary-to-primary-map="$kitabMajelisMap ?? []"
        filter-secondary-fn="filterKitab"
        filter-primary-fn="filterMajelis"
    />
</x-app-layout>
