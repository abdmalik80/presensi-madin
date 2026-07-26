<x-app-layout>
    <x-slot name="header">Semua Notifikasi</x-slot>

    <div class="max-w-3xl mx-auto space-y-6">
        
        <div class="bg-white/70 backdrop-blur-xl rounded-2xl border border-white/50 shadow-sm p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-bell text-hijau-tua"></i> Notifikasi Anda
                </h2>
                <p class="text-sm text-gray-500 mt-1">Menampilkan aktivitas selama 7 hari terakhir.</p>
            </div>
            
            <div class="flex flex-col sm:flex-row items-center gap-2 sm:gap-3 w-full sm:w-auto">
                {{-- Tombol Kembali --}}
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}" 
                   class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100/80 border border-gray-200 rounded-xl hover:bg-gray-200 transition-colors text-center inline-flex justify-center items-center gap-1.5">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>

                {{-- Tombol Tandai Dibaca --}}
                @if($allNotifs->where('isNew', true)->count() > 0)
                <button onclick="markAllAsRead()" id="mark-read-btn"
                        class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-white bg-hijau-tua rounded-xl hover:bg-hijau-muda transition-colors shadow-sm text-center inline-flex justify-center items-center gap-1.5">
                    <i class="fas fa-check-double"></i> Tandai Dibaca
                </button>
                @endif
            </div>
        </div>

        <div class="bg-white/60 backdrop-blur-xl rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 overflow-hidden">
            @if($allNotifs->isEmpty())
                <div class="p-16 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-bell-slash text-3xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-500 font-medium text-lg">Tidak ada notifikasi</p>
                    <p class="text-sm text-gray-400 mt-1">Sistem belum mencatat aktivitas dalam 7 hari terakhir.</p>
                </div>
            @else
                <div class="divide-y divide-gray-100/50">
                    @foreach($allNotifs as $notif)
                    <div id="notif-row-{{ $notif['notif_id'] }}" class="flex items-start gap-4 p-5 hover:bg-white/50 transition-colors group relative">
                        <a href="{{ $notif['url'] }}" class="flex-shrink-0">
                            {{-- Icon --}}
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-sm" style="background-color: {{ $notif['color'] }}15">
                                <i class="fas {{ $notif['icon'] }} text-xl" style="color: {{ $notif['color'] }}"></i>
                            </div>
                        </a>

                        {{-- Text Content --}}
                        <a href="{{ $notif['url'] }}" class="flex-1 min-w-0 pt-0.5">
                            <p class="text-base font-semibold text-gray-800 group-hover:text-hijau-tua transition-colors">{{ $notif['text'] }}</p>
                            <p class="text-sm text-gray-500 mt-0.5">{{ $notif['sub'] }}</p>
                            <p class="text-xs text-gray-400 mt-1.5 flex items-center gap-1.5">
                                <i class="far fa-clock"></i> {{ $notif['time'] }} 
                                <span class="mx-1">&bull;</span> 
                                {{ \Carbon\Carbon::parse($notif['raw_time'])->translatedFormat('d M Y, H:i') }}
                            </p>
                        </a>
                        
                        {{-- New Dot & Three Dots Menu --}}
                        <div class="flex items-center gap-3">
                            @if($notif['isNew'])
                                <span id="dot-{{ $notif['notif_id'] }}" class="notif-dot w-2.5 h-2.5 rounded-full bg-blue-500 shadow-sm"></span>
                            @endif
                            
                            {{-- Dropdown --}}
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" @click.outside="open = false" class="text-gray-400 hover:text-gray-600 p-2 rounded-lg hover:bg-gray-100 transition focus:outline-none">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                
                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 mt-2 w-40 bg-white rounded-xl shadow-lg border border-gray-100 z-10 py-1"
                                     style="display: none;">
                                    
                                    @if($notif['isNew'])
                                    <button onclick="actionNotif('{{ $notif['notif_id'] }}', 'read')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                        <i class="fas fa-check text-blue-500"></i> Tandai Baca
                                    </button>
                                    @endif
                                    
                                    <button onclick="actionNotif('{{ $notif['notif_id'] }}', 'hide')" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                                        <i class="fas fa-trash-alt"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        function markAllAsRead() {
            const btn = document.getElementById('mark-read-btn');
            if(btn) {
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
                btn.disabled = true;
            }

            fetch("{{ route('notifications.mark-read') }}", {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': "{{ csrf_token() }}", 
                    'Accept': 'application/json' 
                }
            }).then(response => {
                if(response.ok) {
                    // Sembunyikan semua titik biru (new)
                    document.querySelectorAll('.notif-dot').forEach(el => el.remove());
                    // Ubah background unread (opsional jika tadinya ada styling background beda)
                    
                    // Sembunyikan badge bell di navbar juga tanpa perlu reload halaman
                    const badge = document.getElementById('notif-badge');
                    if (badge) badge.classList.add('hidden');
                    
                    // Sembunyikan tombol ini
                    if(btn) btn.classList.add('hidden');
                }
            }).catch(err => {
                console.error(err);
                if(btn) {
                    btn.innerHTML = '<i class="fas fa-check-double"></i> Tandai Dibaca';
                    btn.disabled = false;
                }
            });
        }
        function actionNotif(notifId, action) {
            fetch("{{ route('notifications.action') }}", {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': "{{ csrf_token() }}", 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json' 
                },
                body: JSON.stringify({ notif_id: notifId, action: action })
            }).then(response => {
                if(response.ok) {
                    if (action === 'hide') {
                        const row = document.getElementById('notif-row-' + notifId);
                        if (row) {
                            row.style.opacity = '0';
                            setTimeout(() => row.remove(), 300);
                        }
                    } else if (action === 'read') {
                        const dot = document.getElementById('dot-' + notifId);
                        if (dot) dot.remove();
                    }
                }
            }).catch(err => console.error(err));
        }
    </script>
    @endpush
</x-app-layout>
