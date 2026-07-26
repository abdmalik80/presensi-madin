<x-app-layout>
    <x-slot name="header">Edit Jadwal</x-slot>
    <div class="max-w-xl mx-auto">
        <div class="bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 p-8">
            <form method="POST" action="{{ route('admin.jadwal.update', $jadwal) }}" class="space-y-5">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Majelis <span class="text-red-500">*</span></label>
                        @php
                            $majelisOptions = [];
                            foreach($majelis as $m) $majelisOptions[$m->id] = $m->nama_majelis;
                        @endphp
                        <x-custom-select name="majelis_id" :options="$majelisOptions" :selected="old('majelis_id', $jadwal->majelis_id)" placeholder="-- Pilih Majelis --" width="w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kitab <span class="text-red-500">*</span></label>
                        @php
                            $kitabOptions = [];
                            foreach($kitabs as $k) $kitabOptions[$k->id] = $k->nama_kitab;
                        @endphp
                        <x-custom-select name="kitab_id" :options="$kitabOptions" :selected="old('kitab_id', $jadwal->kitab_id)" placeholder="-- Pilih Kitab --" width="w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ustadz <span class="text-red-500">*</span></label>
                        @php
                            $ustadzOptions = [];
                            foreach($ustadzs as $u) $ustadzOptions[$u->id] = $u->user->name;
                        @endphp
                        <x-custom-select name="ustadz_id" :options="$ustadzOptions" :selected="old('ustadz_id', $jadwal->ustadz_id)" placeholder="-- Pilih Ustadz --" width="w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hari <span class="text-red-500">*</span></label>
                        @php
                            $hariOptions = ['Senin'=>'Senin','Selasa'=>'Selasa','Rabu'=>'Rabu','Kamis'=>'Kamis','Jumat'=>'Jumat','Sabtu'=>'Sabtu','Ahad'=>'Ahad'];
                        @endphp
                        <x-custom-select name="hari" :options="$hariOptions" :selected="old('hari', $jadwal->hari)" placeholder="-- Pilih Hari --" width="w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai</label>
                        <input type="time" name="jam_mulai" value="{{ old('jam_mulai', $jadwal->jam_mulai) }}" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai</label>
                        <input type="time" name="jam_selesai" value="{{ old('jam_selesai', $jadwal->jam_selesai) }}" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea name="keterangan" rows="2" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent">{{ old('keterangan', $jadwal->keterangan) }}</textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="bg-hijau-tua text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-hijau-muda transition-colors"><i class="fas fa-save mr-2"></i> Update</button>
                    <a href="{{ route('admin.jadwal.index') }}" class="border border-gray-200 text-gray-600 px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
