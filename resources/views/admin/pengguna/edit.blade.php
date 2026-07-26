<x-app-layout>
    <x-slot name="header">Edit Pengguna</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 p-6">

            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                <div class="w-10 h-10 rounded-xl bg-biru/10 flex items-center justify-center">
                    <i class="fas fa-user-edit text-biru"></i>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-gray-800">Edit Pengguna</h2>
                    <p class="text-xs text-gray-400">{{ $pengguna->name }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.pengguna.update', $pengguna) }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')

                {{-- Foto Profil --}}
                <div class="flex flex-col items-center gap-3 py-2">
                    <div class="w-24 h-24 rounded-full border-4 border-hijau-muda/30 overflow-hidden bg-gray-100">
                        <img id="previewImg"
                             src="{{ $pengguna->foto_profil ? asset('storage/'.$pengguna->foto_profil) : 'https://ui-avatars.com/api/?name='.urlencode($pengguna->name).'&color=14532D&background=F1F5F9' }}"
                             class="w-full h-full object-cover" alt="">
                    </div>
                    <label class="cursor-pointer inline-flex items-center gap-2 text-sm text-hijau-tua hover:text-hijau-muda font-medium">
                        <i class="fas fa-camera"></i> Ganti Foto
                        <input type="file" name="foto_profil" accept="image/*" class="hidden" onchange="previewPhoto(this)">
                    </label>
                    @error('foto_profil')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Nama --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $pengguna->name) }}"
                               class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent bg-white/80 @error('name') border-red-400 @enderror">
                        @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Username --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Username <span class="text-red-500">*</span></label>
                        <input type="text" name="username" value="{{ old('username', $pengguna->username) }}"
                               class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent bg-white/80 font-mono @error('username') border-red-400 @enderror">
                        @error('username')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $pengguna->email) }}"
                               class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent bg-white/80 @error('email') border-red-400 @enderror">
                        @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru <span class="text-gray-400 text-xs">(kosongkan jika tidak diubah)</span></label>
                        <input type="password" name="password"
                               class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent bg-white/80 @error('password') border-red-400 @enderror"
                               placeholder="Minimal 6 karakter">
                        @error('password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation"
                               class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent bg-white/80"
                               placeholder="Ulangi password baru">
                    </div>

                    {{-- Role --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
                        @php
                            $roleOptions = ['admin' => 'Admin', 'ustadz' => 'Ustadz', 'santri' => 'Santri'];
                        @endphp
                        @if($pengguna->id === auth()->id())
                            <x-custom-select name="role_disabled" :options="$roleOptions" :selected="old('role', $pengguna->role)" placeholder="-- Pilih Role --" width="w-full pointer-events-none opacity-60" />
                            <input type="hidden" name="role" value="{{ $pengguna->role }}">
                            <p class="text-xs text-amber-600 mt-1"><i class="fas fa-info-circle"></i> Role tidak dapat diubah untuk akun sendiri.</p>
                        @else
                            <x-custom-select name="role" :options="$roleOptions" :selected="old('role', $pengguna->role)" placeholder="-- Pilih Role --" width="w-full" />
                        @endif
                        @error('role')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Jenis Kelamin --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                        @php
                            $jkOptions = ['L' => 'Laki-laki', 'P' => 'Perempuan'];
                        @endphp
                        <x-custom-select name="jenis_kelamin" :options="$jkOptions" :selected="old('jenis_kelamin', $pengguna->jenis_kelamin)" placeholder="-- Pilih --" width="w-full" />
                    </div>

                    {{-- No HP --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                        <input type="text" name="no_hp" value="{{ old('no_hp', $pengguna->no_hp) }}"
                               class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent bg-white/80"
                               placeholder="08xxxxxxxxxx">
                    </div>

                    {{-- Status Aktif --}}
                    <div class="flex items-center gap-3 pt-2">
                        <input type="checkbox" name="status_aktif" id="status_aktif" value="1"
                               {{ old('status_aktif', $pengguna->status_aktif) ? 'checked' : '' }}
                               class="w-4 h-4 rounded text-hijau-tua border-gray-300 focus:ring-hijau-tua">
                        <label for="status_aktif" class="text-sm font-medium text-gray-700">Akun Aktif</label>
                        <input type="hidden" name="_status_sent" value="1">
                    </div>

                    {{-- Alamat --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                        <textarea name="alamat" rows="2"
                                  class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent bg-white/80 resize-none"
                                  placeholder="Alamat lengkap">{{ old('alamat', $pengguna->alamat) }}</textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                    <a href="{{ route('admin.pengguna.index') }}"
                       class="px-5 py-2 rounded-lg border border-gray-300 text-sm text-gray-600 bg-white hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-2 rounded-lg bg-hijau-tua text-white text-sm font-medium hover:bg-hijau-muda transition-colors shadow-sm">
                        <i class="fas fa-save mr-1"></i> Perbarui
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewPhoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById('previewImg').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-app-layout>
