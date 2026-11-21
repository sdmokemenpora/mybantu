<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WorkerController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\MidtransCallbackController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\WorkerDashboardController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminWorkerController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminBestController;
use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\Admin\AdminSecurityLogController;

// ==========================================
// 1. NAVIGASI UTAMA (HOME & PRODUCT)
// ==========================================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/product', [WorkerController::class, 'index'])->name('product');

// Detail Worker per Provinsi (Halaman Filter Provinsi)
Route::get('/product/provinsi/{provinsi}', [WorkerController::class, 'showByProvinsi'])->name('product.provinsi');

// Detail Spesifik Worker (Halaman Pesan/Detail)
Route::get('/product/worker/{id}', [WorkerController::class, 'show'])->name('product.detail');

// Halaman Statis (Contact & About)
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'storeContact'])->name('contact.store');
Route::get('/about', function () { 
    return view('about'); 
})->name('about');

// API Data Wilayah (Kabupaten berdasarkan Provinsi dari Database)
Route::get('/api/wilayah/kabupaten', function (\Illuminate\Http\Request $request) {
    $provinsiName = $request->query('provinsi');
    $province = \App\Models\Province::where('name', $provinsiName)->first();
    if (!$province) {
        return response()->json([]);
    }
    $regencies = $province->regencies()->pluck('name');
    return response()->json($regencies);
})->name('api.wilayah.kabupaten');


// ==========================================
// 2. AUTENTIKASI (LOGIN & REGISTER)
// ==========================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Worker Auth Logout
Route::post('/worker/logout', [AuthController::class, 'logoutWorker'])->name('worker.logout');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);


// ==========================================
// 3. PENDAFTARAN SELLER / PEKERJA
// ==========================================
Route::get('/register_seller', [WorkerController::class, 'showRegisterSeller'])->name('register.seller');
Route::post('/register_seller', [WorkerController::class, 'storeSeller']);


// ==========================================
// 4. WEBHOOK & CALLBACK MIDTRANS
// ==========================================
Route::post('/midtrans/notification', [MidtransCallbackController::class, 'notification'])->name('midtrans.notification');


// ==========================================
// 5. TRANSAKSI & PROFIL USER / MAJIKAN (WAJIB LOGIN USER)
// ==========================================
Route::middleware('auth:web')->group(function () {
    // Menu Profil Majikan
    Route::get('/profil', [UserProfileController::class, 'show'])->name('user.profile');
    Route::post('/profil', [UserProfileController::class, 'update'])->name('user.profile.update');

    // Halaman List Transaksi Majikan
    Route::get('/transaksi', [TransactionController::class, 'index'])->name('transaksi');
    
    // Proses Simpan Order
    Route::post('/order/{workerId}', [TransactionController::class, 'storeOrder'])->name('order.store');
    
    // Tampilkan View Bayar (Halaman Pembayaran)
    Route::get('/transaksi/bayar/{idTransaksi}', [TransactionController::class, 'showBayar'])->name('transaksi.bayar.form');

    // Callback selesai pembayaran dari Midtrans Snap (Redirect Finish)
    Route::get('/transaksi/bayar/{idTransaksi}/finish', [TransactionController::class, 'finishBayar'])->name('transaksi.bayar.finish');

    // Fitur Ganti Metode Pembayaran (QRIS, VA Bank, Indomaret, Alfamart)
    Route::post('/transaksi/bayar/{idTransaksi}/ganti-metode', [TransactionController::class, 'gantiMetode'])->name('transaksi.bayar.ganti_metode');

    // Proses Konfirmasi Pembayaran Manual / Simulator
    Route::post('/transaksi/bayar/{idTransaksi}', [TransactionController::class, 'prosesBayar'])->name('transaksi.bayar.proses');

    // Proses Pembayaran Menggunakan Saldo Akun Majikan
    Route::post('/transaksi/bayar/{idTransaksi}/saldo', [TransactionController::class, 'bayarDenganSaldo'])->name('transaksi.bayar.saldo');

    // Konfirmasi Pekerjaan Selesai oleh Majikan (Escrow Selesai -> Worker Siap Cairkan)
    Route::post('/transaksi/{idTransaksi}/selesaikan', [TransactionController::class, 'selesaikanPesanan'])->name('transaksi.selesaikan');
    
    // Beri Rating & Review untuk Transaksi yang Sudah Selesai
    Route::post('/transaksi/{idTransaksi}/rating', [TransactionController::class, 'storeRating'])->name('transaksi.rating');

    // Proses Cancel / Hapus Order
    Route::delete('/transaksi/cancel/{idTransaksi}', [TransactionController::class, 'cancel'])->name('transaksi.cancel');
});


// ==========================================
// 5. DASHBOARD & FITUR MITRA ART / WORKER (WAJIB LOGIN WORKER)
// ==========================================
Route::middleware('auth:worker')->prefix('worker')->name('worker.')->group(function () {
    // 1. Menu Edit Profile Worker (Foto, Deskripsi, Harga Harian & Mingguan)
    Route::get('/profile', [WorkerDashboardController::class, 'profile'])->name('profile');
    Route::post('/profile', [WorkerDashboardController::class, 'updateProfile'])->name('profile.update');

    // 2. Menu Transaksi Worker (5 Status & Suggest Transaksi Rekomendasi)
    Route::get('/transaksi', [WorkerDashboardController::class, 'transaksi'])->name('transaksi');
    Route::post('/transaksi/{idTransaksi}/status', [WorkerDashboardController::class, 'updateStatus'])->name('transaksi.status');
    Route::post('/transaksi/{idTransaksi}/cairkan', [WorkerDashboardController::class, 'cairkanDana'])->name('transaksi.cairkan');

    // 3. Menu Pendapatan Worker (Saldo Rekber & Rekap Upah)
    Route::get('/pendapatan', [WorkerDashboardController::class, 'pendapatan'])->name('pendapatan');
});


// ==========================================
// 6. PORTAL ADMINISTRATOR (ADMIN)
// ==========================================
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest Admin (Login)
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');
    });

    // Authenticated Admin (Wajib Login Admin)
    Route::middleware('auth:admin')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/', function () { 
            return redirect()->route('admin.dashboard'); 
        });
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Verifikasi Data Worker (Aksi Cepat & Halaman Khusus)
        Route::get('/workers/verify', [AdminWorkerController::class, 'verifyIndex'])->name('workers.verify');
        Route::post('/workers/{id}/verify', [AdminWorkerController::class, 'updateVerification'])->name('workers.updateVerification');
        Route::post('/workers/{id}/status', [AdminWorkerController::class, 'updateStatus'])->name('workers.updateStatus');

        // Manajemen Worker (Mitra ART)
        Route::resource('workers', AdminWorkerController::class);

        // Manajemen User (Majikan)
        Route::resource('users', AdminUserController::class);

        // Worker & User Majikan Terbaik
        Route::get('/best', [AdminBestController::class, 'index'])->name('best.index');

        // Manajemen Admin (List Admin & Register Admin)
        Route::resource('admins', AdminManagementController::class);

        // Monitoring Keamanan & Log Brute Force
        Route::get('/security-logs', [AdminSecurityLogController::class, 'index'])->name('security-logs.index');
    });
});

