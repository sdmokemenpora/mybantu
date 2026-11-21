@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    
    <!-- BREADCRUMB -->
    <div class="flex items-center space-x-2 text-xs font-semibold text-slate-500">
        <a href="{{ route('home') }}" class="hover:text-blue-600 transition">Beranda</a>
        <span>/</span>
        <a href="{{ route('transaksi') }}" class="hover:text-blue-600 transition">Daftar Transaksi</a>
        <span>/</span>
        <span class="text-slate-900 font-bold">Pembayaran Rekber #{{ $transaction->id_transaksi }}</span>
    </div>

    <!-- MAIN CARD -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-md p-6 sm:p-10 space-y-8">
        
        <!-- HEADER PEMBAYARAN -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-100 gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-2xl flex-shrink-0 border border-emerald-100">
                    💳
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Rekening Bersama MyBantu</span>
                        <span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-0.5 rounded-full">Midtrans Gateway</span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900">Selesaikan Pembayaran Pesanan</h1>
                    <p class="text-xs text-slate-500 mt-0.5">ID Transaksi: <span class="font-mono font-bold text-slate-700">{{ $transaction->id_transaksi }}</span></p>
                </div>
            </div>

            <div class="flex items-center space-x-2 bg-amber-50 border border-amber-200 px-3.5 py-1.5 rounded-xl self-start sm:self-center">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                <span class="text-xs font-bold text-amber-800">Menunggu Pembayaran</span>
            </div>
        </div>

        <!-- DETAIL PESANAN & RINCIAN BIAYA -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
            
            <!-- Ringkasan Pekerja & Jadwal -->
            <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200/70 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wide">Ringkasan Pekerjaan</h2>
                    <span class="text-[11px] font-semibold text-slate-500">ID ART: #{{ $transaction->worker->id ?? '-' }}</span>
                </div>
                
                <div class="flex items-center space-x-3.5 pt-1">
                    <div class="w-12 h-12 rounded-2xl bg-slate-200 overflow-hidden flex-shrink-0 border border-slate-300/80">
                        @if(!empty($transaction->worker->foto_path))
                            <img src="{{ asset('storage/' . $transaction->worker->foto_path) }}" alt="{{ $transaction->worker->fullname }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-400 font-bold text-xs bg-slate-100">
                                {{ strtoupper(substr($transaction->worker->fullname ?? 'M', 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">{{ $transaction->worker->fullname ?? 'Mitra ART' }}</h3>
                        <p class="text-xs text-slate-500">{{ $transaction->worker->kabupaten ?? '-' }}, {{ $transaction->worker->provinsi ?? '-' }}</p>
                    </div>
                </div>

                <div class="space-y-2 pt-2 border-t border-slate-200/60 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Tipe Layanan:</span>
                        <span class="font-bold text-slate-800">{{ $transaction->tipe_pekerjaan }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Durasi (Qty):</span>
                        <span class="font-bold text-slate-800">{{ $transaction->jumlah_qty }} {{ stripos($transaction->tipe_pekerjaan, 'mingguan') !== false ? 'Minggu' : 'Hari' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Tarif Satuan:</span>
                        <span class="font-bold text-slate-800">Rp {{ number_format($hargaSatuan, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Tanggal Mulai:</span>
                        <span class="font-bold text-slate-800">{{ $transaction->date_from ? \Carbon\Carbon::parse($transaction->date_from)->format('d F Y') : ($dateRange && $dateRange->mulai_bekerja ? \Carbon\Carbon::parse($dateRange->mulai_bekerja)->format('d F Y') : '-') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Tanggal Selesai:</span>
                        <span class="font-bold text-slate-800">{{ $transaction->date_to ? \Carbon\Carbon::parse($transaction->date_to)->format('d F Y') : ($dateRange && $dateRange->selesai_bekerja ? \Carbon\Carbon::parse($dateRange->selesai_bekerja)->format('d F Y') : '-') }}</span>
                    </div>
                    @if(!empty($transaction->no_hp))
                        <div class="flex justify-between">
                            <span class="text-slate-500">No. HP Pemesan:</span>
                            <span class="font-bold text-slate-800">{{ $transaction->no_hp }}</span>
                        </div>
                    @endif
                    @if(!empty($transaction->alamat_pemesan))
                        <div class="pt-1 border-t border-slate-200/40 text-[11px]">
                            <span class="text-slate-500 block">Alamat Penugasan:</span>
                            <span class="font-medium text-slate-700 block mt-0.5">{{ $transaction->alamat_pemesan }}, {{ $transaction->kabupaten }}, {{ $transaction->provinsi }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Panel Pembayaran & Pemilihan Metode -->
            <div class="space-y-4">
                <div class="bg-gradient-to-br from-slate-900 via-slate-900 to-indigo-950 rounded-3xl p-6 text-white shadow-xl space-y-5 border border-slate-800">
                    
                    <!-- Header Gateway -->
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-emerald-400 font-bold uppercase tracking-wider flex items-center space-x-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                            <span>Midtrans Payment Gateway</span>
                        </span>
                        <span class="bg-white/10 px-2 py-0.5 rounded text-[10px] font-bold text-slate-200">
                            {{ config('services.midtrans.is_production') ? 'Production' : 'Sandbox Mode' }}
                        </span>
                    </div>

                    <!-- Nominal Total -->
                    <div class="border-t border-white/10 pt-3">
                        <span class="text-xs text-slate-300 block">Total Tagihan Pembayaran:</span>
                        <div class="text-2xl sm:text-3xl font-black text-emerald-400 tracking-tight mt-1">
                            Rp {{ number_format($totalHarga, 0, ',', '.') }}
                        </div>
                        <p class="text-[11px] text-slate-400 mt-0.5">
                            *Biaya perlindungan Rekber MyBantu sudah termasuk.
                        </p>
                    </div>

                    <!-- PILIHAN 4 METODE PEMBAYARAN RESMI -->
                    <div class="space-y-2.5 pt-3 border-t border-white/10">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-slate-300 uppercase tracking-wider">Metode Pembayaran:</span>
                            <span id="label-metode-terpilih" class="text-[11px] font-extrabold text-emerald-400">Semua Pilihan</span>
                        </div>

                        <!-- 4 Kartu Metode (QRIS, VA Bank, Indomaret, Alfamart) -->
                        <div class="grid grid-cols-2 gap-2 text-left">
                            <!-- 1. QRIS -->
                            <button type="button" 
                                    onclick="pilihMetode('qris', 'QRIS & E-Wallet')" 
                                    id="card-qris" 
                                    class="method-card p-3 rounded-2xl border border-white/15 bg-white/5 hover:bg-white/10 transition text-left space-y-1 relative group focus:outline-none">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-1.5">
                                        <span class="text-base">📱</span>
                                        <span class="text-xs font-black text-white">QRIS</span>
                                    </div>
                                    <span class="check-icon hidden text-emerald-400 text-xs font-bold">✓</span>
                                </div>
                                <p class="text-[10px] text-slate-300 leading-tight">GoPay, ShopeePay, BCA/Mandiri QRIS</p>
                            </button>

                            <!-- 2. VA Bank -->
                            <button type="button" 
                                    onclick="pilihMetode('va', 'Virtual Account Bank')" 
                                    id="card-va" 
                                    class="method-card p-3 rounded-2xl border border-white/15 bg-white/5 hover:bg-white/10 transition text-left space-y-1 relative group focus:outline-none">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-1.5">
                                        <span class="text-base">🏦</span>
                                        <span class="text-xs font-black text-white">VA Bank</span>
                                    </div>
                                    <span class="check-icon hidden text-emerald-400 text-xs font-bold">✓</span>
                                </div>
                                <p class="text-[10px] text-slate-300 leading-tight">BCA, Mandiri, BNI, BRI, Permata</p>
                            </button>

                            <!-- 3. Indomaret -->
                            <button type="button" 
                                    onclick="pilihMetode('indomaret', 'Indomaret')" 
                                    id="card-indomaret" 
                                    class="method-card p-3 rounded-2xl border border-white/15 bg-white/5 hover:bg-white/10 transition text-left space-y-1 relative group focus:outline-none">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-1.5">
                                        <span class="text-base">🏪</span>
                                        <span class="text-xs font-black text-white">Indomaret</span>
                                    </div>
                                    <span class="check-icon hidden text-emerald-400 text-xs font-bold">✓</span>
                                </div>
                                <p class="text-[10px] text-slate-300 leading-tight">Bayar tunai di kasir gerai Indomaret</p>
                            </button>

                            <!-- 4. Alfamart -->
                            <button type="button" 
                                    onclick="pilihMetode('alfamart', 'Alfamart')" 
                                    id="card-alfamart" 
                                    class="method-card p-3 rounded-2xl border border-white/15 bg-white/5 hover:bg-white/10 transition text-left space-y-1 relative group focus:outline-none">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-1.5">
                                        <span class="text-base">🏪</span>
                                        <span class="text-xs font-black text-white">Alfamart</span>
                                    </div>
                                    <span class="check-icon hidden text-emerald-400 text-xs font-bold">✓</span>
                                </div>
                                <p class="text-[10px] text-slate-300 leading-tight">Bayar tunai di Alfamart / Alfamidi</p>
                            </button>
                        </div>

                        <!-- Bar ganti metode / reset -->
                        <div class="flex items-center justify-between pt-1 text-[11px] text-slate-400">
                            <span>*Bebas pilih salah satu atau lihat semua</span>
                            <button type="button" 
                                    onclick="pilihMetode('all', 'Semua Pilihan')" 
                                    class="text-blue-400 hover:text-blue-300 underline font-semibold transition">
                                Buka Semua Pilihan
                            </button>
                        </div>
                    </div>

                    <!-- Tombol Utama Pembayaran -->
                    <div class="pt-2 space-y-2">
                        <button id="pay-button" 
                                type="button" 
                                class="w-full bg-emerald-500 hover:bg-emerald-600 active:scale-[0.99] text-slate-950 font-black text-sm sm:text-base py-3.5 px-6 rounded-2xl transition duration-150 shadow-lg shadow-emerald-500/20 flex items-center justify-center space-x-2">
                            <span id="pay-button-text">💳 Bayar Sekarang via Midtrans</span>
                            <span class="text-xs bg-slate-950/20 px-2 py-0.5 rounded-md font-bold">&rarr;</span>
                        </button>

                        <button type="button" 
                                onclick="pilihMetode('all', 'Semua Pilihan', true)" 
                                class="w-full text-center text-xs text-slate-300 hover:text-white font-bold py-1.5 transition flex items-center justify-center space-x-1">
                            <span>🔄</span>
                            <span>Ganti Metode Pembayaran Lainnya</span>
                        </button>
                    </div>

                    <!-- Loading Indicator saat Ganti Metode -->
                    <div id="method-loading" class="hidden text-center text-xs text-emerald-400 font-bold py-1">
                        <span class="inline-block animate-spin mr-1">⏳</span> Menyiapkan metode pembayaran...
                    </div>

                </div>

                @php
                    $userSaldo = Auth::guard('web')->user()?->saldo ?? 0;
                @endphp

                @if($userSaldo >= $totalHarga)
                    <!-- Opsi Bayar dengan Saldo -->
                    <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-200 space-y-3">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-emerald-900 flex items-center space-x-1.5">
                                <span>💰</span>
                                <span>Saldo Akun Anda Tersedia</span>
                            </span>
                            <span class="font-black text-emerald-700 text-sm">Rp {{ number_format($userSaldo, 0, ',', '.') }}</span>
                        </div>
                        <p class="text-[11px] text-emerald-800 leading-relaxed">
                            Saldo Anda mencukupi untuk melunasi pesanan ini secara instan tanpa perlu transfer bank atau gerai retail.
                        </p>
                        <form action="{{ route('transaksi.bayar.saldo', $transaction->id_transaksi) }}" 
                              method="POST" 
                              onsubmit="return confirm('Gunakan saldo akun Anda sebesar Rp {{ number_format($totalHarga, 0, ',', '.') }} untuk membayar pesanan ini?')">
                            @csrf
                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs py-3 px-4 rounded-xl transition shadow-xs flex items-center justify-center space-x-1.5">
                                <span>Gunakan Saldo Akun (Rp {{ number_format($totalHarga, 0, ',', '.') }}) &rarr;</span>
                            </button>
                        </form>
                    </div>
                @elseif($userSaldo > 0)
                    <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 text-xs flex justify-between items-center text-slate-600">
                        <span class="flex items-center space-x-1.5">
                            <span>💰</span>
                            <span>Saldo Akun: <strong class="text-slate-800">Rp {{ number_format($userSaldo, 0, ',', '.') }}</strong></span>
                        </span>
                        <span class="text-[10px] text-slate-400 font-semibold">(Kurang Rp {{ number_format($totalHarga - $userSaldo, 0, ',', '.') }})</span>
                    </div>
                @endif

                <!-- Jaminan Rekber -->
                <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-200/80 flex items-start space-x-3 text-xs text-emerald-800">
                    <span class="text-lg flex-shrink-0">🛡️</span>
                    <p class="leading-relaxed">
                        <strong>Jaminan Escrow 100% Aman:</strong> Dana Anda disimpan di rekening penampung resmi. Mitra ART baru akan menerima pencairan dana setelah pekerjaan selesai dan Anda setujui.
                    </p>
                </div>
            </div>

        </div>

        <!-- FORM SIMULASI / CANCEL / NAVIGASI -->
        <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <a href="{{ route('transaksi') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 transition">
                &larr; Bayar Nanti (Kembali ke Pesanan)
            </a>

            <div class="flex items-center space-x-3 w-full sm:w-auto justify-end">
                <!-- Batalkan Pesanan -->
                <form action="{{ route('transaksi.cancel', $transaction->id_transaksi) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?')" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs font-bold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 px-4 py-2.5 rounded-xl transition">
                        Batalkan Pesanan
                    </button>
                </form>

                <!-- Tombol Simulasi Manual (Untuk pengujian cepat tanpa gateway) -->
                <form action="{{ route('transaksi.bayar.proses', $transaction->id_transaksi) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                            title="Simulasi pembayaran manual untuk keperluan testing cepat" 
                            class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs px-4 py-2.5 rounded-xl transition border border-slate-300">
                        ⚡ Simulasi Cepat (Test Lunas)
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>

<!-- SCRIPT MIDTRANS SNAP -->
@php
    $midtransSnapUrl = config('services.midtrans.is_production') 
        ? 'https://app.midtrans.com/snap/snap.js' 
        : 'https://app.sandbox.midtrans.com/snap/snap.js';
@endphp
<script src="{{ $midtransSnapUrl }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>

<script>
    let currentSnapToken = @json($snapToken);
    let selectedChannel = 'all';

    // Highlight card metode yang dipilih
    function highlightSelectedCard(channel) {
        document.querySelectorAll('.method-card').forEach(card => {
            card.classList.remove('border-emerald-400', 'bg-emerald-500/20', 'ring-2', 'ring-emerald-400/40');
            card.classList.add('border-white/15', 'bg-white/5');
            const check = card.querySelector('.check-icon');
            if (check) check.classList.add('hidden');
        });

        if (channel && channel !== 'all') {
            const activeCard = document.getElementById('card-' + channel);
            if (activeCard) {
                activeCard.classList.remove('border-white/15', 'bg-white/5');
                activeCard.classList.add('border-emerald-400', 'bg-emerald-500/20', 'ring-2', 'ring-emerald-400/40');
                const check = activeCard.querySelector('.check-icon');
                if (check) check.classList.remove('hidden');
            }
        }
    }

    // Fungsi Pilih & Ganti Metode Pembayaran
    async function pilihMetode(channel, label, autoOpen = false) {
        selectedChannel = channel;
        document.getElementById('label-metode-terpilih').innerText = label;
        highlightSelectedCard(channel);

        const btnText = document.getElementById('pay-button-text');
        if (channel === 'all') {
            btnText.innerText = '💳 Bayar Sekarang via Midtrans';
        } else {
            btnText.innerText = '💳 Bayar via ' + label;
        }

        // Tampilkan loading
        const loading = document.getElementById('method-loading');
        loading.classList.remove('hidden');

        try {
            const response = await fetch("{{ route('transaksi.bayar.ganti_metode', $transaction->id_transaksi) }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                },
                body: JSON.stringify({ channel: channel })
            });

            const data = await response.json();
            if (data.success && data.snap_token) {
                currentSnapToken = data.snap_token;
                if (autoOpen) {
                    bukaSnapPopup();
                }
            } else {
                console.error("Gagal mendapatkan snap token baru");
            }
        } catch (err) {
            console.error("Error ganti metode:", err);
        } finally {
            loading.classList.add('hidden');
        }
    }

    // Fungsi untuk membuka Popup Snap
    function bukaSnapPopup() {
        if (!currentSnapToken) {
            alert('Token pembayaran belum tersedia. Silakan pilih metode pembayaran terlebih dahulu.');
            return;
        }

        window.snap.pay(currentSnapToken, {
            onSuccess: function (result) {
                const finishUrl = "{{ route('transaksi.bayar.finish', $transaction->id_transaksi) }}";
                const orderId = result.order_id || "{{ $transaction->id_transaksi }}";
                const paymentType = result.payment_type || 'midtrans';
                window.location.href = finishUrl + "?order_id=" + encodeURIComponent(orderId) + "&payment_type=" + encodeURIComponent(paymentType);
            },
            onPending: function (result) {
                alert('Kode pembayaran telah diterbitkan! Silakan selesaikan pembayaran sesuai petunjuk (QRIS, VA Bank, Indomaret, atau Alfamart).');
                window.location.href = "{{ route('transaksi', ['status' => 'belum_bayar']) }}";
            },
            onError: function (result) {
                alert('Pembayaran gagal: ' + (result.status_message || 'Terjadi kesalahan sistem.'));
            },
            onClose: function () {
                console.log('Popup Snap ditutup. Anda dapat mengganti metode pembayaran jika diinginkan.');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const payButton = document.getElementById('pay-button');
        if (payButton) {
            payButton.addEventListener('click', function () {
                bukaSnapPopup();
            });
        }
    });
</script>
@endsection
