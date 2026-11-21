@extends('layouts.app')

@section('content')
<div class="space-y-16 pb-16">
    
    <!-- HERO SECTION (High-Trust, Clean Architecture) -->
    <section class="relative bg-gradient-to-b from-white via-slate-50 to-blue-50/30 border-b border-slate-200/60 pt-10 pb-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                <!-- Hero Left: Value Proposition -->
                <div class="lg:col-span-7 space-y-6 text-left">
                    <div class="inline-flex items-center space-x-2 bg-blue-50 border border-blue-200/80 text-blue-700 text-xs font-bold px-3.5 py-1.5 rounded-full shadow-2xs">
                        <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                        <span>Platform Jasa ART Resmi & Bergaransi</span>
                    </div>

                    <h1 class="text-3xl sm:text-5xl lg:text-5xl font-extrabold text-slate-900 tracking-tight leading-[1.15]">
                        Asisten Rumah Tangga Terpercaya, <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Aman & Terverifikasi</span>
                    </h1>

                    <p class="text-slate-600 text-base sm:text-lg leading-relaxed max-w-2xl font-normal">
                        Dapatkan Asisten Rumah Tangga harian dan mingguan yang telah melalui verifikasi identitas resmi (KTP & SKCK). Dilindungi 100% oleh sistem <strong class="text-slate-900 font-semibold">Rekening Bersama (Rekber)</strong>.
                    </p>

                    <!-- Trust Checklist Items -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 text-xs sm:text-sm font-semibold text-slate-700">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0 text-xs font-bold">✓</div>
                            <span>Verifikasi KTP & Berkas Resmi</span>
                        </div>
                        <div class="flex items-center space-x-2.5">
                            <div class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0 text-xs font-bold">✓</div>
                            <span>Garansi Penggantian Pekerja</span>
                        </div>
                        <div class="flex items-center space-x-2.5">
                            <div class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0 text-xs font-bold">✓</div>
                            <span>Rekber: Gaji Cair Setelah Kerja Selesai</span>
                        </div>
                        <div class="flex items-center space-x-2.5">
                            <div class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0 text-xs font-bold">✓</div>
                            <span>Tarif Transparan Tanpa Biaya Tersembunyi</span>
                        </div>
                    </div>
                </div>

                <!-- Hero Right: Quick Filter & Booking Card -->
                <div class="lg:col-span-5">
                    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-200/50 border border-slate-200/80 relative">
                        <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                            <div>
                                <h2 class="text-lg font-extrabold text-slate-900">Cari Asisten di Daerah Anda</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Temukan mitra ART yang berdomisili dekat lokasi hunian Anda</p>
                            </div>
                            <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                        </div>

                        <form id="formProvinsi" onsubmit="handleProvinsiSearch(event)" class="space-y-4">
                            <div>
                                <label for="selectProvinsi" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">
                                    Pilih Provinsi Domisili
                                </label>
                                <div class="relative">
                                    <select id="selectProvinsi" class="w-full appearance-none bg-slate-50 border border-slate-300 hover:border-slate-400 focus:border-blue-600 focus:bg-white rounded-xl px-4 py-3 text-sm text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition">
                                        <option value="">-- Tampilkan Semua Provinsi --</option>
                                        @if(isset($provinsis) && $provinsis->count() > 0)
                                            @foreach($provinsis as $prov)
                                                <option value="{{ Str::slug($prov) }}">{{ $prov }}</option>
                                            @endforeach
                                        @else
                                            <option value="dki-jakarta">DKI Jakarta</option>
                                            <option value="jawa-barat">Jawa Barat</option>
                                            <option value="jawa-tengah">Jawa Tengah</option>
                                            <option value="jawa-timur">Jawa Timur</option>
                                            <option value="banten">Banten</option>
                                        @endif
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-2">
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 px-4 rounded-xl text-sm transition duration-150 shadow-md shadow-blue-600/25 flex items-center justify-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    <span>Temukan Pekerja Tersedia</span>
                                </button>
                            </div>
                        </form>

                        <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-xs bg-slate-50/80 -mx-6 sm:-mx-8 -mb-6 sm:-mb-8 px-6 sm:px-8 py-3.5 rounded-b-3xl border-t border-slate-200/60">
                            <span class="text-slate-600 flex items-center font-medium">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                                Ingin bekerja sebagai ART?
                            </span>
                            <a href="{{ route('register.seller') }}" class="font-bold text-emerald-700 hover:text-emerald-800 transition flex items-center space-x-1">
                                <span>Gabung Mitra ART</span>
                                <span>&rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- SECTION STATS & TRUST BADGES -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs flex items-center space-x-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0 text-xl font-black">
                    🛡️
                </div>
                <div>
                    <p class="text-xl sm:text-2xl font-black text-slate-900">100%</p>
                    <p class="text-xs text-slate-500 font-medium">Garansi Rekber Aman</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs flex items-center space-x-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0 text-xl font-black">
                    📄
                </div>
                <div>
                    <p class="text-xl sm:text-2xl font-black text-slate-900">KTP & SKCK</p>
                    <p class="text-xs text-slate-500 font-medium">Verifikasi Berkas Lengkap</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs flex items-center space-x-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0 text-xl font-black">
                    ⭐
                </div>
                <div>
                    <p class="text-xl sm:text-2xl font-black text-slate-900">4.9 / 5.0</p>
                    <p class="text-xs text-slate-500 font-medium">Rating Kepuasan Majikan</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs flex items-center space-x-4">
                <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0 text-xl font-black">
                    ⚡
                </div>
                <div>
                    <p class="text-xl sm:text-2xl font-black text-slate-900">Cepat</p>
                    <p class="text-xs text-slate-500 font-medium">Pesan Harian & Mingguan</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION PILIHAN AKSES: GABUNG MITRA ART vs DAFTAR MAJIKAN & MASUK (MOCKUP DISPLAY) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-10 space-y-3">
            <span class="inline-flex items-center space-x-2 text-xs font-bold uppercase tracking-wider text-blue-700 bg-blue-50 border border-blue-200/80 px-3.5 py-1.5 rounded-full shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                <span>Mulai Bersama MyBantu</span>
            </span>
            <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                Pilih Peran Anda di Platform Kami
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed max-w-xl mx-auto">
                Apakah Anda seorang majikan yang membutuhkan asisten rumah tangga terpercaya, atau pekerja yang ingin mendapatkan penghasilan pasti?
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-stretch">
            
            <!-- MOCKUP CARD 1: JALUR MAJIKAN (Pemberi Kerja) -->
            <div class="bg-gradient-to-br from-white via-slate-50/60 to-blue-50/30 rounded-3xl p-6 sm:p-8 border border-slate-200/90 hover:border-blue-300 transition-all duration-300 shadow-sm hover:shadow-xl flex flex-col justify-between group relative overflow-hidden">
                <!-- Ambient Glow Effect -->
                <div class="absolute -top-12 -right-12 w-48 h-48 bg-blue-500/10 rounded-full blur-3xl pointer-events-none group-hover:bg-blue-500/15 transition-all duration-300"></div>

                <div class="space-y-6 relative">
                    <!-- Top Badge & Header -->
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-blue-700 text-white flex items-center justify-center text-xl font-black shadow-md shadow-blue-500/25 group-hover:scale-105 transition-transform duration-200">
                                👨‍💼
                            </div>
                            <div>
                                <h3 class="text-lg font-extrabold text-slate-900">Untuk Majikan & Keluarga</h3>
                                <p class="text-xs text-slate-500">Mencari bantuan pekerjaan rumah tangga</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center text-[10px] font-extrabold text-blue-700 bg-blue-100/80 border border-blue-200 px-3 py-1 rounded-full">
                            Pemberi Kerja
                        </span>
                    </div>

                    <!-- Value Proposition -->
                    <div class="space-y-2">
                        <h4 class="text-base sm:text-lg font-extrabold text-slate-900 leading-snug">
                            Rekrut ART Terverifikasi dengan Garansi Rekber Aman 100%
                        </h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Pesan asisten rumah tangga harian atau mingguan yang telah melewati verifikasi KTP & SKCK. Dana aman tersimpan di Rekber resmi sampai tugas selesai tuntas.
                        </p>
                    </div>

                    <!-- UI MOCKUP PREVIEW WIDGET: Booking Ticket Simulation -->
                    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-2xs space-y-3">
                        <div class="flex items-center justify-between text-[11px] pb-2 border-b border-slate-100">
                            <span class="font-bold text-slate-700 flex items-center">
                                <span class="w-2 h-2 rounded-full bg-blue-600 mr-1.5"></span>
                                Simulasi Kartu Pemesanan
                            </span>
                            <span class="font-extrabold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-md text-[10px]">
                                ✓ Rekber Terlindungi
                            </span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center font-bold text-sm border border-slate-200">
                                👩‍🍳
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-slate-900 truncate">Siti Aminah • ART Terverifikasi</p>
                                <p class="text-[10px] text-slate-500 flex items-center space-x-1 mt-0.5">
                                    <span class="text-amber-500 font-bold">★ 4.9</span>
                                    <span>(18 ulasan puas)</span>
                                    <span>•</span>
                                    <span>Siap Kerja Harian/Mingguan</span>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-[10px] pt-1 text-slate-500 bg-slate-50 p-2 rounded-xl border border-slate-100">
                            <span>🛡️ Garansi Penggantian ART</span>
                            <span class="font-bold text-blue-700">Dana Cair Pasca Selesai</span>
                        </div>
                    </div>

                    <!-- Checklist -->
                    <div class="space-y-2 pt-1 text-xs font-semibold text-slate-700">
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-[10px] font-bold">✓</div>
                            <span>Pilih langsung ART terdekat sesuai kabupaten & provinsi domisili</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-[10px] font-bold">✓</div>
                            <span>Uang aman di Rekber: bayar di awal, diteruskan saat pekerjaan tuntas</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-[10px] font-bold">✓</div>
                            <span>Rating & ulasan transparan dari sesama majikan</span>
                        </div>
                    </div>
                </div>

                <!-- Call To Action Buttons (Majikan) -->
                <div class="pt-6 mt-6 border-t border-slate-100 space-y-2.5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <a href="{{ route('register') }}" 
                           class="w-full text-center bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 hover:from-blue-700 hover:to-indigo-700 text-white font-extrabold py-3.5 px-4 rounded-xl text-xs sm:text-sm transition-all duration-200 shadow-md shadow-blue-500/25 hover:shadow-lg hover:shadow-blue-500/35 hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center space-x-1.5">
                            <span>Daftar Akun Majikan</span>
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </a>
                        <a href="{{ route('login', ['role' => 'user']) }}" 
                           class="w-full text-center bg-white hover:bg-slate-100 text-slate-800 font-bold py-3.5 px-4 rounded-xl text-xs sm:text-sm transition duration-150 border border-slate-200/90 shadow-2xs hover:shadow-xs flex items-center justify-center space-x-1.5">
                            <svg class="w-4 h-4 text-slate-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            <span>Masuk Akun Majikan</span>
                        </a>
                    </div>
                    <p class="text-[11px] text-center text-slate-400">Pendaftaran cepat hanya butuh KTP & kontak aktif</p>
                </div>
            </div>

            <!-- MOCKUP CARD 2: JALUR MITRA ART (Pekerja / Penyedia Jasa) -->
            <div class="bg-gradient-to-br from-white via-slate-50/60 to-emerald-50/30 rounded-3xl p-6 sm:p-8 border border-slate-200/90 hover:border-emerald-300 transition-all duration-300 shadow-sm hover:shadow-xl flex flex-col justify-between group relative overflow-hidden">
                <!-- Ambient Glow Effect -->
                <div class="absolute -top-12 -right-12 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none group-hover:bg-emerald-500/15 transition-all duration-300"></div>

                <div class="space-y-6 relative">
                    <!-- Top Badge & Header -->
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-600 via-teal-600 to-emerald-700 text-white flex items-center justify-center text-xl font-black shadow-md shadow-emerald-500/25 group-hover:scale-105 transition-transform duration-200">
                                🧹
                            </div>
                            <div>
                                <h3 class="text-lg font-extrabold text-slate-900">Untuk Mitra ART & Pekerja</h3>
                                <p class="text-xs text-slate-500">Mencari penghasilan & pekerjaan terpercaya</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center text-[10px] font-extrabold text-emerald-800 bg-emerald-100/80 border border-emerald-200 px-3 py-1 rounded-full space-x-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Buka Lowongan</span>
                        </span>
                    </div>

                    <!-- Value Proposition -->
                    <div class="space-y-2">
                        <h4 class="text-base sm:text-lg font-extrabold text-slate-900 leading-snug">
                            Bekerja Fleksibel, Upah Bersih 85% Pasti Cair Tanpa Potongan Liar
                        </h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Tentukan sendiri jadwal kerja Anda (Harian / Mingguan). Setiap upah hasil kerja Anda dijamin 100% aman di Rekber dan langsung dapat dicairkan ke rekening bank atau e-wallet Anda.
                        </p>
                    </div>

                    <!-- UI MOCKUP PREVIEW WIDGET: Payout Voucher Simulation -->
                    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-2xs space-y-3">
                        <div class="flex items-center justify-between text-[11px] pb-2 border-b border-slate-100">
                            <span class="font-bold text-slate-700 flex items-center">
                                <span class="w-2 h-2 rounded-full bg-emerald-600 mr-1.5"></span>
                                Simulasi Pencairan Upah Mitra
                            </span>
                            <span class="font-extrabold text-blue-700 bg-blue-50 border border-blue-200 px-2 py-0.5 rounded-md text-[10px]">
                                Potongan Transparan 15%
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-[10px] text-slate-400 uppercase font-semibold">Total Nilai Transaksi</p>
                                <p class="text-xs font-bold text-slate-700 line-through">Rp 300.000</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-emerald-600 uppercase font-bold">Upah Bersih Diterima (85%)</p>
                                <p class="text-base font-black text-emerald-700">Rp 255.000</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-[10px] pt-1 text-slate-500 bg-slate-50 p-2 rounded-xl border border-slate-100">
                            <span>🏦 Transfer ke: BCA / Mandiri / BRI / E-Wallet</span>
                            <span class="font-bold text-emerald-700">Langsung Masuk Rekening</span>
                        </div>
                    </div>

                    <!-- Checklist -->
                    <div class="space-y-2 pt-1 text-xs font-semibold text-slate-700">
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] font-bold">✓</div>
                            <span>Pencairan dana langsung ke rekening bank atau dompet digital pilihan Anda</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] font-bold">✓</div>
                            <span>Tentukan tarif Anda sendiri secara bebas melalui panel pengaturan profil</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] font-bold">✓</div>
                            <span>Perlindungan resmi tanpa risiko upah tidak dibayar oleh majikan</span>
                        </div>
                    </div>
                </div>

                <!-- Call To Action Buttons (Mitra ART) -->
                <div class="pt-6 mt-6 border-t border-slate-100 space-y-2.5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <a href="{{ route('register.seller') }}" 
                           class="w-full text-center bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 hover:from-emerald-700 hover:to-teal-700 text-white font-extrabold py-3.5 px-4 rounded-xl text-xs sm:text-sm transition-all duration-200 shadow-md shadow-emerald-500/25 hover:shadow-lg hover:shadow-emerald-500/35 hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center space-x-1.5">
                            <span>Gabung Mitra ART Sekarang</span>
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </a>
                        <a href="{{ route('login', ['role' => 'worker']) }}" 
                           class="w-full text-center bg-white hover:bg-slate-100 text-slate-800 font-bold py-3.5 px-4 rounded-xl text-xs sm:text-sm transition duration-150 border border-slate-200/90 shadow-2xs hover:shadow-xs flex items-center justify-center space-x-1.5">
                            <svg class="w-4 h-4 text-slate-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            <span>Masuk Panel Mitra ART</span>
                        </a>
                    </div>
                    <p class="text-[11px] text-center text-slate-400">Pendaftaran gratis tanpa biaya pendaftaran di muka</p>
                </div>
            </div>

        </div>
    </section>

    <!-- SECTION REKOMENDASI WORKER TERBAIK -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 gap-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-blue-600 bg-blue-50 px-2.5 py-1 rounded-md">Pilihan Siap Kerja</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2">Rekomendasi Mitra ART Terbaik</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Pekerja telah siap dengan data terverifikasi dan siap membantu kenyamanan rumah Anda</p>
            </div>
            <a href="{{ route('product') }}" class="inline-flex items-center text-sm font-bold text-blue-600 hover:text-blue-700 group transition">
                <span>Lihat Seluruh Pekerja</span>
                <svg class="w-4 h-4 ml-1.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($workers ?? [] as $worker)
                @php
                    $activePrices = $worker->prices->filter(fn($p) => $p->harga > 0);
                    $minPrice = $activePrices->min('harga') ?? 0;
                    $minPriceRow = $activePrices->firstWhere('harga', $minPrice);
                    $avgRating = $worker->ratings && $worker->ratings->count() > 0 ? round($worker->ratings->avg('rating'), 1) : 0;
                    $reviewCount = $worker->ratings ? $worker->ratings->count() : 0;
                @endphp
                <div class="bg-white rounded-3xl overflow-hidden shadow-xs hover:shadow-lg transition-all duration-200 border border-slate-200/80 flex flex-col justify-between group">
                    <div>
                        <!-- Foto Worker Frame -->
                        <div class="h-52 bg-slate-100 relative overflow-hidden">
                            @if(!empty($worker->foto_path))
                                <img src="{{ asset('storage/' . $worker->foto_path) }}" 
                                     alt="{{ $worker->fullname }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 bg-gradient-to-br from-slate-100 to-slate-200">
                                    <svg class="w-12 h-12 mb-1 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span class="text-[11px] font-semibold text-slate-400">Foto Terverifikasi</span>
                                </div>
                            @endif

                            <!-- Status Badge Floating -->
                            <div class="absolute top-3 left-3">
                                @if(($worker->status_data ?? '') === 'sudah terverifikasi')
                                    <span class="inline-flex items-center text-[10px] font-bold bg-emerald-500/95 text-white px-2.5 py-1 rounded-full shadow-sm">
                                        ✓ Terverifikasi
                                    </span>
                                @else
                                    <span class="inline-flex items-center text-[10px] font-bold bg-amber-500/95 text-white px-2.5 py-1 rounded-full shadow-sm">
                                        Sedang Verifikasi
                                    </span>
                                @endif
                            </div>

                            <div class="absolute top-3 right-3">
                                <span class="text-[10px] font-bold bg-slate-900/80 backdrop-blur-xs text-white px-2 py-0.5 rounded-md">
                                    {{ $worker->status_worker ?? 'AKTIF' }}
                                </span>
                            </div>
                        </div>

                        <!-- Info Worker -->
                        <div class="p-5 space-y-3">
                            <div>
                                <div class="flex items-center justify-between gap-2">
                                    <h3 class="font-bold text-slate-900 text-base leading-snug group-hover:text-blue-600 transition truncate">
                                        {{ $worker->fullname }}
                                    </h3>
                                    <div class="flex items-center space-x-1 text-amber-500 font-bold text-xs flex-shrink-0 bg-amber-50 px-2 py-0.5 rounded-lg border border-amber-200/60">
                                        <span>★</span>
                                        <span class="text-slate-800">{{ $avgRating > 0 ? number_format($avgRating, 1) : '5.0' }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between mt-1 text-xs text-slate-500">
                                    <p class="flex items-center truncate">
                                        <svg class="w-3.5 h-3.5 mr-1 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        </svg>
                                        <span class="truncate">{{ $worker->kabupaten }}, {{ $worker->provinsi }}</span>
                                    </p>
                                    @if($reviewCount > 0)
                                        <span class="text-[10px] text-slate-400 flex-shrink-0">({{ $reviewCount }} ulasan)</span>
                                    @endif
                                </div>
                            </div>

                            <!-- List Opsi Harga dari Tabel price_worker -->
                            <div class="space-y-1 pt-1 border-t border-slate-100">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Tarif Layanan:</span>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($activePrices as $priceItem)
                                        <span class="inline-flex items-center text-[10px] font-bold text-slate-800 bg-slate-50 px-2 py-0.5 rounded-md border border-slate-200">
                                            <span class="w-1.5 h-1.5 rounded-full {{ stripos($priceItem->tipe_pekerjaan, 'mingguan') !== false ? 'bg-purple-500' : 'bg-emerald-500' }} mr-1"></span>
                                            {{ $priceItem->tipe_pekerjaan }}: Rp {{ number_format($priceItem->harga, 0, ',', '.') }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Starting Price -->
                            @if($minPrice > 0)
                                <div class="pt-2 border-t border-slate-100 flex items-baseline justify-between text-xs">
                                    <span class="text-slate-400 font-medium">Mulai dari</span>
                                    <span class="font-extrabold text-blue-700 text-sm">
                                        Rp {{ number_format($minPrice, 0, ',', '.') }}
                                        <span class="text-[10px] text-slate-400 font-normal">
                                            /{{ $minPriceRow && stripos($minPriceRow->tipe_pekerjaan, 'mingguan') !== false ? 'minggu' : 'hari' }}
                                        </span>
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Button Pesan -->
                    <div class="px-5 pb-5 pt-1">
                        <a href="{{ route('product.detail', $worker->id) }}" 
                           class="block w-full text-center bg-slate-900 hover:bg-blue-600 text-white font-bold py-2.5 rounded-xl text-xs transition duration-150 shadow-xs">
                            Lihat Profil & Pesan
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-3xl p-12 text-center border border-slate-200/80 shadow-xs space-y-4">
                    <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-2xl">
                        👥
                    </div>
                    <div class="max-w-md mx-auto">
                        <h3 class="text-base font-bold text-slate-800">Belum Ada Data Pekerja</h3>
                        <p class="text-xs text-slate-500 mt-1">Saat ini belum ada data mitra asisten rumah tangga yang aktif. Daftarkan diri Anda sebagai mitra sekarang.</p>
                    </div>
                    <a href="{{ route('register.seller') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-xs transition">
                        Daftar Sebagai Mitra ART
                    </a>
                </div>
            @endforelse
        </div>
    </section>

    <!-- SECTION CARA KERJA REKBER MYBANTU (Trust Assurance) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-950 rounded-3xl p-8 sm:p-12 text-white shadow-xl relative overflow-hidden">
            <div class="max-w-2xl mb-10 space-y-3">
                <span class="bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 text-[11px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                    Perlindungan 100% Majikan & Mitra
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight">Bagaimana Sistem Rekber Melindungi Anda?</h2>
                <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                    Kami memastikan tidak ada pihak yang dirugikan. Uang Anda aman sampai pekerjaan benar-benar selesai dilakukan.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white/10 backdrop-blur-xs p-6 rounded-2xl border border-white/10 space-y-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-500 text-white font-extrabold flex items-center justify-center text-sm">1</div>
                    <h3 class="font-bold text-sm text-white">Pilih & Buat Pesanan</h3>
                    <p class="text-xs text-slate-300 leading-relaxed">Pilih pekerja sesuai kriteria, tentukan tanggal mulai dan durasi (harian/mingguan).</p>
                </div>

                <div class="bg-white/10 backdrop-blur-xs p-6 rounded-2xl border border-white/10 space-y-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-500 text-white font-extrabold flex items-center justify-center text-sm">2</div>
                    <h3 class="font-bold text-sm text-white">Bayar ke Rekber</h3>
                    <p class="text-xs text-slate-300 leading-relaxed">Dana disimpan di rekening penampung resmi platform. Pekerja belum menerima uang sampai kerja selesai.</p>
                </div>

                <div class="bg-white/10 backdrop-blur-xs p-6 rounded-2xl border border-white/10 space-y-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-500 text-white font-extrabold flex items-center justify-center text-sm">3</div>
                    <h3 class="font-bold text-sm text-white">Pekerja Bertugas</h3>
                    <p class="text-xs text-slate-300 leading-relaxed">Mitra ART datang sesuai jadwal dan menyelesaikan tugas kerumahtanggaan Anda.</p>
                </div>

                <div class="bg-white/10 backdrop-blur-xs p-6 rounded-2xl border border-white/10 space-y-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500 text-white font-extrabold flex items-center justify-center text-sm">4</div>
                    <h3 class="font-bold text-sm text-white">Konfirmasi & Cair</h3>
                    <p class="text-xs text-slate-300 leading-relaxed">Setelah Anda puas dengan hasil kerja, konfirmasi di aplikasi dan dana diteruskan ke pekerja.</p>
                </div>
            </div>
        </div>
    </section>

</div>

<!-- JAVASCRIPT REDIRECT SEARCH KE FORMAT SLUG URL -->
<script>
    function handleProvinsiSearch(event) {
        event.preventDefault();
        
        const selectElement = document.getElementById('selectProvinsi');
        const selectedValue = selectElement.value;
        
        if (selectedValue) {
            window.location.href = "{{ url('/product/provinsi') }}/" + selectedValue;
        } else {
            window.location.href = "{{ route('product') }}";
        }
    }
</script>
@endsection
