@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    
    <!-- BREADCRUMB & HEADER -->
    <div class="space-y-3">
        <div class="flex items-center space-x-2 text-xs font-semibold text-slate-500">
            <a href="{{ route('home') }}" class="hover:text-blue-600 transition">Beranda</a>
            <span>/</span>
            <a href="{{ route('product') }}" class="hover:text-blue-600 transition">Daftar ART</a>
            <span>/</span>
            <span class="text-slate-900 font-bold">Provinsi {{ $provinsiName }}</span>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-200/80 pb-6 gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Mitra Asisten Rumah Tangga - Provinsi {{ $provinsiName }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Daftar seluruh pekerja terverifikasi yang siap bertugas di wilayah {{ $provinsiName }}.
                </p>
            </div>
            
            <a href="{{ route('product') }}" 
               class="inline-flex items-center text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 px-4 py-2.5 rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Semua Wilayah
            </a>
        </div>
    </div>

    <!-- WORKER LIST GRID -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($workers as $w)
            @php
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
                            <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 bg-slate-100">
                                <svg class="w-12 h-12 mb-1 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="text-xs">Foto Belum Tersedia</span>
                            </div>
                        @endif

                        <div class="absolute top-3 left-3">
                            @if(($w->status_data ?? '') === 'sudah terverifikasi')
                                <span class="inline-flex items-center text-[10px] font-bold bg-emerald-500 text-white px-2.5 py-1 rounded-full shadow-sm">
                                    ✓ Terverifikasi
                                </span>
                            @else
                                <span class="inline-flex items-center text-[10px] font-bold bg-amber-500 text-white px-2.5 py-1 rounded-full shadow-sm">
                                    Verifikasi Berkas
                                </span>
                            @endif
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
                <div class="px-5 pb-5">
                    <a href="{{ route('product.detail', $w->id) }}" 
                       class="block w-full text-center bg-slate-900 hover:bg-blue-600 text-white font-bold py-2.5 rounded-xl text-xs transition duration-150 shadow-xs">
                        Pesan Pekerja
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-3xl p-12 text-center border border-slate-200/80 shadow-xs space-y-4">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-2xl">
                    📍
                </div>
                <div class="max-w-md mx-auto">
                    <h3 class="text-base font-bold text-slate-800">Belum Ada Pekerja di {{ $provinsiName }}</h3>
                    <p class="text-xs text-slate-500 mt-1">Saat ini belum ada mitra ART yang terdaftar di wilayah ini. Anda dapat mendaftar sebagai mitra untuk wilayah ini.</p>
                </div>
                <a href="{{ route('register.seller') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl transition">
                    + Daftarkan Diri Jadi Mitra di {{ $provinsiName }}
                </a>
            </div>
        @endforelse
    </div>

</div>
@endsection
