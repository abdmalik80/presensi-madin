<x-app-layout>
    <x-slot name="header">Tambah Majelis</x-slot>
    <div class="max-w-xl mx-auto">
        <div class="bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 p-8">
            <form method="POST" action="{{ route('admin.majelis.store') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Majelis <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_majelis" value="{{ old('nama_majelis') }}" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent">
                    @error('nama_majelis')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tempat</label>
                    <input type="text" name="tempat" value="{{ old('tempat') }}" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Koordinator</label>
                    <input type="text" name="koordinator" value="{{ old('koordinator') }}" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea name="keterangan" rows="3" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent">{{ old('keterangan') }}</textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="bg-hijau-tua text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-hijau-muda transition-colors"><i class="fas fa-save mr-2"></i> Simpan</button>
                    <a href="{{ route('admin.majelis.index') }}" class="border border-gray-200 text-gray-600 px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
