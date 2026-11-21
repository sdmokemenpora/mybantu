@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    
    <!-- PAGE HEADER -->
    <div class="flex flex-col md:flex-row md:items-end justify-between border-b border-slate-200/80 pb-6 gap-4">
        <div>
            <div class="inline-flex items-center space-x-2 bg-emerald-50 text-emerald-700 text-xs font-bold px-3 py-1 rounded-full mb-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Dashboard Mitra ART</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Daftar Transaksi & Penugasan Kerja
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Kelola pesanan majikan, konfirmasi ketersediaan kerja, dan pantau penyelesaian serta pencairan upah Anda.
            </p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('worker.pendapatan') }}" class="inline-flex items-center text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 px-4 py-2.5 rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Lihat Rekap Pendapatan</span>
            </a>
        </div>
    </div>

    <!-- SECTION: SUGGEST TRANSAKSI YANG DIREKOMENDASIKAN -->
    @if(isset($suggestedTransactions) && $suggestedTransactions->isNotEmpty())
        <div class="bg-gradient-to-br from-emerald-900 via-slate-900 to-indigo-950 rounded-3xl p-6 sm:p-8 text-white shadow-lg space-y-5 border border-emerald-500/20">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-500/20 text-emerald-300 flex items-center justify-center font-bold text-lg border border-emerald-400/30">
                        ⭐
                    </div>
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-400">Rekomendasi Tindakan</span>
                        <h2 class="text-lg sm:text-xl font-extrabold text-white">Transaksi yang Perlu Ditindaklanjuti</h2>
                    </div>
                </div>
                <span class="text-xs font-medium text-slate-300 bg-white/10 px-3 py-1.5 rounded-full self-start sm:self-center border border-white/10">
                    {{ $suggestedTransactions->count() }} Pesanan Prioritas
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($suggestedTransactions as $st)
                    @php
                        $cleanHp = preg_replace('/[^0-9]/', '', $st->no_hp ?? '');
                        if(str_starts_with($cleanHp, '0')) {
                            $cleanHp = '62' . substr($cleanHp, 1);
                        }
                    @endphp
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-5 border border-white/15 space-y-3 flex flex-col justify-between hover:border-emerald-400/40 transition">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-mono font-bold text-emerald-300">#{{ $st->id_transaksi }}</span>
                                @if($st->konfirmasi === 'No')
                                    @if(!empty($st->is_past_due))
                                        <span class="bg-rose-500/90 text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full animate-pulse">
                                            Tanggal Lewat (Wajib Cancel)
                                        </span>
                                    @else
                                        <span class="bg-amber-500/90 text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full animate-pulse">
                                            Perlu Konfirmasi!
                                        </span>
                                    @endif
                                @else
                                    <span class="bg-blue-500/90 text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full">
                                        Siap Mulai
                                    </span>
                                @endif
                            </div>

                            <div class="text-xs space-y-1">
                                <p class="font-bold text-white text-sm">{{ $st->user->name ?? 'Majikan' }}</p>
                                <p class="text-slate-300 text-[11px] flex items-center">
                                    <span class="mr-1">📍</span> {{ $st->kabupaten ?? $st->user->kabupaten ?? 'Lokasi Majikan' }}, {{ $st->provinsi ?? $st->user->provinsi ?? '' }}
                                </p>
                                <p class="text-slate-300 text-[11px] line-clamp-1" title="{{ $st->alamat_pemesan ?? $st->user->alamat_lengkap }}">
                                    🏠 {{ $st->alamat_pemesan ?? $st->user->alamat_lengkap ?? 'Alamat tertera di detail' }}
                                </p>
                                <p class="text-emerald-300 text-[11px] font-semibold">
                                    🗓️ {{ Carbon\Carbon::parse($st->date_from ?? $st->mulai_bekerja)->format('d M') }} - {{ Carbon\Carbon::parse($st->date_to ?? $st->selesai_bekerja)->format('d M Y') }} ({{ $st->tipe_pekerjaan }})
                                </p>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-white/10 flex items-center gap-2">
                            @if(!empty($cleanHp))
                                <a href="https://wa.me/{{ $cleanHp }}?text=Halo%20{{ urlencode($st->user->name ?? 'Majikan') }},%20saya%20{{ urlencode($worker->fullname) }}%20mitra%20ART%20MyBantu%20terkait%20pesanan%20%23{{ $st->id_transaksi }}" 
                                   target="_blank" 
                                   class="bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold p-2 rounded-xl text-xs flex items-center justify-center transition"
                                   title="Hubungi Majikan via WhatsApp">
                                    💬 WA
                                </a>
                            @endif

                            @if($st->konfirmasi === 'No')
                                <div class="flex items-center gap-1.5 flex-1">
                                    @if(!empty($st->is_past_due))
                                        <form action="{{ route('worker.transaksi.status', $st->id_transaksi) }}" method="POST" onsubmit="return confirm('Tanggal mulai pesanan sudah melewati tanggal sekarang. Batalkan pesanan ini (Tidak)? Dana akan di-refund ke saldo majikan.')" class="flex-1">
                                            @csrf
                                            <input type="hidden" name="action" value="konfirmasi_tidak">
                                            <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-extrabold py-2 px-2 rounded-xl text-xs transition shadow-sm text-center" title="Tanggal mulai sudah lewat dari tanggal sekarang. Wajib cancel!">
                                                ✕ Tidak (Wajib Cancel)
                                            </button>
                                        </form>
                                    @elseif(!empty($st->conflict_data))
                                        <button type="button" 
                                                onclick="openConflictModal('{{ $st->id_transaksi }}', '{{ $st->conflict_data['conflict_id'] }}', '{{ $st->conflict_data['conflict_periode'] }}', '{{ $st->conflict_data['current_periode'] }}')" 
                                                class="flex-1 bg-amber-500/30 hover:bg-amber-500/50 text-amber-200 border border-amber-400/40 font-bold py-2 px-2 rounded-xl text-xs transition shadow-sm text-center" 
                                                title="Jadwal bentrok dengan order #{{ $st->conflict_data['conflict_id'] }}">
                                            ⚠️ Bentrok
                                        </button>
                                        <form action="{{ route('worker.transaksi.status', $st->id_transaksi) }}" method="POST" onsubmit="return confirm('Jadwal bentrok dengan order #{{ $st->conflict_data['conflict_id'] }}. Batalkan pesanan ini (Tidak)? Dana majikan akan di-refund ke saldo.')" class="flex-1">
                                            @csrf
                                            <input type="hidden" name="action" value="konfirmasi_tidak">
                                            <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-extrabold py-2 px-2 rounded-xl text-xs transition shadow-sm text-center" title="Wajib cancel karena jadwal bentrok">
                                                ✕ Tidak (Cancel)
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('worker.transaksi.status', $st->id_transaksi) }}" method="POST" class="flex-1">
                                            @csrf
                                            <input type="hidden" name="action" value="konfirmasi_iya">
                                            <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-extrabold py-2 px-2.5 rounded-xl text-xs transition shadow-sm text-center">
                                                ✓ Iya
                                            </button>
                                        </form>
                                        <form action="{{ route('worker.transaksi.status', $st->id_transaksi) }}" method="POST" onsubmit="return confirm('Yakin ingin menolak pesanan ini (Tidak)?')" class="flex-1">
                                            @csrf
                                            <input type="hidden" name="action" value="konfirmasi_tidak">
                                            <button type="submit" class="w-full bg-white/20 hover:bg-rose-600 text-white font-extrabold py-2 px-2.5 rounded-xl text-xs transition shadow-sm text-center">
                                                ✕ Tidak
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @elseif(empty($st->status_bekerja) || $st->status_bekerja === 'Menunggu')
                                <form action="{{ route('worker.transaksi.status', $st->id_transaksi) }}" method="POST" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="action" value="mulai_kerja">
                                    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-extrabold py-2 px-3 rounded-xl text-xs transition shadow-sm text-center">
                                        ▶ Mulai Bertugas
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- SUB MENU FILTER 5 STATUS TRANSAKSI -->
    <div class="space-y-2">
        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Filter Berdasarkan Alur Kerja:</span>
        <div class="flex flex-wrap gap-2 text-xs font-bold">
            <!-- Semua -->
            <a href="{{ route('worker.transaksi') }}" 
               class="px-3.5 py-2 rounded-xl transition border {{ empty($statusFilter) ? 'bg-slate-900 text-white border-slate-900 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
               Semua Pesanan ({{ $stats['total'] }})
            </a>

            <!-- 1. Konfirmasi seller/worker -->
            <a href="{{ route('worker.transaksi', ['status' => 'konfirmasi_seller']) }}" 
               class="px-3.5 py-2 rounded-xl transition border flex items-center space-x-1.5 {{ $statusFilter === 'konfirmasi_seller' ? 'bg-amber-600 text-white border-amber-600 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
               <span>Konfirmasi Seller/Worker</span>
               @if($stats['konfirmasi_seller'] > 0)
                   <span class="bg-amber-100 text-amber-800 text-[10px] px-1.5 py-0.2 rounded-full font-black">{{ $stats['konfirmasi_seller'] }}</span>
               @endif
            </a>

            <!-- 2. Menunggu jasa dilakukan -->
            <a href="{{ route('worker.transaksi', ['status' => 'menunggu_jasa']) }}" 
               class="px-3.5 py-2 rounded-xl transition border flex items-center space-x-1.5 {{ $statusFilter === 'menunggu_jasa' ? 'bg-blue-600 text-white border-blue-600 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
               <span>Menunggu Jasa Dilakukan</span>
               @if($stats['menunggu_jasa'] > 0)
                   <span class="bg-blue-100 text-blue-800 text-[10px] px-1.5 py-0.2 rounded-full font-black">{{ $stats['menunggu_jasa'] }}</span>
               @endif
            </a>

            <!-- 3. Jasa sedang dikerjakan -->
            <a href="{{ route('worker.transaksi', ['status' => 'sedang_dikerjakan']) }}" 
               class="px-3.5 py-2 rounded-xl transition border flex items-center space-x-1.5 {{ $statusFilter === 'sedang_dikerjakan' ? 'bg-indigo-600 text-white border-indigo-600 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
               <span>Jasa Sedang Dikerjakan</span>
               @if($stats['sedang_dikerjakan'] > 0)
                   <span class="bg-indigo-100 text-indigo-800 text-[10px] px-1.5 py-0.2 rounded-full font-black">{{ $stats['sedang_dikerjakan'] }}</span>
               @endif
            </a>

            <!-- 4. Jasa selesai dilakukan -->
            <a href="{{ route('worker.transaksi', ['status' => 'jasa_selesai']) }}" 
               class="px-3.5 py-2 rounded-xl transition border flex items-center space-x-1.5 {{ $statusFilter === 'jasa_selesai' ? 'bg-purple-600 text-white border-purple-600 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
               <span>Jasa Selesai Dilakukan</span>
               @if($stats['jasa_selesai'] > 0)
                   <span class="bg-purple-100 text-purple-800 text-[10px] px-1.5 py-0.2 rounded-full font-black">{{ $stats['jasa_selesai'] }}</span>
               @endif
            </a>

            <!-- 5. Pembayaran sudah diteruskan keseller -->
            <a href="{{ route('worker.transaksi', ['status' => 'dana_terkirim']) }}" 
               class="px-3.5 py-2 rounded-xl transition border flex items-center space-x-1.5 {{ $statusFilter === 'dana_terkirim' ? 'bg-emerald-600 text-white border-emerald-600 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
               <span>Pembayaran Diteruskan ke Seller</span>
               @if($stats['dana_terkirim'] > 0)
                   <span class="bg-emerald-100 text-emerald-800 text-[10px] px-1.5 py-0.2 rounded-full font-black">{{ $stats['dana_terkirim'] }}</span>
               @endif
            </a>

            <!-- 6. Ditolak / Dibatalkan -->
            <a href="{{ route('worker.transaksi', ['status' => 'ditolak']) }}" 
               class="px-3.5 py-2 rounded-xl transition border flex items-center space-x-1.5 {{ $statusFilter === 'ditolak' ? 'bg-rose-600 text-white border-rose-600 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
               <span>Ditolak / Dibatalkan</span>
               @if($stats['ditolak'] > 0)
                   <span class="bg-rose-100 text-rose-800 text-[10px] px-1.5 py-0.2 rounded-full font-black">{{ $stats['ditolak'] }}</span>
               @endif
            </a>
        </div>
    </div>

    <!-- TABEL TRANSAKSI WORKER -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs whitespace-nowrap">
                <thead class="bg-slate-50 text-slate-700 font-extrabold uppercase tracking-wider text-[11px] border-b border-slate-200/80">
                    <tr>
                        <th class="px-5 py-4">ID & Tanggal Order</th>
                        <th class="px-5 py-4">Data Pemesan (Majikan)</th>
                        <th class="px-5 py-4">Alamat Penugasan</th>
                        <th class="px-5 py-4">Layanan & Durasi</th>
                        <th class="px-5 py-4">Periode Kerja</th>
                        <th class="px-5 py-4 text-center">Status Alur</th>
                        <th class="px-5 py-4 text-center">Aksi Kerja</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($transactions as $trx)
                        @php
                            $cleanHp = preg_replace('/[^0-9]/', '', $trx->no_hp ?? '');
                            if(str_starts_with($cleanHp, '0')) {
                                $cleanHp = '62' . substr($cleanHp, 1);
                            }
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition duration-150">
                            <!-- ID & Tanggal -->
                            <td class="px-5 py-4">
                                <span class="font-mono font-bold text-slate-900 block">#{{ $trx->id_transaksi }}</span>
                                <span class="text-[10px] text-slate-400 block">{{ Carbon\Carbon::parse($trx->created_at)->format('d M Y, H:i') }}</span>
                            </td>

                            <!-- Data Majikan -->
                            <td class="px-5 py-4">
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                        {{ strtoupper(substr($trx->user->name ?? 'M', 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-900 block">{{ $trx->user->name ?? '-' }}</span>
                                        @if(!empty($trx->no_hp))
                                            <a href="https://wa.me/{{ $cleanHp }}" target="_blank" class="text-[11px] text-emerald-600 hover:text-emerald-700 font-semibold inline-flex items-center">
                                                💬 {{ $trx->no_hp }}
                                            </a>
                                        @else
                                            <span class="text-[10px] text-slate-400">{{ $trx->user->phone_number ?? '-' }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Alamat Penugasan -->
                            <td class="px-5 py-4 max-w-xs truncate" title="{{ $trx->alamat_pemesan ?? $trx->user->alamat_lengkap }}">
                                <span class="font-semibold text-slate-800 block truncate">{{ $trx->kabupaten ?? $trx->user->kabupaten ?? '-' }}, {{ $trx->provinsi ?? $trx->user->provinsi ?? '' }}</span>
                                <span class="text-[10px] text-slate-500 block truncate">{{ $trx->alamat_pemesan ?? $trx->user->alamat_lengkap ?? '-' }}</span>
                            </td>

                            <!-- Tipe & Durasi -->
                            <td class="px-5 py-4">
                                <span class="font-bold text-slate-900 block">{{ $trx->tipe_pekerjaan }}</span>
                                <span class="text-[10px] text-slate-500">{{ $trx->jumlah_qty }} {{ stripos($trx->tipe_pekerjaan, 'mingguan') !== false ? 'Minggu' : 'Hari' }}</span>
                            </td>

                            <!-- Periode Kerja -->
                            <td class="px-5 py-4">
                                <span class="font-bold text-slate-800 block">
                                    {{ Carbon\Carbon::parse($trx->date_from ?? $trx->mulai_bekerja)->format('d/m/Y') }}
                                </span>
                                <span class="text-[10px] text-slate-500">
                                    s/d {{ Carbon\Carbon::parse($trx->date_to ?? $trx->selesai_bekerja)->format('d/m/Y') }}
                                </span>
                            </td>

                            <!-- Status Alur 5 Kondisi -->
                            <td class="px-5 py-4 text-center">
                                @if($trx->konfirmasi === 'Rejected' || $trx->status_bekerja === 'Ditolak' || $trx->status_bekerja === 'Dibatalkan' || !empty($trx->alasan_cancel))
                                    <div class="flex flex-col items-center space-y-1">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            Pesanan Dibatalkan / Ditolak
                                        </span>
                                        @if(!empty($trx->alasan_cancel))
                                            <span class="text-[9px] text-slate-500 font-medium max-w-xs text-center whitespace-normal" title="{{ $trx->alasan_cancel }}">
                                                {{ $trx->alasan_cancel }}
                                            </span>
                                        @endif
                                    </div>
                                @elseif($trx->status_pembayaran === 'Belum Bayar')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                                        Menunggu Bayar Majikan
                                    </span>
                                @elseif($trx->konfirmasi === 'No')
                                    @if(!empty($trx->is_past_due))
                                        <div class="flex flex-col items-center space-y-0.5">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 border border-rose-300">
                                                ⚠️ Tanggal Lewat
                                            </span>
                                            <span class="text-[9px] text-rose-600 font-extrabold">Wajib Cancel</span>
                                        </div>
                                    @elseif(!empty($trx->conflict_data))
                                        <div class="flex flex-col items-center space-y-0.5">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 border border-rose-300">
                                                ⚠️ Jadwal Bentrok
                                            </span>
                                            <span class="text-[9px] text-rose-600 font-mono">dg #{{ $trx->conflict_data['conflict_id'] }}</span>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 animate-pulse">
                                            1. Menunggu Konfirmasi (Iya/Tidak)
                                        </span>
                                    @endif
                                @elseif(empty($trx->status_bekerja) || $trx->status_bekerja === 'Menunggu')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        2. Menunggu Jasa Dilakukan
                                    </span>
                                @elseif($trx->status_bekerja === 'Proses' && ($trx->status_selesai !== 'Selesai'))
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        3. Jasa Sedang Dikerjakan
                                    </span>
                                @elseif($trx->status_selesai === 'Selesai' && $trx->status_dana_terkirim !== 'Terkirim')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                        4. Jasa Selesai Dilakukan
                                    </span>
                                @elseif($trx->status_dana_terkirim === 'Terkirim')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        5. Pembayaran Diteruskan ke Seller ✓
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi Transaksi -->
                            <td class="px-5 py-4 text-center">
                                @if($trx->konfirmasi === 'Rejected' || $trx->status_bekerja === 'Ditolak' || $trx->status_bekerja === 'Dibatalkan' || !empty($trx->alasan_cancel))
                                    <div class="flex flex-col items-center space-y-1">
                                        <span class="inline-flex items-center text-[10px] font-bold text-rose-700 bg-rose-50 border border-rose-200 px-2.5 py-1 rounded-md">
                                            ✕ Ditolak / Dibatalkan
                                        </span>
                                        @if(!empty($trx->alasan_cancel))
                                            <span class="text-[9px] text-rose-600 font-semibold max-w-[180px] text-center whitespace-normal">
                                                {{ $trx->alasan_cancel }}
                                            </span>
                                        @endif
                                    </div>
                                @elseif($trx->status_pembayaran === 'Sudah Bayar')
                                    @if($trx->konfirmasi === 'No')
                                        @if(!empty($trx->is_past_due))
                                            <div class="flex flex-col items-center space-y-1">
                                                <form action="{{ route('worker.transaksi.status', $trx->id_transaksi) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('Tanggal mulai pesanan sudah melewati tanggal sekarang. Batalkan pesanan ini (Tidak)? Dana akan di-refund ke saldo majikan.')">
                                                    @csrf
                                                    <input type="hidden" name="action" value="konfirmasi_tidak">
                                                    <button type="submit" 
                                                            title="Tanggal mulai sudah melewati tanggal sekarang. Anda wajib membatalkan pesanan ini."
                                                            class="bg-rose-600 hover:bg-rose-700 text-white font-extrabold px-3 py-1.5 rounded-lg text-xs transition shadow-xs">
                                                        ✕ Tidak (Wajib Cancel)
                                                    </button>
                                                </form>
                                                <span class="text-[9px] text-rose-600 font-extrabold tracking-tight">Tanggal Lewat</span>
                                            </div>
                                        @elseif(!empty($trx->conflict_data))
                                            <div class="flex flex-col items-center space-y-1">
                                                <div class="flex items-center space-x-1">
                                                    <button type="button" 
                                                            onclick="openConflictModal('{{ $trx->id_transaksi }}', '{{ $trx->conflict_data['conflict_id'] }}', '{{ $trx->conflict_data['conflict_periode'] }}', '{{ $trx->conflict_data['current_periode'] }}')" 
                                                            class="bg-amber-100 hover:bg-amber-200 text-amber-800 border border-amber-300 font-extrabold px-2.5 py-1.5 rounded-lg text-xs transition shadow-xs" 
                                                            title="Klik untuk melihat rincian bentrok jadwal">
                                                        ⚠️ Bentrok
                                                    </button>
                                                    <form action="{{ route('worker.transaksi.status', $trx->id_transaksi) }}" 
                                                          method="POST" 
                                                          onsubmit="return confirm('Jadwal bentrok dengan order #{{ $trx->conflict_data['conflict_id'] }}. Batalkan pesanan ini (Tidak)? Dana akan di-refund ke saldo majikan.')">
                                                        @csrf
                                                        <input type="hidden" name="action" value="konfirmasi_tidak">
                                                        <button type="submit" 
                                                                title="Wajib membatalkan pesanan ini karena jadwal bentrok"
                                                                class="bg-rose-600 hover:bg-rose-700 text-white font-extrabold px-2.5 py-1.5 rounded-lg text-xs transition shadow-xs">
                                                            ✕ Tidak (Cancel)
                                                        </button>
                                                    </form>
                                                </div>
                                                <span class="text-[9px] text-rose-600 font-extrabold tracking-tight">Wajib Cancel</span>
                                            </div>
                                        @else
                                            <div class="flex items-center justify-center space-x-1.5">
                                                <form action="{{ route('worker.transaksi.status', $trx->id_transaksi) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="action" value="konfirmasi_iya">
                                                    <button type="submit" 
                                                            title="Terima pesanan (Iya)"
                                                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold px-3 py-1.5 rounded-lg text-xs transition shadow-xs">
                                                        ✓ Iya
                                                    </button>
                                                </form>
                                                <form action="{{ route('worker.transaksi.status', $trx->id_transaksi) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('Yakin ingin menolak pesanan #{{ $trx->id_transaksi }} (Tidak)?')">
                                                    @csrf
                                                    <input type="hidden" name="action" value="konfirmasi_tidak">
                                                    <button type="submit" 
                                                            title="Tolak pesanan (Tidak)"
                                                            class="bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 font-extrabold px-2.5 py-1.5 rounded-lg text-xs transition">
                                                        ✕ Tidak
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    @elseif($trx->konfirmasi === 'Rejected')
                                        <span class="inline-flex items-center text-[10px] font-bold text-rose-700 bg-rose-50 border border-rose-200 px-2.5 py-1 rounded-md">
                                            ✕ Ditolak (Tidak)
                                        </span>
                                    @elseif(empty($trx->status_bekerja) || $trx->status_bekerja === 'Menunggu')
                                        <form action="{{ route('worker.transaksi.status', $trx->id_transaksi) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="action" value="mulai_kerja">
                                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-3 py-1.5 rounded-lg text-xs transition shadow-xs">
                                                Mulai Bertugas
                                            </button>
                                        </form>
                                    @elseif($trx->status_bekerja === 'Proses' && ($trx->status_selesai !== 'Selesai'))
                                        <form action="{{ route('worker.transaksi.status', $trx->id_transaksi) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="action" value="selesai_kerja">
                                            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-bold px-3 py-1.5 rounded-lg text-xs transition shadow-xs">
                                                Tandai Jasa Selesai
                                            </button>
                                        </form>
                                    @elseif($trx->status_selesai === 'Selesai' && $trx->status_dana_terkirim !== 'Terkirim')
                                        @php
                                            $priceRow = $trx->worker && $trx->worker->prices ? $trx->worker->prices->filter(fn($p) => stripos($p->tipe_pekerjaan, trim($trx->tipe_pekerjaan)) !== false)->first() : null;
                                            $satuan = $priceRow ? $priceRow->harga : ($trx->tipe_pekerjaan === 'Mingguan' ? 750000 : 120000);
                                            $totalGross = $satuan * ($trx->jumlah_qty ?? 1);
                                            $fee15 = round($totalGross * 0.15);
                                            $net85 = $totalGross - $fee15;
                                        @endphp
                                        <button type="button" 
                                                onclick="openCairkanModal('{{ $trx->id_transaksi }}', {{ $totalGross }}, {{ $fee15 }}, {{ $net85 }})" 
                                                class="bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold px-3 py-1.5 rounded-lg text-xs transition shadow-xs flex items-center space-x-1">
                                            <span>💸 Cairkan Dana</span>
                                        </button>
                                    @else
                                        @php
                                            $priceRow = $trx->worker && $trx->worker->prices ? $trx->worker->prices->filter(fn($p) => stripos($p->tipe_pekerjaan, trim($trx->tipe_pekerjaan)) !== false)->first() : null;
                                            $satuan = $priceRow ? $priceRow->harga : ($trx->tipe_pekerjaan === 'Mingguan' ? 750000 : 120000);
                                            $totalGross = $satuan * ($trx->jumlah_qty ?? 1);
                                            $netCair = $trx->nominal_cair > 0 ? $trx->nominal_cair : ($totalGross * 0.85);
                                        @endphp
                                        <div class="flex flex-col items-center">
                                            <span class="inline-flex text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-md">
                                                ✓ Telah Cair: Rp {{ number_format($netCair, 0, ',', '.') }}
                                            </span>
                                            @if(!empty($trx->bank_tujuan))
                                                <span class="text-[9px] text-slate-400 mt-0.5">{{ $trx->bank_tujuan }} - {{ $trx->no_rekening }}</span>
                                            @endif
                                        </div>
                                    @endif
                                @else
                                    <span class="text-xs text-slate-400">Menunggu Majikan</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 space-y-2">
                                <div class="text-2xl">📋</div>
                                <p class="font-bold text-slate-600">Tidak ada transaksi ditemukan</p>
                                <p class="text-xs">Belum ada transaksi pada kategori status ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- MODAL POPUP PENCAIRAN DANA OLEH WORKER -->
<div id="cairkanModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6 relative animate-in fade-in zoom-in-95 duration-200">
        
        <!-- Modal Header -->
        <div class="flex items-start justify-between border-b border-slate-100 pb-4">
            <div>
                <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider block">Pencairan Hasil Penugasan</span>
                <h3 class="text-xl font-extrabold text-slate-900 mt-0.5">Klaim Pembayaran Upah</h3>
                <p class="text-xs text-slate-500 font-mono mt-0.5" id="cairkanTrxId">ID Transaksi: -</p>
            </div>
            <button type="button" 
                    onclick="closeCairkanModal()" 
                    class="text-slate-400 hover:text-slate-600 p-2 rounded-xl hover:bg-slate-100 transition">
                ✕
            </button>
        </div>

        <!-- Rincian Biaya & Potongan Fee 15% -->
        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 space-y-2.5 text-xs">
            <div class="flex justify-between text-slate-600">
                <span>Total Nilai Jasa Transaksi</span>
                <span class="font-bold text-slate-900" id="cairkanGrossDisplay">Rp 0</span>
            </div>
            <div class="flex justify-between text-rose-600">
                <span>Fee Platform MyBantu (15%)</span>
                <span class="font-bold" id="cairkanFeeDisplay">- Rp 0</span>
            </div>
            <div class="pt-2 border-t border-slate-200 flex justify-between items-baseline">
                <div>
                    <span class="text-xs font-bold text-emerald-800 uppercase tracking-wide block">Dana Bersih yang Diterima</span>
                    <span class="text-[10px] text-slate-400">(85% dari total nilai transaksi)</span>
                </div>
                <span class="text-lg font-black text-emerald-600" id="cairkanNetDisplay">Rp 0</span>
            </div>
        </div>

        <!-- Form Rekening Tujuan -->
        <form id="cairkanForm" action="" method="POST" class="space-y-4">
            @csrf

            <!-- Bank Tujuan -->
            <div>
                <label for="bank_tujuan" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">
                    Bank / E-Wallet Tujuan
                </label>
                <select name="bank_tujuan" 
                        id="bank_tujuan" 
                        class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" 
                        required>
                    <option value="">-- Pilih Bank / E-Wallet --</option>
                    <option value="BCA">Bank BCA</option>
                    <option value="Mandiri">Bank Mandiri</option>
                    <option value="BRI">Bank BRI</option>
                    <option value="BNI">Bank BNI</option>
                    <option value="BSI">Bank Syariah Indonesia (BSI)</option>
                    <option value="CIMB Niaga">Bank CIMB Niaga</option>
                    <option value="Permata">Bank Permata</option>
                    <option value="Danamon">Bank Danamon</option>
                    <option value="Seabank">SeaBank</option>
                    <option value="Bank Jago">Bank Jago</option>
                    <option value="DANA">DANA E-Wallet</option>
                    <option value="Gopay">GoPay E-Wallet</option>
                    <option value="OVO">OVO E-Wallet</option>
                </select>
            </div>

            <!-- Nomor Rekening -->
            <div>
                <label for="no_rekening" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">
                    Nomor Rekening / No. E-Wallet
                </label>
                <input type="text" 
                       name="no_rekening" 
                       id="no_rekening" 
                       placeholder="Contoh: 8820192831 atau 0812xxxxxxxx" 
                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" 
                       required>
            </div>

            <!-- Nama Pemilik Rekening -->
            <div>
                <label for="nama_rekening" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">
                    Nama Pemilik Rekening (Sesuai Buku Tabungan)
                </label>
                <input type="text" 
                       name="nama_rekening" 
                       id="nama_rekening" 
                       value="{{ $worker->fullname }}"
                       placeholder="Nama lengkap pemilik rekening" 
                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" 
                       required>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center space-x-3 pt-3 border-t border-slate-100">
                <button type="button" 
                        onclick="closeCairkanModal()" 
                        class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-3 px-4 rounded-xl text-xs transition">
                    Batal
                </button>
                <button type="submit" 
                        class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold py-3 px-4 rounded-xl text-xs transition shadow-md shadow-emerald-600/25">
                    Konfirmasi Pencairan Dana &rarr;
                </button>
            </div>
        </form>

    </div>
</div>

<!-- MODAL POPUP ERROR JADWAL BENTROK -->
<div id="conflictModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-rose-200 space-y-5 relative animate-in fade-in zoom-in-95 duration-200">
        
        <!-- Icon & Header -->
        <div class="flex items-start space-x-3.5">
            <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center text-2xl font-bold flex-shrink-0 border border-rose-200">
                ⚠️
            </div>
            <div>
                <span class="text-[11px] font-extrabold text-rose-600 uppercase tracking-wider block">Validasi Penugasan</span>
                <h3 class="text-lg font-black text-slate-900 leading-snug">Jadwal Kerja Bentrok!</h3>
                <p class="text-xs text-slate-500 font-mono" id="conflictTargetTrx">Pesanan: -</p>
            </div>
        </div>

        <!-- Rincian Bentrok -->
        <div class="bg-rose-50/80 rounded-2xl p-4 border border-rose-200/80 space-y-2.5 text-xs text-slate-700">
            <div class="flex justify-between items-start">
                <span class="text-slate-500">Jadwal Pesanan Ini:</span>
                <span class="font-bold text-slate-900 text-right" id="conflictCurPeriode">-</span>
            </div>
            <div class="pt-2 border-t border-rose-200/60 flex justify-between items-start">
                <span class="text-rose-700 font-bold">Bentrok dg Order:</span>
                <div class="text-right">
                    <span class="font-mono font-black text-rose-700 block" id="conflictConfId">-</span>
                    <span class="text-[10px] text-slate-500 block" id="conflictConfPeriode">-</span>
                </div>
            </div>
            <div class="p-3 bg-white/90 rounded-xl border border-rose-200 text-[11px] text-rose-800 leading-relaxed font-medium">
                🛑 <strong>Anda tidak dapat mengonfirmasi (Iya) pesanan ini</strong> karena tanggal penugasan bertabrakan dengan pesanan yang sudah Anda ambil sebelumnya. Anda wajib melakukan pembatalan (Tidak).
            </div>
        </div>

        <!-- Tombol Aksi Wajib Cancel -->
        <div class="space-y-2 pt-2 border-t border-slate-100">
            <form id="conflictCancelForm" action="" method="POST">
                @csrf
                <input type="hidden" name="action" value="konfirmasi_tidak">
                <button type="submit" 
                        class="w-full bg-rose-600 hover:bg-rose-700 active:scale-[0.99] text-white font-extrabold text-xs sm:text-sm py-3 px-4 rounded-xl transition shadow-md shadow-rose-600/25 flex items-center justify-center space-x-1.5">
                    <span>✕ Batalkan Pesanan Ini Sekarang (Tidak) &rarr;</span>
                </button>
            </form>

            <button type="button" 
                    onclick="closeConflictModal()" 
                    class="w-full text-center text-xs font-bold text-slate-500 hover:text-slate-700 py-1.5 transition">
                Tutup Pesan
            </button>
        </div>

    </div>
</div>

<script>
    function openCairkanModal(trxId, gross, fee, net) {
        document.getElementById('cairkanTrxId').innerText = 'ID Transaksi: #' + trxId;
        document.getElementById('cairkanForm').action = '/worker/transaksi/' + trxId + '/cairkan';
        
        document.getElementById('cairkanGrossDisplay').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(gross);
        document.getElementById('cairkanFeeDisplay').innerText = '- Rp ' + new Intl.NumberFormat('id-ID').format(fee);
        document.getElementById('cairkanNetDisplay').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(net);

        document.getElementById('cairkanModal').classList.remove('hidden');
    }

    function closeCairkanModal() {
        document.getElementById('cairkanModal').classList.add('hidden');
    }

    function openConflictModal(trxId, conflictId, conflictPeriode, curPeriode) {
        document.getElementById('conflictTargetTrx').innerText = 'Pesanan: #' + trxId;
        document.getElementById('conflictCurPeriode').innerText = curPeriode || '-';
        document.getElementById('conflictConfId').innerText = '#' + conflictId;
        document.getElementById('conflictConfPeriode').innerText = 'Periode: ' + (conflictPeriode || '-');
        document.getElementById('conflictCancelForm').action = '/worker/transaksi/' + trxId + '/status';
        document.getElementById('conflictModal').classList.remove('hidden');
    }

    function closeConflictModal() {
        document.getElementById('conflictModal').classList.add('hidden');
    }

    @if(session('conflict_popup'))
        document.addEventListener('DOMContentLoaded', function () {
            const cp = @json(session('conflict_popup'));
            if (cp) {
                openConflictModal(cp.id_transaksi, cp.conflict_id, cp.conflict_periode, cp.current_periode);
            }
        });
    @endif
</script>
@endsection

