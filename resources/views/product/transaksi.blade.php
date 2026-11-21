@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    
    <!-- PAGE HEADER -->
    <div class="flex flex-col md:flex-row md:items-end justify-between border-b border-slate-200/80 pb-6 gap-4">
        <div>
            <div class="inline-flex items-center space-x-2 bg-blue-50 text-blue-700 text-xs font-bold px-3 py-1 rounded-full mb-2">
                <span>📋 Manajemen Pesanan</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Daftar Transaksi & Jadwal Kerja ART
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Pantau status pembayaran rekber, konfirmasi jadwal, dan perkembangan kerja asisten Anda.
            </p>
        </div>

        <a href="{{ route('product') }}" class="inline-flex items-center text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 px-4 py-2.5 rounded-xl shadow-xs transition">
            <span>+ Pesan Asisten Baru</span>
        </a>
    </div>

    <!-- SUB MENU FILTER STATUS (Pills Navigation) -->
    @php
        $currentUserSaldo = Auth::guard('web')->user()?->saldo ?? 0;
    @endphp
    @if($currentUserSaldo > 0)
        <div class="bg-gradient-to-r from-emerald-600 to-teal-700 rounded-3xl p-5 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm border border-emerald-500/30">
            <div class="flex items-center space-x-3.5">
                <div class="w-11 h-11 rounded-2xl bg-white/20 flex items-center justify-center text-2xl font-bold flex-shrink-0">
                    💰
                </div>
                <div>
                    <span class="text-[11px] font-bold text-emerald-200 uppercase tracking-wider block">Saldo Refund Akun MyBantu</span>
                    <div class="text-xl sm:text-2xl font-black text-white tracking-tight">
                        Rp {{ number_format($currentUserSaldo, 0, ',', '.') }}
                    </div>
                </div>
            </div>
            <p class="text-xs text-emerald-100 max-w-sm">
                Dana refund tersimpan di saldo akun Anda dan dapat digunakan langsung untuk memesan Mitra ART lainnya secara instan.
            </p>
        </div>
    @endif

    <div class="flex flex-wrap gap-2">
        <a href="{{ route('transaksi') }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition duration-150 border {{ !request('status') ? 'bg-slate-900 text-white border-slate-900 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
           Semua Pesanan
        </a>
        <a href="{{ route('transaksi', ['status' => 'belum_bayar']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition duration-150 border {{ request('status') == 'belum_bayar' ? 'bg-slate-900 text-white border-slate-900 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
           Belum Bayar
        </a>
        <a href="{{ route('transaksi', ['status' => 'menunggu_konfirmasi']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition duration-150 border {{ request('status') == 'menunggu_konfirmasi' ? 'bg-slate-900 text-white border-slate-900 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
           Menunggu Konfirmasi
        </a>
        <a href="{{ route('transaksi', ['status' => 'status_bekerja']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition duration-150 border {{ request('status') == 'status_bekerja' ? 'bg-slate-900 text-white border-slate-900 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
           Status Bekerja
        </a>
        <a href="{{ route('transaksi', ['status' => 'status_selesai']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition duration-150 border {{ request('status') == 'status_selesai' ? 'bg-slate-900 text-white border-slate-900 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
           Selesai
        </a>
        <a href="{{ route('transaksi', ['status' => 'dibatalkan']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition duration-150 border flex items-center space-x-1.5 {{ request('status') == 'dibatalkan' || request('status') == 'cancel' ? 'bg-rose-700 text-white border-rose-700 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
           <span>Dibatalkan / Cancel</span>
           @if(isset($countCancel) && $countCancel > 0)
               <span class="px-1.5 py-0.2 rounded-full text-[10px] font-extrabold {{ request('status') == 'dibatalkan' || request('status') == 'cancel' ? 'bg-white text-rose-700' : 'bg-rose-100 text-rose-700' }}">{{ $countCancel }}</span>
           @endif
        </a>
    </div>

    <!-- TABLE WRAPPER WITH MODERN CRAFT STYLING -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs whitespace-nowrap">
                <thead class="bg-slate-50/80 text-slate-700 font-extrabold uppercase tracking-wider text-[11px] border-b border-slate-200/80">
                    <tr>
                        <th class="px-5 py-4">ID Transaksi</th>
                        <th class="px-5 py-4">Nama Mitra ART</th>
                        <th class="px-5 py-4">Tipe & Durasi</th>
                        <th class="px-5 py-4">Total Biaya Rekber</th>
                        <th class="px-5 py-4">Periode Kerja</th>
                        <th class="px-5 py-4 text-center">Konfirmasi</th>
                        <th class="px-5 py-4 text-center">Status Kerja</th>
                        <th class="px-5 py-4 text-center">Pembayaran & Aksi</th>
                        <th class="px-5 py-4 text-center">Rating</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($transactions as $trx)
                        @php
                            // Ambil harga dari relasi worker.prices yang sudah di-eager-load (Bebas N+1 Query)
                            $priceRow = null;
                            if ($trx->worker && $trx->worker->prices) {
                                $priceRow = $trx->worker->prices->filter(function($p) use ($trx) {
                                    return stripos($p->tipe_pekerjaan, trim($trx->tipe_pekerjaan)) !== false;
                                })->first();
                            }
                            
                            $hargaSatuan = $priceRow ? $priceRow->harga : 0;
                            $totalHarga = $hargaSatuan * ($trx->jumlah_qty ?? 1);

                            $isCancelled = in_array($trx->status_bekerja, ['Dibatalkan', 'Ditolak'])
                                || strtolower($trx->konfirmasi ?? '') === 'rejected'
                                || in_array(strtolower($trx->status_pembayaran ?? ''), ['batal', 'dibatalkan'])
                                || $trx->status_dana_terkirim === 'Refund ke Saldo'
                                || !empty($trx->alasan_cancel);

                            $alasanTampil = $trx->alasan_cancel;
                            if (empty($alasanTampil)) {
                                if ($trx->status_dana_terkirim === 'Refund ke Saldo') {
                                    $alasanTampil = 'Refund (dibatalkan majikan setelah bayar)';
                                } elseif (strtolower($trx->status_pembayaran ?? '') === 'batal') {
                                    $alasanTampil = 'Cancel pesanan ketika belum bayar';
                                } elseif (strtolower($trx->konfirmasi ?? '') === 'rejected' || $trx->status_bekerja === 'Ditolak') {
                                    $alasanTampil = 'Tercancel karena tidak dikonfirmasi (tidak) oleh worker';
                                }
                            }
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition-colors {{ $isCancelled ? 'bg-rose-50/20' : '' }}">
                            <!-- ID Transaksi -->
                            <td class="px-5 py-4">
                                <span class="font-mono font-bold text-slate-900 bg-slate-100 px-2.5 py-1 rounded-md text-[11px] border border-slate-200/80">
                                    {{ $trx->id_transaksi }}
                                </span>
                                @if($isCancelled && !empty($alasanTampil))
                                    <div class="mt-1.5 max-w-[200px]">
                                        <span class="inline-block text-[10px] font-semibold text-rose-700 bg-rose-50 border border-rose-200 rounded px-1.5 py-0.5 whitespace-normal leading-tight">
                                            {{ $alasanTampil }}
                                        </span>
                                    </div>
                                @endif
                            </td>

                            <!-- Nama Worker -->
                            <td class="px-5 py-4">
                                <div class="flex items-center space-x-2.5">
                                    <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-[10px] flex-shrink-0">
                                        {{ strtoupper(substr($trx->worker->fullname ?? 'M', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800">{{ $trx->worker->fullname ?? ('Mitra #' . $trx->worker_id) }}</p>
                                        <p class="text-[10px] text-slate-400">{{ $trx->worker->kabupaten ?? 'Wilayah Terdaftar' }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Tipe & Qty -->
                            <td class="px-5 py-4 text-slate-700">
                                <span class="font-semibold">{{ $trx->tipe_pekerjaan }}</span>
                                <span class="text-slate-400 text-[10px] ml-1">({{ $trx->jumlah_qty }} {{ stripos($trx->tipe_pekerjaan, 'mingguan') !== false ? 'Minggu' : 'Hari' }})</span>
                            </td>

                            <!-- Total Biaya -->
                            <td class="px-5 py-4 font-extrabold text-blue-600">
                                Rp {{ number_format($totalHarga, 0, ',', '.') }}
                            </td>

                            <!-- Periode Kerja -->
                            <td class="px-5 py-4 text-slate-600">
                                <div class="space-y-0.5">
                                    <p class="text-[11px] font-bold text-slate-800">
                                        {{ isset($trx->date_from) ? \Carbon\Carbon::parse($trx->date_from)->format('d/m/Y') : (isset($trx->mulai_bekerja) ? \Carbon\Carbon::parse($trx->mulai_bekerja)->format('d/m/Y') : '-') }}
                                    </p>
                                    <p class="text-[10px] text-slate-400">
                                        s/d {{ isset($trx->date_to) ? \Carbon\Carbon::parse($trx->date_to)->format('d/m/Y') : (isset($trx->selesai_bekerja) ? \Carbon\Carbon::parse($trx->selesai_bekerja)->format('d/m/Y') : '-') }}
                                    </p>
                                </div>
                            </td>

                            <!-- Konfirmasi -->
                            <td class="px-5 py-4 text-center">
                                @if($isCancelled)
                                    <span class="inline-flex items-center text-[10px] font-bold text-rose-700 bg-rose-100 px-2.5 py-0.5 rounded-full">
                                        ✕ Dibatalkan
                                    </span>
                                @elseif(strtolower($trx->konfirmasi) === 'yes')
                                    <span class="inline-flex items-center text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2.5 py-0.5 rounded-full">
                                        ✓ Diterima (Iya)
                                    </span>
                                @else
                                    <span class="inline-flex items-center text-[10px] font-bold text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full">
                                        Menunggu Konfirmasi
                                    </span>
                                @endif
                            </td>

                            <!-- Status Bekerja -->
                            <td class="px-5 py-4 text-center">
                                @if($isCancelled)
                                    <span class="inline-flex text-[10px] font-bold text-slate-600 bg-slate-100 border border-slate-200 px-2.5 py-1 rounded-lg">
                                        Dibatalkan
                                    </span>
                                @elseif($trx->status_selesai === 'Selesai')
                                    <span class="inline-flex text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-lg">
                                        Selesai
                                    </span>
                                @elseif($trx->status_bekerja === 'Proses')
                                    <span class="inline-flex text-[10px] font-bold text-blue-700 bg-blue-50 border border-blue-200 px-2.5 py-1 rounded-lg animate-pulse">
                                        Sedang Bekerja
                                    </span>
                                @elseif($trx->status_bekerja === 'Menunggu' || (strtolower($trx->konfirmasi) === 'yes' && empty($trx->status_bekerja)))
                                    <span class="inline-flex text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-2.5 py-1 rounded-lg">
                                        Menunggu Bekerja
                                    </span>
                                @elseif($trx->status_bekerja === 'Ditolak' || strtolower($trx->konfirmasi) === 'rejected')
                                    <span class="inline-flex text-[10px] font-bold text-rose-700 bg-rose-50 border border-rose-200 px-2.5 py-1 rounded-lg">
                                        Ditolak
                                    </span>
                                @elseif($trx->status_bekerja)
                                    <span class="inline-flex text-[10px] font-bold text-blue-700 bg-blue-50 border border-blue-200 px-2.5 py-1 rounded-lg">
                                        {{ $trx->status_bekerja }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>

                            <!-- Pembayaran & Aksi -->
                            <td class="px-5 py-4 text-center">
                                @if($isCancelled)
                                    <div class="flex flex-col items-center justify-center space-y-1 text-center">
                                        @if($trx->status_dana_terkirim === 'Refund ke Saldo' || stripos($alasanTampil, 'refund') !== false || stripos($alasanTampil, 'worker') !== false)
                                            <span class="inline-flex items-center text-[10px] font-bold text-emerald-800 bg-emerald-100 border border-emerald-200 px-2.5 py-0.5 rounded-full">
                                                ✓ Masuk ke Saldo
                                            </span>
                                            <span class="text-[9px] text-emerald-600 font-semibold">Refund Selesai</span>
                                        @else
                                            <span class="inline-flex items-center text-[10px] font-bold text-slate-600 bg-slate-100 border border-slate-200 px-2.5 py-0.5 rounded-full">
                                                ✕ Dibatalkan
                                            </span>
                                            <span class="text-[9px] text-slate-400 font-semibold">Belum Bayar</span>
                                        @endif
                                        <span class="text-[10px] font-bold text-rose-600 max-w-[190px] whitespace-normal leading-snug">
                                            {{ $alasanTampil }}
                                        </span>
                                    </div>
                                @elseif($trx->status_pembayaran === 'Belum Bayar')
                                    <div class="flex items-center justify-center space-x-1.5">
                                        <a href="{{ route('transaksi.bayar.form', $trx->id_transaksi) }}" 
                                           class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-xl text-xs font-bold transition shadow-xs flex items-center space-x-1">
                                            <span>Bayar</span>
                                        </a>

                                        <form action="{{ route('transaksi.cancel', $trx->id_transaksi) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?')" 
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    title="Batalkan pesanan"
                                                    class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 px-2.5 py-1.5 rounded-xl text-xs font-bold transition">
                                                Batal
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <div class="flex flex-col items-center justify-center space-y-1">
                                        <span class="inline-flex items-center text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2.5 py-0.5 rounded-full">
                                            ✓ Lunas (Rekber)
                                        </span>
                                        @if($trx->status_bekerja === 'Proses' && $trx->status_selesai !== 'Selesai')
                                            <form action="{{ route('transaksi.selesaikan', $trx->id_transaksi) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Konfirmasi bahwa Mitra ART telah menyelesaikan pekerjaan dengan baik?')" 
                                                  class="mt-1 inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-extrabold px-2.5 py-1 rounded-lg transition shadow-xs flex items-center space-x-1" 
                                                        title="Selesaikan pesanan agar mitra ART dapat mencairkan dana">
                                                    <span>Konfirmasi Selesai ✓</span>
                                                </button>
                                            </form>
                                        @elseif((strtolower($trx->konfirmasi ?? '') === 'no' || empty($trx->konfirmasi)) && $trx->status_selesai !== 'Selesai')
                                            <form action="{{ route('transaksi.cancel', $trx->id_transaksi) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Mitra ART belum mengonfirmasi pesanan ini. Batalkan pesanan dan kembalikan dana ke Saldo Akun Anda?')" 
                                                  class="mt-0.5 inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-[10px] font-bold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 px-2 py-0.5 rounded-md transition" 
                                                        title="Batalkan pesanan dan kembalikan dana ke saldo">
                                                    Batalkan (Refund)
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endif
                            </td>

                            <!-- Rating -->
                            <td class="px-5 py-4 text-center">
                                @if($trx->rating)
                                    <div class="inline-flex flex-col items-center">
                                        <span class="font-bold text-amber-500 text-xs">★ {{ $trx->rating }} / 5</span>
                                        <button type="button" 
                                                onclick="openRatingModal('{{ $trx->id_transaksi }}', '{{ addslashes($trx->worker->fullname ?? 'Mitra') }}', '{{ $trx->rating }}', '{{ addslashes($trx->ratingData->review ?? '') }}')" 
                                                class="text-[10px] text-blue-600 hover:underline mt-0.5 font-bold">
                                            Edit Ulasan
                                        </button>
                                    </div>
                                @elseif($trx->status_selesai === 'Selesai')
                                    <button type="button" 
                                            onclick="openRatingModal('{{ $trx->id_transaksi }}', '{{ addslashes($trx->worker->fullname ?? 'Mitra') }}', 5, '')" 
                                            class="bg-amber-400 hover:bg-amber-500 text-amber-950 font-black px-3 py-1.5 rounded-xl text-xs shadow-xs transition inline-flex items-center space-x-1">
                                        <span>★ Beri Rating</span>
                                    </button>
                                @else
                                    <span class="text-slate-400 text-[10px]">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-slate-400">
                                <div class="max-w-sm mx-auto space-y-2">
                                    <span class="text-2xl block">📦</span>
                                    <p class="font-bold text-slate-700 text-sm">Tidak ada transaksi ditemukan</p>
                                    <p class="text-xs text-slate-400">Belum ada pesanan pada kategori filter ini. Mulai pesan asisten rumah tangga sekarang.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- MODAL POPUP BERI RATING & REVIEW -->
<div id="ratingModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6 relative animate-in fade-in zoom-in-95 duration-200">
        
        <!-- Modal Header -->
        <div class="flex items-start justify-between border-b border-slate-100 pb-4">
            <div>
                <span class="text-[11px] font-bold text-amber-600 uppercase tracking-wider block">Penilaian Layanan ART</span>
                <h3 class="text-xl font-extrabold text-slate-900 mt-0.5" id="modalWorkerName">Beri Rating Pekerja</h3>
                <p class="text-xs text-slate-500 font-mono mt-0.5" id="modalTrxId">ID Transaksi: -</p>
            </div>
            <button type="button" 
                    onclick="closeRatingModal()" 
                    class="text-slate-400 hover:text-slate-600 p-2 rounded-xl hover:bg-slate-100 transition">
                ✕
            </button>
        </div>

        <!-- Form Rating & Review -->
        <form id="ratingForm" action="" method="POST" class="space-y-5">
            @csrf
            <input type="hidden" name="rating" id="ratingInput" value="5">

            <!-- Star Rating Interactive Selector -->
            <div class="text-center space-y-2 py-2 bg-slate-50 rounded-2xl border border-slate-200/60">
                <span class="text-xs font-bold text-slate-700 block uppercase tracking-wide">Tingkat Kepuasan Anda</span>
                <div class="flex items-center justify-center space-x-2 text-3xl sm:text-4xl select-none" id="starContainer">
                    <span class="cursor-pointer transition-transform hover:scale-125 text-amber-400" data-star="1" onclick="setRating(1)">★</span>
                    <span class="cursor-pointer transition-transform hover:scale-125 text-amber-400" data-star="2" onclick="setRating(2)">★</span>
                    <span class="cursor-pointer transition-transform hover:scale-125 text-amber-400" data-star="3" onclick="setRating(3)">★</span>
                    <span class="cursor-pointer transition-transform hover:scale-125 text-amber-400" data-star="4" onclick="setRating(4)">★</span>
                    <span class="cursor-pointer transition-transform hover:scale-125 text-amber-400" data-star="5" onclick="setRating(5)">★</span>
                </div>
                <p class="text-xs font-extrabold text-amber-700" id="ratingLabel">5 Bintang - Sangat Puas</p>
            </div>

            <!-- Review Textarea -->
            <div>
                <label for="reviewTextarea" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">
                    Ulasan & Pengalaman Kerja
                </label>
                <textarea name="review" 
                          id="reviewTextarea" 
                          rows="4" 
                          placeholder="Ceritakan bagaimana kinerja mitra ini (contoh: Pekerja datang tepat waktu, hasil pembersihan sangat rapi dan ramah)..."
                          class="w-full bg-slate-50 border border-slate-300 rounded-2xl px-4 py-3 text-xs sm:text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition" 
                          required></textarea>
                <p class="text-[11px] text-slate-400 mt-1">Ulasan Anda akan tampil secara publik di halaman profil mitra untuk membantu majikan lainnya.</p>
            </div>

            <!-- Modal Action Buttons -->
            <div class="flex items-center space-x-3 pt-2">
                <button type="button" 
                        onclick="closeRatingModal()" 
                        class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-3 px-4 rounded-xl text-xs transition">
                    Batal
                </button>
                <button type="submit" 
                        class="flex-1 bg-amber-500 hover:bg-amber-600 text-white font-extrabold py-3 px-4 rounded-xl text-xs transition shadow-md shadow-amber-500/25">
                    Kirim Penilaian & Ulasan &rarr;
                </button>
            </div>
        </form>

    </div>
</div>

<script>
    const ratingLabels = {
        1: '1 Bintang - Sangat Kecewa',
        2: '2 Bintang - Kurang Puas',
        3: '3 Bintang - Cukup Baik',
        4: '4 Bintang - Puas & Rekomendasi',
        5: '5 Bintang - Sangat Puas & Luar Biasa'
    };

    function setRating(val) {
        document.getElementById('ratingInput').value = val;
        document.getElementById('ratingLabel').innerText = ratingLabels[val] || (val + ' Bintang');

        const stars = document.querySelectorAll('#starContainer span');
        stars.forEach(s => {
            const starNum = parseInt(s.getAttribute('data-star'));
            if (starNum <= val) {
                s.innerText = '★';
                s.className = 'cursor-pointer transition-transform hover:scale-125 text-amber-400';
            } else {
                s.innerText = '☆';
                s.className = 'cursor-pointer transition-transform hover:scale-125 text-slate-300';
            }
        });
    }

    function openRatingModal(trxId, workerName, currentRating, currentReview) {
        document.getElementById('modalWorkerName').innerText = 'Penilaian untuk ' + workerName;
        document.getElementById('modalTrxId').innerText = 'ID Transaksi: #' + trxId;
        document.getElementById('ratingForm').action = '/transaksi/' + trxId + '/rating';
        document.getElementById('reviewTextarea').value = currentReview || '';
        
        const initialRating = parseInt(currentRating) || 5;
        setRating(initialRating);

        document.getElementById('ratingModal').classList.remove('hidden');
    }

    function closeRatingModal() {
        document.getElementById('ratingModal').classList.add('hidden');
    }
</script>
@endsection
