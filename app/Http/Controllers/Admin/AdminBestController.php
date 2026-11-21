<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Worker;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminBestController extends Controller
{
    /**
     * Halaman Data Worker dan User Majikan Terbaik
     */
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'worker');

        // ==========================================
        // 1. DATA WORKER TERBAIK (Opsi 1: Order Selesai -> Rating -> Ulasan)
        // ==========================================
        $topWorkers = Worker::select(
                'workers.*',
                DB::raw('COALESCE(AVG(ratings.rating), 0) as avg_rating'),
                DB::raw('COUNT(ratings.id) as total_reviews')
            )
            ->leftJoin('ratings', 'workers.id', '=', 'ratings.worker_id')
            ->groupBy('workers.id')
            ->get();

        foreach ($topWorkers as $worker) {
            $worker->completed_orders = Transaction::where('worker_id', $worker->id)
                ->where('status_selesai', 'selesai')
                ->distinct('id_transaksi')
                ->count('id_transaksi');

            $worker->total_earned = Transaction::where('worker_id', $worker->id)
                ->where('status_dana_terkirim', 'Terkirim')
                ->sum('nominal_cair');
        }

        // Urutkan berdasarkan Opsi 1:
        // 1. Jumlah Order Selesai (DESC)
        // 2. Rata-rata Rating Bintang (DESC)
        // 3. Jumlah Ulasan (DESC)
        $topWorkers = $topWorkers->sort(function ($a, $b) {
            if ($b->completed_orders !== $a->completed_orders) {
                return $b->completed_orders <=> $a->completed_orders;
            }
            if ($b->avg_rating != $a->avg_rating) {
                return $b->avg_rating <=> $a->avg_rating;
            }
            return $b->total_reviews <=> $a->total_reviews;
        })->values()->take(20);

        // ==========================================
        // 2. DATA USER MAJIKAN TERBAIK (Opsi 1: Total Order Terbayar -> Order Selesai -> Ulasan Diberikan)
        // ==========================================
        $topUsers = User::select(
                'users.*',
                DB::raw('COUNT(DISTINCT transactions.id_transaksi) as total_orders')
            )
            ->leftJoin('transactions', function ($join) {
                $join->on('users.id', '=', 'transactions.user_id')
                     ->where('transactions.status_pembayaran', '=', 'Sudah Bayar');
            })
            ->groupBy('users.id')
            ->get();

        foreach ($topUsers as $user) {
            $user->completed_orders = Transaction::where('user_id', $user->id)
                ->where('status_selesai', 'selesai')
                ->distinct('id_transaksi')
                ->count('id_transaksi');

            $user->reviews_given = Rating::where('user_id', $user->id)->count();
        }

        // Urutkan berdasarkan:
        // 1. Total Order Terbayar (DESC)
        // 2. Total Order Selesai (DESC)
        // 3. Jumlah Ulasan yang Diberikan (DESC)
        $topUsers = $topUsers->sort(function ($a, $b) {
            if ($b->total_orders !== $a->total_orders) {
                return $b->total_orders <=> $a->total_orders;
            }
            if ($b->completed_orders !== $a->completed_orders) {
                return $b->completed_orders <=> $a->completed_orders;
            }
            return $b->reviews_given <=> $a->reviews_given;
        })->values()->take(20);

        return view('admin.best.index', compact('topWorkers', 'topUsers', 'tab'));
    }
}

