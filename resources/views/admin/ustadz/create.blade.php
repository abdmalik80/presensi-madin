<x-app-layout>
    <x-slot name="header">Tambah Ustadz</x-slot>
    <div class="max-w-2xl mx-auto">
        <div class="bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 p-8">
            <form method="POST" action="{{ route('admin.ustadz.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Username <span class="text-red-500">*</span></label>
                        <input type="text" name="username" value="{{ old('username') }}" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                        <input type="text" name="no_hp" value="{{ old('no_hp') }}" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                        @php $jkOptions = ['L' => 'Laki-laki', 'P' => 'Perempuan']; @endphp
                        <x-custom-select name="jenis_kelamin" :options="$jkOptions" :selected="old('jenis_kelamin')" placeholder="-- Pilih --" width="w-full" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <textarea name="alamat" rows="2" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent">{{ old('alamat') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto Profil</label>
                    <input type="file" name="foto_profil" accept="image/*" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm">
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="status_aktif" value="0">
                    <input type="checkbox" name="status_aktif" value="1" checked class="w-4 h-4 accent-hijau-tua">
                    <span class="text-sm text-gray-700">Status Aktif</span>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="bg-hijau-tua text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-hijau-muda transition-colors"><i class="fas fa-save mr-2"></i> Simpan</button>
                    <a href="{{ route('admin.ustadz.index') }}" class="border border-gray-200 text-gray-600 px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
