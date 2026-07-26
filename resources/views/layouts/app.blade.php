<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-abu-muda">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Presensi Madin') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        
        <!-- Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Chart.js (dimuat di head agar selalu tersedia saat SPA navigasi) -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        
        
        <!-- TomSelect CSS for custom dropdowns -->
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.default.min.css" rel="stylesheet">
        <style>
            .ts-control {
                border-radius: 0.75rem !important;
                border-color: #e5e7eb !important;
                padding: 0.625rem 0.75rem !important;
                font-size: 0.875rem !important;
                background-color: #ffffff !important;
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
            /* Khusus untuk dropdown filter agar tingginya pas 36px */
            .ts-wrapper.ts-filter {
                width: 100% !important;
            }
            .ts-wrapper.ts-filter .ts-control {
                min-height: 36px !important;
                padding: 0.375rem 0.75rem !important; 
                border-radius: 0.5rem !important;
            }
            .ts-dropdown {
                border-radius: 0.75rem !important;
                border-color: #e5e7eb !important;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
                overflow: hidden !important;
            }
            .ts-dropdown .ts-dropdown-content {
                max-height: 250px !important;
            }
            @media (max-width: 768px) {
                .ts-dropdown .ts-dropdown-content {
                    max-height: 200px !important; /* Lebih pendek di HP agar mudah di-scroll */
                }
                .ts-control {
                    padding: 0.75rem 1rem !important; /* Sedikit lebih besar untuk area sentuh di HP */
                }
            }
            .ts-wrapper.focus .ts-control {
                border-color: #187a41 !important;
                box-shadow: 0 0 0 2px rgba(24, 122, 65, 0.2) !important;
            }
        </style>
        
        <!-- TomSelect JS -->
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    
        
    </head>
    <body class="font-sans antialiased bg-abu-muda text-gray-800 overscroll-y-none">

        <div class="flex h-screen overflow-hidden relative">
            
            <!-- Mobile Overlay -->
            <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-[55] hidden lg:hidden transition-opacity" onclick="toggleSidebar()"></div>

            <!-- Sidebar Container -->
            <div id="sidebar-container" class="fixed inset-y-0 left-0 z-[60] transform -translate-x-full lg:translate-x-0 lg:static transition-transform duration-300 ease-in-out">
                @include('layouts.sidebar')
            </div>

            <!-- Content Area -->
            <div id="content-area" class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden w-full lg:w-auto overscroll-y-none">
                <!-- Navbar -->
                @include('layouts.navbar', ['header' => trim(strip_tags((string)($header ?? 'Dashboard')))])

                <!-- Page Content -->
                <main class="p-4 sm:p-5 lg:p-8 pb-12 md:pb-8 w-full max-w-7xl mx-auto">
                    {{ $slot }}
                </main>
            </div>
        </div>
        @stack('modals')
        @stack('scripts')
        <script>
            function toggleSidebar() {
                const sidebar = document.getElementById('sidebar-container');
                const overlay = document.getElementById('sidebar-overlay');
                if (sidebar.classList.contains('-translate-x-full')) {
                    sidebar.classList.remove('-translate-x-full');
                    overlay.classList.remove('hidden');
                    localStorage.setItem('sidebarOpen', 'true');
                } else {
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.add('hidden');
                    localStorage.setItem('sidebarOpen', 'false');
                }
            }

            // Memori untuk sidebar di layar HP
            document.addEventListener('DOMContentLoaded', () => {
                const urlParams = new URLSearchParams(window.location.search);

                // Jika baru saja login (?fresh=1), tutup paksa sidebar & hapus memori lama
                if (urlParams.get('fresh') === '1') {
                    localStorage.setItem('sidebarOpen', 'false');
                    // Hapus parameter ?fresh=1 dari URL tanpa reload
                    const cleanUrl = window.location.pathname;
                    window.history.replaceState({}, '', cleanUrl);
                }

                // Buka sidebar jika memori localStorage menyimpan 'true'
                if (window.innerWidth < 1024 && localStorage.getItem('sidebarOpen') === 'true') {
                    const sidebar = document.getElementById('sidebar-container');
                    const overlay = document.getElementById('sidebar-overlay');
                    
                    sidebar.style.transition = 'none';
                    sidebar.classList.remove('-translate-x-full');
                    overlay.classList.remove('hidden');
                    
                    setTimeout(() => {
                        sidebar.style.transition = '';
                    }, 50);
                }
            });

            // Navigasi Mulus (Tanpa Refresh) Khusus Sidebar
            document.addEventListener('DOMContentLoaded', () => {
                const sidebarContainer = document.getElementById('sidebar-container');
                if (!sidebarContainer) return;

                sidebarContainer.addEventListener('click', function(e) {
                    const link = e.target.closest('a[href]');
                    if (!link) return;
                    
                    const href = link.getAttribute('href');
                    if (href === '#' || href.startsWith('javascript') || link.getAttribute('target') === '_blank') return;
                    if (link.origin !== window.location.origin) return;
                    if (link.closest('form')) return; // Jangan halangi form logout

                    e.preventDefault();

                    fetch(href)
                    .then(res => {
                        // Jika server mengarahkan ke URL berbeda (misal sesi habis → /login),
                        // lakukan redirect penuh bukan inject HTML
                        if (res.url && res.url !== window.location.origin + href && 
                            new URL(res.url).pathname !== new URL(window.location.origin + href).pathname) {
                            window.location.href = res.url;
                            return null;
                        }
                        return res.text();
                    })
                    .then(html => {
                        if (!html) return; // sudah redirect penuh, hentikan
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');

                        // 1. Ganti konten utama
                        const currentMain = document.querySelector('main');
                        const newMain = doc.querySelector('main');
                        if (currentMain && newMain) {
                            currentMain.innerHTML = newMain.innerHTML;
                            
                            // Eksekusi ulang tag <script> yang ada di dalam main (jika ada)
                            const scripts = currentMain.querySelectorAll('script');
                            scripts.forEach(oldScript => {
                                const newScript = document.createElement('script');
                                Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                                newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                                oldScript.parentNode.replaceChild(newScript, oldScript);
                            });
                            
                            // Re-init TomSelect
                            if(typeof initTomSelect === 'function') initTomSelect();
                        }

                        // 2. Perbarui state menu aktif di sidebar
                        const currentNav = document.querySelector('#sidebar-container nav');
                        const newNav = doc.querySelector('#sidebar-container nav');
                        if (currentNav && newNav) {
                            currentNav.innerHTML = newNav.innerHTML;
                        }

                        // 3. Perbarui header/navbar (judul dll) jika perlu
                        const currentNavbar = document.querySelector('#main-navbar');
                        const newNavbar = doc.querySelector('#main-navbar');
                        if (currentNavbar && newNavbar) {
                            currentNavbar.innerHTML = newNavbar.innerHTML;
                        }

                        // 4. Update judul & URL
                        document.title = doc.title;
                        window.history.pushState({}, '', href);
                        
                        // Hapus memori agar sidebar otomatis tertutup jika direfresh manual nantinya (opsional)
                        // localStorage.removeItem('sidebarOpen'); 
                    })
                    .catch(() => {
                        window.location.href = href; // Fallback jika gagal
                    });
                });
                
                // Handle tombol back di HP/browser
                window.addEventListener('popstate', () => {
                    window.location.reload();
                });
            });
            
            // Inisialisasi global untuk TomSelect
            function initTomSelect() {
                if(typeof TomSelect !== 'undefined') {
                    // Deteksi jika dibuka di layar HP
                    const isMobile = window.innerWidth <= 768;
                    
                    document.querySelectorAll('select:not(.no-tomselect)').forEach(function(el) {
                        if (!el.tomselect) {
                            new TomSelect(el, {
                                create: false,
                                sortField: {
                                    field: "text",
                                    direction: "asc"
                                },
                                maxOptions: 50,
                                dropdownParent: 'body',
                                onInitialize: function() {
                                    // Matikan keyboard otomatis di HP dengan membuat input readonly
                                    if (isMobile && this.control_input) {
                                        this.control_input.setAttribute('readonly', 'readonly');
                                    }
                                }
                            });
                        }
                    });
                }
            }
            
            
            document.addEventListener('DOMContentLoaded', initTomSelect);
        </script>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if(typeof flatpickr !== 'undefined') {
                    
                    const fpBackdrop = document.createElement("div");
                    fpBackdrop.className = "fixed inset-0 bg-black/60 backdrop-blur-sm z-[99999] hidden transition-opacity";
                    document.body.appendChild(fpBackdrop);
                    
                    flatpickr(".datepicker", {
                        locale: "id",
                        dateFormat: "Y-m-d",
                        disableMobile: true,
                        onOpen: function(selectedDates, dateStr, instance) {
                            fpBackdrop.classList.remove("hidden");
                            instance.calendarContainer.classList.add("fp-modal-style");
                        },
                        onClose: function(selectedDates, dateStr, instance) {
                            fpBackdrop.classList.add("hidden");
                            instance.calendarContainer.classList.remove("fp-modal-style");
                        }
                    });
                }
            });
        </script>
    </body>
</html>
