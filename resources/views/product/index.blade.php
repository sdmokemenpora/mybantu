@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    
    <!-- PAGE HEADER -->
    <div class="flex flex-col md:flex-row md:items-end justify-between border-b border-slate-200/80 pb-6 gap-4">
        <div>
            <div class="inline-flex items-center space-x-2 bg-blue-50 text-blue-700 text-xs font-bold px-3 py-1 rounded-full mb-2">
                <span>📍 Katalog Resmi Mitra ART</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Daftar Asisten Rumah Tangga
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Daftar mitra ART aktif yang telah menetapkan tarif layanan resmi di tabel sistem Rekber MyBantu.
            </p>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('register.seller') }}" class="inline-flex items-center text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-4 py-2.5 rounded-xl transition">
                <span>+ Gabung Mitra ART</span>
            </a>
        </div>
    </div>

    <!-- FILTER & PENCARIAN BAR -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-xs space-y-4">
        <form action="{{ route('product') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3">
            
            <!-- Input Keyword Pencarian -->
            <div class="md:col-span-5 relative">
                <label for="q" class="sr-only">Cari ART</label>
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" 
                       name="q" 
                       id="q" 
                       value="{{ request('q') }}" 
                       placeholder="Cari nama ART, kabupaten, atau kota..." 
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
            </div>

            <!-- Dropdown Filter Provinsi -->
            <div class="md:col-span-4 relative">
                <label for="provinsi" class="sr-only">Pilih Provinsi</label>
                <select name="provinsi" 
                        id="provinsi" 
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                    <option value="">Semua Wilayah Provinsi</option>
                    @foreach($provinsis as $prov)
                        <option value="{{ $prov }}" {{ request('provinsi') == $prov ? 'selected' : '' }}>
                            Provinsi {{ $prov }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tombol Aksi -->
            <div class="md:col-span-3 flex items-center space-x-2">
                <button type="submit" 
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-xl text-xs sm:text-sm transition duration-150 shadow-xs flex items-center justify-center space-x-1.5">
                    <span>Terapkan</span>
                </button>
                @if(request()->filled('q') || request()->filled('provinsi'))
                    <a href="{{ route('product') }}" 
                       class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition"
                       title="Reset Filter">
                        ✕ Reset
                    </a>
                @endif
            </div>
        </form>

        <!-- Quick Filter Pills Provinsi -->
        @if($provinsis->isNotEmpty())
            <div class="pt-3 border-t border-slate-100 flex items-center space-x-2 overflow-x-auto text-xs pb-1">
                <span class="text-slate-400 text-[11px] font-bold uppercase flex-shrink-0">Filter Cepat:</span>
                <a href="{{ route('product') }}" 
                   class="px-3 py-1 rounded-lg text-xs font-bold transition flex-shrink-0 {{ !request('provinsi') ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Semua ({{ $workers->count() }})
                </a>
                @foreach($provinsis as $pName)
                    @php
                        $isProvActive = request('provinsi') === $pName;
                    @endphp
                    <a href="{{ route('product', ['provinsi' => $pName]) }}" 
                       class="px-3 py-1 rounded-lg text-xs font-bold transition flex-shrink-0 {{ $isProvActive ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        {{ $pName }}
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    <!-- DAFTAR WORKER (ART) GRID -->
    <div>
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-base sm:text-lg font-extrabold text-slate-900">
                Mitra ART Terdaftar ({{ $workers->count() }})
            </h2>
            <span class="text-xs text-slate-400 font-semibold">
                Tarif resmi langsung dari database price_worker
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($workers as $w)
                @php
                    // Ambil harga aktif yang > 0 dari relasi price_worker
                    $activePrices = $w->prices->filter(fn($p) => $p->harga > 0);
                    $minPrice = $activePrices->min('harga') ?? 0;
                    $minPriceRow = $activePrices->firstWhere('harga', $minPrice);
                    $avgRating = $w->ratings && $w->ratings->count() > 0 ? round($w->ratings->avg('rating'), 1) : 0;
                    $reviewCount = $w->ratings ? $w->ratings->count() : 0;
                @endphp

                <div class="bg-white rounded-3xl overflow-hidden shadow-xs hover:shadow-lg transition-all duration-200 border border-slate-200/80 flex flex-col justify-between group">
                    <div>
                        <!-- Foto Worker Frame -->
                        <div class="h-48 bg-slate-100 relative overflow-hidden">
                            @if(!empty($w->foto_path))
                                <img src="{{ asset('storage/' . $w->foto_path) }}" 
                                     alt="{{ $w->fullname }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 bg-gradient-to-br from-slate-100 to-slate-200">
                                    <svg class="w-12 h-12 mb-1 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span class="text-[11px] font-semibold text-slate-400">Foto Belum Diunggah</span>
                                </div>
                            @endif

                            <div class="absolute top-3 left-3">
                                @if(($w->status_data ?? '') === 'sudah terverifikasi')
                                    <span class="inline-flex items-center text-[10px] font-bold bg-emerald-500 text-white px-2.5 py-1 rounded-full shadow-sm">
                                        ✓ Terverifikasi
                                    </span>
                                @else
                                    <span class="inline-flex items-center text-[10px] font-bold bg-amber-500 text-white px-2.5 py-1 rounded-full shadow-sm">
                                        Belum Terverifikasi
                                    </span>
                                @endif
                            </div>

                            <div class="absolute top-3 right-3">
                                <span class="inline-flex items-center text-[9px] font-bold bg-slate-900/80 text-white px-2 py-0.5 rounded-md backdrop-blur-xs">
                                    {{ $w->status_worker ?? 'AKTIF' }}
                                </span>
                            </div>
                        </div>

                        <!-- Info Worker -->
                        <div class="p-5 space-y-3">
                            <div>
                                <div class="flex items-center justify-between gap-2">
                                    <h3 class="font-bold text-slate-900 text-base leading-snug group-hover:text-blue-600 transition truncate">
                                        {{ $w->fullname }}
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
                                        <span class="truncate">{{ $w->kabupaten }}, {{ $w->provinsi }}</span>
                                    </p>
                                    @if($reviewCount > 0)
                                        <span class="text-[10px] text-slate-400 flex-shrink-0">({{ $reviewCount }} ulasan)</span>
                                    @endif
                                </div>
                            </div>

                            <!-- List Opsi Harga dari Tabel price_worker -->
                            <div class="space-y-1 pt-1 border-t border-slate-100">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Tarif Layanan Aktif:</span>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($activePrices as $priceItem)
                                        <span class="inline-flex items-center text-[11px] font-bold text-slate-800 bg-slate-50 px-2 py-1 rounded-lg border border-slate-200">
                                            <span class="w-1.5 h-1.5 rounded-full {{ stripos($priceItem->tipe_pekerjaan, 'mingguan') !== false ? 'bg-purple-500' : 'bg-emerald-500' }} mr-1.5"></span>
                                            {{ $priceItem->tipe_pekerjaan }}: Rp {{ number_format($priceItem->harga, 0, ',', '.') }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Minimum Rate Info -->
                            @if($minPrice > 0)
                                <div class="pt-2 border-t border-slate-100 flex items-baseline justify-between text-xs">
                                    <span class="text-slate-400 text-[11px]">Tarif mulai</span>
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
                        <a href="{{ route('product.detail', $w->id) }}" 
                           class="block w-full text-center bg-slate-900 hover:bg-blue-600 text-white font-bold py-2.5 rounded-xl text-xs transition duration-150 shadow-xs">
                            Pesan ART Sekarang &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-3xl p-12 text-center border border-slate-200/80 shadow-xs space-y-4">
                    <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-2xl">
                        🔍
                    </div>
                    <div class="max-w-md mx-auto">
                        <h3 class="text-base font-bold text-slate-800">Tidak Ada Mitra ART Ditemukan</h3>
                        <p class="text-xs text-slate-500 mt-1">
                            @if(request()->filled('q') || request()->filled('provinsi'))
                                Tidak ditemukan mitra ART yang sesuai dengan filter pencarian Anda. Coba reset filter atau gunakan kata kunci lain.
                            @else
                                Belum ada mitra ART yang menetapkan tarif layanan di tabel harga. Silakan atur tarif terlebih dahulu melalui menu profil mitra.
                            @endif
                        </p>
                    </div>
                    @if(request()->filled('q') || request()->filled('provinsi'))
                        <div>
                            <a href="{{ route('product') }}" class="inline-block bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs px-5 py-2.5 rounded-xl transition">
                                Reset Semua Filter
                            </a>
                        </div>
                    @else
                        <div>
                            <a href="{{ route('register.seller') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl transition">
                                + Gabung Jadi Mitra ART
                            </a>
                        </div>
                    @endif
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
