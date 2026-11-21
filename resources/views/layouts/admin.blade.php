<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - MyBantu Administrator</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        admin: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            700: '#334155',
                            800: '#1e293b',
                            900: '#0f172a',
                            950: '#020617',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full bg-slate-100 text-slate-800 antialiased flex flex-col min-h-screen">

    @php
        $pendingVerifyCount = \App\Models\Worker::where(function($q) {
            $q->where('status_data', 'belum terverifikasi')
              ->orWhereNull('status_data')
              ->orWhere('status_data', '');
        })->count();
        $currentAdmin = Auth::guard('admin')->user();
    @endphp

    <div class="flex h-screen overflow-hidden bg-slate-100">
        
        <!-- SIDEBAR BACKDROP (MOBILE) -->
        <div id="sidebarBackdrop" 
             onclick="toggleSidebar()" 
             class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-xs hidden lg:hidden transition-opacity"></div>

        <!-- SIDEBAR UTAMA ADMIN -->
        <aside id="sidebar" 
               class="fixed lg:static inset-y-0 left-0 z-50 w-72 bg-slate-900 text-slate-300 flex flex-col justify-between transition-transform duration-300 transform -translate-x-full lg:translate-x-0 border-r border-slate-800 shadow-2xl lg:shadow-none">
            
            <div class="flex flex-col h-full">
                <!-- BRAND HEADER -->
                <div class="p-6 border-b border-slate-800/80 flex items-center justify-between">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-cyan-500 flex items-center justify-center text-white font-black text-xl shadow-lg shadow-blue-500/25 group-hover:scale-105 transition-transform">
                            B
                        </div>
                        <div>
                            <div class="flex items-center space-x-1.5">
                                <span class="text-lg font-black text-white tracking-tight leading-tight">My<span class="text-blue-400">Bantu</span></span>
                                <span class="bg-blue-950 text-blue-300 border border-blue-800/80 text-[10px] font-black uppercase px-1.5 py-0.5 rounded">Admin</span>
                            </div>
                            <p class="text-[11px] text-slate-400 font-medium">Control Management Panel</p>
                        </div>
                    </a>
                    
                    <!-- Close button on mobile -->
                    <button onclick="toggleSidebar()" class="lg:hidden text-slate-400 hover:text-white p-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- NAVIGATION MENUS -->
                <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-1 text-sm font-semibold">
                    <div class="px-3 pb-2 text-[10px] font-black tracking-wider text-slate-400 uppercase">
                        Utama & Verifikasi
                    </div>

                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white font-bold shadow-md shadow-blue-600/30' : 'hover:bg-slate-800/70 hover:text-white text-slate-300' }}">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            <span>Dashboard</span>
                        </div>
                    </a>

                    <!-- Verifikasi Data Worker -->
                    <a href="{{ route('admin.workers.verify') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.workers.verify') ? 'bg-blue-600 text-white font-bold shadow-md shadow-blue-600/30' : 'hover:bg-slate-800/70 hover:text-white text-slate-300' }}">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            <span>Verifikasi Worker</span>
                        </div>
                        @if($pendingVerifyCount > 0)
                            <span class="bg-amber-500 text-slate-950 font-black text-xs px-2 py-0.5 rounded-full animate-pulse shadow-sm">
                                {{ $pendingVerifyCount }}
                            </span>
                        @endif
                    </a>

                    <div class="px-3 pt-5 pb-2 text-[10px] font-black tracking-wider text-slate-400 uppercase">
                        Manajemen Pengguna
                    </div>

                    <!-- List Worker (Mitra ART) -->
                    <a href="{{ route('admin.workers.index') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.workers.index', 'admin.workers.show', 'admin.workers.edit') ? 'bg-blue-600 text-white font-bold shadow-md shadow-blue-600/30' : 'hover:bg-slate-800/70 hover:text-white text-slate-300' }}">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span>Data Mitra Worker</span>
                        </div>
                    </a>

                    <!-- List User (Majikan) -->
                    <a href="{{ route('admin.users.index') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.users.*') ? 'bg-blue-600 text-white font-bold shadow-md shadow-blue-600/30' : 'hover:bg-slate-800/70 hover:text-white text-slate-300' }}">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span>Data User Majikan</span>
                        </div>
                    </a>

                    <!-- Worker & User Terbaik -->
                    <a href="{{ route('admin.best.index') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.best.*') ? 'bg-blue-600 text-white font-bold shadow-md shadow-blue-600/30' : 'hover:bg-slate-800/70 hover:text-white text-slate-300' }}">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 flex-shrink-0 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h8V3a1 1 0 112 0v1h1a2 2 0 012 2v1a4 4 0 01-4 4h-1.07A5.992 5.992 0 0111 14.93V17h2a1 1 0 110 2H7a1 1 0 110-2h2v-2.07A5.992 5.992 0 016.07 12H5a4 4 0 01-4-4V6a2 2 0 012-2h1V3a1 1 0 011-1zm1 4v3a2 2 0 002 2h4a2 2 0 002-2V6H6z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Terbaik & Leaderboard</span>
                        </div>
                        <span class="text-[10px] font-extrabold uppercase bg-amber-400/20 text-amber-300 border border-amber-400/30 px-2 py-0.5 rounded-md">Top</span>
                    </a>

                    <div class="px-3 pt-5 pb-2 text-[10px] font-black tracking-wider text-slate-400 uppercase">
                        Sistem & Administrator
                    </div>

                    <!-- List Admin -->
                    <a href="{{ route('admin.admins.index') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.admins.index', 'admin.admins.edit') ? 'bg-blue-600 text-white font-bold shadow-md shadow-blue-600/30' : 'hover:bg-slate-800/70 hover:text-white text-slate-300' }}">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <span>List Admin</span>
                        </div>
                    </a>

                    <!-- Register Admin Baru -->
                    <a href="{{ route('admin.admins.create') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.admins.create') ? 'bg-blue-600 text-white font-bold shadow-md shadow-blue-600/30' : 'hover:bg-slate-800/70 hover:text-white text-slate-300' }}">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 flex-shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            <span>Register Admin Baru</span>
                        </div>
                        <span class="text-xs text-emerald-400 font-black">+</span>
                    </a>

                    <!-- Monitoring Keamanan & Brute Force -->
                    <a href="{{ route('admin.security-logs.index') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.security-logs.*') ? 'bg-blue-600 text-white font-bold shadow-md shadow-blue-600/30' : 'hover:bg-slate-800/70 hover:text-white text-slate-300' }}">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 flex-shrink-0 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <span>Keamanan & Log</span>
                        </div>
                    </a>
                </nav>

                <!-- ADMIN PROFILE FOOTER -->
                <div class="p-4 border-t border-slate-800/80 bg-slate-950/40">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3 overflow-hidden">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-slate-700 to-slate-600 border border-slate-700 flex items-center justify-center text-white font-black text-sm flex-shrink-0">
                                {{ strtoupper(substr($currentAdmin->name ?? 'A', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-white truncate">{{ $currentAdmin->name ?? 'Admin' }}</p>
                                <div class="flex items-center space-x-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span class="text-[10px] text-slate-400 truncate">{{ $currentAdmin->role ?? 'Administrator' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Form Logout Admin -->
                        <form action="{{ route('admin.logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" 
                                    title="Keluar dari Panel Admin"
                                    class="p-2 text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded-lg transition"
                                    onclick="return confirm('Apakah Anda yakin ingin keluar dari Panel Administrator?')">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <!-- MAIN CONTENT AREA -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            
            <!-- TOP NAVBAR -->
            <header class="bg-white border-b border-slate-200/80 sticky top-0 z-30 shadow-2xs">
                <div class="px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-xl text-slate-600 hover:bg-slate-100 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <div class="flex items-center space-x-2 text-xs font-semibold text-slate-500">
                            <span class="hidden sm:inline">Admin Panel</span>
                            <span class="hidden sm:inline text-slate-300">/</span>
                            <span class="font-bold text-slate-800 text-sm">@yield('page_title', 'Dashboard')</span>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <a href="{{ route('home') }}" 
                           target="_blank"
                           class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50/50 transition">
                            <span>Lihat Website</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </a>

                        <div class="hidden sm:flex items-center space-x-2 pl-3 border-l border-slate-200">
                            <span class="text-xs font-bold text-slate-700">{{ $currentAdmin->name ?? 'Admin' }}</span>
                            <span class="text-[10px] font-extrabold uppercase bg-blue-100 text-blue-700 px-2 py-0.5 rounded-md">
                                {{ $currentAdmin->role ?? 'Admin' }}
                            </span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- NOTIFICATION BANNERS -->
            <div class="px-4 sm:px-6 lg:px-8 pt-4">
                @if(session('success'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs font-medium flex items-center justify-between shadow-2xs mb-4">
                        <div class="flex items-center space-x-2.5">
                            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="font-semibold">{{ session('success') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold">&times;</button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl text-xs font-medium flex items-center justify-between shadow-2xs mb-4">
                        <div class="flex items-center space-x-2.5">
                            <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="font-semibold">{{ session('error') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 font-bold">&times;</button>
                    </div>
                @endif

                @if(isset($errors) && $errors->any())
                    <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl text-xs font-medium shadow-2xs mb-4">
                        <div class="flex items-center space-x-2 font-bold mb-1">
                            <svg class="w-4 h-4 text-rose-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Terjadi kesalahan input:</span>
                        </div>
                        <ul class="list-disc list-inside space-y-0.5 text-rose-700 pl-2">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <!-- MAIN SCROLLABLE CONTENT -->
            <main class="flex-1 overflow-y-auto px-4 sm:px-6 lg:px-8 pb-12">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- SCRIPT UTILITY ADMIN -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
        }

        // Chained Wilayah Helper
        function onProvinsiChange(provinsiName, targetKabupatenId, selectedKabupaten = '') {
            const kabupatenSelect = document.getElementById(targetKabupatenId);
            if (!kabupatenSelect) return;

            if (!provinsiName) {
                kabupatenSelect.innerHTML = '<option value="">-- Pilih Provinsi Dahulu --</option>';
                return;
            }

            kabupatenSelect.innerHTML = '<option value="">Memuat data kabupaten...</option>';

            fetch(`{{ route('api.wilayah.kabupaten') }}?provinsi=${encodeURIComponent(provinsiName)}`)
                .then(res => res.json())
                .then(data => {
                    let options = '<option value="">-- Pilih Kabupaten / Kota --</option>';
                    if (data && data.length > 0) {
                        data.forEach(item => {
                            const isSelected = (selectedKabupaten && item.toLowerCase() === selectedKabupaten.toLowerCase()) ? 'selected' : '';
                            options += `<option value="${item}" ${isSelected}>${item}</option>`;
                        });
                    } else {
                        options = '<option value="">Tidak ada data kabupaten</option>';
                    }
                    kabupatenSelect.innerHTML = options;
                })
                .catch(err => {
                    console.error('Error fetching kabupaten:', err);
                    kabupatenSelect.innerHTML = '<option value="">Gagal memuat kabupaten</option>';
                });
        }
    </script>
    @stack('scripts')
</body>
</html>

