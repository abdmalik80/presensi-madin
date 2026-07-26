<x-app-layout>
    <x-slot name="header">Edit Ustadz</x-slot>
    <div class="max-w-2xl mx-auto">
        <div class="bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 p-8">
            <form method="POST" action="{{ route('admin.ustadz.update', $ustadz) }}" enctype="multipart/form-data" class="space-y-5">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $ustadz->user->name) }}" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Username <span class="text-red-500">*</span></label>
                        <input type="text" name="username" value="{{ old('username', $ustadz->user->username) }}" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-gray-400 font-normal">(kosongkan jika tidak diubah)</span></label>
                        <input type="password" name="password" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                        <input type="text" name="no_hp" value="{{ old('no_hp', $ustadz->user->no_hp) }}" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                        @php $jkOptions = ['L' => 'Laki-laki', 'P' => 'Perempuan']; @endphp
                        <x-custom-select name="jenis_kelamin" :options="$jkOptions" :selected="old('jenis_kelamin', $ustadz->user->jenis_kelamin)" placeholder="-- Pilih --" width="w-full" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <textarea name="alamat" rows="2" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent">{{ old('alamat', $ustadz->user->alamat) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto Profil</label>
                    @if($ustadz->user->foto_profil)
                        <img src="{{ asset('storage/'.$ustadz->user->foto_profil) }}" class="w-16 h-16 rounded-full object-cover mb-2 border border-gray-200">
                    @endif
                    <input type="file" name="foto_profil" accept="image/*" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm">
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="status_aktif" value="1" {{ $ustadz->user->status_aktif ? 'checked' : '' }} class="w-4 h-4 accent-hijau-tua">
                    <span class="text-sm text-gray-700">Status Aktif</span>
                    <input type="hidden" name="_status_sent" value="1">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="bg-hijau-tua text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-hijau-muda transition-colors"><i class="fas fa-save mr-2"></i> Update</button>
                    <a href="{{ route('admin.ustadz.index') }}" class="border border-gray-200 text-gray-600 px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
