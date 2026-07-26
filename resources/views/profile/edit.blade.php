<x-app-layout>
    <x-slot name="header">Profil</x-slot>
<div class="space-y-6">

    {{-- HEADER --}}
    <div>
        <h1 class="text-xl font-bold text-gray-800">Profil Saya</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola informasi akun dan keamanan Anda</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- KOLOM KIRI: Avatar Card --}}
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center">
                {{-- Foto Profil --}}
                <div class="relative inline-block mb-4">
                    <img id="preview-foto"
                         src="{{ $user->foto_profil ? asset('storage/'.$user->foto_profil) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&color=14532D&background=dcfce7&size=200' }}"
                         alt="Foto Profil"
                         class="w-28 h-28 rounded-full object-cover border-4 border-green-100 shadow">
                    <label for="foto_profil_input"
                           class="absolute bottom-0 right-0 w-8 h-8 rounded-full flex items-center justify-center cursor-pointer shadow-md"
                           style="background: linear-gradient(135deg, #22C55E, #14532D);"
                           title="Ganti Foto">
                        <i class="fas fa-camera text-white text-xs"></i>
                    </label>
                </div>

                <h2 class="text-base font-bold text-gray-800">{{ $user->name }}</h2>
                <span class="inline-block mt-1 px-3 py-0.5 rounded-full text-xs font-semibold capitalize text-white"
                      style="background: linear-gradient(135deg, #22C55E, #14532D);">
                    {{ $user->role }}
                </span>
                <p class="text-xs text-gray-400 mt-2">{{ $user->email ?? '-' }}</p>

                {{-- Info tambahan di card kiri sesuai role --}}
                @if($user->role === 'santri' && $santri)
                <div class="mt-4 pt-4 border-t border-gray-100 space-y-1.5 text-left">
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <i class="fas fa-id-card w-4 text-center text-green-600"></i>
                        <span class="text-gray-400">NIS:</span>
                        <span class="font-semibold text-gray-700">{{ $santri->nis ?? '-' }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <i class="fas fa-mosque w-4 text-center text-green-600"></i>
                        <span class="text-gray-400">Majelis:</span>
                        <span class="font-semibold text-gray-700">{{ $santri->majelis->nama_majelis ?? '-' }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <i class="fas fa-layer-group w-4 text-center text-green-600"></i>
                        <span class="text-gray-400">Angkatan:</span>
                        <span class="font-semibold text-gray-700">{{ $santri->angkatan ?? '-' }}</span>
                    </div>
                </div>
                @elseif($user->role === 'ustadz' && $ustadz)
                <div class="mt-4 pt-4 border-t border-gray-100 space-y-1.5 text-left">
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <i class="fas fa-chalkboard-teacher w-4 text-center text-green-600"></i>
                        <span class="text-gray-400">Jabatan:</span>
                        <span class="font-semibold text-gray-700">Ustadz / Pengajar</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <i class="fas fa-book w-4 text-center text-green-600"></i>
                        <span class="text-gray-400">Majelis:</span>
                        <span class="font-semibold text-gray-700">{{ $ustadz->jadwals()->count() }} jadwal</span>
                    </div>
                </div>
                @endif

                <div class="mt-4 pt-4 border-t border-gray-100 text-xs text-gray-400">
                    <i class="fas fa-clock mr-1"></i> Bergabung {{ $user->created_at->translatedFormat('d M Y') }}
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: Form --}}
        <div class="lg:col-span-2 space-y-5">

            @if(session('status') === 'profile-updated')
            <div class="px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm flex items-center gap-2">
                <i class="fas fa-check-circle"></i> Profil berhasil diperbarui.
            </div>
            @endif

            {{-- Form Update Info Profil --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
                    <i class="fas fa-user-edit text-green-600"></i> Informasi Akun
                </h3>

                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('patch')

                    <input id="foto_profil_input" type="file" name="foto_profil" accept="image/*" class="hidden"
                           onchange="previewFoto(this)">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Nama --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 bg-gray-50 transition">
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 bg-gray-50 transition">
                            <x-input-error :messages="$errors->get('email')" class="mt-1" />
                        </div>

                        {{-- No HP --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">No. HP</label>
                            <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}"
                                   placeholder="08xxxxxxxxxx"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 bg-gray-50 transition">
                        </div>

                        {{-- Alamat --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Alamat</label>
                            <input type="text" name="alamat" value="{{ old('alamat', $user->alamat) }}"
                                   placeholder="Alamat tinggal"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 bg-gray-50 transition">
                        </div>
                    </div>

                    <div class="pt-1">
                        <button type="submit"
                                class="px-6 py-2.5 rounded-xl text-white text-sm font-semibold transition hover:opacity-90 active:scale-95"
                                style="background: linear-gradient(135deg, #22C55E, #14532D);">
                            <i class="fas fa-save mr-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            {{-- === DATA SANTRI (hanya jika santri) === --}}
            @if($user->role === 'santri' && $santri)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-700 mb-1 flex items-center gap-2">
                    <i class="fas fa-user-graduate text-green-600"></i> Data Santri
                </h3>
                <p class="text-xs text-gray-400 mb-4">Data berikut dikelola oleh admin. Anda hanya dapat mengubah data wali.</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                    {{-- Read-only: NIS --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">NIS <span class="text-gray-300 font-normal">(hanya baca)</span></label>
                        <div class="w-full border border-dashed border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 text-gray-500">
                            {{ $santri->nis ?? '-' }}
                        </div>
                    </div>
                    {{-- Read-only: Majelis --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Majelis <span class="text-gray-300 font-normal">(hanya baca)</span></label>
                        <div class="w-full border border-dashed border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 text-gray-500">
                            {{ $santri->majelis->nama_majelis ?? '-' }}
                        </div>
                    </div>
                    {{-- Read-only: Tempat Lahir --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Tempat Lahir <span class="text-gray-300 font-normal">(hanya baca)</span></label>
                        <div class="w-full border border-dashed border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 text-gray-500">
                            {{ $santri->tempat_lahir ?? '-' }}
                        </div>
                    </div>
                    {{-- Read-only: Tanggal Lahir --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Tanggal Lahir <span class="text-gray-300 font-normal">(hanya baca)</span></label>
                        <div class="w-full border border-dashed border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 text-gray-500">
                            {{ $santri->tanggal_lahir ? \Carbon\Carbon::parse($santri->tanggal_lahir)->translatedFormat('d F Y') : '-' }}
                        </div>
                    </div>
                    {{-- Read-only: Angkatan --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Angkatan <span class="text-gray-300 font-normal">(hanya baca)</span></label>
                        <div class="w-full border border-dashed border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 text-gray-500">
                            {{ $santri->angkatan ?? '-' }}
                        </div>
                    </div>
                </div>

                {{-- Editable: Data Wali --}}
                <div class="pt-4 border-t border-gray-100">
                    <p class="text-xs font-semibold text-gray-600 mb-3 flex items-center gap-1.5">
                        <i class="fas fa-pen text-green-500 text-[10px]"></i> Data yang dapat Anda ubah:
                    </p>
                    <form method="POST" action="{{ route('profile.update-santri') }}" class="space-y-4">
                        @csrf @method('patch')
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Wali</label>
                                <input type="text" name="nama_wali" value="{{ old('nama_wali', $santri->nama_wali) }}"
                                       placeholder="Nama orang tua/wali"
                                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 bg-gray-50 transition">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">No. HP Wali</label>
                                <input type="text" name="no_hp_wali" value="{{ old('no_hp_wali', $santri->no_hp_wali) }}"
                                       placeholder="08xxxxxxxxxx"
                                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 bg-gray-50 transition">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $santri->tempat_lahir) }}"
                                       placeholder="Kota tempat lahir"
                                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 bg-gray-50 transition">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir"
                                       value="{{ old('tanggal_lahir', $santri->tanggal_lahir ? \Carbon\Carbon::parse($santri->tanggal_lahir)->format('Y-m-d') : '') }}"
                                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 bg-gray-50 transition">
                            </div>
                        </div>
                        <button type="submit"
                                class="px-6 py-2.5 rounded-xl text-white text-sm font-semibold transition hover:opacity-90 active:scale-95"
                                style="background: linear-gradient(135deg, #22C55E, #14532D);">
                            <i class="fas fa-save mr-1"></i> Simpan Data
                        </button>
                    </form>
                </div>
            </div>
            @endif

            {{-- === DATA USTADZ (hanya jika ustadz) === --}}
            @if($user->role === 'ustadz' && $ustadz)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-700 mb-1 flex items-center gap-2">
                    <i class="fas fa-chalkboard-teacher text-green-600"></i> Data Pengajar
                </h3>
                <p class="text-xs text-gray-400 mb-4">Informasi jadwal mengajar Anda di sistem.</p>

                @php
                    $jadwalUstadz = $ustadz->jadwals()->with(['majelis','kitab'])->get();
                @endphp

                @if($jadwalUstadz->isEmpty())
                <div class="py-6 text-center text-gray-400 text-sm">
                    <i class="fas fa-calendar-times text-3xl mb-2 block text-gray-200"></i>
                    Belum ada jadwal mengajar yang terdaftar.
                </div>
                @else
                <div class="space-y-2">
                    @foreach($jadwalUstadz as $jdwl)
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 border border-gray-100">
                        <div class="w-9 h-9 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-book text-green-700 text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800">{{ $jdwl->kitab->nama_kitab ?? '-' }}</p>
                            <p class="text-xs text-gray-500">{{ $jdwl->majelis->nama_majelis ?? '-' }} &bull; {{ $jdwl->hari }}, {{ \Carbon\Carbon::parse($jdwl->jam_mulai)->format('H:i') }}–{{ \Carbon\Carbon::parse($jdwl->jam_selesai)->format('H:i') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endif

            {{-- Form Ganti Password --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
                    <i class="fas fa-lock text-green-600"></i> Ganti Password
                </h3>

                @if(session('status') === 'password-updated')
                <div class="mb-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm flex items-center gap-2">
                    <i class="fas fa-check-circle"></i> Password berhasil diperbarui.
                </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                    @csrf
                    @method('put')

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Password Lama</label>
                            <div class="relative">
                                <input type="password" id="pwd_current" name="current_password" autocomplete="current-password"
                                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 bg-gray-50 transition"
                                       placeholder="••••••••">
                                <button type="button" onclick="togglePwd('pwd_current', this)"
                                    class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600 transition-colors">
                                    <i class="fas fa-eye text-sm"></i>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Password Baru</label>
                            <div class="relative">
                                <input type="password" id="pwd_new" name="password" autocomplete="new-password"
                                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 bg-gray-50 transition"
                                       placeholder="••••••••">
                                <button type="button" onclick="togglePwd('pwd_new', this)"
                                    class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600 transition-colors">
                                    <i class="fas fa-eye text-sm"></i>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Konfirmasi Password</label>
                            <div class="relative">
                                <input type="password" id="pwd_confirm" name="password_confirmation" autocomplete="new-password"
                                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 bg-gray-50 transition"
                                       placeholder="••••••••">
                                <button type="button" onclick="togglePwd('pwd_confirm', this)"
                                    class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600 transition-colors">
                                    <i class="fas fa-eye text-sm"></i>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1" />
                        </div>
                    </div>

                    <div class="pt-1">
                        <button type="submit"
                                class="px-6 py-2.5 rounded-xl text-white text-sm font-semibold transition hover:opacity-90 active:scale-95"
                                style="background: linear-gradient(135deg, #22C55E, #14532D);">
                            <i class="fas fa-key mr-1"></i> Perbarui Password
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
    function previewFoto(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('preview-foto').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
            input.closest('form') && input.closest('form').submit();
        }
    }

    function togglePwd(fieldId, btn) {
        const input = document.getElementById(fieldId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
</script>
</x-app-layout>
