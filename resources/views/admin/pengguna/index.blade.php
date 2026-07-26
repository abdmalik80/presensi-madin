<x-app-layout>
    <x-slot name="header">Manajemen Pengguna</x-slot>

    <div class="space-y-4 pb-8 md:pb-0" x-data="{ 
        showFilterModal: false,
        selectedIds: [],
        selectAllPages: false,
        isPageAllChecked: false,
        allIds: {{ json_encode($pengguna->pluck('id')) }},
        pageIds: {{ json_encode($pengguna->pluck('id')) }},
        
        togglePageAll(checked) {
            this.isPageAllChecked = checked;
            if (checked) {
                this.pageIds.forEach(id => {
                    if (!this.selectedIds.includes(id)) this.selectedIds.push(id);
                });
            } else {
                this.selectedIds = this.selectedIds.filter(id => !this.pageIds.includes(id));
                this.selectAllPages = false;
            }
        },
        selectEveryPage() {
            this.selectedIds = [...this.allIds];
            this.selectAllPages = true;
            this.isPageAllChecked = true;
        },
        clearAll() {
            this.selectedIds = [];
            this.selectAllPages = false;
            this.isPageAllChecked = false;
        }
    }">
        <x-alert />

        {{-- DESKTOP VIEW --}}
        {{-- Header Bar Desktop --}}
        <div class="hidden md:flex flex-col sm:flex-row items-start sm:items-end justify-between gap-3 mb-4" style="position: relative; z-index: 40;">
            <form method="GET" class="flex items-end gap-2">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1 pl-1">Cari Pengguna</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama, username, email..."
                        class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent bg-white/80 w-64 lg:w-80 text-ellipsis h-[38px]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1 pl-1">Role</label>
                    <x-custom-select name="role" :options="['admin'=>'Admin','ustadz'=>'Ustadz','santri'=>'Santri']" :selected="request('role')" placeholder="Semua Role" />
                </div>
                <button type="submit" class="bg-hijau-tua text-white px-4 py-2 rounded-lg text-sm hover:bg-hijau-muda transition-colors h-[38px] flex items-center gap-1.5">
                    <i class="fas fa-search"></i> Tampilkan
                </button>
                @if(request()->hasAny(['search', 'role']))
                    <a href="{{ route('admin.pengguna.index') }}" class="px-4 py-2 rounded-lg text-sm border border-gray-300 bg-white text-gray-600 hover:bg-gray-50 transition-colors h-[38px] flex items-center justify-center">
                        <i class="fas fa-times mr-1"></i> Reset
                    </a>
                @endif
            </form>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.pengguna.create') }}"
                   class="inline-flex items-center gap-2 bg-hijau-tua text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-hijau-muda transition-colors shadow-sm whitespace-nowrap h-[38px]">
                    <i class="fas fa-user-plus"></i> Tambah Pengguna
                </a>
            </div>
        </div>

        {{-- Stats Cards --}}
        @php
            $totalAdmin  = \App\Models\User::where('role', 'admin')->count();
            $totalUstadz = \App\Models\User::where('role', 'ustadz')->count();
            $totalSantri = \App\Models\User::where('role', 'santri')->count();
        @endphp
        <div class="flex items-center gap-2 w-full">
            <div class="flex-1 bg-white/60 backdrop-blur rounded-xl border border-white/50 px-3 py-2 flex items-center justify-center gap-1.5">
                <i class="fas fa-user-shield text-purple-500 text-xs"></i>
                <span class="text-xs text-gray-500">Admin</span>
                <span class="text-xs font-bold text-gray-800">{{ $totalAdmin }}</span>
            </div>
            <div class="flex-1 bg-white/60 backdrop-blur rounded-xl border border-white/50 px-3 py-2 flex items-center justify-center gap-1.5">
                <i class="fas fa-chalkboard-teacher text-blue-500 text-xs"></i>
                <span class="text-xs text-gray-500">Ustadz</span>
                <span class="text-xs font-bold text-gray-800">{{ $totalUstadz }}</span>
            </div>
            <div class="flex-1 bg-white/60 backdrop-blur rounded-xl border border-white/50 px-3 py-2 flex items-center justify-center gap-1.5">
                <i class="fas fa-user-graduate text-green-600 text-xs"></i>
                <span class="text-xs text-gray-500">Santri</span>
                <span class="text-xs font-bold text-gray-800">{{ $totalSantri }}</span>
            </div>
        </div>

        {{-- Desktop Table --}}
                {{-- Bulk Actions Banner --}}
        <div x-show="selectedIds.length > 0" style="display: none;" x-transition.opacity
             class="mb-4 bg-red-50 border border-red-100 rounded-xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-red-700">
                    <i class="fas fa-check-square mr-1"></i>
                    <span x-text="selectedIds.length"></span>
                    <span x-text="selectAllPages ? ' pengguna (semua data) dipilih' : ' pengguna dipilih'"></span>
                </p>
                <p x-show="isPageAllChecked && !selectAllPages && allIds.length > pageIds.length"
                   class="text-xs text-red-600 mt-0.5">
                    Semua {{ $pengguna->count() }} pengguna di halaman ini dipilih.
                    <button type="button" @click="selectEveryPage()"
                            class="font-bold underline hover:text-red-800 ml-1">
                        Pilih semua <span x-text="allIds.length"></span> pengguna?
                    </button>
                </p>
                <p x-show="selectAllPages" class="text-xs text-red-600 mt-0.5">
                    Semua <span x-text="allIds.length"></span> pengguna dipilih.
                    <button type="button" @click="clearAll()" class="font-bold underline hover:text-red-800 ml-1">Batalkan pilihan</button>
                </p>
            </div>
            
            <form method="POST" action="{{ route('admin.pengguna.bulk-action') }}" id="bulkActionFormPengguna">
                @csrf
                <input type="hidden" name="action" id="bulkActionTypePengguna" value="">
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                <div class="flex gap-2">
                    <button type="button" onclick="document.getElementById('bulkActionTypePengguna').value='set_aktif'; document.getElementById('bulkActionFormPengguna').submit();"
                            class="bg-[#187a41] hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold shadow-sm transition-colors">
                        <i class="fas fa-check-circle mr-1"></i> Set Aktif
                    </button>
                    <button type="button" onclick="document.getElementById('bulkActionTypePengguna').value='set_nonaktif'; document.getElementById('bulkActionFormPengguna').submit();"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold shadow-sm transition-colors">
                        <i class="fas fa-times-circle mr-1"></i> Set Non-Aktif
                    </button>
                    <button type="button" onclick="if(confirm('Yakin ingin menghapus data yang dipilih?')) { document.getElementById('bulkActionTypePengguna').value='delete'; document.getElementById('bulkActionFormPengguna').submit(); }"
                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold shadow-sm transition-colors">
                        <i class="fas fa-trash-alt mr-1"></i> Hapus
                    </button>
                </div>
            </form>
        </div>
