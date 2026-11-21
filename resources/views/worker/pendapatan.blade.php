@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- PAGE HEADER -->
    <div class="flex flex-col md:flex-row md:items-end justify-between border-b border-slate-200/80 pb-6 gap-4">
        <div>
            <div class="inline-flex items-center space-x-2 bg-emerald-50 text-emerald-700 text-xs font-bold px-3 py-1 rounded-full mb-2">
                <span>💰 Saldo & Keuangan Mitra ART</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Rekap Pendapatan & Pencairan Upah
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Laporan seluruh penghasilan dari jasa asisten rumah tangga yang telah ditransfer oleh majikan dan dicairkan ke rekening Anda (dipotong 15% fee platform MyBantu).
            </p>
        </div>

        <a href="{{ route('worker.transaksi') }}" 
           class="inline-flex items-center text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 px-4 py-2.5 rounded-xl shadow-xs transition">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Daftar Transaksi
        </a>
    </div>

    <!-- STATS CARDS (4 Grid Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- CARD 1: DANA DITERIMA BERSIH (85%) -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-2 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold uppercase tracking-wider text-emerald-700">Total Upah Cair (85%)</span>
                <span class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg font-bold">✓</span>
            </div>
            <p class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                Rp {{ number_format($totalDiterimaBersih ?? 0, 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-slate-400">Telah berhasil dicairkan ke rekening/e-wallet Anda</p>
        </div>

        <!-- CARD 2: DANA TERTAHAN BERSIH (85%) -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-2 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold uppercase tracking-wider text-amber-700">Saldo Bersih Tertahan</span>
                <span class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg font-bold">⏳</span>
            </div>
            <p class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                Rp {{ number_format($totalTertahanBersih ?? 0, 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-slate-400">Total upah dari pekerjaan yang telah selesai dan siap Anda cairkan</p>
        </div>

        <!-- CARD 3: FEE PLATFORM MYBANTU (15%) -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-2 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold uppercase tracking-wider text-rose-700">Fee MyBantu (15%)</span>
                <span class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg font-bold">%</span>
            </div>
            <p class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                Rp {{ number_format($totalFeePlatform ?? 0, 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-slate-400">Biaya pemeliharaan platform, garansi & perlindungan rekber</p>
        </div>

        <!-- CARD 4: TOTAL JASA SELESAI -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-2 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold uppercase tracking-wider text-blue-700">Pesanan Selesai</span>
                <span class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg font-bold">🧹</span>
            </div>
            <p class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                {{ $totalSelesai ?? 0 }} <span class="text-sm font-semibold text-slate-400">Order</span>
            </p>
            <p class="text-[11px] text-slate-400">Total penugasan yang tuntas Anda selesaikan</p>
        </div>
    </div>

    <!-- TABEL RIWAYAT PENDAPATAN -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-extrabold text-slate-900">Rincian Riwayat Pendapatan Per Pesanan</h2>
                <p class="text-xs text-slate-500 mt-0.5">Daftar transaksi majikan yang telah melakukan pembayaran ke platform MyBantu</p>
            </div>
            <span class="text-xs font-bold text-slate-400 bg-slate-50 px-3 py-1 rounded-xl border border-slate-200">
                {{ $transactions->count() }} Data Transaksi
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs whitespace-nowrap">
                <thead class="bg-slate-50 text-slate-700 font-extrabold uppercase tracking-wider text-[11px] border-b border-slate-200/80">
                    <tr>
                        <th class="px-5 py-4">ID Transaksi</th>
                        <th class="px-5 py-4">Nama Majikan</th>
                        <th class="px-5 py-4">Layanan & Durasi</th>
                        <th class="px-5 py-4">Total Transaksi</th>
                        <th class="px-5 py-4 text-rose-600">Fee MyBantu (15%)</th>
                        <th class="px-5 py-4 text-emerald-700">Upah Bersih Mitra (85%)</th>
                        <th class="px-5 py-4 text-center">Status Pencairan & Rekening</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-4 font-mono font-bold text-slate-900">
                                #{{ $trx->id_transaksi }}
                            </td>
                            <td class="px-5 py-4 font-bold text-slate-800">
                                {{ $trx->user->name ?? '-' }}
                            </td>
                            <td class="px-5 py-4">
                                <span class="bg-slate-100 text-slate-800 px-2.5 py-1 rounded-lg font-bold text-[11px]">
                                    {{ $trx->tipe_pekerjaan }} ({{ $trx->jumlah_qty }} {{ stripos($trx->tipe_pekerjaan, 'mingguan') !== false ? 'Minggu' : 'Hari' }})
                                </span>
                            </td>
                            <td class="px-5 py-4 font-bold text-slate-700">
                                Rp {{ number_format($trx->nominal_gross ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 font-bold text-rose-600">
                                - Rp {{ number_format($trx->nominal_fee ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 font-extrabold text-emerald-600 text-sm">
                                Rp {{ number_format($trx->nominal_net ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($trx->status_dana_terkirim === 'Refund ke Saldo' || strtolower($trx->konfirmasi ?? '') === 'rejected')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        ✕ Ditolak (Refund Saldo Majikan)
                                    </span>
                                @elseif($trx->status_dana_terkirim === 'Terkirim')
                                    <div class="flex flex-col items-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            ✓ Sudah Dicairkan
                                        </span>
                                        @if(!empty($trx->bank_tujuan))
                                            <span class="text-[10px] text-slate-500 font-mono mt-0.5">
                                                {{ $trx->bank_tujuan }} - {{ $trx->no_rekening }} (a.n {{ $trx->nama_rekening }})
                                            </span>
                                        @endif
                                    </div>
                                @elseif($trx->status_selesai === 'Selesai')
                                    <a href="{{ route('worker.transaksi', ['status' => 'jasa_selesai']) }}" 
                                       class="inline-flex items-center px-3 py-1 rounded-xl text-[10px] font-bold bg-emerald-600 hover:bg-emerald-700 text-white transition shadow-xs">
                                        Siap Dicairkan &rarr;
                                    </a>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        ⏳ Tersimpan Aman di Rekber
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 space-y-2">
                                <div class="text-2xl">💰</div>
                                <p class="font-bold text-slate-600">Belum Ada Transaksi Masuk</p>
                                <p class="text-xs">Pendapatan Anda akan tercatat secara otomatis setelah majikan memesan dan membayar via Rekber.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
