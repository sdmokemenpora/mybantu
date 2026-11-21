@extends('layouts.admin')

@section('title', 'Worker & User Majikan Terbaik')
@section('page_title', 'Data Worker & User Majikan Terbaik')

@section('content')
<div class="space-y-6 pt-2">

    <!-- HEADER & INTRO -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-black text-slate-900 tracking-tight">Leaderboard & Peringkat Terbaik</h1>
            <p class="text-xs text-slate-500">Peringkat Worker terbaik ditentukan murni dari jumlah pesanan selesai dan rating bintang tertinggi. Peringkat Majikan ditentukan dari total pesanan terbayar dan pesanan selesai.</p>
        </div>

        <!-- TAB BUTTONS -->
        <div class="flex items-center space-x-2 bg-white p-1 rounded-2xl border border-slate-200 shadow-2xs">
            <a href="{{ route('admin.best.index', ['tab' => 'worker']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-extrabold transition flex items-center space-x-1.5 {{ $tab === 'worker' ? 'bg-amber-500 text-slate-950 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                <span>🏆 Worker Terbaik</span>
            </a>
            <a href="{{ route('admin.best.index', ['tab' => 'user']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-extrabold transition flex items-center space-x-1.5 {{ $tab === 'user' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                <span>🌟 Majikan Terbaik</span>
            </a>
        </div>
    </div>

    @if($tab === 'worker')
        <!-- ========================================== -->
        <!-- TAB 1: DATA WORKER TERBAIK -->
        <!-- ========================================== -->
        
        <!-- PODIUM TOP 3 WORKER -->
        @if($topWorkers->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 pt-2">
                @php
                    $goldWorker = $topWorkers->get(0);
                    $silverWorker = $topWorkers->get(1);
                    $bronzeWorker = $topWorkers->get(2);
                @endphp

                <!-- JUARA 2: PERAK (KIRI PADA DESKTOP) -->
                @if($silverWorker)
                    <div class="bg-white rounded-3xl border border-slate-300 p-6 shadow-xs flex flex-col justify-between space-y-4 relative overflow-hidden order-2 md:order-1">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center space-x-1 text-xs font-black uppercase tracking-wider text-slate-700 bg-slate-100 border border-slate-300 px-2.5 py-1 rounded-full">
                                <span>🥈 Juara 2 (Perak)</span>
                            </span>
                            <span class="text-xs font-mono font-bold text-slate-400">#WRK-{{ str_pad($silverWorker->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>

                        <div class="text-center space-y-2">
                            @if(!empty($silverWorker->foto_path))
                                <img src="{{ asset('storage/' . $silverWorker->foto_path) }}" alt="{{ $silverWorker->fullname }}" class="w-20 h-20 rounded-3xl object-cover mx-auto border-2 border-slate-300 shadow-md">
                            @else
                                <div class="w-20 h-20 rounded-3xl bg-slate-200 text-slate-700 font-black text-2xl flex items-center justify-center mx-auto shadow-md">
                                    {{ strtoupper(substr($silverWorker->fullname, 0, 1)) }}
                                </div>
                            @endif
                            <h3 class="font-extrabold text-base text-slate-900">{{ $silverWorker->fullname }}</h3>
                            <p class="text-xs text-slate-500">{{ $silverWorker->kabupaten }}, {{ $silverWorker->provinsi }}</p>
                            
                            <div class="inline-flex items-center space-x-1 bg-amber-50 border border-amber-200 px-3 py-1 rounded-full text-amber-700 font-extrabold text-sm">
                                <span>★</span>
                                <span>{{ number_format((float)$silverWorker->avg_rating, 1) }}</span>
                                <span class="text-xs text-slate-400 font-medium">({{ $silverWorker->total_reviews }} ulasan)</span>
                            </div>
                        </div>

                        <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100 space-y-1.5 text-xs">
                            <div class="flex justify-between">
                                <span class="text-slate-400">Pesanan Selesai:</span>
                                <span class="font-bold text-slate-800">{{ $silverWorker->completed_orders }} Order</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Penghasilan Bersih (85%):</span>
                                <span class="font-bold text-emerald-700">Rp {{ number_format($silverWorker->total_earned, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <a href="{{ route('admin.workers.show', $silverWorker->id) }}" class="block text-center py-2 text-xs font-bold text-slate-700 hover:text-blue-600 bg-slate-50 hover:bg-slate-100 rounded-xl transition border border-slate-200">
                            Lihat Profil Lengkap &rarr;
                        </a>
                    </div>
                @endif

                <!-- JUARA 1: EMAS (TENGAH & LEBIH BESAR) -->
                @if($goldWorker)
                    <div class="bg-gradient-to-b from-amber-50/80 via-white to-amber-50/30 rounded-3xl border-2 border-amber-400 p-6 sm:p-7 shadow-lg flex flex-col justify-between space-y-4 relative overflow-hidden order-1 md:order-2 md:-mt-4">
                        <div class="absolute -top-12 -right-12 w-32 h-32 bg-amber-400/20 rounded-full blur-xl pointer-events-none"></div>

                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center space-x-1.5 text-xs font-black uppercase tracking-wider text-amber-950 bg-amber-400 border border-amber-500 px-3 py-1 rounded-full shadow-xs">
                                <span>🥇 JUARA 1 (EMAS)</span>
                            </span>
                            <span class="text-xs font-mono font-bold text-amber-700">#WRK-{{ str_pad($goldWorker->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>

                        <div class="text-center space-y-2">
                            @if(!empty($goldWorker->foto_path))
                                <img src="{{ asset('storage/' . $goldWorker->foto_path) }}" alt="{{ $goldWorker->fullname }}" class="w-24 h-24 rounded-3xl object-cover mx-auto border-4 border-amber-300 shadow-xl">
                            @else
                                <div class="w-24 h-24 rounded-3xl bg-gradient-to-tr from-amber-400 to-amber-500 text-amber-950 font-black text-3xl flex items-center justify-center mx-auto shadow-xl">
                                    {{ strtoupper(substr($goldWorker->fullname, 0, 1)) }}
                                </div>
                            @endif
                            <h3 class="font-black text-lg text-slate-900">{{ $goldWorker->fullname }}</h3>
                            <p class="text-xs text-slate-500 font-semibold">{{ $goldWorker->kabupaten }}, {{ $goldWorker->provinsi }}</p>
                            
                            <div class="inline-flex items-center space-x-1.5 bg-amber-100 border border-amber-300 px-4 py-1.5 rounded-full text-amber-800 font-black text-base shadow-2xs">
                                <span>★</span>
                                <span>{{ number_format((float)$goldWorker->avg_rating, 1) }}</span>
                                <span class="text-xs text-amber-900/80 font-bold">({{ $goldWorker->total_reviews }} ulasan)</span>
                            </div>
                        </div>

                        <div class="bg-amber-100/50 p-4 rounded-2xl border border-amber-200 space-y-2 text-xs">
                            <div class="flex justify-between">
                                <span class="text-amber-900 font-semibold">Pesanan Selesai:</span>
                                <span class="font-black text-amber-950 text-sm">{{ $goldWorker->completed_orders }} Order</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-amber-900 font-semibold">Penghasilan Bersih (85%):</span>
                                <span class="font-bold text-emerald-700">Rp {{ number_format($goldWorker->total_earned, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <a href="{{ route('admin.workers.show', $goldWorker->id) }}" class="block text-center py-2.5 text-xs font-black text-slate-950 bg-amber-400 hover:bg-amber-500 rounded-xl transition shadow-xs">
                            Lihat Profil Juara 1 &rarr;
                        </a>
                    </div>
                @endif

                <!-- JUARA 3: PERUNGGU (KANAN PADA DESKTOP) -->
                @if($bronzeWorker)
                    <div class="bg-white rounded-3xl border border-amber-700/30 p-6 shadow-xs flex flex-col justify-between space-y-4 relative overflow-hidden order-3">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center space-x-1 text-xs font-black uppercase tracking-wider text-amber-900 bg-amber-100 border border-amber-300 px-2.5 py-1 rounded-full">
                                <span>🥉 Juara 3 (Perunggu)</span>
                            </span>
                            <span class="text-xs font-mono font-bold text-slate-400">#WRK-{{ str_pad($bronzeWorker->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>

                        <div class="text-center space-y-2">
                            @if(!empty($bronzeWorker->foto_path))
                                <img src="{{ asset('storage/' . $bronzeWorker->foto_path) }}" alt="{{ $bronzeWorker->fullname }}" class="w-20 h-20 rounded-3xl object-cover mx-auto border-2 border-amber-700/30 shadow-md">
                            @else
                                <div class="w-20 h-20 rounded-3xl bg-amber-100 text-amber-900 font-black text-2xl flex items-center justify-center mx-auto shadow-md">
                                    {{ strtoupper(substr($bronzeWorker->fullname, 0, 1)) }}
                                </div>
                            @endif
                            <h3 class="font-extrabold text-base text-slate-900">{{ $bronzeWorker->fullname }}</h3>
                            <p class="text-xs text-slate-500">{{ $bronzeWorker->kabupaten }}, {{ $bronzeWorker->provinsi }}</p>
                            
                            <div class="inline-flex items-center space-x-1 bg-amber-50 border border-amber-200 px-3 py-1 rounded-full text-amber-700 font-extrabold text-sm">
                                <span>★</span>
                                <span>{{ number_format((float)$bronzeWorker->avg_rating, 1) }}</span>
                                <span class="text-xs text-slate-400 font-medium">({{ $bronzeWorker->total_reviews }} ulasan)</span>
                            </div>
                        </div>

                        <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100 space-y-1.5 text-xs">
                            <div class="flex justify-between">
                                <span class="text-slate-400">Pesanan Selesai:</span>
                                <span class="font-bold text-slate-800">{{ $bronzeWorker->completed_orders }} Order</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Penghasilan Bersih (85%):</span>
                                <span class="font-bold text-emerald-700">Rp {{ number_format($bronzeWorker->total_earned, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <a href="{{ route('admin.workers.show', $bronzeWorker->id) }}" class="block text-center py-2 text-xs font-bold text-slate-700 hover:text-blue-600 bg-slate-50 hover:bg-slate-100 rounded-xl transition border border-slate-200">
                            Lihat Profil Lengkap &rarr;
                        </a>
                    </div>
                @endif
            </div>
        @endif

        <!-- TABEL LENGKAP LEADERBOARD WORKER -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900 uppercase tracking-wide">
                        Peringkat Lengkap Mitra ART Berprestasi
                    </h2>
                    <p class="text-xs text-slate-400">Peringkat diurutkan dari jumlah pesanan selesai terbanyak, kemudian rata-rata rating bintang tertinggi.</p>
                </div>
                <span class="text-xs bg-amber-50 text-amber-800 font-bold px-3 py-1 rounded-full border border-amber-200">
                    Top 20 Mitra
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 text-slate-500 uppercase tracking-wider border-b border-slate-200 text-[10px]">
                            <th class="py-3.5 px-4 font-bold text-center">Rank</th>
                            <th class="py-3.5 px-4 font-bold">Mitra ART</th>
                            <th class="py-3.5 px-4 font-bold">Lokasi</th>
                            <th class="py-3.5 px-4 font-bold">Rating Bintang</th>
                            <th class="py-3.5 px-4 font-bold">Order Selesai</th>
                            <th class="py-3.5 px-4 font-bold">Penghasilan Cair</th>
                            <th class="py-3.5 px-4 font-bold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($topWorkers as $rank => $w)
                            <tr class="hover:bg-slate-50/60 transition {{ $rank < 3 ? 'bg-amber-50/20' : '' }}">
                                <td class="py-3.5 px-4 text-center font-black">
                                    @if($rank === 0)
                                        <span class="inline-flex w-7 h-7 rounded-full bg-amber-400 text-amber-950 items-center justify-center text-xs shadow-xs">🥇 1</span>
                                    @elseif($rank === 1)
                                        <span class="inline-flex w-7 h-7 rounded-full bg-slate-300 text-slate-800 items-center justify-center text-xs">🥈 2</span>
                                    @elseif($rank === 2)
                                        <span class="inline-flex w-7 h-7 rounded-full bg-amber-700/30 text-amber-900 items-center justify-center text-xs">🥉 3</span>
                                    @else
                                        <span class="text-slate-500 font-mono text-xs">#{{ $rank + 1 }}</span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4">
                                    <div class="flex items-center space-x-3">
                                        @if(!empty($w->foto_path))
                                             <img src="{{ asset('storage/' . $w->foto_path) }}" alt="{{ $w->fullname }}" class="w-9 h-9 rounded-xl object-cover border border-slate-200">
                                        @else
                                            <div class="w-9 h-9 rounded-xl bg-slate-200 text-slate-700 font-extrabold text-xs flex items-center justify-center">
                                                {{ strtoupper(substr($w->fullname, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <a href="{{ route('admin.workers.show', $w->id) }}" class="font-extrabold text-slate-900 hover:text-blue-600 block">
                                                {{ $w->fullname }}
                                            </a>
                                            <span class="font-mono text-[10px] text-slate-400">#WRK-{{ str_pad($w->id, 5, '0', STR_PAD_LEFT) }}</span>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3.5 px-4">
                                    <p class="font-bold text-slate-800">{{ $w->kabupaten }}</p>
                                    <p class="text-slate-400 text-[11px]">{{ $w->provinsi }}</p>
                                </td>

                                <td class="py-3.5 px-4">
                                    <div class="flex items-center space-x-1 text-amber-500 font-extrabold">
                                        <span>★</span>
                                        <span>{{ number_format((float)$w->avg_rating, 1) }}</span>
                                    </div>
                                    <span class="text-[10px] text-slate-400">({{ $w->total_reviews }} review)</span>
                                </td>

                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        {{ $w->completed_orders }} Selesai
                                    </span>
                                </td>

                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-emerald-700 text-xs">
                                        Rp {{ number_format($w->total_earned, 0, ',', '.') }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4 text-center">
                                    <a href="{{ route('admin.workers.show', $w->id) }}" class="text-blue-600 hover:text-blue-800 font-bold text-xs">
                                        Detail &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    Belum ada data worker yang memiliki penilaian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @else
        <!-- ========================================== -->
        <!-- TAB 2: DATA USER MAJIKAN TERBAIK -->
        <!-- ========================================== -->

        <!-- PODIUM TOP 3 MAJIKAN -->
        @if($topUsers->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 pt-2">
                @php
                    $goldUser = $topUsers->get(0);
                    $silverUser = $topUsers->get(1);
                    $bronzeUser = $topUsers->get(2);
                @endphp

                <!-- JUARA 2: PERAK MAJIKAN -->
                @if($silverUser)
                    <div class="bg-white rounded-3xl border border-slate-300 p-6 shadow-xs flex flex-col justify-between space-y-4 relative overflow-hidden order-2 md:order-1">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center space-x-1 text-xs font-black uppercase tracking-wider text-slate-700 bg-slate-100 border border-slate-300 px-2.5 py-1 rounded-full">
                                <span>🥈 Juara 2 (Perak)</span>
                            </span>
                            <span class="text-xs font-mono font-bold text-slate-400">#USR-{{ str_pad($silverUser->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>

                        <div class="text-center space-y-2">
                            <div class="w-20 h-20 rounded-3xl bg-blue-100 text-blue-800 font-black text-2xl flex items-center justify-center mx-auto shadow-md">
                                {{ strtoupper(substr($silverUser->name, 0, 1)) }}
                            </div>
                            <h3 class="font-extrabold text-base text-slate-900">{{ $silverUser->name }}</h3>
                            <p class="text-xs text-slate-500">{{ $silverUser->kabupaten ?? '-' }}</p>
                            
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200 text-xs font-black">
                                {{ $silverUser->total_orders }} Transaksi Terbayar
                            </span>
                        </div>

                        <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100 space-y-1.5 text-xs">
                            <div class="flex justify-between">
                                <span class="text-slate-400">Pesanan Selesai:</span>
                                <span class="font-bold text-slate-800">{{ $silverUser->completed_orders }} Selesai</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Ulasan Diberikan:</span>
                                <span class="font-bold text-slate-700">{{ $silverUser->reviews_given }} Ulasan</span>
                            </div>
                        </div>

                        <a href="{{ route('admin.users.show', $silverUser->id) }}" class="block text-center py-2 text-xs font-bold text-slate-700 hover:text-blue-600 bg-slate-50 hover:bg-slate-100 rounded-xl transition border border-slate-200">
                            Lihat Profil Majikan &rarr;
                        </a>
                    </div>
                @endif

                <!-- JUARA 1: EMAS MAJIKAN -->
                @if($goldUser)
                    <div class="bg-gradient-to-b from-blue-50/80 via-white to-blue-50/30 rounded-3xl border-2 border-blue-500 p-6 sm:p-7 shadow-lg flex flex-col justify-between space-y-4 relative overflow-hidden order-1 md:order-2 md:-mt-4">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center space-x-1.5 text-xs font-black uppercase tracking-wider text-white bg-blue-600 border border-blue-700 px-3 py-1 rounded-full shadow-xs">
                                <span>🥇 JUARA 1 MAJIKAN</span>
                            </span>
                            <span class="text-xs font-mono font-bold text-blue-700">#USR-{{ str_pad($goldUser->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>

                        <div class="text-center space-y-2">
                            <div class="w-24 h-24 rounded-3xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-black text-3xl flex items-center justify-center mx-auto shadow-xl">
                                {{ strtoupper(substr($goldUser->name, 0, 1)) }}
                            </div>
                            <h3 class="font-black text-lg text-slate-900">{{ $goldUser->name }}</h3>
                            <p class="text-xs text-slate-500 font-semibold">{{ $goldUser->kabupaten ?? '-' }}, {{ $goldUser->provinsi ?? '-' }}</p>
                            
                            <span class="inline-flex items-center px-4 py-1.5 rounded-full bg-blue-100 text-blue-800 border border-blue-300 text-sm font-black shadow-2xs">
                                {{ $goldUser->total_orders }} Transaksi Terbayar
                            </span>
                        </div>

                        <div class="bg-blue-100/50 p-4 rounded-2xl border border-blue-200 space-y-2 text-xs">
                            <div class="flex justify-between">
                                <span class="text-blue-900 font-semibold">Pesanan Berhasil:</span>
                                <span class="font-black text-blue-950 text-sm">{{ $goldUser->completed_orders }} Selesai</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-blue-900 font-semibold">Ulasan Diberikan:</span>
                                <span class="font-bold text-slate-800">{{ $goldUser->reviews_given }} Ulasan</span>
                            </div>
                        </div>

                        <a href="{{ route('admin.users.show', $goldUser->id) }}" class="block text-center py-2.5 text-xs font-black text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition shadow-xs">
                            Lihat Profil Juara 1 &rarr;
                        </a>
                    </div>
                @endif

                <!-- JUARA 3: PERUNGGU MAJIKAN -->
                @if($bronzeUser)
                    <div class="bg-white rounded-3xl border border-slate-300 p-6 shadow-xs flex flex-col justify-between space-y-4 relative overflow-hidden order-3">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center space-x-1 text-xs font-black uppercase tracking-wider text-amber-900 bg-amber-100 border border-amber-300 px-2.5 py-1 rounded-full">
                                <span>🥉 Juara 3 (Perunggu)</span>
                            </span>
                            <span class="text-xs font-mono font-bold text-slate-400">#USR-{{ str_pad($bronzeUser->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>

                        <div class="text-center space-y-2">
                            <div class="w-20 h-20 rounded-3xl bg-slate-100 text-slate-700 font-black text-2xl flex items-center justify-center mx-auto shadow-md">
                                {{ strtoupper(substr($bronzeUser->name, 0, 1)) }}
                            </div>
                            <h3 class="font-extrabold text-base text-slate-900">{{ $bronzeUser->name }}</h3>
                            <p class="text-xs text-slate-500">{{ $bronzeUser->kabupaten ?? '-' }}</p>
                            
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200 text-xs font-black">
                                {{ $bronzeUser->total_orders }} Transaksi Terbayar
                            </span>
                        </div>

                        <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100 space-y-1.5 text-xs">
                            <div class="flex justify-between">
                                <span class="text-slate-400">Pesanan Selesai:</span>
                                <span class="font-bold text-slate-800">{{ $bronzeUser->completed_orders }} Selesai</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Ulasan Diberikan:</span>
                                <span class="font-bold text-slate-700">{{ $bronzeUser->reviews_given }} Ulasan</span>
                            </div>
                        </div>

                        <a href="{{ route('admin.users.show', $bronzeUser->id) }}" class="block text-center py-2 text-xs font-bold text-slate-700 hover:text-blue-600 bg-slate-50 hover:bg-slate-100 rounded-xl transition border border-slate-200">
                            Lihat Profil Majikan &rarr;
                        </a>
                    </div>
                @endif
            </div>
        @endif

        <!-- TABEL LENGKAP MAJIKAN TERBAIK -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900 uppercase tracking-wide">
                        Peringkat Pengguna Majikan Paling Loyal
                    </h2>
                    <p class="text-xs text-slate-400">Peringkat diurutkan dari total pesanan terbayar terbanyak, kemudian pesanan yang tuntas diselesaikan.</p>
                </div>
                <span class="text-xs bg-blue-50 text-blue-800 font-bold px-3 py-1 rounded-full border border-blue-200">
                    Top 20 Majikan
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 text-slate-500 uppercase tracking-wider border-b border-slate-200 text-[10px]">
                            <th class="py-3.5 px-4 font-bold text-center">Rank</th>
                            <th class="py-3.5 px-4 font-bold">Nama Majikan</th>
                            <th class="py-3.5 px-4 font-bold">Domisili</th>
                            <th class="py-3.5 px-4 font-bold">Total Order Lunas</th>
                            <th class="py-3.5 px-4 font-bold">Order Selesai</th>
                            <th class="py-3.5 px-4 font-bold">Ulasan Diberikan</th>
                            <th class="py-3.5 px-4 font-bold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($topUsers as $rank => $u)
                            <tr class="hover:bg-slate-50/60 transition {{ $rank < 3 ? 'bg-blue-50/20' : '' }}">
                                <td class="py-3.5 px-4 text-center font-black">
                                    @if($rank === 0)
                                        <span class="inline-flex w-7 h-7 rounded-full bg-blue-600 text-white items-center justify-center text-xs shadow-xs">🥇 1</span>
                                    @elseif($rank === 1)
                                        <span class="inline-flex w-7 h-7 rounded-full bg-slate-300 text-slate-800 items-center justify-center text-xs">🥈 2</span>
                                    @elseif($rank === 2)
                                        <span class="inline-flex w-7 h-7 rounded-full bg-amber-100 text-amber-900 items-center justify-center text-xs">🥉 3</span>
                                    @else
                                        <span class="text-slate-500 font-mono text-xs">#{{ $rank + 1 }}</span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-800 font-extrabold text-xs flex items-center justify-center">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.users.show', $u->id) }}" class="font-extrabold text-slate-900 hover:text-blue-600 block">
                                                {{ $u->name }}
                                            </a>
                                            <span class="font-mono text-[10px] text-slate-400">#USR-{{ str_pad($u->id, 5, '0', STR_PAD_LEFT) }}</span>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3.5 px-4">
                                    <p class="font-bold text-slate-800">{{ $u->kabupaten ?? '-' }}</p>
                                    <p class="text-slate-400 text-[11px]">{{ $u->provinsi ?? '-' }}</p>
                                </td>

                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-slate-900">{{ $u->total_orders }} Transaksi</span>
                                </td>

                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        {{ $u->completed_orders }} Selesai
                                    </span>
                                </td>

                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                        ★ {{ $u->reviews_given }} Ulasan
                                    </span>
                                </td>

                                <td class="py-3.5 px-4 text-center">
                                    <a href="{{ route('admin.users.show', $u->id) }}" class="text-blue-600 hover:text-blue-800 font-bold text-xs">
                                        Detail &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    Belum ada transaksi majikan yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @endif

</div>
@endsection