<div class="hidden md:block bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 overflow-hidden" style="position: relative; z-index: 10;">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-hijau-tua text-white">
                    <tr>
                        <th class="px-4 py-3 text-left w-10">
                            <input type="checkbox"
                                   :checked="isPageAllChecked"
                                   @change="togglePageAll($event.target.checked)"
                                   class="rounded border-gray-300 text-hijau-muda focus:ring-hijau-muda cursor-pointer"
                                   title="Pilih semua di halaman ini">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Foto</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Username</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Role</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pengguna as $user)
                    <tr class="group hover:bg-gray-100 transition-colors"
                        :class="selectedIds.includes({{ $user->id }}) ? 'bg-red-50/70' : ''">
                        <td class="px-4 py-3 w-10 transition-opacity duration-200"
                            :class="selectedIds.includes({{ $user->id }}) || selectedIds.length > 0 ? 'opacity-100' : 'opacity-0 group-hover:opacity-100 focus-within:opacity-100'">
                            <input type="checkbox"
                                   :checked="selectedIds.includes({{ $user->id }})"
                                   @change="if($event.target.checked) { selectedIds.push({{ $user->id }}) } else { selectedIds = selectedIds.filter(id => id !== {{ $user->id }}); selectAllPages = false; isPageAllChecked = false; }"
                                   class="rounded border-gray-300 text-hijau-muda focus:ring-hijau-muda cursor-pointer">
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $loop->iteration + ($pengguna->currentPage()-1)*$pengguna->perPage() }}</td>
                        <td class="px-4 py-3">
                            <img src="{{ $user->foto_profil ? asset('storage/'.$user->foto_profil) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&color=14532D&background=F1F5F9' }}"
                                 class="w-9 h-9 rounded-full object-cover border-2 border-hijau-muda/30" alt="">
                        </td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 font-mono">{{ $user->username }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $user->email ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @php
                                $roleColor = match($user->role) {
                                    'admin'  => 'bg-purple-100 text-purple-700',
                                    'ustadz' => 'bg-blue-100 text-blue-700',
                                    'santri' => 'bg-green-100 text-green-700',
                                    default  => 'bg-gray-100 text-gray-600',
                                };
                                $roleLabel = match($user->role) {
                                    'admin'  => 'Admin',
                                    'ustadz' => 'Ustadz',
                                    'santri' => 'Santri',
                                    default  => ucfirst($user->role),
                                };
                            @endphp
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $roleColor }}">{{ $roleLabel }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @if($user->status_aktif)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-hijau-muda/20 text-green-800">Aktif</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">Non-Aktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1">
                                <a href="{{ route('admin.pengguna.show', $user) }}" class="p-1.5 rounded-lg text-hijau-tua hover:bg-hijau-muda/20 transition-colors" title="Detail"><i class="fas fa-eye text-sm"></i></a>
                                <a href="{{ route('admin.pengguna.edit', $user) }}" class="p-1.5 rounded-lg text-biru hover:bg-biru/10 transition-colors" title="Edit"><i class="fas fa-edit text-sm"></i></a>
                                @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.pengguna.destroy', $user) }}" class="inline"
                                      onsubmit="return confirm('Yakin hapus pengguna {{ $user->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-merah hover:bg-red-50 transition-colors" title="Hapus"><i class="fas fa-trash text-sm"></i></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-gray-400">
                            <i class="fas fa-users text-4xl mb-3 block text-gray-200"></i>
                            Tidak ada data pengguna.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @if($pengguna->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-white/40">
                {{ $pengguna->appends(request()->query())->links() }}
            </div>
            @endif
        </div>

        {{-- MOBILE VIEW --}}
        <div class="md:hidden flex items-center gap-2 mb-4 bg-white p-3 rounded-xl shadow-sm border border-gray-100 sticky top-0 z-30">
            <form method="GET" class="flex-1 relative flex items-center">
                <i class="fas fa-search absolute left-3 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama, username..."
                       class="w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-hijau-tua focus:border-transparent bg-gray-50/50">
                <input type="hidden" name="role" value="{{ request('role') }}">
            </form>
            <button type="button" @click="showFilterModal = true" class="p-2 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 flex-shrink-0 flex items-center justify-center w-10 h-10 bg-white">
                <i class="fas fa-sliders-h"></i>
            </button>
        </div>

        <div class="md:hidden flex items-center justify-between mb-3 px-1">
            <div>
                <h2 class="text-sm font-bold text-gray-800">Manajemen Pengguna</h2>
                <p class="text-[10px] text-gray-500">Total {{ $pengguna->total() }} Pengguna</p>
            </div>
            <div class="flex items-center gap-2">
                @if(request()->hasAny(['search','role']))
                <a href="{{ route('admin.pengguna.index') }}" class="px-2 py-1 text-[10px] font-semibold text-red-600 bg-red-50 border border-red-200 rounded-full">
                    <i class="fas fa-times mr-0.5"></i> Reset
                </a>
                @endif
                <a href="{{ route('admin.pengguna.create') }}" class="inline-flex items-center gap-1.5 bg-[#187a41] text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-green-700 transition-colors shadow-sm">
                    <i class="fas fa-user-plus"></i> Tambah
                </a>
            </div>
        </div>

        <div class="md:hidden space-y-3">
            @forelse($pengguna as $user)
            @php
                $roleBadge = match($user->role) {
                    'admin'  => 'bg-purple-100 text-purple-700',
                    'ustadz' => 'bg-blue-100 text-blue-700',
                    'santri' => 'bg-green-100 text-[#187a41]',
                    default  => 'bg-gray-100 text-gray-600',
                };
                $roleLabel = match($user->role) {
                    'admin'  => 'Admin',
                    'ustadz' => 'Ustadz',
                    'santri' => 'Santri',
                    default  => ucfirst($user->role),
                };
            @endphp
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 relative flex flex-col gap-3"
                 x-data="{ pressTimer: null }"
                 @touchstart="pressTimer = setTimeout(() => { if(!selectedIds.includes({{ $user->id }})) { selectedIds.push({{ $user->id }}) } }, 500)"
                 @touchend="clearTimeout(pressTimer)"
                 @touchmove="clearTimeout(pressTimer)"
                 :class="selectedIds.includes({{ $user->id }}) ? 'bg-red-50' : ''">
                
                {{-- Checkbox for Mobile --}}
                <div class="absolute left-2 top-4 z-10"
                     x-show="selectedIds.length > 0" style="display: none;"
                     x-transition.opacity>
                    <input type="checkbox"
                           :checked="selectedIds.includes({{ $user->id }})"
                           @change="if($event.target.checked) { selectedIds.push({{ $user->id }}) } else { selectedIds = selectedIds.filter(id => id !== {{ $user->id }}); selectAllPages = false; isPageAllChecked = false; }"
                           class="rounded border-gray-300 text-[#187a41] focus:ring-[#187a41] shadow-sm w-5 h-5">
                </div>
                <div class="flex items-start gap-3 transition-transform duration-300"
                     :class="selectedIds.length > 0 ? 'translate-x-8' : ''">
                    {{-- Avatar & Status --}}
                    <div class="relative shrink-0">
                        <img src="{{ $user->foto_profil ? asset('storage/'.$user->foto_profil) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&color=14532D&background=F1F5F9' }}"
                             class="w-12 h-12 rounded-full object-cover border-2 border-gray-50" alt="">
                        @if($user->status_aktif)
                            <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full" title="Aktif"></div>
                        @else
                            <div class="absolute bottom-0 right-0 w-3 h-3 bg-red-500 border-2 border-white rounded-full" title="Non-Aktif"></div>
                        @endif
                    </div>
                    
                    {{-- User Info --}}
                    <div class="flex-1 min-w-0 pt-0.5">
                        <h4 class="text-sm font-bold text-gray-800 truncate leading-tight">{{ $user->name }}</h4>
                        <p class="text-xs text-gray-500 font-mono mt-1 truncate">{{ $user->username }}</p>
                        @if($user->email)
                            <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $user->email }}</p>
                        @endif
                    </div>
                    
                    {{-- Role Badge --}}
                    <div class="shrink-0">
                        <span class="px-2.5 py-1 text-[10px] font-bold rounded-md {{ $roleBadge }} uppercase tracking-wider">{{ $roleLabel }}</span>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-2 pt-3 border-t border-gray-50">
                    <a href="{{ route('admin.pengguna.show', $user) }}" class="flex-1 flex justify-center items-center gap-1.5 py-2 text-xs font-semibold text-hijau-tua bg-hijau-tua/5 rounded-lg hover:bg-hijau-tua/10 transition-colors">
                        <i class="fas fa-eye text-[10px]"></i> Detail
                    </a>
                    <a href="{{ route('admin.pengguna.edit', $user) }}" class="flex-1 flex justify-center items-center gap-1.5 py-2 text-xs font-semibold text-biru bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                        <i class="fas fa-edit text-[10px]"></i> Edit
                    </a>
                    @if($user->id !== auth()->id())
                    <form method="POST" action="{{ route('admin.pengguna.destroy', $user) }}" class="flex-1" onsubmit="return confirm('Yakin hapus pengguna ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full flex justify-center items-center gap-1.5 py-2 text-xs font-semibold text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                            <i class="fas fa-trash text-[10px]"></i> Hapus
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-12 bg-white rounded-xl shadow-sm border border-gray-100">
                <i class="fas fa-users text-4xl text-gray-200 mb-3"></i>
                <p class="text-sm text-gray-500 font-medium">Belum ada data pengguna</p>
            </div>
            @endforelse
        </div>

        {{-- Pagination Mobile --}}
        @if($pengguna->hasPages())
        <div class="md:hidden mt-4 pb-8 px-1">
            <div class="flex items-center justify-between bg-white rounded-xl shadow-sm border border-gray-100 p-2">
                @if ($pengguna->onFirstPage())
                    <span class="p-2 w-10 h-10 flex items-center justify-center rounded-lg text-gray-300"><i class="fas fa-chevron-left"></i></span>
                @else
                    <a href="{{ $pengguna->appends(request()->query())->previousPageUrl() }}" class="p-2 w-10 h-10 flex items-center justify-center rounded-lg text-[#187a41] hover:bg-green-50 transition-colors"><i class="fas fa-chevron-left"></i></a>
                @endif
                <span class="text-xs font-semibold text-gray-600">Hal {{ $pengguna->currentPage() }} dari {{ $pengguna->lastPage() }}</span>
                @if ($pengguna->hasMorePages())
                    <a href="{{ $pengguna->appends(request()->query())->nextPageUrl() }}" class="p-2 w-10 h-10 flex items-center justify-center rounded-lg text-[#187a41] hover:bg-green-50 transition-colors"><i class="fas fa-chevron-right"></i></a>
                @else
                    <span class="p-2 w-10 h-10 flex items-center justify-center rounded-lg text-gray-300"><i class="fas fa-chevron-right"></i></span>
                @endif
            </div>
        </div>
        @endif

        {{-- Filter Modal Mobile --}}
        <div x-show="showFilterModal" x-effect="document.body.style.overflow = showFilterModal ? 'hidden' : ''" class="md:hidden fixed inset-0 z-50 flex flex-col justify-end bg-black/50" x-transition.opacity style="display:none">
            <div @click.away="showFilterModal = false" class="bg-white w-full rounded-t-2xl shadow-xl overflow-hidden"
                 x-show="showFilterModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full">
                <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-gray-800">Filter Role</h3>
                    <button @click="showFilterModal = false" class="text-gray-400 bg-gray-50 rounded-full w-8 h-8 flex items-center justify-center"><i class="fas fa-times"></i></button>
                </div>
                <div class="p-5">
                    <form method="GET" action="{{ route('admin.pengguna.index') }}" x-data="{ selectedRole: '{{ request('role') }}' }">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <div class="grid grid-cols-2 gap-2 mb-4">
                            <label class="flex items-center gap-2 p-2 border rounded-lg cursor-pointer transition-colors" 
                                   :class="selectedRole === '' ? 'border-[#187a41] bg-green-50' : 'border-gray-200'">
                                <input type="radio" name="role" value="" class="sr-only" x-model="selectedRole">
                                <span class="text-xs font-medium" :class="selectedRole === '' ? 'text-[#187a41]' : 'text-gray-600'">Semua</span>
                            </label>
                            <label class="flex items-center gap-2 p-2 border rounded-lg cursor-pointer transition-colors"
                                   :class="selectedRole === 'admin' ? 'border-purple-500 bg-purple-50' : 'border-gray-200'">
                                <input type="radio" name="role" value="admin" class="sr-only" x-model="selectedRole">
                                <i class="fas fa-user-shield text-xs" :class="selectedRole === 'admin' ? 'text-purple-600' : 'text-purple-400'"></i>
                                <span class="text-xs font-medium" :class="selectedRole === 'admin' ? 'text-purple-600' : 'text-gray-600'">Admin</span>
                            </label>
                            <label class="flex items-center gap-2 p-2 border rounded-lg cursor-pointer transition-colors"
                                   :class="selectedRole === 'ustadz' ? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
                                <input type="radio" name="role" value="ustadz" class="sr-only" x-model="selectedRole">
                                <i class="fas fa-chalkboard-teacher text-xs" :class="selectedRole === 'ustadz' ? 'text-blue-600' : 'text-blue-400'"></i>
                                <span class="text-xs font-medium" :class="selectedRole === 'ustadz' ? 'text-blue-600' : 'text-gray-600'">Ustadz</span>
                            </label>
                            <label class="flex items-center gap-2 p-2 border rounded-lg cursor-pointer transition-colors"
                                   :class="selectedRole === 'santri' ? 'border-[#187a41] bg-green-50' : 'border-gray-200'">
                                <input type="radio" name="role" value="santri" class="sr-only" x-model="selectedRole">
                                <i class="fas fa-user-graduate text-xs" :class="selectedRole === 'santri' ? 'text-[#187a41]' : 'text-green-500'"></i>
                                <span class="text-xs font-medium" :class="selectedRole === 'santri' ? 'text-[#187a41]' : 'text-gray-600'">Santri</span>
                            </label>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" @click="window.location.href='{{ route('admin.pengguna.index') }}'" class="flex-1 py-2.5 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Reset Filter</button>
                            <button type="submit" class="flex-1 py-2.5 text-xs font-semibold text-white bg-[#187a41] hover:bg-green-700 rounded-xl transition-colors shadow-sm">Terapkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>



