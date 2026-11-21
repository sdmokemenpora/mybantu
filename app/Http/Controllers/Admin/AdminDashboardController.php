<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Worker;
use App\Models\Transaction;
use App\Models\Rating;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * Dashboard Utama Administrator
     */
    public function index()
    {
        // 1. Statistik Kunci
        $totalUsers = User::count();
        $totalWorkers = Worker::count();
        $workersActive = Worker::where('status_worker', 'AKTIF')->count();
        $workersPendingVerify = Worker::where(function ($q) {
            $q->where('status_data', 'belum terverifikasi')
              ->orWhereNull('status_data')
              ->orWhere('status_data', '');
        })->count();
        $workersVerified = Worker::whereIn('status_data', ['sudah terverifikasi', 'terverifikasi'])->count();
        $totalAdmins = Admin::count();

        // Transaksi & Finansial
        $totalTransactions = Transaction::distinct('id_transaksi')->count('id_transaksi');
        $completedTransactions = Transaction::where('status_selesai', 'selesai')->distinct('id_transaksi')->count('id_transaksi');
        $totalFeePlatform = Transaction::where('status_dana_terkirim', 'Terkirim')->sum('fee_platform');
        $totalDanaCair = Transaction::where('status_dana_terkirim', 'Terkirim')->sum('nominal_cair');

        // 2. Worker yang membutuhkan verifikasi segera (Terbaru)
        $pendingWorkers = Worker::where(function ($q) {
            $q->where('status_data', 'belum terverifikasi')
              ->orWhereNull('status_data')
              ->orWhere('status_data', '');
        })
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();

        // 3. Top 3 Worker Terbaik (Berdasarkan rating rata-rata & jumlah ulasan)
        $topWorkers = Worker::select('workers.*', 
                DB::raw('COALESCE(AVG(ratings.rating), 0) as avg_rating'),
                DB::raw('COUNT(ratings.id) as total_reviews')
            )
            ->leftJoin('ratings', 'workers.id', '=', 'ratings.worker_id')
            ->groupBy('workers.id')
            ->orderByDesc('avg_rating')
            ->orderByDesc('total_reviews')
            ->take(3)
            ->get();

        // Hitung juga jumlah order selesai untuk tiap top worker
        foreach ($topWorkers as $tw) {
            $tw->completed_orders = Transaction::where('worker_id', $tw->id)
                ->where('status_selesai', 'selesai')
                ->distinct('id_transaksi')
                ->count('id_transaksi');
        }

        // 4. Top 3 Majikan Terbaik (Berdasarkan total pesanan selesai/terbayar)
        $topUsers = User::select('users.*',
                DB::raw('COUNT(DISTINCT transactions.id_transaksi) as total_orders')
            )
            ->leftJoin('transactions', function ($join) {
                $join->on('users.id', '=', 'transactions.user_id')
                     ->where('transactions.status_pembayaran', '=', 'Sudah Bayar');
            })
            ->groupBy('users.id')
            ->orderByDesc('total_orders')
            ->take(3)
            ->get();

        // 5. Transaksi Terbaru (5 transaksi terakhir)
        $recentTransactions = Transaction::select(
                'id_transaksi',
                'user_id',
                'worker_id',
                'tipe_pekerjaan',
                'konfirmasi',
                'status_bekerja',
                'status_selesai',
                'status_pembayaran',
                DB::raw('MIN(tanggal_kerja) as date_from'),
                DB::raw('MAX(tanggal_kerja) as date_to'),
                DB::raw('MAX(created_at) as created_at')
            )
            ->with(['user', 'worker'])
            ->groupBy('id_transaksi', 'user_id', 'worker_id', 'tipe_pekerjaan', 'konfirmasi', 'status_bekerja', 'status_selesai', 'status_pembayaran')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalWorkers',
            'workersActive',
            'workersPendingVerify',
            'workersVerified',
            'totalAdmins',
            'totalTransactions',
            'completedTransactions',
            'totalFeePlatform',
            'totalDanaCair',
            'pendingWorkers',
            'topWorkers',
            'topUsers',
            'recentTransactions'
        ));
    }
}

