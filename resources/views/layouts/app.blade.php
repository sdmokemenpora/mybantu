<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyBantu - Platform Jasa Asisten Rumah Tangga Terpercaya & Terverifikasi</title>
    <meta name="description" content="Temukan dan pesan Asisten Rumah Tangga (ART) harian dan mingguan profesional dengan garansi sistem Rekening Bersama aman di MyBantu.">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0f7ff',
                            100: '#e0effe',
                            200: '#bae0fd',
                            500: '#0284c7',
                            600: '#0369a1',
                            700: '#075985',
                            900: '#0c4a6e',
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
<body class="bg-slate-50 text-slate-800 antialiased flex flex-col min-h-screen">
    
    <!-- NAVBAR UTAMA -->
    <header class="bg-white/95 backdrop-blur-md sticky top-0 z-50 border-b border-slate-200/80 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                
                @if(Auth::guard('worker')->check())
                    @php $currentWorker = Auth::guard('worker')->user(); @endphp
                    <!-- BRAND & LOGO (TAMPILAN ADMIN MITRA ART) -->
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('worker.transaksi') }}" class="flex items-center space-x-2.5 group">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 via-teal-600 to-emerald-700 flex items-center justify-center text-white font-extrabold text-xl shadow-md shadow-emerald-500/20 group-hover:scale-105 transition-transform duration-200">
                                W
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xl font-extrabold text-slate-900 tracking-tight leading-tight">
                                    My<span class="text-emerald-600">Bantu</span>
                                </span>
                                <span class="text-[10px] font-bold text-emerald-700 tracking-wider uppercase -mt-0.5 flex items-center">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1 animate-pulse"></span> Panel Mitra ART
                                </span>
                            </div>
                        </a>
                    </div>

                    <!-- NAV LINKS ADMIN WORKER (TANPA BERANDA, DAFTAR ART, TENTANG KAMI, KONTAK) -->
                    <nav class="hidden md:flex items-center space-x-1 lg:space-x-2 text-sm font-semibold">
                        <a href="{{ route('worker.transaksi') }}" 
                           class="px-3.5 py-2 rounded-xl transition flex items-center space-x-2 {{ request()->routeIs('worker.transaksi*') ? 'bg-emerald-50 text-emerald-800 font-bold border border-emerald-200 shadow-2xs' : 'text-slate-600 hover:text-emerald-700 hover:bg-slate-50' }}">
                            <span>📋</span>
                            <span>Transaksi</span>
                        </a>

                        <a href="{{ route('worker.pendapatan') }}" 
                           class="px-3.5 py-2 rounded-xl transition flex items-center space-x-2 {{ request()->routeIs('worker.pendapatan*') ? 'bg-emerald-50 text-emerald-800 font-bold border border-emerald-200 shadow-2xs' : 'text-slate-600 hover:text-emerald-700 hover:bg-slate-50' }}">
                            <span>💰</span>
                            <span>Pendapatan</span>
                        </a>

                        <a href="{{ route('worker.profile') }}" 
                           class="px-3.5 py-2 rounded-xl transition flex items-center space-x-2 {{ request()->routeIs('worker.profile*') ? 'bg-emerald-50 text-emerald-800 font-bold border border-emerald-200 shadow-2xs' : 'text-slate-600 hover:text-emerald-700 hover:bg-slate-50' }}">
                            <span>⚙️</span>
                            <span>Profil & Tarif</span>
                        </a>

                        <a href="{{ route('product.detail', $currentWorker->id) }}" target="_blank"
                           class="px-3 py-2 rounded-xl transition flex items-center space-x-1.5 text-xs text-slate-500 hover:text-blue-600 hover:bg-slate-50"
                           title="Buka pratinjau bagaimana majikan melihat profil Anda">
                            <span>🌐</span>
                            <span>Pratinjau Publik</span>
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </a>
                    </nav>

                    <!-- ACTION BUTTONS WORKER (PROFIL PILL & LOGOUT) -->
                    <div class="hidden sm:flex items-center space-x-3">
                        <div class="flex items-center space-x-3 bg-slate-50 border border-slate-200/80 pl-3 pr-2 py-1.5 rounded-2xl shadow-2xs">
                            <div class="flex items-center space-x-2">
                                @if(!empty($currentWorker->foto_path))
                                    <img src="{{ asset('storage/' . $currentWorker->foto_path) }}" alt="{{ $currentWorker->fullname }}" class="w-8 h-8 rounded-full object-cover border border-emerald-300">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-black shadow-xs">
                                        {{ strtoupper(substr($currentWorker->fullname, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-slate-800 leading-tight">{{ Str::limit($currentWorker->fullname, 14) }}</span>
                                    <span class="text-[9px] font-bold text-emerald-600 uppercase flex items-center">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1 animate-pulse"></span> Mitra Aktif
                                    </span>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('worker.logout') }}" class="inline ml-1">
                                @csrf
                                <button type="submit" 
                                        title="Keluar dari panel worker"
                                        class="text-xs bg-white hover:bg-red-50 text-slate-600 hover:text-red-600 font-bold px-3 py-1.5 rounded-xl transition border border-slate-200 hover:border-red-200 shadow-2xs">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>

                @else
                    <!-- BRAND & LOGO (TAMPILAN PUBLIK / MAJIKAN) -->
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('home') }}" class="flex items-center space-x-2.5 group">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-blue-700 flex items-center justify-center text-white font-extrabold text-xl shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform duration-200">
                                B
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xl font-extrabold text-slate-900 tracking-tight leading-tight">
                                    My<span class="text-blue-600">Bantu</span>
                                </span>
                                <span class="text-[10px] font-semibold text-slate-400 tracking-wider uppercase -mt-0.5">Platform Jasa ART</span>
                            </div>
                        </a>
                    </div>

                    <!-- NAV LINKS (DESKTOP PUBLIK) -->
                    <nav class="hidden md:flex items-center space-x-1 lg:space-x-2 text-sm font-semibold text-slate-600">
                        <a href="{{ route('home') }}" 
                           class="px-3.5 py-2 rounded-lg transition-colors {{ request()->routeIs('home') ? 'bg-blue-50 text-blue-700 font-bold' : 'hover:text-blue-600 hover:bg-slate-50' }}">
                            Beranda
                        </a>
                        <a href="{{ route('product') }}" 
                           class="px-3.5 py-2 rounded-lg transition-colors {{ request()->routeIs('product*') ? 'bg-blue-50 text-blue-700 font-bold' : 'hover:text-blue-600 hover:bg-slate-50' }}">
                            Daftar ART
                        </a>
                        <a href="{{ route('about') }}" 
                           class="px-3.5 py-2 rounded-lg transition-colors {{ request()->routeIs('about') ? 'bg-blue-50 text-blue-700 font-bold' : 'hover:text-blue-600 hover:bg-slate-50' }}">
                            Tentang Kami
                        </a>
                        <a href="{{ route('contact') }}" 
                           class="px-3.5 py-2 rounded-lg transition-colors {{ request()->routeIs('contact') ? 'bg-blue-50 text-blue-700 font-bold' : 'hover:text-blue-600 hover:bg-slate-50' }}">
                            Kontak & Bantuan
                        </a>
                    </nav>

                    <!-- ACTION BUTTONS (MAJIKAN / GUEST) -->
                    <div class="hidden sm:flex items-center space-x-3">
                        @if(Auth::guard('web')->check())
                            @php $currentUser = Auth::guard('web')->user(); @endphp
                            <!-- Majikan Navigation Links -->
                            <a href="{{ route('transaksi') }}" 
                               class="inline-flex items-center text-sm font-bold {{ request()->routeIs('transaksi*') ? 'text-blue-600 bg-blue-50' : 'text-slate-700 hover:text-blue-600 hover:bg-slate-50' }} px-3.5 py-2 rounded-xl transition">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                Pesanan Saya
                            </a>

                            <a href="{{ route('user.profile') }}" 
                               class="inline-flex items-center text-sm font-bold {{ request()->routeIs('user.profile*') ? 'text-blue-600 bg-blue-50' : 'text-slate-700 hover:text-blue-600 hover:bg-slate-50' }} px-3.5 py-2 rounded-xl transition">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Profil Saya
                            </a>

                            <div class="flex items-center space-x-3 border-l pl-4 border-slate-200">
                                <div class="flex items-center space-x-2">
                                    @if(!empty($currentUser->foto_path))
                                        <img src="{{ asset('storage/' . $currentUser->foto_path) }}" alt="{{ $currentUser->name }}" class="w-8 h-8 rounded-full object-cover border border-blue-200">
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold shadow-xs">
                                            {{ strtoupper(substr($currentUser->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-slate-800 leading-tight">{{ Str::limit($currentUser->name, 12) }}</span>
                                        <span class="text-[10px] text-emerald-600 font-extrabold">Saldo: Rp {{ number_format($currentUser->saldo ?? 0, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('logout') }}" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            title="Keluar dari akun"
                                            class="text-xs bg-slate-100 hover:bg-red-50 text-slate-600 hover:text-red-600 font-bold px-2.5 py-1.5 rounded-lg transition border border-slate-200/80 hover:border-red-200">
                                        Logout
                                    </button>
                                </form>
                            </div>

                        @else
                            <!-- Guest Action Cluster: Gabung Mitra ART, Masuk, Daftar Majikan -->
                            <div class="flex items-center space-x-2">
                                <!-- Tombol Gabung Mitra ART -->
                                <a href="{{ route('register.seller') }}" 
                                   class="group relative inline-flex items-center space-x-2 text-xs font-bold text-emerald-800 bg-gradient-to-r from-emerald-50 via-teal-50 to-emerald-100 hover:from-emerald-100 hover:to-teal-100 border border-emerald-300/80 px-3.5 py-2 rounded-xl transition-all duration-200 shadow-2xs hover:shadow-xs hover:-translate-y-0.5">
                                    <span class="flex h-2 w-2 relative">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                    </span>
                                    <span class="tracking-tight">Gabung Mitra ART</span>
                                    <span class="text-[9px] bg-white/90 text-emerald-900 border border-emerald-200 px-1.5 py-0.5 rounded-md font-extrabold group-hover:bg-white transition-colors">Cari Kerja</span>
                                </a>

                                <div class="h-5 w-px bg-slate-200/90 mx-0.5"></div>

                                <!-- Tombol Masuk -->
                                <a href="{{ route('login') }}" 
                                   class="inline-flex items-center space-x-1.5 text-xs font-bold text-slate-700 hover:text-blue-700 bg-slate-50 hover:bg-slate-100 border border-slate-200/90 px-3.5 py-2 rounded-xl transition-all duration-150 shadow-2xs hover:shadow-xs">
                                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-blue-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                    </svg>
                                    <span>Masuk</span>
                                </a>

                                <!-- Tombol Daftar Majikan -->
                                <a href="{{ route('register') }}" 
                                   class="inline-flex items-center space-x-1.5 text-xs font-extrabold text-white bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 hover:from-blue-700 hover:to-indigo-700 px-4 py-2 rounded-xl transition-all duration-200 shadow-sm shadow-blue-500/25 hover:shadow-md hover:shadow-blue-500/35 hover:-translate-y-0.5 active:translate-y-0">
                                    <svg class="w-3.5 h-3.5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                    </svg>
                                    <span>Daftar Majikan</span>
                                </a>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- MOBILE HAMBURGER BUTTON -->
                <div class="flex md:hidden items-center space-x-2">
                    <button type="button" 
                            id="mobileMenuBtn" 
                            onclick="toggleMobileMenu()" 
                            class="p-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            aria-label="Toggle navigation menu">
                        <svg class="w-6 h-6" id="hamburgerIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <svg class="w-6 h-6 hidden" id="closeIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

            </div>
        </div>

        <!-- MOBILE MENU DRAWER -->
        <div id="mobileMenu" class="hidden md:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-6 space-y-3">
            @if(Auth::guard('worker')->check())
                @php $mobWorker = Auth::guard('worker')->user(); @endphp
                <!-- Worker Admin Profile Card -->
                <div class="flex items-center justify-between py-2.5 px-3 bg-emerald-50 rounded-2xl border border-emerald-200">
                    <div class="flex items-center space-x-2.5">
                        @if(!empty($mobWorker->foto_path))
                            <img src="{{ asset('storage/' . $mobWorker->foto_path) }}" alt="{{ $mobWorker->fullname }}" class="w-9 h-9 rounded-full object-cover border border-emerald-300">
                        @else
                            <div class="w-9 h-9 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold">
                                {{ strtoupper(substr($mobWorker->fullname, 0, 1)) }}
                            </div>
                        @endif
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-slate-900">{{ $mobWorker->fullname }}</span>
                            <span class="text-[10px] font-bold text-emerald-700 uppercase flex items-center">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1 animate-pulse"></span> Panel Mitra ART
                            </span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('worker.logout') }}">
                        @csrf
                        <button type="submit" class="text-xs text-red-600 font-bold bg-white px-3 py-1.5 rounded-xl border border-red-200 shadow-2xs">Logout</button>
                    </form>
                </div>

                <!-- Worker Admin Links (TANPA BERANDA, DAFTAR ART, TENTANG KAMI, KONTAK) -->
                <div class="space-y-1 pt-1">
                    <a href="{{ route('worker.transaksi') }}" 
                       class="flex items-center space-x-2.5 px-3.5 py-2.5 rounded-xl text-sm font-bold {{ request()->routeIs('worker.transaksi*') ? 'bg-emerald-100 text-emerald-800' : 'text-slate-700 hover:bg-slate-50' }}">
                        <span>📋</span>
                        <span>Transaksi Masuk</span>
                    </a>
                    <a href="{{ route('worker.pendapatan') }}" 
                       class="flex items-center space-x-2.5 px-3.5 py-2.5 rounded-xl text-sm font-bold {{ request()->routeIs('worker.pendapatan*') ? 'bg-emerald-100 text-emerald-800' : 'text-slate-700 hover:bg-slate-50' }}">
                        <span>💰</span>
                        <span>Rekap Pendapatan</span>
                    </a>
                    <a href="{{ route('worker.profile') }}" 
                       class="flex items-center space-x-2.5 px-3.5 py-2.5 rounded-xl text-sm font-bold {{ request()->routeIs('worker.profile*') ? 'bg-emerald-100 text-emerald-800' : 'text-slate-700 hover:bg-slate-50' }}">
                        <span>⚙️</span>
                        <span>Pengaturan Profil & Tarif</span>
                    </a>
                    <a href="{{ route('product.detail', $mobWorker->id) }}" target="_blank" 
                       class="flex items-center space-x-2.5 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50">
                        <span>🌐</span>
                        <span>Lihat Profil Publik &rarr;</span>
                    </a>
                </div>

            @else
                <!-- Public Menu untuk Majikan / Guest -->
                <div class="space-y-1">
                    <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('home') ? 'bg-blue-50 text-blue-700' : 'text-slate-700' }}">Beranda</a>
                    <a href="{{ route('product') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('product*') ? 'bg-blue-50 text-blue-700' : 'text-slate-700' }}">Daftar ART</a>
                    <a href="{{ route('about') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('about') ? 'bg-blue-50 text-blue-700' : 'text-slate-700' }}">Tentang Kami</a>
                    <a href="{{ route('contact') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('contact') ? 'bg-blue-50 text-blue-700' : 'text-slate-700' }}">Kontak & Bantuan</a>
                </div>

                <div class="border-t border-slate-100 pt-3 space-y-2">
                    @if(Auth::guard('web')->check())
                        @php $mobUser = Auth::guard('web')->user(); @endphp
                        <div class="flex items-center justify-between py-2 px-3 bg-slate-50 rounded-xl border border-slate-200">
                            <div class="flex items-center space-x-2.5">
                                @if(!empty($mobUser->foto_path))
                                    <img src="{{ asset('storage/' . $mobUser->foto_path) }}" alt="{{ $mobUser->name }}" class="w-8 h-8 rounded-full object-cover">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold">
                                        {{ strtoupper(substr($mobUser->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-slate-800">{{ $mobUser->name }}</span>
                                    <span class="text-[10px] text-emerald-600 font-extrabold">Saldo: Rp {{ number_format($mobUser->saldo ?? 0, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-xs text-red-600 font-bold bg-white px-2.5 py-1 rounded-lg border border-red-200">Logout</button>
                            </form>
                        </div>
                        <div class="grid grid-cols-2 gap-2 pt-1 text-center text-xs font-bold">
                            <a href="{{ route('transaksi') }}" class="py-2.5 bg-blue-50 text-blue-700 rounded-xl">Pesanan Saya</a>
                            <a href="{{ route('user.profile') }}" class="py-2.5 bg-slate-100 text-slate-700 rounded-xl">Profil Saya</a>
                        </div>

                    @else
                        <!-- Mobile Role Mockup Card: Jalur Majikan & Jalur Mitra ART -->
                        <div class="space-y-2.5 pt-1">
                            <!-- Card Mockup 1: Majikan -->
                            <div class="bg-gradient-to-br from-blue-50/70 via-white to-slate-50 border border-blue-200/80 rounded-2xl p-3.5 shadow-2xs space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2.5">
                                        <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center text-sm font-black shadow-xs">
                                            👨‍💼
                                        </div>
                                        <div>
                                            <span class="text-xs font-extrabold text-slate-900 block leading-tight">Untuk Majikan</span>
                                            <span class="text-[10px] text-slate-500 block leading-tight">Pesan ART bergaransi rekber resmi</span>
                                        </div>
                                    </div>
                                    <span class="text-[9px] font-bold text-blue-700 bg-blue-100/70 px-2 py-0.5 rounded-full">Pemberi Kerja</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2 pt-0.5">
                                    <a href="{{ route('register') }}" 
                                       class="text-center text-xs font-extrabold text-white bg-blue-600 hover:bg-blue-700 py-2.5 px-2 rounded-xl transition shadow-xs">
                                        Daftar Majikan
                                    </a>
                                    <a href="{{ route('login', ['role' => 'user']) }}" 
                                       class="text-center text-xs font-bold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 py-2.5 px-2 rounded-xl transition">
                                        Masuk Majikan
                                    </a>
                                </div>
                            </div>

                            <!-- Card Mockup 2: Mitra ART -->
                            <div class="bg-gradient-to-br from-emerald-50/70 via-white to-teal-50/50 border border-emerald-200/80 rounded-2xl p-3.5 shadow-2xs space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2.5">
                                        <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-sm font-black shadow-xs">
                                            🧹
                                        </div>
                                        <div>
                                            <span class="text-xs font-extrabold text-slate-900 block leading-tight">Untuk Mitra ART</span>
                                            <span class="text-[10px] text-slate-500 block leading-tight">Cari kerja & upah bersih 85% pasti cair</span>
                                        </div>
                                    </div>
                                    <span class="text-[9px] font-bold text-emerald-800 bg-emerald-100/80 px-2 py-0.5 rounded-full flex items-center">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1 animate-pulse"></span> Buka Lowongan
                                    </span>
                                </div>
                                <div class="grid grid-cols-2 gap-2 pt-0.5">
                                    <a href="{{ route('register.seller') }}" 
                                       class="text-center text-xs font-extrabold text-white bg-emerald-600 hover:bg-emerald-700 py-2.5 px-2 rounded-xl transition shadow-xs">
                                        Gabung Mitra ART
                                    </a>
                                    <a href="{{ route('login', ['role' => 'worker']) }}" 
                                       class="text-center text-xs font-bold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 py-2.5 px-2 rounded-xl transition">
                                        Masuk Panel ART
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </header>

    <!-- NOTIFIKASI FLASH MESSAGE (SUCCESS & ERROR) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
            <div class="flex items-center p-4 mb-4 text-sm text-emerald-800 rounded-2xl bg-emerald-50 border border-emerald-200 shadow-xs" role="alert">
                <svg class="flex-shrink-0 inline w-5 h-5 mr-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <div class="font-medium">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="flex items-center p-4 mb-4 text-sm text-rose-800 rounded-2xl bg-rose-50 border border-rose-200 shadow-xs" role="alert">
                <svg class="flex-shrink-0 inline w-5 h-5 mr-3 text-rose-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <div class="font-medium">
                    {{ session('error') }}
                </div>
            </div>
        @endif
    </div>

    <!-- MAIN CONTENT -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- FOOTER RESMI MYBANTU -->
    <footer class="bg-slate-900 text-slate-300 pt-16 pb-8 border-t border-slate-800 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-12">
                
                <!-- Brand Info -->
                <div class="space-y-4 md:col-span-1">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center text-white font-extrabold text-lg shadow-md shadow-blue-500/20">
                            B
                        </div>
                        <span class="text-xl font-extrabold text-white tracking-tight">
                            My<span class="text-blue-400">Bantu</span>
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Platform pencarian dan pemesanan Asisten Rumah Tangga harian & mingguan resmi dengan perlindungan Rekening Bersama dan garansi penggantian mitra.
                    </p>
                    <div class="flex items-center space-x-2 pt-1 text-xs text-emerald-400 font-semibold">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                        <span>100% Pembayaran Rekber Amanah</span>
                    </div>
                </div>

                <!-- Navigasi -->
                <div>
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4">Navigasi Utama</h4>
                    <ul class="space-y-2.5 text-xs text-slate-400">
                        <li><a href="{{ route('home') }}" class="hover:text-white transition">Beranda</a></li>
                        <li><a href="{{ route('product') }}" class="hover:text-white transition">Cari Asisten Rumah Tangga</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-white transition">Sistem Garansi & Rekber</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-white transition">Pusat Bantuan & Kontak</a></li>
                    </ul>
                </div>

                <!-- Untuk Mitra & Majikan -->
                <div>
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4">Layanan & Mitra</h4>
                    <ul class="space-y-2.5 text-xs text-slate-400">
                        <li><a href="{{ route('register.seller') }}" class="hover:text-white transition">Pendaftaran Mitra ART</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white transition">Daftar Akun Majikan</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-white transition">Masuk Akun</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-white transition">Standar Verifikasi KTP & SKCK</a></li>
                    </ul>
                </div>

                <!-- Keamanan & Kontak -->
                <div>
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4">Keamanan & Bantuan</h4>
                    <div class="space-y-3 text-xs text-slate-400">
                        <div class="flex items-start space-x-2.5">
                            <svg class="w-4 h-4 text-blue-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span>bantuan@mybantu.id</span>
                        </div>
                        <div class="flex items-start space-x-2.5">
                            <svg class="w-4 h-4 text-emerald-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Senin - Minggu: 08.00 - 21.00 WIB</span>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-800">
                            <span class="inline-flex items-center text-[11px] font-semibold text-slate-300 bg-slate-800/80 px-2.5 py-1 rounded-lg border border-slate-700">
                                🛡️ Terverifikasi & Bebas Penipuan
                            </span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Bottom Copyright -->
            <div class="border-t border-slate-800/80 pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} MyBantu Indonesia. Hak Cipta Dilindungi.</p>
                <div class="flex space-x-6 mt-4 sm:mt-0 items-center">
                    <a href="{{ route('about') }}" class="hover:text-slate-400 transition">Kebijakan Privasi</a>
                    <a href="{{ route('about') }}" class="hover:text-slate-400 transition">Syarat & Ketentuan</a>
                    <a href="{{ route('admin.login') }}" class="hover:text-slate-300 text-slate-500 transition font-bold flex items-center space-x-1">
                        <span>🔒 Portal Admin</span>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Mobile Menu Script -->
    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            const hamburger = document.getElementById('hamburgerIcon');
            const close = document.getElementById('closeIcon');
            
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                hamburger.classList.add('hidden');
                close.classList.remove('hidden');
            } else {
                menu.classList.add('hidden');
                hamburger.classList.remove('hidden');
                close.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
