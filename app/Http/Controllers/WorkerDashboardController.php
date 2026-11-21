<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Worker;
use App\Models\PriceWorker;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Province;
use App\Models\Regency;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WorkerDashboardController extends Controller
{
    /**
     * Tampilkan Halaman Edit Profile Worker
     * Membaca ke tabel price_worker, jika tidak ada maka dibuatkan menjadi 0
     */
    public function profile()
    {
        $worker = Auth::guard('worker')->user();

        // Membaca ke table price_worker, jika tidak ada maka dibuatkan record baru dengan harga 0
        $harianPrice = PriceWorker::firstOrCreate(
            ['worker_id' => $worker->id, 'tipe_pekerjaan' => 'Harian'],
            ['harga' => 0]
        );

        $mingguanPrice = PriceWorker::firstOrCreate(
            ['worker_id' => $worker->id, 'tipe_pekerjaan' => 'Mingguan'],
            ['harga' => 0]
        );

        $prices = PriceWorker::where('worker_id', $worker->id)->get();
        $provinces = Province::with('regencies')->orderBy('name')->get();
        $adminContact = \App\Models\Admin::where('status', 'AKTIF')->first() ?? \App\Models\Admin::first();

        return view('worker.profile', compact('worker', 'prices', 'harianPrice', 'mingguanPrice', 'provinces', 'adminContact'));
    }

    /**
     * Proses Pembaruan Profile Worker (Deskripsi, Foto, SKCK, Harga Harian/Mingguan)
     * Salah satu opsi layanan (Harian atau Mingguan) harus dicentang dan wajib diisi tarifnya jika aktif
     */
    public function updateProfile(Request $request)
    {
        $worker = Auth::guard('worker')->user();

        $selectedTypes = $request->input('tipe_pekerjaan', []);

        $rules = [
            'email'            => 'required|email|max:255|unique:workers,email,' . $worker->id,
            'phone_number'     => 'required|string|max:20',
            'provinsi'         => 'required|string|exists:provinces,name',
            'kabupaten'        => 'required|string|exists:regencies,name',
            'alamat_lengkap'   => 'required|string',
            'description'      => 'nullable|string',
            'foto'             => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'skck'             => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'password'         => 'nullable|string|min:6|confirmed',
            'tipe_pekerjaan'   => 'required|array|min:1',
            'tipe_pekerjaan.*' => 'string|in:Harian,Mingguan',
            'harga_harian'     => 'nullable|numeric',
            'harga_mingguan'   => 'nullable|numeric',
        ];

        $messages = [
            'provinsi.exists'         => 'Provinsi yang dipilih tidak terdaftar di sistem.',
            'kabupaten.exists'        => 'Kabupaten/Kota yang dipilih tidak terdaftar di sistem.',
            'tipe_pekerjaan.required' => 'Minimal salah satu opsi layanan (Harian atau Mingguan) harus dicentang untuk menyimpan perubahan.',
            'tipe_pekerjaan.min'      => 'Minimal salah satu opsi layanan (Harian atau Mingguan) harus dicentang untuk menyimpan perubahan.',
            'harga_harian.required'   => 'Tarif Harian wajib diisi jika layanan Harian diaktifkan.',
            'harga_harian.min'        => 'Tarif Harian harus diisi dengan nominal lebih dari Rp 0 jika layanan aktif.',
            'harga_mingguan.required' => 'Tarif Mingguan wajib diisi jika layanan Mingguan diaktifkan.',
            'harga_mingguan.min'      => 'Tarif Mingguan harus diisi dengan nominal lebih dari Rp 0 jika layanan aktif.',
        ];

        // Validasi wajib (mandatory) diisi jika dicentang aktif
        if (in_array('Harian', $selectedTypes)) {
            $rules['harga_harian'] = 'required|numeric|min:1';
        }
        if (in_array('Mingguan', $selectedTypes)) {
            $rules['harga_mingguan'] = 'required|numeric|min:1';
        }

        $validated = $request->validate($rules, $messages);

        $worker->email          = $validated['email'];
        $worker->phone_number   = $validated['phone_number'];
        $worker->provinsi       = $validated['provinsi'];
        $worker->kabupaten      = $validated['kabupaten'];
        $worker->alamat_lengkap = $validated['alamat_lengkap'];
        $worker->description    = $validated['description'] ?? null;

        // Ubah Password jika diisi
        if ($request->filled('password')) {
            $worker->password = Hash::make($validated['password']);
        }

        // Upload / Ubah Foto Profil
        if ($request->hasFile('foto')) {
            if ($worker->foto_path && Storage::disk('public')->exists($worker->foto_path)) {
                Storage::disk('public')->delete($worker->foto_path);
            }

            $cleanName = Str::slug($worker->fullname, '_');
            $extension = $request->file('foto')->getClientOriginalExtension();
            $newFilename = 'Foto_' . $cleanName . '_' . time() . '.' . $extension;

            $fotoPath = $request->file('foto')->storeAs('uploads/foto', $newFilename, 'public');
            $worker->foto_path = $fotoPath;
        }

        // Upload / Ubah Dokumen SKCK Kepolisian
        if ($request->hasFile('skck')) {
            if ($worker->skck_path && Storage::disk('public')->exists($worker->skck_path)) {
                Storage::disk('public')->delete($worker->skck_path);
            }

            $cleanName = Str::slug($worker->fullname, '_');
            $extension = $request->file('skck')->getClientOriginalExtension();
            $newFilename = 'SKCK_' . $cleanName . '_' . time() . '.' . $extension;

            $skckPath = $request->file('skck')->storeAs('uploads/skck', $newFilename, 'public');
            $worker->skck_path = $skckPath;
        }

        // Ambil pilihan tipe pekerjaan dari form
        $isHarianChecked = in_array('Harian', $selectedTypes);
        $isMingguanChecked = in_array('Mingguan', $selectedTypes);

        $hargaHarianInput = (int) $request->input('harga_harian', 0);
        $hargaMingguanInput = (int) $request->input('harga_mingguan', 0);

        // Jika opsi dicentang aktif dan harga diisi > 0, gunakan harga tersebut. Jika tidak dicentang, set menjadi 0.
        $finalHargaHarian = ($isHarianChecked && $hargaHarianInput > 0) ? $hargaHarianInput : 0;
        $finalHargaMingguan = ($isMingguanChecked && $hargaMingguanInput > 0) ? $hargaMingguanInput : 0;

        // Update ke table price_worker jika sudah ada, insert jika masih kosong
        PriceWorker::updateOrCreate(
            ['worker_id' => $worker->id, 'tipe_pekerjaan' => 'Harian'],
            ['harga' => $finalHargaHarian]
        );

        PriceWorker::updateOrCreate(
            ['worker_id' => $worker->id, 'tipe_pekerjaan' => 'Mingguan'],
            ['harga' => $finalHargaMingguan]
        );

        // Update kolom tipe_pekerjaan di tabel workers (hanya tipe layanan yang memiliki harga > 0)
        $activeTypes = [];
        if ($finalHargaHarian > 0) {
            $activeTypes[] = 'Harian';
        }
        if ($finalHargaMingguan > 0) {
            $activeTypes[] = 'Mingguan';
        }

        $worker->tipe_pekerjaan = implode(', ', $activeTypes);
        $worker->save();

        return redirect()->route('worker.profile')->with('success', 'Profil dan pengaturan tarif Anda berhasil diperbarui!');
    }

    /**
     * Tampilkan Halaman Transaksi Worker beserta Filter Status & Rekomendasi
     */
    public function transaksi(Request $request)
    {
        $worker = Auth::guard('worker')->user();
        $statusFilter = $request->query('status');

        $query = Transaction::with(['user', 'worker.prices'])
            ->select(
                'id_transaksi',
                DB::raw('MAX(worker_id) as worker_id'),
                DB::raw('MAX(user_id) as user_id'),
                DB::raw('MAX(tipe_pekerjaan) as tipe_pekerjaan'),
                DB::raw('MAX(jumlah_qty) as jumlah_qty'),
                DB::raw('MAX(konfirmasi) as konfirmasi'),
                DB::raw('MAX(status_bekerja) as status_bekerja'),
                DB::raw('MAX(status_selesai) as status_selesai'),
                DB::raw('MAX(status_pembayaran) as status_pembayaran'),
                DB::raw('MAX(status_dana_terkirim) as status_dana_terkirim'),
                DB::raw('MAX(rating) as rating'),
                DB::raw('MIN(tanggal_kerja) as mulai_bekerja'),
                DB::raw('MAX(tanggal_kerja) as selesai_bekerja'),
                DB::raw('MAX(created_at) as created_at'),
                DB::raw('MAX(date_from) as date_from'),
                DB::raw('MAX(date_to) as date_to'),
                DB::raw('MAX(no_hp) as no_hp'),
                DB::raw('MAX(alamat_pemesan) as alamat_pemesan'),
                DB::raw('MAX(kabupaten) as kabupaten'),
                DB::raw('MAX(provinsi) as provinsi'),
                DB::raw('MAX(bank_tujuan) as bank_tujuan'),
                DB::raw('MAX(no_rekening) as no_rekening'),
                DB::raw('MAX(nama_rekening) as nama_rekening'),
                DB::raw('MAX(fee_platform) as fee_platform'),
                DB::raw('MAX(nominal_cair) as nominal_cair'),
                DB::raw('MAX(waktu_cair) as waktu_cair'),
                DB::raw('MAX(alasan_cancel) as alasan_cancel')
            )
            ->where('worker_id', $worker->id);

        // Filter berdasarkan 5 status yang diminta
        if ($statusFilter === 'konfirmasi_seller') {
            // Konfirmasi Seller: Majikan sudah bayar tapi belum dikonfirmasi worker
            $query->where('status_pembayaran', 'Sudah Bayar')
                  ->where('konfirmasi', 'No');
        } elseif ($statusFilter === 'menunggu_jasa') {
            // Menunggu jasa dilakukan: Sudah dikonfirmasi, menunggu jadwal kerja dimulai
            $query->where('status_pembayaran', 'Sudah Bayar')
                  ->where('konfirmasi', 'Yes')
                  ->where(function($q) {
                      $q->whereNull('status_bekerja')
                        ->orWhere('status_bekerja', 'Menunggu');
                  });
        } elseif ($statusFilter === 'sedang_dikerjakan') {
            // Jasa sedang dikerjakan: Tanggal kerja berjalan / status proses
            $query->where('status_bekerja', 'Proses')
                  ->where(function($q) {
                      $q->whereNull('status_selesai')
                        ->orWhere('status_selesai', '!=', 'Selesai');
                  });
        } elseif ($statusFilter === 'jasa_selesai') {
            // Jasa selesai dilakukan: Menunggu verifikasi / pencairan
            $query->whereIn('status_selesai', ['Selesai', 'selesai'])
                  ->where(function ($q) {
                      $q->whereNull('status_dana_terkirim')
                        ->orWhere('status_dana_terkirim', '!=', 'Terkirim');
                  });
        } elseif ($statusFilter === 'dana_terkirim') {
            // Pembayaran sudah diteruskan ke seller
            $query->where('status_dana_terkirim', 'Terkirim');
        } elseif ($statusFilter === 'ditolak') {
            // Pesanan yang ditolak oleh worker atau dibatalkan
            $query->where(function ($q) {
                $q->where('konfirmasi', 'Rejected')
                  ->orWhere('status_bekerja', 'Ditolak')
                  ->orWhere('status_bekerja', 'Dibatalkan')
                  ->orWhereNotNull('alasan_cancel');
            });
        }

        $transactions = $query->groupBy('id_transaksi')
            ->orderBy('created_at', 'desc')
            ->get();

        // Cek validasi bentrok jadwal dan validasi tanggal terlewat untuk pesanan yang masih berstatus konfirmasi = 'No'
        foreach ($transactions as $t) {
            $orderFrom = $t->date_from ?? $t->mulai_bekerja;
            $t->is_past_due = $orderFrom && Carbon::parse($orderFrom)->startOfDay()->lt(Carbon::today());

            if ($t->konfirmasi === 'No') {
                $t->conflict_data = $this->findScheduleConflict($worker->id, $t->id_transaksi);
            } else {
                $t->conflict_data = null;
            }
        }

        // Hitung statistik untuk badge status
        $allWorkerTrx = Transaction::where('worker_id', $worker->id)
            ->select(
                'id_transaksi',
                DB::raw('MAX(konfirmasi) as konfirmasi'),
                DB::raw('MAX(status_bekerja) as status_bekerja'),
                DB::raw('MAX(status_selesai) as status_selesai'),
                DB::raw('MAX(status_pembayaran) as status_pembayaran'),
                DB::raw('MAX(status_dana_terkirim) as status_dana_terkirim'),
                DB::raw('MAX(alasan_cancel) as alasan_cancel')
            )
            ->groupBy('id_transaksi')
            ->get();

        $stats = [
            'total'             => $allWorkerTrx->count(),
            'konfirmasi_seller' => $allWorkerTrx->where('status_pembayaran', 'Sudah Bayar')->where('konfirmasi', 'No')->filter(fn($t) => empty($t->alasan_cancel))->count(),
            'menunggu_jasa'     => $allWorkerTrx->where('status_pembayaran', 'Sudah Bayar')->where('konfirmasi', 'Yes')->filter(fn($t) => empty($t->status_bekerja) || $t->status_bekerja === 'Menunggu')->count(),
            'sedang_dikerjakan' => $allWorkerTrx->where('status_bekerja', 'Proses')->filter(fn($t) => empty($t->status_selesai) || !in_array(strtolower($t->status_selesai), ['selesai']))->count(),
            'jasa_selesai'      => $allWorkerTrx->filter(fn($t) => in_array(strtolower($t->status_selesai ?? ''), ['selesai']) && strtolower($t->status_dana_terkirim ?? '') !== 'terkirim')->count(),
            'dana_terkirim'     => $allWorkerTrx->where('status_dana_terkirim', 'Terkirim')->count(),
            'ditolak'           => $allWorkerTrx->filter(fn($t) => $t->konfirmasi === 'Rejected' || in_array($t->status_bekerja, ['Ditolak', 'Dibatalkan']) || !empty($t->alasan_cancel))->count(),
        ];

        // Suggest Transaksi yang Direkomendasikan
        $suggestedTransactions = Transaction::with(['user'])
            ->select(
                'id_transaksi',
                DB::raw('MAX(worker_id) as worker_id'),
                DB::raw('MAX(user_id) as user_id'),
                DB::raw('MAX(tipe_pekerjaan) as tipe_pekerjaan'),
                DB::raw('MAX(jumlah_qty) as jumlah_qty'),
                DB::raw('MAX(konfirmasi) as konfirmasi'),
                DB::raw('MAX(status_bekerja) as status_bekerja'),
                DB::raw('MAX(status_selesai) as status_selesai'),
                DB::raw('MAX(status_pembayaran) as status_pembayaran'),
                DB::raw('MAX(status_dana_terkirim) as status_dana_terkirim'),
                DB::raw('MIN(tanggal_kerja) as mulai_bekerja'),
                DB::raw('MAX(tanggal_kerja) as selesai_bekerja'),
                DB::raw('MAX(date_from) as date_from'),
                DB::raw('MAX(date_to) as date_to'),
                DB::raw('MAX(no_hp) as no_hp'),
                DB::raw('MAX(alamat_pemesan) as alamat_pemesan'),
                DB::raw('MAX(kabupaten) as kabupaten'),
                DB::raw('MAX(provinsi) as provinsi')
            )
            ->where('worker_id', $worker->id)
            ->where(function ($q) {
                // Rekomendasi 1: Pesanan baru yang butuh konfirmasi
                $q->where(function($sub) {
                    $sub->where('status_pembayaran', 'Sudah Bayar')
                        ->where('konfirmasi', 'No');
                })
                // Rekomendasi 2: Pesanan siap mulai hari ini / besok
                ->orWhere(function($sub) {
                    $sub->where('konfirmasi', 'Yes')
                        ->where(function($b) {
                            $b->whereNull('status_bekerja')
                              ->orWhere('status_bekerja', 'Menunggu');
                        });
                });
            })
            ->groupBy('id_transaksi')
            ->take(3)
            ->get();

        foreach ($suggestedTransactions as $st) {
            $orderFrom = $st->date_from ?? $st->mulai_bekerja;
            $st->is_past_due = $orderFrom && Carbon::parse($orderFrom)->startOfDay()->lt(Carbon::today());

            if ($st->konfirmasi === 'No') {
                $st->conflict_data = $this->findScheduleConflict($worker->id, $st->id_transaksi);
            } else {
                $st->conflict_data = null;
            }
        }

        return view('worker.transaksi', compact('transactions', 'statusFilter', 'stats', 'suggestedTransactions', 'worker'));
    }

    /**
     * Update Status Transaksi dari Sisi Worker
     */
    public function updateStatus(Request $request, $idTransaksi)
    {
        $worker = Auth::guard('worker')->user();
        $action = $request->input('action');

        $exists = Transaction::where('id_transaksi', $idTransaksi)
            ->where('worker_id', $worker->id)
            ->exists();

        if (!$exists) {
            return back()->with('error', 'Transaksi tidak ditemukan atau bukan milik Anda.');
        }

        if ($action === 'konfirmasi' || $action === 'konfirmasi_iya') {
            $firstTrx = Transaction::where('id_transaksi', $idTransaksi)
                ->where('worker_id', $worker->id)
                ->first();

            $orderFrom = $firstTrx?->date_from ?? Transaction::where('id_transaksi', $idTransaksi)->min('tanggal_kerja');
            if ($orderFrom && Carbon::parse($orderFrom)->startOfDay()->lt(Carbon::today())) {
                return back()->with('error', "Tanggal mulai pesanan (" . Carbon::parse($orderFrom)->format('d/m/Y') . ") sudah melewati tanggal sekarang (" . Carbon::today()->format('d/m/Y') . "). Anda tidak dapat menerima pesanan ini dan wajib melakukan cancel (Tidak).");
            }

            // Validasi apakah jadwal bentrok dengan pesanan yang SUDAH dikonfirmasi sebelumnya
            $conflict = $this->findScheduleConflict($worker->id, $idTransaksi);

            if ($conflict) {
                return back()->with('conflict_popup', $conflict)
                    ->with('error', "Jadwal bentrok dengan order #{$conflict['conflict_id']} ({$conflict['conflict_periode']}). Anda tidak dapat menerima pesanan ini dan wajib melakukan cancel (Tidak).");
            }

            // Worker mengonfirmasi IYA (Terima Pesanan)
            Transaction::where('id_transaksi', $idTransaksi)
                ->where('worker_id', $worker->id)
                ->update([
                    'konfirmasi'     => 'Yes',
                    'status_bekerja' => 'Menunggu'
                ]);
            return back()->with('success', "Pesanan #{$idTransaksi} berhasil dikonfirmasi (IYA)! Jadwal Anda telah dicatat.");
        }

        if ($action === 'konfirmasi_tidak' || $action === 'tolak' || $action === 'batal') {
            // Worker mengonfirmasi TIDAK (Tolak Pesanan / Cancel)
            $firstTrx = Transaction::where('id_transaksi', $idTransaksi)
                ->where('worker_id', $worker->id)
                ->first();

            $orderFrom = $firstTrx?->date_from ?? Transaction::where('id_transaksi', $idTransaksi)->min('tanggal_kerja');
            $isPastDate = $orderFrom && Carbon::parse($orderFrom)->startOfDay()->lt(Carbon::today());

            $conflict = $this->findScheduleConflict($worker->id, $idTransaksi);
            if ($isPastDate) {
                $alasan = "Tercancel karena tidak dikonfirmasi (tidak) oleh worker (tanggal mulai pesanan sudah melewati tanggal sekarang)";
            } elseif ($conflict) {
                $alasan = "Tercancel karena tidak dikonfirmasi (tidak) oleh worker (jadwal bentrok dengan order #{$conflict['conflict_id']})";
            } else {
                $alasan = "Tercancel karena tidak dikonfirmasi (tidak) oleh worker";
            }

            if ($firstTrx && in_array(strtolower($firstTrx->status_pembayaran), ['sudah bayar', 'settlement', 'capture'])) {
                // Pesanan sudah dibayar oleh majikan -> Refund dana ke saldo majikan
                if ($firstTrx->status_dana_terkirim !== 'Refund ke Saldo') {
                    $priceRow = PriceWorker::where('worker_id', $worker->id)
                        ->where('tipe_pekerjaan', $firstTrx->tipe_pekerjaan)
                        ->first();
                    $hargaSatuan = $priceRow ? $priceRow->harga : ($firstTrx->tipe_pekerjaan === 'Mingguan' ? 750000 : 120000);
                    $totalRefund = $hargaSatuan * ($firstTrx->jumlah_qty ?? 1);

                    $majikan = User::find($firstTrx->user_id);
                    if ($majikan) {
                        $majikan->increment('saldo', $totalRefund);
                    }

                    Transaction::where('id_transaksi', $idTransaksi)
                        ->where('worker_id', $worker->id)
                        ->update([
                            'status_pembayaran'    => 'Batal',
                            'konfirmasi'           => 'Rejected',
                            'status_bekerja'       => 'Ditolak',
                            'status_dana_terkirim' => 'Refund ke Saldo',
                            'alasan_cancel'        => $alasan,
                            'nominal_cair'         => 0,
                        ]);

                    $formattedRefund = number_format($totalRefund, 0, ',', '.');
                    return back()->with('info', "Pesanan #{$idTransaksi} telah ditolak (TIDAK). Dana pembayaran sebesar Rp {$formattedRefund} telah otomatis dikembalikan ke saldo akun majikan ({$majikan?->name}). Alasan: {$alasan}");
                }
            }

            // Jika pesanan belum dibayar
            Transaction::where('id_transaksi', $idTransaksi)
                ->where('worker_id', $worker->id)
                ->update([
                    'status_pembayaran' => 'Batal',
                    'konfirmasi'        => 'Rejected',
                    'status_bekerja'    => 'Ditolak',
                    'alasan_cancel'     => $alasan,
                ]);
            return back()->with('success', "Pesanan #{$idTransaksi} telah ditolak (TIDAK). Status pesanan telah diperbarui.");
        }

        if ($action === 'mulai_kerja') {
            $firstTrx = Transaction::where('id_transaksi', $idTransaksi)
                ->where('worker_id', $worker->id)
                ->first();

            $orderFrom = $firstTrx?->date_from ?? Transaction::where('id_transaksi', $idTransaksi)->min('tanggal_kerja');
            $today = Carbon::today()->format('Y-m-d');
            $formattedOrderFrom = $orderFrom ? Carbon::parse($orderFrom)->format('Y-m-d') : null;

            if ($formattedOrderFrom !== $today) {
                return back()->with('error', 'tanggal mulai bekerja belum sesuai');
            }

            // Worker mulai bekerja
            Transaction::where('id_transaksi', $idTransaksi)
                ->where('worker_id', $worker->id)
                ->update([
                    'status_bekerja' => 'Proses'
                ]);
            return back()->with('success', "Status pesanan #{$idTransaksi} diubah: Jasa sedang Anda kerjakan.");
        }

        if ($action === 'selesai_kerja') {
            $firstTrx = Transaction::where('id_transaksi', $idTransaksi)
                ->where('worker_id', $worker->id)
                ->first();

            $orderTo = $firstTrx?->date_to ?? Transaction::where('id_transaksi', $idTransaksi)->max('tanggal_kerja');
            $today = Carbon::today()->format('Y-m-d');
            $formattedOrderTo = $orderTo ? Carbon::parse($orderTo)->format('Y-m-d') : null;

            if ($formattedOrderTo !== $today) {
                return back()->with('error', 'tanggal selesai bekerja belum sesuai');
            }

            // Worker menandai jasa selesai dilakukan
            Transaction::where('id_transaksi', $idTransaksi)
                ->where('worker_id', $worker->id)
                ->update([
                    'status_selesai' => 'Selesai',
                    'status_bekerja' => 'Selesai'
                ]);
            return back()->with('success', "Jasa pesanan #{$idTransaksi} berhasil ditandai selesai! Silakan cairkan pembayaran ke rekening bank Anda.");
        }

        return back()->with('error', 'Aksi tidak dikenali.');
    }

    /**
     * Proses Pencairan Pembayaran oleh Worker
     * Worker memasukkan rekening & bank tujuan, sistem memotong 15% fee platform dan mentransfer 85% ke worker
     */
    public function cairkanDana(Request $request, $idTransaksi)
    {
        $worker = Auth::guard('worker')->user();

        $validated = $request->validate([
            'bank_tujuan'   => 'required|string|max:50',
            'no_rekening'   => 'required|string|max:50',
            'nama_rekening' => 'required|string|max:100',
        ]);

        $firstTrx = Transaction::where('id_transaksi', $idTransaksi)
            ->where('worker_id', $worker->id)
            ->whereIn('status_pembayaran', ['Sudah Bayar', 'sudah bayar', 'settlement', 'capture'])
            ->whereIn('status_selesai', ['Selesai', 'selesai'])
            ->firstOrFail();

        if ($firstTrx->status_dana_terkirim === 'Terkirim') {
            return back()->with('error', 'Dana untuk pesanan ini sudah berhasil dicairkan sebelumnya.');
        }

        // Hitung total nilai transaksi berdasarkan tarif di price_worker
        $priceRow = PriceWorker::where('worker_id', $worker->id)
            ->where('tipe_pekerjaan', $firstTrx->tipe_pekerjaan)
            ->first();

        $hargaSatuan = $priceRow ? $priceRow->harga : ($firstTrx->tipe_pekerjaan === 'Mingguan' ? 750000 : 120000);
        $totalNilaiTransaksi = $hargaSatuan * ($firstTrx->jumlah_qty ?? 1);

        // Potongan Fee Platform MyBantu 15%
        $feePlatform = round($totalNilaiTransaksi * 0.15);
        // Nilai bersih yang diterima worker (85%)
        $nominalCair = $totalNilaiTransaksi - $feePlatform;

        Transaction::where('id_transaksi', $idTransaksi)
            ->where('worker_id', $worker->id)
            ->update([
                'status_dana_terkirim' => 'Terkirim',
                'bank_tujuan'          => $validated['bank_tujuan'],
                'no_rekening'          => $validated['no_rekening'],
                'nama_rekening'        => $validated['nama_rekening'],
                'fee_platform'         => $feePlatform,
                'nominal_cair'         => $nominalCair,
                'waktu_cair'           => Carbon::now(),
            ]);

        $formattedNet = number_format($nominalCair, 0, ',', '.');
        $formattedFee = number_format($feePlatform, 0, ',', '.');
        return back()->with('success', "Pencairan dana #{$idTransaksi} berhasil! Dana sebesar Rp {$formattedNet} (setelah potongan fee platform 15% senilai Rp {$formattedFee}) telah berhasil diproses ke rekening {$validated['bank_tujuan']} ({$validated['no_rekening']} a.n {$validated['nama_rekening']}).");
    }

    /**
     * Tampilkan Halaman Pendapatan Worker
     */
    public function pendapatan()
    {
        $worker = Auth::guard('worker')->user();

        // Ambil semua transaksi worker yang sudah dibayar oleh user
        $transactions = Transaction::with(['user', 'worker.prices'])
            ->select(
                'id_transaksi',
                DB::raw('MAX(worker_id) as worker_id'),
                DB::raw('MAX(user_id) as user_id'),
                DB::raw('MAX(tipe_pekerjaan) as tipe_pekerjaan'),
                DB::raw('MAX(jumlah_qty) as jumlah_qty'),
                DB::raw('MAX(konfirmasi) as konfirmasi'),
                DB::raw('MAX(status_bekerja) as status_bekerja'),
                DB::raw('MAX(status_selesai) as status_selesai'),
                DB::raw('MAX(status_pembayaran) as status_pembayaran'),
                DB::raw('MAX(status_dana_terkirim) as status_dana_terkirim'),
                DB::raw('MIN(tanggal_kerja) as mulai_bekerja'),
                DB::raw('MAX(tanggal_kerja) as selesai_bekerja'),
                DB::raw('MAX(created_at) as created_at'),
                DB::raw('MAX(bank_tujuan) as bank_tujuan'),
                DB::raw('MAX(no_rekening) as no_rekening'),
                DB::raw('MAX(nama_rekening) as nama_rekening'),
                DB::raw('MAX(fee_platform) as fee_platform'),
                DB::raw('MAX(nominal_cair) as nominal_cair'),
                DB::raw('MAX(waktu_cair) as waktu_cair')
            )
            ->where('worker_id', $worker->id)
            ->where('status_pembayaran', 'Sudah Bayar')
            ->groupBy('id_transaksi')
            ->orderBy('created_at', 'desc')
            ->get();

        $prices = PriceWorker::where('worker_id', $worker->id)->get();

        $totalDiterimaBersih = 0;
        $totalTertahanBersih = 0;
        $totalFeePlatform    = 0;
        $totalSelesai        = 0;

        foreach ($transactions as $trx) {
            $priceRow = $prices->firstWhere('tipe_pekerjaan', $trx->tipe_pekerjaan);
            $hargaSatuan = $priceRow ? $priceRow->harga : ($trx->tipe_pekerjaan === 'Mingguan' ? 750000 : 120000);
            $gross = $hargaSatuan * ($trx->jumlah_qty ?? 1);
            $fee = round($gross * 0.15);
            $net = $gross - $fee;

            $trx->nominal_gross = $gross;
            $trx->nominal_fee   = ($trx->fee_platform && $trx->fee_platform > 0) ? $trx->fee_platform : $fee;
            $trx->nominal_net   = ($trx->nominal_cair && $trx->nominal_cair > 0) ? $trx->nominal_cair : $net;

            if ($trx->status_dana_terkirim === 'Terkirim') {
                $totalDiterimaBersih += $trx->nominal_net;
                $totalFeePlatform    += $trx->nominal_fee;
            } elseif (
                $trx->status_selesai === 'Selesai' 
                && $trx->status_dana_terkirim !== 'Refund ke Saldo' 
                && strtolower($trx->konfirmasi ?? '') !== 'rejected'
            ) {
                // Sesuai requirement: hanya menghitung dari pekerjaan yang sudah selesai
                $totalTertahanBersih += $trx->nominal_net;
            }

            if ($trx->status_selesai === 'Selesai') {
                $totalSelesai++;
            }
        }

        return view('worker.pendapatan', compact(
            'worker', 
            'transactions', 
            'totalDiterimaBersih', 
            'totalTertahanBersih', 
            'totalFeePlatform', 
            'totalSelesai'
        ));
    }

    /**
     * Memeriksa apakah jadwal pesanan bentrok dengan jadwal pesanan yang SUDAH dikonfirmasi (Yes)
     *
     * @param int $workerId
     * @param string $idTransaksi
     * @return array|null Info bentrok jika ada, null jika aman
     */
    protected function findScheduleConflict($workerId, $idTransaksi)
    {
        // Ambil data tanggal kerja dari pesanan yang sedang dievaluasi
        $targetRows = Transaction::where('id_transaksi', $idTransaksi)
            ->where('worker_id', $workerId)
            ->get();

        if ($targetRows->isEmpty()) {
            return null;
        }

        // Ambil list tanggal kerja spesifik
        $targetDates = $targetRows->pluck('tanggal_kerja')->filter()->unique()->values()->all();

        // Ambil tanggal mulai dan selesai
        $firstRow = $targetRows->first();
        $minDate = $targetRows->min('tanggal_kerja') ?: ($firstRow->date_from ?: date('Y-m-d'));
        $maxDate = $targetRows->max('tanggal_kerja') ?: ($firstRow->date_to ?: $minDate);

        // Cari pesanan lain milik worker yang SUDAH dikonfirmasi (konfirmasi = 'Yes')
        // dan masih aktif (belum selesai, belum ditolak, belum refund)
        $conflictingTrx = Transaction::where('worker_id', $workerId)
            ->where('id_transaksi', '!=', $idTransaksi)
            ->where('konfirmasi', 'Yes')
            ->where(function ($q) {
                $q->whereNull('status_selesai')
                  ->orWhere('status_selesai', '!=', 'Selesai');
            })
            ->where(function ($q) {
                $q->whereNull('status_bekerja')
                  ->orWhereNotIn('status_bekerja', ['Ditolak', 'Dibatalkan']);
            })
            ->where(function ($q) {
                $q->whereNull('status_dana_terkirim')
                  ->orWhere('status_dana_terkirim', '!=', 'Refund ke Saldo');
            })
            ->where(function ($q) use ($targetDates, $minDate, $maxDate) {
                if (!empty($targetDates)) {
                    $q->whereIn('tanggal_kerja', $targetDates);
                }
                if (!empty($minDate) && !empty($maxDate)) {
                    $q->orWhere(function ($sub) use ($minDate, $maxDate) {
                        $sub->where('date_from', '<=', $maxDate)
                            ->where('date_to', '>=', $minDate);
                    });
                }
            })
            ->first();

        if (!$conflictingTrx) {
            return null;
        }

        $conflictId = $conflictingTrx->id_transaksi;
        $conflictRows = Transaction::where('id_transaksi', $conflictId)->get();

        $cMin = $conflictRows->min('tanggal_kerja') ?: ($conflictingTrx->date_from ?: date('Y-m-d'));
        $cMax = $conflictRows->max('tanggal_kerja') ?: ($conflictingTrx->date_to ?: $cMin);

        $curFrom = Carbon::parse($minDate)->format('d/m/Y');
        $curTo   = Carbon::parse($maxDate)->format('d/m/Y');
        $cFrom   = Carbon::parse($cMin)->format('d/m/Y');
        $cTo     = Carbon::parse($cMax)->format('d/m/Y');

        return [
            'id_transaksi'     => $idTransaksi,
            'current_periode'  => "{$curFrom} - {$curTo}",
            'conflict_id'      => $conflictId,
            'conflict_periode' => "{$cFrom} - {$cTo}",
            'message'          => "Jadwal bentrok dengan order #{$conflictId} (Periode: {$cFrom} - {$cTo}). Anda tidak dapat menerima pesanan ini dan wajib melakukan cancel (Tidak)."
        ];
    }
}

