<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Worker;
use App\Models\Rating;
use App\Models\PriceWorker;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Menampilkan daftar transaksi milik user yang sedang login beserta filter sub-menu status
     */
    public function index(Request $request)
    {
        $statusFilter = $request->query('status');

        $query = Transaction::with(['worker.prices', 'ratingData'])
            ->select(
                'id_transaksi',
                DB::raw('MAX(worker_id) as worker_id'),
                DB::raw('MAX(tipe_pekerjaan) as tipe_pekerjaan'),
                DB::raw('MAX(jumlah_qty) as jumlah_qty'),
                DB::raw('MAX(konfirmasi) as konfirmasi'),
                DB::raw('MAX(status_bekerja) as status_bekerja'),
                DB::raw('MAX(status_selesai) as status_selesai'),
                DB::raw('MAX(status_pembayaran) as status_pembayaran'),
                DB::raw('MAX(status_dana_terkirim) as status_dana_terkirim'),
                DB::raw('MAX(alasan_cancel) as alasan_cancel'),
                DB::raw('MAX(rating) as rating'),
                DB::raw('MIN(tanggal_kerja) as mulai_bekerja'),
                DB::raw('MAX(tanggal_kerja) as selesai_bekerja'),
                DB::raw('MAX(created_at) as created_at'),
                DB::raw('MAX(date_from) as date_from'),
                DB::raw('MAX(date_to) as date_to'),
                DB::raw('MAX(no_hp) as no_hp'),
                DB::raw('MAX(alamat_pemesan) as alamat_pemesan'),
                DB::raw('MAX(kabupaten) as kabupaten'),
                DB::raw('MAX(provinsi) as provinsi')
            )
            ->where('user_id', Auth::id());

        // Hitung total transaksi dibatalkan untuk badge sub-menu
        $allUserTrx = Transaction::where('user_id', Auth::id())
            ->select(
                'id_transaksi',
                DB::raw('MAX(konfirmasi) as konfirmasi'),
                DB::raw('MAX(status_bekerja) as status_bekerja'),
                DB::raw('MAX(status_pembayaran) as status_pembayaran'),
                DB::raw('MAX(status_dana_terkirim) as status_dana_terkirim'),
                DB::raw('MAX(alasan_cancel) as alasan_cancel')
            )
            ->groupBy('id_transaksi')
            ->get();

        $countCancel = $allUserTrx->filter(function($t) {
            return in_array($t->status_bekerja, ['Dibatalkan', 'Ditolak'])
                || $t->konfirmasi === 'Rejected'
                || in_array($t->status_pembayaran, ['Batal', 'Dibatalkan'])
                || $t->status_dana_terkirim === 'Refund ke Saldo'
                || !empty($t->alasan_cancel);
        })->count();

        // Logika Filter Sub-Menu yang Disempurnakan
        if ($statusFilter === 'belum_bayar') {
            $query->where('status_pembayaran', 'Belum Bayar')
                  ->where(function ($q) {
                      $q->whereNull('status_bekerja')
                        ->orWhereNotIn('status_bekerja', ['Dibatalkan', 'Ditolak']);
                  })
                  ->whereNull('alasan_cancel');
        } elseif ($statusFilter === 'menunggu_konfirmasi') {
            $query->where('status_pembayaran', 'Sudah Bayar')
                  ->where('konfirmasi', 'No')
                  ->where(function ($q) {
                      $q->whereNull('status_bekerja')
                        ->orWhereNotIn('status_bekerja', ['Dibatalkan', 'Ditolak']);
                  })
                  ->whereNull('alasan_cancel');
        } elseif ($statusFilter === 'status_bekerja') {
            // Menampilkan transaksi yang menunggu bekerja dan sedang bekerja
            $query->where('konfirmasi', 'Yes')
                  ->where(function ($q) {
                      $q->whereIn('status_bekerja', ['Menunggu', 'Proses'])
                        ->orWhereNull('status_bekerja');
                  })
                  ->where(function ($q) {
                      $q->whereNull('status_selesai')
                        ->orWhere('status_selesai', '!=', 'Selesai');
                  });
        } elseif ($statusFilter === 'status_selesai') {
            $query->where('status_selesai', 'Selesai');
        } elseif ($statusFilter === 'dibatalkan' || $statusFilter === 'cancel') {
            // Tab Khusus Pesanan yang Dicancel / Dibatalkan
            $query->where(function ($q) {
                $q->whereIn('status_bekerja', ['Dibatalkan', 'Ditolak'])
                  ->orWhere('konfirmasi', 'Rejected')
                  ->orWhereIn('status_pembayaran', ['Batal', 'Dibatalkan'])
                  ->orWhere('status_dana_terkirim', 'Refund ke Saldo')
                  ->orWhereNotNull('alasan_cancel');
            });
        }

        $transactions = $query->groupBy('id_transaksi')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('product.transaksi', compact('transactions', 'statusFilter', 'countCancel'));
    }

    /**
     * Menyimpan order baru dan melakukan looping tanggal kerja ke database
     */
    public function storeOrder(Request $request, $workerId)
    {
        $request->validate([
            'jumlah_qty'     => 'required|integer|min:1',
            'tanggal_mulai'  => 'required|date',
            'tipe_pekerjaan' => 'required|string',
            'no_hp'          => 'nullable|string',
            'alamat_pemesan' => 'nullable|string',
            'kabupaten'      => 'nullable|string',
            'provinsi'       => 'nullable|string',
        ]);

        $worker = Worker::findOrFail($workerId);
        if (strtoupper($worker->status_worker ?? '') !== 'AKTIF') {
            return back()->with('error', 'Mitra ART ini sedang tidak aktif dan tidak dapat menerima pesanan baru.');
        }
        $user = Auth::guard('web')->user() ?? Auth::user();
        $tanggalTransaksi = Carbon::now();
        $qty = (int) $request->jumlah_qty;
        $tipe = $request->tipe_pekerjaan;

        // Ambil data kontak & alamat pemesan (dari input form yang dapat diubah atau fallback ke profil majikan)
        $noHp = filled(trim($request->input('no_hp', ''))) ? trim($request->input('no_hp')) : ($user->phone_number ?? '');
        $alamatPemesan = filled(trim($request->input('alamat_pemesan', ''))) ? trim($request->input('alamat_pemesan')) : ($user->alamat_lengkap ?? '');
        $kabupaten = filled(trim($request->input('kabupaten', ''))) ? trim($request->input('kabupaten')) : ($user->kabupaten ?? '');
        $provinsi = filled(trim($request->input('provinsi', ''))) ? trim($request->input('provinsi')) : ($user->provinsi ?? '');

        // Generate Format ID Transaksi: TRX-YYYYMMDD + ID Worker + Sequence (0001)
        $datePrefix = $tanggalTransaksi->format('Ymd'); 
        $prefixId = 'TRX-' . $datePrefix . $worker->id; 

        $lastTransaction = Transaction::where('id_transaksi', 'like', $prefixId . '%')
            ->orderBy('id_transaksi', 'desc')
            ->first();

        if ($lastTransaction) {
            $lastSequence = (int) substr($lastTransaction->id_transaksi, -4);
            $nextSequence = str_pad($lastSequence + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextSequence = '0001';
        }

        $idTransaksi = $prefixId . $nextSequence;

        // Aturan Pengali Hari: Jika mengandung kata 'mingguan', 1 qty = 7 hari. Jika harian, 1 qty = 1 hari.
        $pengaliHari = (stripos(strtolower($tipe), 'mingguan') !== false) ? 7 : 1;
        $totalHariLoop = $qty * $pengaliHari;

        $tanggalMulai = Carbon::parse($request->tanggal_mulai);
        $tanggalSelesai = (clone $tanggalMulai)->addDays($totalHariLoop - 1);

        // Looping pembuatan row data sebanyak total hari kerja ke tabel transactions
        for ($i = 0; $i < $totalHariLoop; $i++) {
            $tanggalKerja = (clone $tanggalMulai)->addDays($i);

            Transaction::create([
                'id_transaksi'         => $idTransaksi,
                'tanggal_transaksi'    => $tanggalTransaksi->format('Y-m-d'),
                'user_id'              => Auth::id(),
                'worker_id'            => $worker->id,
                'tipe_pekerjaan'       => $tipe,
                'jumlah_qty'           => $qty,
                'date_from'            => $tanggalMulai->format('Y-m-d'),
                'date_to'              => $tanggalSelesai->format('Y-m-d'),
                'no_hp'                => $noHp,
                'alamat_pemesan'       => $alamatPemesan,
                'kabupaten'            => $kabupaten,
                'provinsi'             => $provinsi,
                'tanggal_kerja'        => $tanggalKerja->format('Y-m-d'),
                'konfirmasi'           => 'No',
                'status_bekerja'       => null, 
                'status_selesai'       => null, 
                'status_pembayaran'    => 'Belum Bayar',
                'status_dana_terkirim' => 'Belum',
                'rating'               => null,
            ]);
        }

        return redirect()->route('transaksi.bayar.form', $idTransaksi)->with('success', 'Pemesanan berhasil dibuat! Silakan lakukan pembayaran untuk memproses pesanan.');
    }

    /**
     * Menampilkan halaman/view bayar untuk transaksi tertentu + Generate Midtrans Snap Token
     */
    public function showBayar($idTransaksi)
    {
        $transaction = Transaction::with(['worker.prices', 'user'])
            ->where('id_transaksi', $idTransaksi)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $dateRange = Transaction::where('id_transaksi', $idTransaksi)
            ->selectRaw('MIN(tanggal_kerja) as mulai_bekerja, MAX(tanggal_kerja) as selesai_bekerja, COUNT(*) as total_hari')
            ->first();

        // Cari harga satuan dari tabel price_worker
        $priceRow = $transaction->worker->prices
            ->filter(fn($p) => stripos($p->tipe_pekerjaan, trim($transaction->tipe_pekerjaan)) !== false)
            ->first();

        $hargaSatuan = $priceRow ? $priceRow->harga : ($transaction->tipe_pekerjaan === 'Mingguan' ? 750000 : 120000);
        $totalHarga = $hargaSatuan * ($transaction->jumlah_qty ?? 1);

        // ==========================================
        // INTEGRASI MIDTRANS SNAP (Hanya QRIS, VA Bank, Indomaret & Alfamart)
        // ==========================================
        $snapToken = $transaction->snap_token;
        $serverKey = config('services.midtrans.server_key');

        if (!$snapToken && !empty($serverKey) && $transaction->status_pembayaran !== 'Sudah Bayar') {
            $snapToken = $this->generateMidtransSnapToken($transaction, $totalHarga, $hargaSatuan, 'all');
        }

        return view('product.bayar', compact('transaction', 'dateRange', 'hargaSatuan', 'totalHarga', 'snapToken'));
    }

    /**
     * Helper untuk generate Midtrans Snap Token dengan batasan metode: QRIS, VA Bank, Indomaret, Alfamart
     */
    protected function generateMidtransSnapToken($transaction, $totalHarga, $hargaSatuan, $channel = 'all')
    {
        $serverKey = config('services.midtrans.server_key');
        if (empty($serverKey)) {
            return null;
        }

        \Midtrans\Config::$serverKey = $serverKey;
        \Midtrans\Config::$isProduction = (bool) config('services.midtrans.is_production');
        \Midtrans\Config::$isSanitized = (bool) config('services.midtrans.is_sanitized', true);
        \Midtrans\Config::$is3ds = (bool) config('services.midtrans.is_3ds', true);

        $custName = $transaction->user->name ?? 'Majikan MyBantu';
        $custEmail = $transaction->user->email ?? 'majikan@mybantu.com';
        $custPhone = $transaction->no_hp ?: ($transaction->user->phone_number ?: '081234567890');

        // Daftar metode pembayaran yang diizinkan sesuai instruksi:
        // 1. Virtual Account Bank
        $vaChannels = ['bca_va', 'bni_va', 'bri_va', 'echannel', 'permata_va', 'cimb_va', 'other_va'];
        // 2. QRIS (GoPay, ShopeePay, Semua QRIS)
        $qrisChannels = ['qris', 'gopay', 'shopeepay'];
        // 3. Retail: Indomaret & Alfamart
        $cstoreChannels = ['cstore'];

        if ($channel === 'va') {
            $enabledPayments = $vaChannels;
        } elseif ($channel === 'qris') {
            $enabledPayments = $qrisChannels;
        } elseif ($channel === 'indomaret' || $channel === 'alfamart' || $channel === 'cstore') {
            $enabledPayments = $cstoreChannels;
        } else {
            // Default semua yang diizinkan (VA, QRIS, Indomaret & Alfamart)
            $enabledPayments = array_merge($vaChannels, $qrisChannels, $cstoreChannels);
        }

        // Suffix timestamp untuk mencegah error 'order_id duplicate / already taken' saat user ganti metode pembayaran
        $orderIdWithSuffix = $transaction->id_transaksi . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id'     => $orderIdWithSuffix,
                'gross_amount' => (int) $totalHarga,
            ],
            'customer_details' => [
                'first_name' => $custName,
                'email'      => $custEmail,
                'phone'      => $custPhone,
                'address'    => $transaction->alamat_pemesan ?? 'Indonesia',
            ],
            'item_details' => [
                [
                    'id'       => substr($transaction->id_transaksi, 0, 50),
                    'price'    => (int) $hargaSatuan,
                    'quantity' => (int) ($transaction->jumlah_qty ?? 1),
                    'name'     => substr('Jasa: ' . ($transaction->worker->fullname ?? 'Mitra') . ' (' . $transaction->tipe_pekerjaan . ')', 0, 50),
                ]
            ],
            'enabled_payments' => $enabledPayments,
        ];

        if ($channel === 'indomaret') {
            $params['cstore'] = [
                'store'   => 'Indomaret',
                'message' => 'Pembayaran Pesanan MyBantu di Indomaret',
            ];
        } elseif ($channel === 'alfamart') {
            $params['cstore'] = [
                'store'   => 'Alfamart',
                'message' => 'Pembayaran Pesanan MyBantu di Alfamart',
            ];
        }

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            Transaction::where('id_transaksi', $transaction->id_transaksi)->update([
                'snap_token' => $snapToken
            ]);
            return $snapToken;
        } catch (\Exception $e) {
            \Log::error('Midtrans Snap Generation Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Fitur Ganti Metode Pembayaran: Generate token baru berdasarkan channel pilihan user
     */
    public function gantiMetode(Request $request, $idTransaksi)
    {
        $transaction = Transaction::with(['worker.prices', 'user'])
            ->where('id_transaksi', $idTransaksi)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $priceRow = $transaction->worker->prices
            ->filter(fn($p) => stripos($p->tipe_pekerjaan, trim($transaction->tipe_pekerjaan)) !== false)
            ->first();

        $hargaSatuan = $priceRow ? $priceRow->harga : ($transaction->tipe_pekerjaan === 'Mingguan' ? 750000 : 120000);
        $totalHarga = $hargaSatuan * ($transaction->jumlah_qty ?? 1);

        $channel = $request->input('channel', 'all');

        $newToken = $this->generateMidtransSnapToken($transaction, $totalHarga, $hargaSatuan, $channel);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'    => !empty($newToken),
                'snap_token' => $newToken,
                'channel'    => $channel,
            ]);
        }

        return redirect()->route('transaksi.bayar.form', $idTransaksi)
            ->with('success', 'Metode pembayaran berhasil diperbarui. Silakan lanjutkan pembayaran.');
    }

    /**
     * Memproses callback / finish dari Midtrans Snap saat pembayaran berhasil
     */
    public function finishBayar(Request $request, $idTransaksi)
    {
        $paymentType = $request->query('payment_type') ?? $request->input('payment_type') ?? 'midtrans';

        Transaction::where('id_transaksi', $idTransaksi)
            ->where('user_id', Auth::id())
            ->update([
                'status_pembayaran' => 'Sudah Bayar',
                'payment_type'      => $paymentType,
                'konfirmasi'        => 'No',
            ]);

        return redirect()->route('transaksi', ['status' => 'menunggu_konfirmasi'])
            ->with('success', "Pembayaran untuk pesanan #{$idTransaksi} berhasil diverifikasi melalui Midtrans! Pesanan Anda saat ini sedang menunggu konfirmasi dari Mitra ART.");
    }

    /**
     * Memproses pembayaran manual / simulasi (mengubah status menjadi Sudah Bayar)
     */
    public function prosesBayar($idTransaksi)
    {
        Transaction::where('id_transaksi', $idTransaksi)
            ->where('user_id', Auth::id())
            ->update([
                'status_pembayaran' => 'Sudah Bayar',
                'payment_type'      => 'manual_transfer',
                'konfirmasi'        => 'No',
            ]);

        return redirect()->route('transaksi', ['status' => 'menunggu_konfirmasi'])->with('success', 'Pembayaran berhasil dikonfirmasi! Pesanan Anda saat ini berstatus Sudah Bayar dan sedang menunggu konfirmasi (Iya/Tidak) dari mitra ART.');
    }

    /**
     * Majikan mengonfirmasi bahwa pekerjaan telah selesai dilakukan (escrow selesai -> worker bisa cairkan dana)
     */
    public function selesaikanPesanan($idTransaksi)
    {
        Transaction::where('id_transaksi', $idTransaksi)
            ->where('user_id', Auth::id())
            ->update([
                'status_selesai' => 'Selesai',
                'status_bekerja' => 'Selesai',
            ]);

        return redirect()->route('transaksi', ['status' => 'status_selesai'])
            ->with('success', "Pekerjaan untuk pesanan #{$idTransaksi} berhasil Anda konfirmasi SELESAI. Silakan berikan rating & ulasan untuk mitra ART Anda.");
    }

    /**
     * Memproses pembayaran menggunakan saldo akun majikan
     */
    public function bayarDenganSaldo($idTransaksi)
    {
        $user = Auth::guard('web')->user() ?? Auth::user();
        $transaction = Transaction::where('id_transaksi', $idTransaksi)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($transaction->status_pembayaran === 'Sudah Bayar') {
            return redirect()->route('transaksi')->with('info', 'Pesanan ini sudah dibayar sebelumnya.');
        }

        $priceRow = PriceWorker::where('worker_id', $transaction->worker_id)
            ->where('tipe_pekerjaan', $transaction->tipe_pekerjaan)
            ->first();
        $hargaSatuan = $priceRow ? $priceRow->harga : ($transaction->tipe_pekerjaan === 'Mingguan' ? 750000 : 120000);
        $totalHarga = $hargaSatuan * ($transaction->jumlah_qty ?? 1);

        if ($user->saldo < $totalHarga) {
            return back()->with('error', 'Saldo Anda (Rp ' . number_format($user->saldo, 0, ',', '.') . ') tidak mencukupi untuk pembayaran sebesar Rp ' . number_format($totalHarga, 0, ',', '.') . '.');
        }

        $user->decrement('saldo', $totalHarga);

        Transaction::where('id_transaksi', $idTransaksi)
            ->where('user_id', $user->id)
            ->update([
                'status_pembayaran' => 'Sudah Bayar',
                'payment_type'      => 'saldo_user',
                'konfirmasi'        => 'No',
            ]);

        return redirect()->route('transaksi', ['status' => 'menunggu_konfirmasi'])
            ->with('success', "Pembayaran pesanan #{$idTransaksi} berhasil menggunakan Saldo Akun sebesar Rp " . number_format($totalHarga, 0, ',', '.') . "!");
    }

    /**
     * Membatalkan / Menghapus orderan berdasarkan id_transaksi (Refund ke saldo jika sudah bayar tapi belum dikonfirmasi worker)
     */
    public function cancel($idTransaksi)
    {
        $trx = Transaction::where('id_transaksi', $idTransaksi)
            ->where('user_id', Auth::id())
            ->first();

        if (!$trx) {
            return redirect()->route('transaksi')->with('error', 'Pesanan tidak ditemukan.');
        }

        $isPaid = in_array(strtolower($trx->status_pembayaran ?? ''), ['sudah bayar', 'settlement', 'capture']);
        $isAccepted = strtolower($trx->konfirmasi ?? '') === 'yes';

        // Jika pesanan sudah dibayar namun worker belum konfirmasi / tolak -> refund dana ke saldo akun majikan
        if ($isPaid && !$isAccepted && $trx->status_dana_terkirim !== 'Refund ke Saldo') {
            $priceRow = PriceWorker::where('worker_id', $trx->worker_id)
                ->where('tipe_pekerjaan', $trx->tipe_pekerjaan)
                ->first();
            $hargaSatuan = $priceRow ? $priceRow->harga : ($trx->tipe_pekerjaan === 'Mingguan' ? 750000 : 120000);
            $totalRefund = $hargaSatuan * ($trx->jumlah_qty ?? 1);

            $user = Auth::guard('web')->user() ?? Auth::user();
            $user->increment('saldo', $totalRefund);

            Transaction::where('id_transaksi', $idTransaksi)
                ->where('user_id', Auth::id())
                ->update([
                    'status_pembayaran'    => 'Batal',
                    'konfirmasi'           => 'Rejected',
                    'status_bekerja'       => 'Dibatalkan',
                    'status_dana_terkirim' => 'Refund ke Saldo',
                    'alasan_cancel'        => 'Refund (dibatalkan majikan setelah bayar)',
                    'nominal_cair'         => 0,
                ]);

            $formatted = number_format($totalRefund, 0, ',', '.');
            return redirect()->route('transaksi', ['status' => 'dibatalkan'])
                ->with('success', "Pesanan #{$idTransaksi} berhasil dibatalkan. Dana pembayaran sebesar Rp {$formatted} telah dikembalikan ke Saldo Akun Anda (Alasan: Refund).");
        }

        // Cancel pesanan ketika belum bayar (simpan status Batal agar muncul di tab Dibatalkan)
        Transaction::where('id_transaksi', $idTransaksi)
            ->where('user_id', Auth::id())
            ->update([
                'status_pembayaran' => 'Batal',
                'konfirmasi'        => 'Rejected',
                'status_bekerja'    => 'Dibatalkan',
                'alasan_cancel'     => 'Cancel pesanan ketika belum bayar',
            ]);

        return redirect()->route('transaksi', ['status' => 'dibatalkan'])
            ->with('success', "Pesanan #{$idTransaksi} berhasil dibatalkan (Alasan: Cancel pesanan ketika belum bayar).");
    }

    /**
     * Berikan / Simpan Rating dan Ulasan untuk Transaksi yang Sudah Selesai
     */
    public function storeRating(Request $request, $idTransaksi)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        $trx = Transaction::where('id_transaksi', $idTransaksi)
            ->where('user_id', Auth::id())
            ->where('status_selesai', 'Selesai')
            ->firstOrFail();

        // Simpan / update ke tabel ratings
        Rating::updateOrCreate(
            [
                'id_transaksi' => $trx->id_transaksi,
            ],
            [
                'user_id'   => Auth::id(),
                'worker_id' => $trx->worker_id,
                'rating'    => $validated['rating'],
                'review'    => $validated['review'] ?? null,
            ]
        );

        // Update juga kolom rating pada seluruh baris transaksi tersebut
        Transaction::where('id_transaksi', $idTransaksi)
            ->update(['rating' => $validated['rating']]);

        return redirect()->route('transaksi', ['status' => 'status_selesai'])
            ->with('success', "Terima kasih! Penilaian (★ {$validated['rating']}) dan ulasan Anda untuk mitra ART berhasil disimpan.");
    }
}
